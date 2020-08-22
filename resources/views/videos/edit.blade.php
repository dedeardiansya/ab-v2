@extends('layouts.app')

@section('style:before')
<link href="https://unpkg.com/video.js/dist/video-js.min.css" rel="stylesheet">
<link href="https://unpkg.com/@videojs/themes@1/dist/forest/index.css" rel="stylesheet">
@endsection
@section('script:before')
<script src="https://vjs.zencdn.net/7.8.4/video.js"></script>
@endsection

@section('content')
<div class="container-fluid">
  <form class="row" method="post" enctype="multipart/form-data" action="{{ route('videos.update', $playlist->id) }}">
    @csrf
    @method('PUT')
    <div class="col-lg-8">
      <div class="panel panel-bordered">
        <div class="panel-heading">
          <h3 class="panel-title">Daftar video</h3>
        </div>
        <video-order :videos="{{ json_encode($videos) }}" upload-url="{{ route('api.admin.videos.upload', $playlist->id) }}"></video-order>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">Hero image</h3>
        </div>
        <div class="panel-body">
        <hero-chooser inline-template>
          <div class="hero-chooser">
            <img :src="url || '{{ $playlist->heroUrl()['thumb'] }}'" alt="" />
            <div class="chooser text-center pt-2">
              <label for="hero-chooser" class="btn btn-default btn-block m-0">@{{ name || 'Choose image' }}</label>
              <input id="hero-chooser" type="file" class="d-none" name="hero" @change="handleChange">
            </div>
          </div>
        </hero-chooser>
        </div>
      </div>
      <div class="panel">
        <div class="panel-heading">
          <h3 class="panel-title">Informasi</h3>
        </div>
        <div class="panel-body">
          <category-autocomplete :categories="{{json_encode($categories)}}" inline-template>
            <div class="form-group category-autocomplete">
              <label>Kategori</label>
              <input ref="input" id="autoComplete" placeholder="Kategory" type="text" name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category') ?? $playlist->category ? $playlist->category->name : '' }}">
              @error('category')
                <div class="invalid-feedback">
                  <strong>{{ $message }}</strong>
                </div>
              @enderror
            </div>
          </category-autocomplete>

          <div class="form-group">
            <label>Title</label>
            <input placeholder="Title" type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') ?? $playlist->title }}">
            @error('title')
              <div class="invalid-feedback">
                <strong>{{ $message }}</strong>
              </div>
            @enderror
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror">{{  old('description') ?? $playlist->description }}</textarea>
            @error('description')
              <div class="invalid-feedback">
                <strong>{{ $message }}</strong>
              </div>
            @enderror
          </div>

          <div class="form-group">
            <div class="checkbox-custom checkbox-primary">
              <input type="checkbox" id="public" name="public" @if(old('public') ?? !$playlist->draf) checked @endif>
              <label for="public">Publikasikan video</label>
            </div>
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection