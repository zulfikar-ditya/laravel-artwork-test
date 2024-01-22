<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ArticleRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleRepository extends BaseRepository implements ArticleRepositoryInterface
{
    /**
     * Instantiate a new ArticleRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            'title',
            'articleTags.name',
            'createdId.name',
            'createdId.email',
        ]);

        $this->setAllowableSort([
            'title',
            'created_at',
            'updated_at'
        ]);

        $this->setAllowableInclude([
            'createdId' => function ($q) {
                return $q->Select([
                    'id',
                    'name',
                    'email',
                ]);
            },
            'articleTags' => function ($q) {
                return $q->Select([
                    'id',
                    'name',
                ]);
            },
        ]);

        $this->setAllowableInclude([
            'createdId' => function ($q) {
                return $q->Select([
                    'id',
                    'name',
                    'email',
                ]);
            },
            'articleTags' => function ($q) {
                return $q->Select([
                    'id',
                    'name',
                ]);
            },
        ]);
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return Article::query()
            ->with($this->getAllowableInclude())
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->select([
                'id',
                'created_id',
                'title',
                'image',
                'created_at',
                'updated_at',
            ])
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $data): Article
    {
        $data =  Article::create($data);

        return $data;
    }

    /**
     * Show repository
     */
    public function show(string $id): Article
    {
        $data = Article::with($this->getAllowableInclude())->findOrFail($id);

        return $data;
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): Article
    {
        $model = Article::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = Article::findOrFail($id);
        $data->delete();
    }

    /**
     * Get list of articles
     */
    public function getListArticles(Request $request): LengthAwarePaginator
    {
        return Article::query()
            ->with($this->getAllowableInclude())
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->when($request->tags && is_array($request->tags), function ($q) use ($request) {
                $q->whereHas('articleTags', function ($q) use ($request) {
                    $q->whereIn('article_tags.id', $request->tags);
                });
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->select([
                'id',
                'created_id',
                'title',
                'image',
                'slug',
                'created_at',
                'updated_at',
            ])
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Show repository for client
     */
    public function getArticleBySlug(string $slug): Article
    {
        $data = Article::with($this->getAllowableInclude())->where('slug', $slug)->firstOrFail();
        $articleTags = $data->articleTags->pluck('id')->toArray();

        $data->relatedArticles = Article::query()
            ->with($this->getAllowableInclude())
            ->where('id', '!=', $data->id)
            ->when($articleTags, function ($q) use ($articleTags) {
                $q->whereHas('articleTags', function ($q) use ($articleTags) {
                    $q->whereIn('article_tags.id', $articleTags);
                });
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->select([
                'id',
                'created_id',
                'title',
                'image',
                'slug',
                'created_at',
                'updated_at',
            ])
            ->limit(3)
            ->get();

        return $data;
    }
}
