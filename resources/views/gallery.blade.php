@extends('layouts.app')

@section('content')
<link href="{{ asset('css/gallery.css') }}" rel="stylesheet">
<script src="{{ asset('js/gallery.js') }}" defer></script>
<div class="container">
    <h1>Gallery</h1>
    <div class="gallery">
        @foreach ($media_array as $media)
            @if ($media->type === "video")
                <div class="gallery-item">
                    <video controls>
                        <source src="{{ $media->url }}" type="video/mp4">
                    </video>    
                </div>
            @elseif ($media->type === "image")
                <div class="gallery-item">
                    <img src="{{ $media->url }}"/>    
                </div>
            @endif
        @endforeach
    </div>
    <div class="loading" data-next-url="{{ $next_url }}">
        <p>Loading...</p>
    </div>
</div>
@endsection
