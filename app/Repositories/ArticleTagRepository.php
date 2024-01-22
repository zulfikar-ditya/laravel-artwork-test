<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ArticleTagRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Models\ArticleTag;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleTagRepository extends BaseRepository implements ArticleTagRepositoryInterface
{
    /**
     * Instantiate a new ArticleTagRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            'name'
        ]);

        $this->setAllowableSort([
            'name',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return ArticleTag::query()
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $data): ArticleTag
    {
        $data =  ArticleTag::create($data);

        return $data;
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): ArticleTag
    {
        $model = ArticleTag::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = ArticleTag::findOrFail($id);
        $data->delete();
    }
}
