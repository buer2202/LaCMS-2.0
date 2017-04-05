@extends('layouts.home')

@section('content')
<div class="title-bg" style="height: 246px; background: url('@if($description = $cate->document) @if($img = $description->coverImage) {{ $img->uri }} @endif @endif') center no-repeat;"></div>
<div class="container">
    <div class="main">
        <div class="row">
            <div class="col-lg-3">
                <div class="panel sidebar">
                    <div class="panel-header">
                        <h2>{{ $menuTitle }}</h2>
                    </div>
                    <div class="panel-body">
                        <ul class="list-unstyled">
                            @foreach($menu as $m)
                            <li @if($m['id'] == $cate->id) class="active" @endif><a href="{{  $m->link ?: '/category/' . $m->id }}">{{ $m->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="header">
                    <div class="row">
                        <div class="col-lg-3">
                            <h1>{{ $cate->name }}</h1>
                        </div>
                        <div class="col-lg-9 text-right bread-crum">
                            <span><a href="/">首页</a></span>
                            @foreach($crumbs as $crumb)
                            <span><a href="{{ $crumb['link'] ?: '/category/' . $crumb['id'] }}">{{ $crumb['name'] }}</a></span>
                            @endforeach
                            <span class="last">{{ $cate->name }}</span>
                        </div>
                    </div>
                </div>
                <div class="content list">
                    @if($data)
                    <div class="row">
                        @foreach($data->slice(0, 3) as $doc)
                        <div class="col-lg-4">
                            <div class="img-text">
                                <div class="img" data-ride="stretch" data-zoom-effect="true"><img src="@if($img = $doc->coverImage) {{ $img->uri }} @endif" /></div>
                                <h3><a href="{{ route('document.index', $doc->id) }}">{{ $doc->title }}</a></h3>
                                <p class="text">{{ strip_tags($doc->content) }}</p>
                                <p class="detail"><a href="{{ route('document.index', $doc->id) }}">了解详情</a></p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="row">
                        <ul class="col-lg-12 list-unstyled">
                            @foreach($data->slice(3) as $doc)
                            <li class="clearfix"><a href="{{ route('document.index', $doc->id) }}"><span class="title">{{ $doc->title }}</span><span class="date">{{ date('Y-m-d', $doc->time_document) }}</span></a></li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                {{ $data->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
