<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\AttachmentRepository;
use App\Repositories\DocumentAttachmentRepository;

class AttachmentController extends Controller
{
    protected $attachment;

    public function __construct(AttachmentRepository $attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dataList = $this->attachment->notRefer(16);

        return view('admin.attachment.index', compact('dataList'));
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
        //
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
        // 验证
        $this->validate($request, [
            'description' => 'required|max:100',
        ]);

        $data['description'] = $request->description;
        $data['user_id_modify'] = $request->user()->id;

        $result = $this->attachment->update($data, $id);

        if($result) {
            return ['status' => 1];
        } else {
            return ['status' => 0, 'info' => '操作失败'];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if(!$request->has("id")) {
            return ['status' => 0, 'info' => '请先选择要删除的附件'];
        }

        $result = $this->attachment->batchDelete($request->id);

        if($result) {
            return ['status' => 1];
        } else {
            return ['status' => 0, 'info' => $this->attachment->getError()];
        }
    }

    /**
     * 清除无效附件关联
     */
    public function clearInvalidRelation(DocumentAttachmentRepository $docAttach) {
        $result = $docAttach->clearInvalidRelation();

        if($result) {
            return ['status' => 1];
        } else {
            return ['status' => 0, 'info' => '没有可清理的记录！'];
        }
    }
}
