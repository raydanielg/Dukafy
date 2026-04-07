@extends('user.layouts.app')

@section('content')
<div class="container py-5 text-center">
    <div class="card border-0 shadow-sm p-5 mx-auto" style="max-width: 600px;">
        <i class="fa-solid fa-shop-slash text-muted mb-4" style="font-size: 80px;"></i>
        <h2 class="fw-bold">No Business Found</h2>
        <p class="text-muted">You haven't added a business to your account yet. To start using Dukafy, please set up your business details.</p>
        <div class="mt-4">
            <a href="#" class="btn btn-primary btn-lg px-5 rounded-pill">
                <i class="fa-solid fa-plus me-2"></i> Create My Business
            </a>
        </div>
    </div>
</div>
@endsection
