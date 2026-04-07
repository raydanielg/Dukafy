@extends('admin.layouts.app')

@section('page_title', 'Contact Developer')

@section('content')
<div class="admin-page">
    <div class="admin-page-head">
        <div class="admin-page-title">Contact Developer Support</div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="admin-panel">
                <div class="admin-panel-head"><div class="admin-panel-title">Send Message</div></div>
                <div class="admin-panel-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Issue Type</label>
                            <select class="form-select">
                                <option>Bug Report</option>
                                <option>Feature Request</option>
                                <option>Custom Integration</option>
                                <option>General Inquiry</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-control" placeholder="Brief summary of the request">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="5" placeholder="Detailed explanation..."></textarea>
                        </div>
                        <button type="button" class="admin-action-btn" onclick="alert('Demo: Request sent to developer queue.')">Submit Request</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="admin-panel">
                <div class="admin-panel-head"><div class="admin-panel-title">Developer Contact Info</div></div>
                <div class="admin-panel-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle bg-light p-3 me-3"><i class="fa-solid fa-envelope fs-4 text-primary"></i></div>
                        <div>
                            <div class="small text-muted">Email Address</div>
                            <div class="fw-bold">support@zerixa.com</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle bg-light p-3 me-3"><i class="fa-brands fa-whatsapp fs-4 text-success"></i></div>
                        <div>
                            <div class="small text-muted">WhatsApp Support</div>
                            <div class="fw-bold">+255 7XX XXX XXX</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-light p-3 me-3"><i class="fa-solid fa-globe fs-4 text-info"></i></div>
                        <div>
                            <div class="small text-muted">Official Website</div>
                            <div class="fw-bold">www.zerixa.com</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
