<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait DataTableTrait
{
    /**
     * Process datatable request and return paginated results
     *
     * @param Request $request
     * @param Builder $query
     * @param array $searchableColumns
     * @param array $allowedSortFields
     * @param string $defaultSortField
     * @param string $defaultSortDirection
     * @return array
     */
    public function processDataTable(
        Request $request,
        Builder $query,
        array $searchableColumns = [],
        array $allowedSortFields = [],
        string $defaultSortField = 'id',
        string $defaultSortDirection = 'desc'
    ): array {
        // Get query parameters with defaults
        $search = $request->input('search', '');
        $perPage = (int) $request->input('perPage', 10);
        $sortField = $request->input('sortField', $defaultSortField);
        $sortDirection = $request->input('sortDirection', $defaultSortDirection);

        // Validate sort field
        if (!empty($allowedSortFields) && !in_array($sortField, $allowedSortFields)) {
            $sortField = $defaultSortField;
        }

        // Sanitize sort direction
        $sortDirection = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

        // Apply search if provided
        if ($search && !empty($searchableColumns)) {
            $query->where(function (Builder $q) use ($search, $searchableColumns) {
                foreach ($searchableColumns as $index => $column) {
                    if ($index === 0) {
                        $q->where($column, 'like', "%{$search}%");
                    } else {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                }
            });
        }

        // Apply sorting
        $query->orderBy($sortField, $sortDirection);

        // Get paginated results
        $data = $query->paginate($perPage)->withQueryString();

        // Return data and filters
        return [
            'data' => $data,
            'filters' => [
                'search' => $search,
                'perPage' => $perPage,
                'sortField' => $sortField,
                'sortDirection' => $sortDirection,
            ],
        ];
    }

    /**
     * Process datatable request with relationships
     *
     * @param Request $request
     * @param Builder $query
     * @param array $searchableColumns
     * @param array $allowedSortFields
     * @param array $relationships
     * @param string $defaultSortField
     * @param string $defaultSortDirection
     * @return array
     */
    public function processDataTableWithRelationships(
        Request $request,
        Builder $query,
        array $searchableColumns = [],
        array $allowedSortFields = [],
        array $relationships = [],
        string $defaultSortField = 'id',
        string $defaultSortDirection = 'desc'
    ): array {
        // Load relationships
        if (!empty($relationships)) {
            $query->with($relationships);
        }

        return $this->processDataTable(
            $request,
            $query,
            $searchableColumns,
            $allowedSortFields,
            $defaultSortField,
            $defaultSortDirection
        );
    }
}