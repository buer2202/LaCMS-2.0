<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\DocumentRepository;
use App\Repositories\DocumentAttachmentRepository;
use App\Repositories\AttachmentRepository;

class DocumentController extends Controller
{
    protected $document;

    public function __construct(DocumentRepository $document)
    {
        $this->document = $document;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $category_id = $request->category_id;
        $title = trim($request->title);
        $dataList = $this->document->getList($category_id, $title);

        return view('admin.document.index', compact('dataList', 'category_id', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $id = uniqid();
        $action = 'store';
        $formAction = route('admin.document.store');

        return view('admin.document.editor', compact('id', 'action', 'formAction'));
    }

    private function _checkForm($request)
    {
        // 验证
        $this->validate($request, [
            'id'              => 'required|alpha_dash|size:13',
            'title'           => 'required|max:100',
            'category_id'     => 'required|numeric|min:1',
            'title_sub'       => 'max:100',
            'template'        => 'max:50',
            'status'          => 'numeric|max:255',
            'filename'        => 'max:50',
            'sortord'         => 'numeric',
            'time_document'   => 'date',
            'seo_title'       => 'max:200',
            'seo_keywords'    => 'max:200',
            'seo_description' => 'max:200',
            'info_1'          => 'max:200',
            'info_2'          => 'max:200',
            'info_3'          => 'max:200',
            'info_4'          => 'max:200',
            'info_5'          => 'max:200',
            'info_6'          => 'max:200',
            'content'         => 'required',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, DocumentAttachmentRepository $docAttach)
    {
        $this->_checkForm($request);

        $data = array_filter($request->except(['_method', '_token']));
        $data['time_document'] = isset($data['time_document']) ? strtotime($data['time_document']) : time();
        $data['user_id_modify'] = $data['user_id_create'] = $request->user()->id;

        $model = $this->document->store($data);

        if($model->wasRecentlyCreated) {
            // 更新附件关联状态
            $docAttach->relation($model->id);

            return redirect()->route('admin.document.index');
        } else {
            return back()->withInput();
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
        $action = 'update';
        $formAction = route('admin.document.update', ['id' => $id]);
        $method = 'put';
        $row = $this->document->detail($id);

        return view('admin.document.editor', compact('id', 'action', 'formAction', 'method', 'row'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, DocumentAttachmentRepository $docAttach)
    {
        // 验证
        $this->_checkForm($request);

        $data = array_filter($request->except(['_method', '_token', 'id']));
        $data['time_document'] = isset($data['time_document']) ? strtotime($data['time_document']) : time();
        $data['user_id_modify'] = $request->user()->id;

        $result = $this->document->update($data, $id);

        if($result) {
            return redirect()->route('admin.document.index');
        } else {
            return back()->withInput();
        }
    }

    /**
     * 更新字段
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return json
     */
    public function setField(Request $request, $id)
    {
        $data = array_filter($request->except(['_method', '_token']));
        $data['user_id_modify'] = $request->user()->id;

        $result = $this->document->update($data, $id);

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
        //
    }

    /**
     * 删除附件关联
     *
     * @param  int  $attachment_id
     * @return \Illuminate\Http\Response
     */
    public function deleteRelation($attachment_id, Request $request, DocumentAttachmentRepository $docAttach)
    {
        // 删除附件关联
        $result = $docAttach->deleteRelation($request->document_id, $attachment_id);

        if($result) {
            return ['status' => 1];
        } else {
            return ['status' => 0, 'info' => '删除失败'];
        }
    }
}
