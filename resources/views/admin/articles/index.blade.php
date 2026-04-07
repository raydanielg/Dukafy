@extends('admin.layouts.app')

@section('page_title', 'Articles')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Articles</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.articles.create') }}" class="admin-action-btn">New Article</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Published</th>
                            <th class="text-end">Slug</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                            <tr>
                                <td class="fw-semibold">{{ $article->title }}</td>
                                <td>{{ $article->category_name ?? '—' }}</td>
                                <td>{{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('M d, Y') : '—' }}</td>
                                <td class="text-end text-muted">{{ $article->slug }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No articles yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
