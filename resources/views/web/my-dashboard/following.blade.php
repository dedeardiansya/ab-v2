@extends('web.layouts.app')
@section('content')


@include('web.my-dashboard.partials.profile-card-lg')
@include('web.my-dashboard.partials.email-alert')
@include('web.my-dashboard.partials.my-dashboard-nav')


<div class="container-fluid mb-15 my-dashboard-layout">
  @if($followings->total() || request()->input('search'))
    <form method="get" class="form-search-lg">
      <div class="input-group">
        <input type="text" class="form-control" name="search" placeholder="Cari MiniTutor..." value="{{ request()->input('search') }}">
        <span class="input-group-btn">
          <button type="submit" class="btn btn-primary"><i class="icon wb-search" aria-hidden="true"></i></button>
        </span>
      </div>
    </form>

    @foreach($followings as $following)
      @component('web.components.minitutor_card')
        @slot('minitutor', $following)
      @endcomponent
    @endforeach
    @if(!$followings->total())
      <div class="py-100 panel panel-body">
        <h3 class="text-muted text-center">Tidak ada hasil pencarian untuk "{{ request()->input('search') }}"</h3>
      </div>
    @endif
    <div class="card card-block mb-15 empty-none">
      {{ $followings->links() }}
    </div>
  @else
    <div class="py-100 panel panel-body">
      <h3 class="text-muted text-center">Belum ada MiniTutor yang kamu Ikuti.</h3>
    </div>
  @endif
</div>

@endsection