@extends('layouts.admin')

@section('content')
<div class="container">
    <form class="form-horizontal" role="form" method="POST" action="{{ route('admin.administrator.updatePassword') }}">
        <div class="form-group">
            <label class="col-md-2 control-label">当前密码：</label>

            <div class="col-md-3">
                <input type="password" class="form-control" name="password_current">
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label">新密码：</label>

            <div class="col-md-3">
                <input type="password" class="form-control" name="password">
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label">重复新密码：</label>

            <div class="col-md-3">
                <input type="password" class="form-control" name="password_confirmation">
                @if ($errors->first())
                    <span class="help-block">
                        <strong>{{ $errors->first() }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-3 col-md-offset-2">
                <button type="submit" class="btn btn-primary">
                    修改
                </button>
            </div>
        </div>
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
    </form>
</div>
@endsection