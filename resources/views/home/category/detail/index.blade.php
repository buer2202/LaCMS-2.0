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
                            <h2>{{ $cate->name }}</h2>
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
                <div class="content">
                    @if($data)
                    <div class="row">
                        <h1 class="col-lg-12 text-center">{{ $data->title }}</h1>
                        <div class="col-lg-12 text-center info">
                            <span>发布日期：{{ date('Y-m-d', $data->time_document) }}</span>
                            <span>字体显示：<a href="javascript:void(0);" id="cmd-font-big">【大】</a><a href="javascript:void(0);" id="cmd-font-middle">【中】</a><a href="javascript:void(0);" id="cmd-font-small">【小】</a></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="main-body">{!! $data->content !!}</div>
                        </div>
                    </div>
                    @else
                    即将发布
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
$(function () {
    $('#cmd-font-big').click(function () { $('.main .content .main-body p').css('font-size', '18px'); });
    $('#cmd-font-middle').click(function () { $('.main .content .main-body p').css('font-size', '16px'); });
    $('#cmd-font-small').click(function () { $('.main .content .main-body p').css('font-size', '14px'); });
})
</script>
@endsection