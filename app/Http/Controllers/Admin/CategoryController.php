<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\CategoryRepository;
use App\Document;

class CategoryController extends Controller
{
    protected $category;

    public function __construct(CategoryRepository $category)
    {
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tree = $this->category->tree();

        return view('admin.category.index', compact('tree'));
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

    private function _checkForm($request)
    {
        $this->validate($request, [
            'name' => 'required|max:100',
        ]);
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
        $this->_checkForm($request);

        $data = array_filter($request->except(['_method', '_token']));
        $data['user_id_modify'] = $data['user_id_create'] = $request->user()->id;

        $cid = $this->category->store($data);

        if($cid) {
            return ['status' => 1];
        } else {
            return ['status' => 0, 'info' => $this->category->getError()];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(\App\Category $category)
    {
        $document = [];
        if($category->document_id) {
            $document = Document::where('id', $category->document_id)->first();
        }

        return ['category' => $category, 'document' => $document];
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
        $this->_checkForm($request);

        $data = array_filter($request->except(['_method', '_token']));
        $data['user_id_modify'] = $request->user()->id;

        $result = $this->category->modify($data, $id);

        if($result) {
            return ['status' => 1];
        } else {
            return ['status' => 0, 'info' => $this->category->getError()];
        }
    }

    /**
     * 设置状态
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return json
     */
    public function setStatus(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'status' => 'required|numeric|max:2',
        ]);

        $result = $this->category->setStatus($id, $request->status, $request->user()->id);

        if($result) {
            return ['status' => 1];
        } else {
            return ['status' => 0, 'info' => $this->category->getError()];
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
     * 获取栏目文档
     * @param
     */
    public function document($id)
    {
        $category = $this->category->getModel()->find($id);
        $document = $category ? $category->documents()->where('status', 1)->get() : [];

        $html = view('admin.category.document', compact('category', 'document'))->render();

        return ['category' => $category, 'html' => $html];
    }

    /**
     * 设置为主栏目
     */
    public function setDocument(Request $request, $id)
    {
        $result = $this->category->setDocument($id, $request->document_id);

        if($result) {
            return ['status' => 1];
        } else {
            return ['status' => 0, 'info' => $category->getError()];
        }
    }
}
