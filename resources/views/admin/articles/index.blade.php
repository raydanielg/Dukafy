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
                            <th>Slug</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                            <tr>
                                <td class="fw-semibold">{{ $article->title }}</td>
                                <td>{{ $article->category_name ?? '—' }}</td>
                                <td>{{ $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('M d, Y') : '—' }}</td>
                                <td class="text-muted">{{ $article->slug }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.articles.edit', $article->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.articles.destroy', $article->id) }}" class="d-inline" onsubmit="return confirm('Delete this article?');">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No articles yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
