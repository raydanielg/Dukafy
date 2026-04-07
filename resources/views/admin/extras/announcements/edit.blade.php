@extends('admin.layouts.app')

@section('page_title', 'Edit Announcement')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Edit Announcement</div>
        <div class="admin-page-actions">
            <a href="{{ route('admin.extras.announcements.index') }}" class="admin-action-btn admin-action-btn-ghost">Back</a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-body">
            <form method="POST" action="{{ route('admin.extras.announcements.update', $announcement->id) }}">
                @csrf

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Title</label>
                        <input name="title" class="form-control" value="{{ old('title', $announcement->title) }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Body</label>
                        <textarea name="body" class="form-control" rows="5">{{ old('body', $announcement->body) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Published At</label>
                        <input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', $announcement->published_at ? \Carbon\Carbon::parse($announcement->published_at)->format('Y-m-d\TH:i') : '') }}">
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" @checked(old('is_active', $announcement->is_active))>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <button class="admin-action-btn" type="submit">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
