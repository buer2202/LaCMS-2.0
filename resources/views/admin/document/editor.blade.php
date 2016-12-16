@extends('layouts.admin')

@section('css')
<link href="/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
<style type="text/css">
.thumb {
    width: 100%;
    height: 150px;
    background-repeat: no-repeat;
    background-position: 50% 50%;
    background-size: cover;
}
.file {
    font-size: 50px;
    display: block;
    text-align: center;
    height:150px;
    padding-top: 50px;
    margin: 0 50px;
}
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <h3 class="col-md-12">
            <p class="col-md-3">文档编辑器</p>
        </h3>
    </div>
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs" id="nav">
                <li class="active"><a href="#editor" data-toggle="tab">编辑</a></li>
                @if(isset($row) && $row->attachment->count())
                <li><a href="#attachment" data-toggle="tab">附件</a></li>
                @endif
            </ul>
        </div>
    </div>

    <div class="tab-content">
        <div id="editor" class="tab-pane active" style="margin-top: 10px;">
            <form method="post" id="form-edit" action="{{ $formAction }}">
                @if ($errors->has('id'))
                <div class="row">
                    <div class="form-group {{ $errors->has('id') ? ' has-error' : '' }}">
                        <span class="help-block">
                            <strong>{{ $errors->first('id') }}</strong>
                        </span>
                    </div>
                </div>
                @endif
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('category_id') ? ' has-error' : '' }}">
                            <label for="category_id">所属栏目</label>
                            {!! categorySelector('category_id', 'category_id', 'form-control', old('category_id') ?: (isset($row->category_id) ? $row->category_id : 0)) !!}
                            @if ($errors->has('category_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('category_id') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                            <label for="title">文档标题</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') ?: (isset($row->title) ? $row->title : '') }}" placeholder="必填" />
                            @if ($errors->has('title'))
                            <span class="help-block">
                                <strong>{{ $errors->first('title') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('title_sub') ? ' has-error' : '' }}">
                            <label for="title_sub">子标题</label>
                            <input type="text" class="form-control" id="title_sub" name="title_sub" value="{{ old('title_sub') ?: (isset($row->title_sub) ? $row->title_sub : '') }}" placeholder="选填" />
                            @if ($errors->has('title_sub'))
                            <span class="help-block">
                                <strong>{{ $errors->first('title_sub') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('template') ? ' has-error' : '' }}">
                            <label for="template">模板</label>
                            <input type="text" class="form-control" id="template" name="template" value="{{ old('template') ?: (isset($row->template) ? $row->template : 'index') }}" />
                            @if ($errors->has('template'))
                            <span class="help-block">
                                <strong>{{ $errors->first('template') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                            <label for="status">状态{{ old('status') }}</label>
                            <select class="form-control" name="status">
                                @foreach(config('system.document_status') as $key => $value)
                                <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : (isset($row->status) && $row->status == $key ? 'selected' : '') }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('status'))
                            <span class="help-block">
                                <strong>{{ $errors->first('status') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('filename') ? ' has-error' : '' }}">
                            <label for="filename">文件名</label>
                            <input type="text" class="form-control" id="filename" name="filename" value="{{ old('filename') ?: (isset($row->filename) ? $row->filename : '') }}" placeholder="选填" />
                            @if ($errors->has('filename'))
                            <span class="help-block">
                                <strong>{{ $errors->first('filename') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('sortord') ? ' has-error' : '' }}">
                            <label for="sortord">排序</label>
                            <input type="text" class="form-control" id="sortord" name="sortord" value="{{ old('sortord') ?: (isset($row->sortord) ? $row->sortord : '') }}" placeholder="数值，逆序，默认为0" />
                            @if ($errors->has('sortord'))
                            <span class="help-block">
                                <strong>{{ $errors->first('sortord') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('time_document') ? ' has-error' : '' }}">
                            <label for="time_document">文档时间</label>
                            <div class="input-group date form_date">
                                <input type="text" class="form-control" id="time_document" name="time_document" value="{{ old('time_document') ?: (isset($row['time_document']) && $row->time_document > 0 ? date('Y-m-d', $row['time_document']) : '') }}" readonly />
                                <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>

                            @if ($errors->has('time_document'))
                            <span class="help-block">
                                <strong>{{ $errors->first('time_document') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('seo_title') ? ' has-error' : '' }}">
                            <label for="seo_title">SEO-标题</label>
                            <input type="text" class="form-control" id="seo_title" name="seo_title" value="{{ old('seo_title') ?: (isset($row->seo_title) ? $row->seo_title : '') }}" placeholder="选填" />
                            @if ($errors->has('seo_title'))
                            <span class="help-block">
                                <strong>{{ $errors->first('seo_title') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('seo_keywords') ? ' has-error' : '' }}">
                            <label for="seo_keywords">SEO-关键字</label>
                            <input type="text" class="form-control" id="seo_keywords" name="seo_keywords" value="{{ old('seo_keywords') ?: (isset($row->seo_keywords) ? $row->seo_keywords : '') }}" placeholder="选填" />
                            @if ($errors->has('seo_keywords'))
                            <span class="help-block">
                                <strong>{{ $errors->first('seo_keywords') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('seo_description') ? ' has-error' : '' }}">
                            <label for="seo_description">SEO-描述</label>
                            <input type="text" class="form-control" id="seo_description" name="seo_description" value="{{ old('seo_description') ?: (isset($row->seo_description) ? $row->seo_description : '') }}" placeholder="选填" />
                            @if ($errors->has('seo_description'))
                            <span class="help-block">
                                <strong>{{ $errors->first('seo_description') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('info_1') ? ' has-error' : '' }}">
                            <label for="info_1">备选信息1</label>
                            <input type="text" class="form-control" id="info_1" name="info_1" value="{{ old('info_1') ?: (isset($row->info_1) ? $row->info_1 : '') }}" placeholder="选填" />
                            @if ($errors->has('info_1'))
                            <span class="help-block">
                                <strong>{{ $errors->first('info_1') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('info_2') ? ' has-error' : '' }}">
                            <label for="info_2">备选信息2</label>
                            <input type="text" class="form-control" id="info_2" name="info_2" value="{{ old('info_2') ?: (isset($row->info_2) ? $row->info_2 : '') }}" placeholder="选填" />
                            @if ($errors->has('info_2'))
                            <span class="help-block">
                                <strong>{{ $errors->first('info_2') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('info_3') ? ' has-error' : '' }}">
                            <label for="info_3">备选信息3</label>
                            <input type="text" class="form-control" id="info_3" name="info_3" value="{{ old('info_3') ?: (isset($row->info_3) ? $row->info_3 : '') }}" placeholder="选填" />
                            @if ($errors->has('info_3'))
                            <span class="help-block">
                                <strong>{{ $errors->first('info_3') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('info_4') ? ' has-error' : '' }}">
                            <label for="info_4">备选信息4</label>
                            <input type="text" class="form-control" id="info_4" name="info_4" value="{{ old('info_4') ?: (isset($row->info_4) ? $row->info_4 : '') }}" placeholder="选填" />
                            @if ($errors->has('info_4'))
                            <span class="help-block">
                                <strong>{{ $errors->first('info_4') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('info_5') ? ' has-error' : '' }}">
                            <label for="info_5">备选信息5</label>
                            <input type="text" class="form-control" id="info_5" name="info_5" value="{{ old('info_5') ?: (isset($row->info_5) ? $row->info_5 : '') }}" placeholder="选填" />
                            @if ($errors->has('info_5'))
                            <span class="help-block">
                                <strong>{{ $errors->first('info_5') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group {{ $errors->has('info_6') ? ' has-error' : '' }}">
                            <label for="info_6">备选信息6</label>
                            <input type="text" class="form-control" id="info_6" name="info_6" value="{{ old('info_6') ?: (isset($row->info_6) ? $row->info_6 : '') }}" placeholder="选填" />
                            @if ($errors->has('info_6'))
                            <span class="help-block">
                                <strong>{{ $errors->first('info_6') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group {{ $errors->has('content') ? ' has-error' : '' }}">
                            <label for="content">内容</label>
                            <script type="text/plain" id="myEditor" name="content"></script>
                            @if ($errors->has('content'))
                            <span class="help-block">
                                <strong>{{ $errors->first('content') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-12">
                        {!! csrf_field() !!}
                        {{ isset($method) && $method == 'put' ? method_field('PUT') : '' }}
                        <input type="hidden" name="id" value="{{ old('id') ?: $id }}" />
                        <input type="submit" class="btn btn-success btn-xl" value="提交" />
                    </div>
                </div>
            </form>
        </div>

        @if(isset($row) && $row->attachment->count())
            <div id="attachment" class="tab-pane">
                <div class="row" style="padding-top: 20px">
                    @foreach($row->attachment as $vo)
                    <div class="col-md-2">
                        <div class="thumbnail">
                            @if($vo->type == 1)
                            <a href="{{ $vo->uri }}" target="_blank">
                                <div class="thumb" style="background-image:url({{ $vo->uri }});"></div>
                            </a>
                            @else
                            <a href="{{ $vo->uri }}" target="_blank">
                                <span class="glyphicon glyphicon-file file"></span>
                            </a>
                            @endif
                            <div class="caption">
                                <p>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control input-description" value="{{ $vo->description }}" placeholder="请输入附件说明" />
                                        <span class="input-group-btn">
                                            <button class="btn btn-default btn-set-description" type="button" data-id="{{ $vo->id }}">修改</button>
                                        </span>
                                    </div>
                                </p>
                                <p>
                                    @if($vo->type == 1)
                                        @if($row->image != $vo->id)
                                        <button class="btn btn-info btn-sm btn-set-image" data-id="{{ $vo->id }}">设为封面</button>
                                        @else
                                        <button class="btn btn-warning btn-sm btn-set-image" data-id="{{ $vo->id }}" disabled>当前封面</button>
                                        @endif
                                    @endif

                                    <button class="btn btn-danger btn-sm btn-del" data-id="{{ $vo->id }}">删除</button>
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

@section('js')
<!-- datetimepicker插件 -->
<script src="/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script src="/plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js"></script>
<script>
$('.form_date').datetimepicker({
    format: 'yyyy-mm-dd',
    language: 'zh-CN',
    weekStart: 1,
    todayBtn: 1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    minView: 2,
    forceParse: 0
});
</script>

<!-- 富文本编译器插件 -->
<script type="text/javascript" src="/plugins/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/plugins/ueditor/ueditor.all.min.js"></script>

<!-- 实例化编辑器 -->
<script>
var ue = UE.getEditor('myEditor');

// 自定义参数
// 文章自定义编号，以及图文表名
ue.ready(function() {
    ue.execCommand('serverparam', {
        'document_id': "{{ $id }}",
        'document_action': "{{ $action }}"
    });

    ue.setContent('{!! old('content') ?: (isset($row->content) ? $row->content : "") !!}');
});
</script>

<script>
$(function() {
    // CSRF token
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});

    // 修改附件描述
    $(".btn-set-description").click(function () {
        $.ajax({
            type: 'post',
            url: "{{ route('admin.attachment.update', '') }}/" + $(this).data("id"),
            data: {
                description: $(this).parent().siblings(".input-description").val(),
                _method: 'PATCH'
            },
            error: function (data) {
                alert(data.responseJSON.description[0]);
            },
            success: function (data) {
                if(data.status === 1) {
                    alert("操作成功");
                } else {
                    alert(data.info);
                }
            }
        });
    });

    // 设置封面
    $(".btn-set-image").click(function () {
        $.ajax({
            type: 'post',
            url: "{{ route('admin.document.setfield', isset($row->id) ? $row->id : '') }}",
            data: {
                image: $(this).data("id"),
                _method: 'PATCH'
            },
            error: function (data) {
                alert(data.responseJSON.setfield[0]);
            },
            success: function (data) {
                alert("操作成功");
                window.location.reload();
            }
        });
    });

    // 删除附件关联
    $(".btn-del").click(function () {
        if(!confirm("删除附件？")) return false;

        var $that = $(this);
        $.post("{{ route('admin.document.deleteRelation', '') }}/" + $that.data("id"), {
            document_id: "{{ $row->id or '' }}",
            _method: 'DELETE'
        }, function(data) {
            if(data.status === 1) {
                $that.closest(".col-md-2").remove();
            } else {
                alert(data.info);
            }
        }, "json");
    });
});
</script>
@endsection
