<?php

namespace Callmeaf\Base\App\Traits\Repo;

use App\Models\User;
use Callmeaf\Base\App\Enums\EventNameSuffix;
use Callmeaf\Base\App\Enums\ExportType;
use Callmeaf\Base\App\Enums\ImportType;
use Callmeaf\Base\App\Exceptions\ExportClassDoesNotExistsException;
use Callmeaf\Base\App\Exceptions\ImportClassDoesNotExistsException;
use Callmeaf\Base\App\Models\Contracts\BaseConfigurable;
use Callmeaf\Base\App\Repo\Contracts\CoreRepoInterface;
use Callmeaf\Base\App\Services\Importer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\LazyCollection;
use Maatwebsite\Excel\Facades\Excel;

trait CoreRepoMethods
{
    public function getQuery(bool $fresh = false): Builder
    {
        return $fresh ? $this->getModel()->newQuery() : $this->query;
    }

    public function freshQuery(): self
    {
        $this->query = $this->getQuery(fresh: true);

        return $this;
    }

    public function getModel()
    {
        return $this->getQuery()->getModel();
    }

    public function getTable(): string
    {
        return $this->getModel()->getTable();
    }

    public function trashed(bool $only = true): self
    {
        if ($only) {
            $this->getQuery()->onlyTrashed();
        } else {
            $this->getQuery()->withTrashed();
        }

        return $this;
    }


    public function findBy(string $column, mixed $value)
    {
        $column ??= $this->modelKeyName();
        $model = $this->getQuery()->where(column: $column, operator: $value)->firstOrFail();

        $this->eventsCaller($model);

        return $this->toResource(model: $model);
    }

    public function findById(mixed $value)
    {
        return $this->findBy(column: $this->modelKeyName(), value: $value);
    }

    public function first()
    {
        $model = $this->getQuery()->firstOrFail();
        return $this->toResource(model: $model);
    }

    protected function modelKeyName(): string
    {
        return app($this->model)->getRouteKeyName();
    }

    public function toResource(BaseConfigurable|MissingValue|null $model)
    {
        return \Base::toResource(resource: $this->config['resources'][requestType()]['resource'], model: $model);
    }


    public function toResourceCollection(Collection|LengthAwarePaginator|LazyCollection|MissingValue $collection)
    {
        return \Base::toResourceCollection(resourceCollection: $this->config['resources'][requestType()]['resource_collection'], collection: $collection);
    }

    protected function eventsCaller(mixed ...$args): void
    {
        $events = $this->config['events'] ?? [];

        if (empty($events)) {
            return;
        }

        $events = $events[requestType()];

        $caller = debug_backtrace(limit: 2)[1]['function'];
        $eventNameSuffix = EventNameSuffix::from(value: $caller);
        $event = array_filter(array: $events, callback: fn($value, $key) => str_contains(haystack: $key, needle: $eventNameSuffix->name), mode: ARRAY_FILTER_USE_BOTH);

        if (empty($event)) {
            return;
        }

        $eventClass = array_key_first(array: $event);
        $eventClass::dispatch(...$args);
    }

    public function builder(callable $closure): CoreRepoInterface
    {
        $closure($this->getQuery());
        return $this;
    }

    public function enums(): JsonResponse
    {
        $enums = @$this->config['enums'] ?? [];

        $data = [];
        foreach ($enums as $key => $enum) {
            $data[$key] = $enum;
        }

        return response()->json($data);
    }

    public function orderBy(string $column, $direction = 'asc'): self
    {
        $this->getQuery()->orderBy($column, $direction);

        return $this;
    }

    public function latest(string $column = 'created_at'): self
    {
        $this->orderBy($column, 'desc');

        return $this;
    }

    public function oldest(string $column = 'created_at'): self
    {
        $this->orderBy($column, 'asc');

        return $this;
    }

    public function export(ExportType $type)
    {
        $exportObj = $this->config['exports'][requestType()][$type->value] ?? null;
        if (! $exportObj) {
            throw new ExportClassDoesNotExistsException(type: $type->value);
        }

        return match ($type) {
            ExportType::EXCEL => new $exportObj(),
            default => null,
        };
    }

    public function import(ImportType $type, $file): Importer
    {
        $importObj = $this->config['imports'][requestType()][$type->value] ?? null;
        if (! $importObj) {
            throw new ImportClassDoesNotExistsException(type: $type->value);
        }

        $importClass = new $importObj();
        match ($type) {
            ImportType::EXCEL => Excel::import($importClass, $file),
            default => null,
        };

        return $importClass;
    }
}
