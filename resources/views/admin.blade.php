@extends('layouts.app')

@section('content')
<link href="{{ asset('css/admin.css') }}" rel="stylesheet">
<script src="{{ asset('js/admin.js') }}" defer></script>
<div class="container">
    <h1>Admin</h1>
    <div class="alert alert-success" role="alert">
        Gallery successfully updated!
        <button type="button" class="close alert-close-button" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="action">
        <button type="button" id="accept-button" class="btn btn-success action-item" data-toggle="modal" data-target="#acceptModal">Accept</button>
        <button type="button" id="reject-button" class="btn btn-danger action-item" data-toggle="modal" data-target="#rejectModal">Reject</button>
    </div>
    <div class="gallery">
        @foreach ($media_array as $media)
            <div class="gallery-item" data-url="{{ $media->url }}" data-status="{{ $media->status }}">
                @if ($media->type === "video")
                    <video class="video" controls>
                        <source src="{{ $media->url }}" type="video/mp4">
                    </video>    
                @elseif ($media->type === "image")              
                    <img src="{{ $media->url }}"/>    
                @endif
                <div class="gallery-item-select-overlay"></div>
                <div class="gallery-item-reject-overlay"></div>
            </div>
        @endforeach
    </div>
    <div class="modal fade" id="acceptModal" tabindex="-1" role="dialog" aria-labelledby="acceptModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="acceptModalLabel">Confirm accept content?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Content will appear in gallery.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close-button" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary modal-confirm-button" data-dismiss="modal" data-action="accept">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Confirm reject content?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Content will not appear in gallery.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close-button" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary modal-confirm-button" data-dismiss="modal" data-action="reject">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
