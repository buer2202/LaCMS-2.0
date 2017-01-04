@extends('layouts.admin')

@section('css')
<style type="text/css">
.tree ul {margin-left: 2em; margin-bottom: 0; display: none;}
.tree>ul {margin-left: 0; display: block;}
.tree ul li {line-height: 35px;}
.tree ul li button {margin-left: 5px;}
.tree .info {padding: 0 5px;}
.tree .info:hover {background: #f0f0f0;}
.menu-icon {margin-right: 5px;}
.status-disabled {color: #aaa;}
.strong {font-weight: bold;}
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <h3 class="col-md-12">栏目管理</h3>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body tree">
                    <button type="button" id="add-root" class="btn btn-default btn-sm">添加新栏目</button>
                    {{ outputTree($tree) }}
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default" id="editor" style="display:none;">
                <div class="panel-body">
                    <form id="form-editor" action="{{ route('admin.category.store') }}">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="parent_id">父栏目</label>
                                    {!! categorySelector('parent_id', 'parent_id', 'form-control', 0, true) !!}
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="type">栏目类型</label>
                                    <select class="form-control" id="type" name="type">
                                        @foreach(config('system.category_type') as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label class="control-label" for="name">栏目名称</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="栏目名称" />
                                    <label class="control-label block-helper"></label>
                                </div>
                            </div>
                        </div>

                        <hr />

                        <div class="row">
                            <div class="col-xs-6">
                                <label class="control-label" for="name">内容</label>
                                <div class="input-group">
                                    <input id="document-title" type="text" class="form-control" disabled />
                                    <span class="input-group-btn">
                                        <button id="document-modal" class="btn btn-default" type="button">...</button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="sortord">排序</label>
                                    <input type="text" class="form-control" id="sortord" name="sortord" value="1" />
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="link">链接</label>
                                    <input type="text" class="form-control" id="link" name="link" placeholder="选填" />
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="template">模板</label>
                                    <input type="text" class="form-control" id="template" name="template" value="index" />
                                </div>
                            </div>
                        </div>

                        <hr />

                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="seo_title">SEO标题</label>
                                    <input type="text" class="form-control" id="seo_title" name="seo_title" placeholder="SEO标题" />
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="seo_keywords">SEO关键字</label>
                                    <input type="text" class="form-control" id="seo_keywords" name="seo_keywords" placeholder="SEO关键字" />
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="seo_description">SEO描述</label>
                                    <input type="text" class="form-control" id="seo_description" name="seo_description" placeholder="SEO描述" />
                                </div>
                            </div>
                        </div>

                        <hr />

                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="info_1">备选信息1</label>
                                    <input type="text" class="form-control" id="info_1" name="info_1" placeholder="备选信息1" />
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="info_1">备选信息2</label>
                                    <input type="text" class="form-control" id="info_2" name="info_2" placeholder="备选信息2" />
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="info_1">备选信息3</label>
                                    <input type="text" class="form-control" id="info_3" name="info_3" placeholder="备选信息3" />
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="info_1">备选信息4</label>
                                    <input type="text" class="form-control" id="info_4" name="info_4" placeholder="备选信息4" />
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="info_1">备选信息5</label>
                                    <input type="text" class="form-control" id="info_5" name="info_5" placeholder="备选信息5" />
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="info_1">备选信息6</label>
                                    <input type="text" class="form-control" id="info_6" name="info_6" placeholder="备选信息6" />
                                </div>
                            </div>
                            <div class="col-xs-12">
                                {{ method_field('PUT') }}
                                <button type="button" id="submit" class="btn btn-success">提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="docuemnt-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(function () {
    // 工具提示
    // $('.tree button').tooltip();

    // ajax请求头添加csrf令牌
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    // 当前编辑的栏目id
    var categoryId;

    // 新增时初始化表单
    function trait_addnew() {
        $("#editor").show();
        $("#form-editor")[0].reset();
        $("#form-editor").attr("action", "{{ route('admin.category.store') }}");
        $("#form-editor").find("[name='_method']").val('post');
        $(".block-helper").text('');
        $(".form-group").removeClass('has-error');
        categoryId = 0;
    }

    // 选择时高亮
    function trait_bg_highlight(obj) {
        $(".tree .info").removeClass("bg-info").find(".name").removeClass("strong");
        obj.parents('.info').addClass("bg-info").find(".name").addClass("strong");
        $(".block-helper").text('');
    }

    // 鼠标经过显示菜单
    $(".tree .info").mouseover(function () {
        $(this).find(".button-box").show();
    }).mouseout(function () {
        $(this).find(".button-box").hide();
    });

    // 添加新栏目
    $("#add-root").click(function () {
        trait_addnew();
    });

    // 添加子栏目
    $(".add").click(function () {
        trait_addnew();
        trait_bg_highlight($(this));
        $("#parent_id").val($(this).data('id'));

        return false;
    });

    // 编辑栏目
    $(".edit").click(function () {
        var $that = $(this);
        $.get("{{ route('admin.category.show', '') }}/" + $(this).data("id"), function (data) {
            if(typeof(data.category.name) == 'undefined') {
                alert('栏目信息发生变化，请刷新页面！');
                return false;
            }

            $("#form-editor").find("[name='_method']").val('put').end()
                .find("[name='parent_id']").val(data.category.parent_id).end()
                .find("[name='name']").val(data.category.name).end()
                .find("[name='type']").val(data.category.type).end()

                .find("[name='sortord']").val(data.category.sortord).end()
                .find("[name='nav_sortord']").val(data.category.nav_sortord).end()
                .find("[name='link']").val(data.category.link).end()
                .find("[name='template']").val(data.category.template).end()

                .find("[name='seo_title']").val(data.category.seo_title).end()
                .find("[name='seo_keywords']").val(data.category.seo_keywords).end()
                .find("[name='seo_description']").val(data.category.seo_description).end()

                .find("[name='info_1']").val(data.category.info_1).end()
                .find("[name='info_2']").val(data.category.info_2).end()
                .find("[name='info_3']").val(data.category.info_3).end()
                .find("[name='info_4']").val(data.category.info_4).end()
                .find("[name='info_5']").val(data.category.info_5).end()
                .find("[name='info_6']").val(data.category.info_6).end();

            $("#editor").show();
            $("#form-editor").attr("action", "{{ route('admin.category.update', '') }}/" + data.category.id);
            categoryId = data.category.id;
            $("#document-title").val(data.document.title);

            trait_bg_highlight($that);
        });

        return false;
    });

    // 提交表单
    $("#submit").click(function () {
        $.ajax({
            type: 'post',
            url: $("#form-editor").attr("action"),
            error: function (data) {
                for(field in data.responseJSON) {
                    $("[name='" + field + "']").next('.block-helper').text(data.responseJSON[field][0]).parent().addClass("has-error");
                }
            },
            data: $("#form-editor").serialize(),
            success: function (data) {
                if(!data.status) {
                    alert(data.info);
                } else {
                    alert('操作成功，即将刷新页面！');
                    window.location.reload();
                }
            }
        });
    });

    // 设置栏目状态
    $(".set-status").click(function () {
        var $this = $(this);
        var status = $this.data("status");
        if(status == 0 && !confirm("确认删除")) return false;

        $.post("{{ route('admin.category.status', '') }}/" + $(this).data("id"), {
            status: status,
            _method: 'patch'
        }, function (data) {
            if(data.status === 1) {
                if(status == 0) {
                    window.location.reload();
                } else if (status == 1) {
                    $this.removeClass('btn-success').addClass('btn-warning').data('status', 2).attr('title', '禁用本栏目')
                        .find('span').removeClass('glyphicon-ok-circle').addClass('glyphicon-ban-circle');
                    $this.parent().siblings(".name").removeClass("status-disabled");
                } else {
                    $this.removeClass('btn-warning').addClass('btn-success').data('status', 1).attr('title', '启用本栏目')
                        .find('span').removeClass('glyphicon-ban-circle').addClass('glyphicon-ok-circle');
                    $this.parent().siblings(".name").addClass("status-disabled");

                    // 禁用后，所有子栏目都要改状态
                    $this.closest("ul").find("ul .set-status").each(function () {
                        if($(this).data("status") != 0) {
                            $(this).removeClass('btn-warning').addClass('btn-success').data('status', 1).attr('title', '启用本栏目')
                            .find('span').removeClass('glyphicon-ban-circle').addClass('glyphicon-ok-circle');
                            $(this).parent().siblings(".name").addClass("status-disabled");
                        }
                    });
                }
            } else {
                alert(data.info);
            }
        }, "json");
        return false;
    });

    // 点击展开收起
    $('.info').click(function () {
        var $this = $(this), $arraw = $this.children('.glyphicon');
        if($arraw.length) {
            if($arraw.is('.glyphicon-chevron-right')) {
                $arraw.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down')
                    .parent().siblings('ul').slideDown();
            } else {
                $arraw.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right')
                    .parent().siblings('ul').slideUp();
            }
        }
    });

    $('.tree ul li .info').each(function () {
        var $this = $(this);
        if($this.siblings('ul').length == 0) {
            $this.css('padding-left', '24px').children('.glyphicon').remove();
        }
    });

    // 栏目描述文档的模态框弹出
    $("#document-modal").click(function () {
        $.get("{{ route('admin.category.document', '') }}/" + categoryId, function (data) {
            $('#docuemnt-modal .modal-body').html(data.html);
            if(!data.category) {
                $("#docuemnt-modal .modal-header h4").text("新增栏目");
            } else {
                $("#docuemnt-modal .modal-header h4").text("文档列表 - " + data.category.name);
            }

            $("#docuemnt-modal").modal("toggle");
        }, "json");
    });

    // 选择一个文档作为栏目描述
    $("#docuemnt-modal").on("click", "a.list-group-item", function () {
        var $that = $(this);
        $.post("{{ route('admin.category.setDocument', '') }}/" + categoryId, {
            document_id: $that.data("id")
        }, function (data) {
            if(data.status === 1) {
                $that.addClass("active").siblings().removeClass("active");
                $("#document-title").val($that.text());
            } else {
                alert(data.info);
            }
        }, "json");
    });
});
</script>
@endsection
