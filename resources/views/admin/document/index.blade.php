@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>文档管理</h3>

    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="sr-only" for="exampleInputEmail2">Email address</label>
            {!! categorySelector('category_id', 'category_id', 'form-control', $category_id, 1) !!}
        </div>
        <div class="form-group">
            <label class="sr-only" for="title">标题</label>
            <input type="text" class="form-control" name="title" id="title" placeholder="标题（模糊）" value="{{ $title }}">
        </div>
        <button type="submit" class="btn btn-success">查询</button>
        <a href="{{ route('admin.document.create') }}" class="btn btn-primary">新增</a>
    </form>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>文章标题</th>
                <th>栏目</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataList as $data)
            <tr>
                <td>{{ $data->id }}</td>
                <td>{{ $data->title }}</td>
                <td>{{ $data->category_name or '空栏目' }}</td>
                <td>{{ config('system.document_status')[$data->status] }}</td>
                <td>
                    <a href="{{ route('admin.document.edit', ['id' => $data->id]) }}" class="btn btn-info btn-xs btn-edit">编辑</a>

                    @if ($data->status == 1)
                        <button type="button" class="btn btn-warning btn-xs set-status" data-id="{{ $data->id }}" data-status="2">禁用</button>
                    @else
                        <button type="button" class="btn btn-success btn-xs set-status" data-id="{{ $data->id }}" data-status="1">启用</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {!! $dataList->appends(['category_id' => $category_id, 'title' => $title])->links() !!}
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

    // 设置文档状态
    $(".set-status").click(function () {
        $.post("{{ route('admin.document.setfield', '') }}/" + $(this).data('id'), {
            _method: 'PATCH',
            status: $(this).data("status")
        }, function (data) {
            if(data.status === 1) {
                window.location.reload();
            } else {
                alert(data.info);
            }
        }, "json");
    });
});
</script>
@endsection
