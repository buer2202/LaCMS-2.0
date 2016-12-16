@extends('layouts.admin')

@section('css')
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
.description {
    height: 30px;
    overflow: hidden;
    margin: 0;
    line-height: 30px;
    cursor: help;
}

#delete {
    margin-left: 10px;
}

input.big-checkbox {
    width: 25px;
    height: 25px;
    opacity: 1;
    float: left;
    margin: 4px;
}
</style>
@endsection

@section('content')
<div class="container">
    <h3>附件清理</h3>
    <h5>未使用附件列表：</h5>

    @if($dataList->count() > 0)
    <div>
        <label class="checkbox-inline">
            <input type="checkbox" id="check-all" /> 全选
        </label>
        <button type="button" class="btn btn-danger btn-xs" id="delete">删除</button>
    </div>
    @endif
    <div class="row" style="margin-top: 10px;">
        <form id="from-attachment">
            @forelse($dataList as $data)
            <div class="col-sm-3 col-md-2">
                <div class="thumbnail">
                    <input type="checkbox" class="destroy big-checkbox" name="id[]" value="{{ $data->id }}" />
                    @if($data->type == 1)
                    <a href="{{ $data->uri }}" target="_blank">
                        <div class="thumb" style="background-image:url({{ $data->uri }});"></div>
                    </a>
                    @elseif($data->type == 2)
                    <a href="{{ $data->uri }}" target="_blank">
                        <span class="glyphicon glyphicon-file file"></span>
                    </a>
                    @endif
                    <div class="caption">
                        <p class="description" title="{{ $data->description }}">
                            {{ $data->description }}
                        </p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-md-6">
                <div class="alert alert-success" role="alert">
                    没有需要清理的附件了！
                    <button id="clear-invalid-relation" type="button" class="btn btn-success btn-xs pull-right" title="此操作仅清理无效的附件关联，请放心操作！">深度清理</button>
                </div>
            </div>
            @endforelse
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
        </form>
    </div>
    <p>{{ $dataList->links() }}</p>
</div>
@endsection

@section('js')
<script>
$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 全选/全部选
    $("#check-all").change(function () {
        if($(this).is(":checked")) {
            $(".destroy").prop("checked", true);
        } else {
            $(".destroy").prop("checked", false);
        }
    });

    // 勾选子项目与全选框联动
    $(".destroy").change(function () {
        if($(this).is(":checked")) {
            var checkAll = true;
            $(".destroy").each(function () {
                if(!$(this).is(":checked")) {
                    checkAll = false;
                    return false;
                }
            });
            $("#check-all").prop("checked", checkAll);
        } else {
            $("#check-all").prop("checked", false);
        }
    });

    // 删除
    $("#delete").click(function () {
        if(!confirm('确定删除选中的附件？')) return false;

        $.post("{{ route('admin.attachment.destroy', '0') }}", $("#from-attachment").serialize(), function (data) {
            if(data.status === 1) {
                $(".destroy").each(function () {
                    window.location.reload();
                });
            } else {
                alert(data.info);
            }
        }, "json");
    });

    // 清除无效关联
    $("#clear-invalid-relation").click(function () {
        $.post("{{ route('admin.attachment.clearInvalidRelation') }}", {
                _method: 'DELETE'
        }, function (data) {
            if(data.status === 1) {
                alert("操作成功！");
            } else {
                alert(data.info);
            }
        }, "json");
    });
});
</script>
@endsection
