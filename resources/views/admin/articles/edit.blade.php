@extends('admin.layouts.app')

@section('page_title', 'Edit Article')

@section('content')
<div class="admin-page animate__animated animate__fadeIn">
    <div class="admin-page-head">
        <div class="admin-page-title">Edit Article</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.articles.index') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.articles.update', $article->id) }}" class="admin-form" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" value="{{ old('title', $article->title) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $article->slug) }}" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">—</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (int) $article->category_id === (int) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Audience / Tag</label>
                        <input type="text" name="age_range" value="{{ old('age_range', $article->age_range) }}" class="form-control" placeholder="e.g. Retail, Wholesale, Accounting">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Featured</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="is_featured" name="is_featured" {{ $article->is_featured ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Mark as featured</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Upload Image (optional)</label>
                        <input type="file" name="image_file" class="form-control">
                        <div class="form-text">Max 5MB. Stored in public disk.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Or Image Path / URL</label>
                        <input type="text" name="image" value="{{ old('image', $article->image) }}" class="form-control" placeholder="e.g. images/blog/cover.png or https://...">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Excerpt</label>
                        <textarea name="excerpt" rows="2" class="form-control">{{ old('excerpt', $article->excerpt) }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Content (HTML)</label>
                        <textarea name="content" rows="10" class="form-control" required>{{ old('content', $article->content) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Published At</label>
                        <input type="date" name="published_at" value="{{ old('published_at', $article->published_at) }}" class="form-control">
                    </div>

                    <div class="col-md-12">
                        <button class="admin-action-btn" type="submit">Save Changes</button>
                    </div>
                </div>
            </form>

            <div class="mt-4">
                <form method="POST" action="{{ route('admin.articles.destroy', $article->id) }}" onsubmit="return confirm('Delete this article?');">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete Article</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
