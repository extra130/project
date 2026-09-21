<?php

namespace App\Services;

use App\Models\Record;
use Illuminate\Database\Eloquent\Builder;

/**
 * RecordSearchService
 *
 * Handles search across records, projects, modules, and record_files
 * per spec §19 and §20.
 */
class RecordSearchService
{
    /**
     * Build a query with the given filter parameters.
     *
     * @param array{
     *   project_id?: int|null,
     *   module_id?: int|null,
     *   type?: string|null,
     *   keyword?: string|null,
     *   source?: string|null,
     *   date_from?: string|null,
     *   date_to?: string|null,
     * } $filters
     */
    public function search(array $filters): Builder
    {
        $query = Record::with(['project', 'module', 'files'])
            ->orderByDesc('created_at');

        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (!empty($filters['module_id'])) {
            $query->where('module_id', $filters['module_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['tag'])) {
            $query->whereHas('tags', function (Builder $q) use ($filters) {
                $q->where('name', $filters['tag']);
            });
        }

        if (!empty($filters['keyword'])) {
            $kw = $filters['keyword'];

            $query->where(function (Builder $q) use ($kw) {
                // Search in records fields
                $q->where('title', 'like', "%{$kw}%")
                  ->orWhere('content', 'like', "%{$kw}%")
                  ->orWhere('git_branch', 'like', "%{$kw}%")
                  ->orWhere('git_commit', 'like', "%{$kw}%")
                  // Search in record_files fields
                  ->orWhereHas('files', function (Builder $fq) use ($kw) {
                      $fq->where('original_name', 'like', "%{$kw}%")
                         ->orWhere('display_name', 'like', "%{$kw}%")
                         ->orWhere('note', 'like', "%{$kw}%");
                  })
                  // Search in project fields
                  ->orWhereHas('project', function (Builder $pq) use ($kw) {
                      $pq->where('name', 'like', "%{$kw}%")
                         ->orWhere('description', 'like', "%{$kw}%");
                  })
                  // Search in module fields
                  ->orWhereHas('module', function (Builder $mq) use ($kw) {
                      $mq->where('name', 'like', "%{$kw}%")
                         ->orWhere('description', 'like', "%{$kw}%");
                  });
            });
        }

        return $query;
    }
}
