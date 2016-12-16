@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>
        系统管理员
        @if($isAdministrator)
        <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#create-user">添加新用户</button>
        @endif
    </h3>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>用户名</th>
                <th>创建时间</th>
                <th>更新时间</th>
                @if($isAdministrator)
                <th>操作</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($dataList as $data)
            <tr>
                <td>{{ $data->id }}</td>
                <td>{{ $data->name }}</td>
                <td>{{ $data->created_at }}</td>
                <td>{{ $data->updated_at }}</td>
                @if($isAdministrator)
                <td>
                    @if($data->id != 1)
                        <button class="btn btn-info btn-xs reset-pwd" data-id="{{ $data->id }}">重置密码</button>

                        @if ($data->deleted_at === null)
                            <button class="btn btn-warning btn-xs set-status" data-id="{{ $data->id }}">禁用</button>
                        @else
                            <button class="btn btn-success btn-xs set-status" data-id="{{ $data->id }}">启用</button>
                        @endif
                    @else
                        -----
                    @endif
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    {!! $dataList->links() !!}
</div>

<!-- Modal -->
<div class="modal fade" id="create-user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">添加新用户</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">用户名：</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="name" placeholder="新用户密码为：123456">
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-success" id="create-user-submit">创建</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section ('js')
<script>
$(function () {
    // ajax请求头添加csrf令牌
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    // 设置状态
    $(".set-status").click(function () {
        $.post("{{ route('admin.administrator.destroy', '') }}/" + $(this).data('id'), {
            _method: 'DELETE'
        }, function (data) {
            if(data.status === 1) {
                location.reload();
            } else {
                alert(data.info);
            }
        }, "json");
    });

    // 新建
    $("#create-user-submit").click(function () {
        $.post("{{ route('admin.administrator.store') }}", {
            name: $("#name").val()
        }, function (data) {
            if(data.status) {
                location.reload();
            } else {
                alert(data.info);
            }
        }, "json");

        $.ajax({
            type: 'POST',
            url: "{{ route('admin.administrator.store') }}",
            data: {name: $("#name").val()},
            dataType: "json",
            error: function (data) {
                alert(data.responseJSON.name[0]);
            },
            success: function (data) {
                if(!data.status) {
                    alert(data.info);
                } else {
                    window.location.reload();
                }
            }
        });
    });

    // 重置密码
    $(".reset-pwd").click(function () {
        if(!confirm("重置密码为：123456")) return false;

        $.post("{{ route('admin.administrator.update', '') }}/" + $(this).data("id"), {
            _method: "PUT"
        }, function (data) {
            if(!data.status) {
                alert(data.info);
            } else {
                alert("操作成功！");
            }
        }, "json");
    });
});
</script>
@endsection
