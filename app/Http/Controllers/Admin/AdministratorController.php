<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\UserRepository;

class AdministratorController extends Controller
{
    protected $user;

    public function __construct(UserRepository $user)
    {
        $this->middleware('administrator')->except('index', 'resetPassword', 'updatePassword');

        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dataList = $this->user->getModel()->withTrashed()->paginate();
        $isAdministrator = ($request->user()->id == 1);

        return view('admin.administrator.index', compact('dataList', 'isAdministrator'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 验证
        $this->validate($request, [
            'name' => 'required|min:6|max:30|unique:users,name',
        ]);

        $result = $this->user->store(['name' => trim($request->name), 'password' => bcrypt(123456)]);

        if($result->wasRecentlyCreated) {
            return ['status' => 1];
        } else {
            return ['status' => 0, 'info' => '操作失败'];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data['password'] = bcrypt(123456);

        $result = $this->user->update($data, $id);

        if($result) {
            return ['status' => 1];
        } else {
            return ['status' => 0, 'info' => '操作失败'];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = $this->user->getModel()->withTrashed()->find($id);

        if(!$user) {
            return ['status' => 0, 'info' => '该用户不存在'];
        }

        if(!$user->deleted_at) {
            $result = $user->delete();
        } else {
            $user->deleted_at = null;
            $result = $user->save();
        }

        if($result) {
            return ['status' => 1];
        } else {
            return ['status' => 0, 'info' => '操作失败'];
        }
    }

    public function resetPassword()
    {
        return view('admin.administrator.resetPassword');
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'password_current' => 'required',
            'password'         => 'required|min:6|max:20|confirmed',
        ]);

        $result = $this->user->updatePassword($request->user()->id, $request->password_current, $request->password);

        if($result) {
            $err = '密码更新成功';
        } else {
            $err = $this->user->getError();
        }

        return redirect()->back()->withErrors(['err' => $err]);
    }
}
