@extends('mobile/layout')

@section('title', '提示')

@section('content')
    <article class="weui-article">
        <h1>{{ $message }}</h1>
    </article>
@endsection