@extends('layouts.app')

@section('content')
<link href="{{ asset('css/gallery.css') }}" rel="stylesheet">
<script src="{{ asset('js/gallery.js') }}" defer></script>
<div class="container">
    <h1>Gallery</h1>
    <div class="gallery">
        @foreach ($media_array as $media)
            <div class="gallery-item">
                <a href="{{ $media->link }}" target="_blank">
                    @if ($media->type === "video")
                        <div class="video-container">
                            <video>
                                <source src="{{ $media->url }}" type="video/mp4">
                            </video>
                            <div class="video-overlay">
                                <span class="oi" data-glyph="media-play"></span>
                            </div>
                        </div>
                    @elseif ($media->type === "image")
                        <img src="{{ $media->url }}"/>    
                    @endif
                </a>
            </div> 
        @endforeach
    </div>
    <div class="loading">
        <p>No more content to load.</p>
    </div>
</div>
@endsection
