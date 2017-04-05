@extends('layouts.home')

@section('content')
<!-- banner 轮播 -->
<div class="container-fluid banner">
    <div class="row">
        <div class="col-lg-12 f-rolling f-rolling-mask" id="home-banner" data-ride="rolling" data-num="1" data-auto-width="true" data-auto-indicators="false">
            <ul class="f-rolling-images">
                @foreach($slideShow as $val)
                <li><a href="{{ $val->description }}" target="_blank"><img src="{{ $val->uri }}" /></a></li>
                @endForeach
            </ul>
            <a href="javascript:void(0);" class="f-rolling-btn f-rolling-btn-prev" data-target="#home-banner">prev</a>
            <a href="javascript:void(0);" class="f-rolling-btn f-rolling-btn-next" data-target="#home-banner">next</a>
        </div>
    </div>
</div>
<!-- /banner 轮播 -->

<!-- 快捷导航 -->
<div class="q-nav">
    <div class="container">
        <div class="row">
            @foreach($quickNav as $k => $val)
            <div class="col-lg-2 text-center q-nav-items q-nav-item{{ $k + 1 }} {{ $k === 0 ? 'active' : '' }}"><a href="{{ $val->link }}">{{ $val->name }}</a></div>
            @endforeach
        </div>
    </div>
</div>
<!-- /快捷导航 -->

<div class="container">
    <div class="row ">
        <div class="col-lg-8">
            <div class="panel news">
                <div class="panel-heading clearfix">
                    <div class="row">
                        <h3 class="col-lg-10"><span>{{ $solutions->name }}</span></h3>
                        <a class="col-lg-2 more text-center" href="{{ route('category.index', $solutions->id) }}">MORE</a>
                    </div>
                </div>
                <div class="palel-body">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="f-rolling f-rolling-mask" id="home-news" data-ride="rolling" data-num="1" data-auto-width="true">
                                <div class="f-rolling-whole"></div>
                                <ul class="f-rolling-images">
                                    @if($documents = $solutions->documents()->orderBy('sortord', 'desc')->orderBy('id', 'desc')->limit(6)->get())
                                        @foreach($documents as $value)
                                        <li>
                                            <a href="{{ route('document.index', $value->id) }}"><img src="@if($img = $value->coverImage) {{ $img->uri }} @endif" /></a>
                                            <div class="f-rolling-text"><div style="width: 135px">{{ $value->title }}</div></div>
                                        </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <ul class="list list-unstyled">
                                @if($documents)
                                    @foreach($documents as $value)
                                    <li><a href="{{ route('document.index', $value->id) }}">{{ $value->title }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel summary">
                <div class="panel-heading">
                    <div class="row">
                        <h3 class="col-lg-8"><span>{{ $aboutUs->name }}</span></h3>
                        <a class="col-lg-4 more text-center" href="{{ route('category.index', $aboutUs->id) }}">MORE</a>
                    </div>
                </div>
                <div class="panel-body">{{ mb_substr(strip_tags($aboutUs->document->content), 0, 200) }}</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="panel expert">
                <div class="panel-heading">
                    <div class="row">
                        <h3 class="col-lg-12"><span>{{ $products->name }}</span></h3>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="f-rolling" id="home-expert" data-ride="rolling" data-num="4" data-auto-indicators="false">
                                <div class="f-rolling-mask">
                                    <ul class="f-rolling-images">
                                        @if($documents = $products->documents()->orderBy('sortord', 'desc')->orderBy('id', 'desc')->limit(10)->get())
                                            @foreach($documents as $value)
                                            <li>
                                                <a href="{{ route('document.index', $value->id) }}"><img src="@if($img = $value->coverImage) {{ $img->uri }} @endif" /></a>
                                                <div class="f-rolling-text">
                                                    <p class="name">{{ $value->title }}</p>
                                                    <p class="ranks">{{ $value->info_1 }}</p>
                                                </div>
                                            </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                                <a href="javascript:void(0);" class="f-rolling-btn f-rolling-btn-prev" data-target="#home-expert">prev</a>
                                <a href="javascript:void(0);" class="f-rolling-btn f-rolling-btn-next" data-target="#home-expert">next</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel insurance">
                <div class="panel-heading">
                    <div class="row">
                        <h3 class="col-lg-8"><span>{{ $services->name }}</span></h3>
                        <a class="col-lg-4 more text-center" href="{{ route('category.index', $services->id) }}">MORE</a>
                    </div>
                </div>
                <div class="panel-body">
                    <ul class="list list-unstyled">
                        @if($documents = $services->documents()->orderBy('sortord', 'desc')->orderBy('id', 'desc')->limit(7)->get())
                            @foreach($documents as $value)
                            <li><a href="{{ route('document.index', $value->id) }}">{{ $value->title }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel departments" data-ride="tab">
                <div class="panel-heading">
                    <div class="row">
                        <h3 class="col-lg-2"><span>{{ $support->name }}</span></h3>
                        <div class="col-lg-8">
                            <ul class="f-tab-nav nav nav-tabs">
                                @if($documents = $support->documents()->orderBy('sortord', 'desc')->orderBy('id', 'desc')->limit(6)->get())
                                    @foreach($documents as $key => $value)
                                    <li @if($key === 0) class="active" @endif><a href="{{ route('document.index', $value->id) }}">{{ mb_substr($value->title, 0, 4) }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        <div class="col-lg-2">
                            <div class="row">
                                <a class="col-lg-8 col-lg-offset-4 more text-center" href="{{ route('category.index', $support->id) }}">MORE</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <ul class="f-tab-content list-unstyled">
                        @if($documents)
                            @foreach($documents as $key => $value)
                            <li class="row @if($key === 0) active @endif">
                                @if($images = $value->attachment()->orderBy('updated_at', 'desc')->limit(2)->get())
                                    @foreach($images as $img)
                                    <div class="col-lg-3" data-ride="stretch">
                                        <div class="img"><img src="{{ $img->uri }}" /></div>
                                    </div>
                                    @endforeach
                                @endif
                                <div class="col-lg-6">
                                    <div class="doc">
                                        <h4>{{ $value->title }}</h4>
                                        {{ $value->info_1 }}
                                    </div>
                                    <div class="intro">
                                        {{ $value->info_2 }}
                                        <a href="{{ route('document.index', $value->id) }}">[详细]</a>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
$(function () {
    $('.q-nav-items').mouseover(function () {
        $(this).addClass('active').siblings().removeClass('active');
    });
});
</script>
@endsection
