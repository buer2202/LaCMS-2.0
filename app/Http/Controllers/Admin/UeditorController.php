<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Libraries\UeditorUploader as Uploader;
use App\Repositories\DocumentRepository as Doc;
use App\Repositories\AttachmentRepository as Attach;
use App\Repositories\DocumentAttachmentRepository as DocAttach;

class UeditorController extends Controller
{
    private $_config;
    protected $request;
    protected $attach;
    protected $docAttach;

    public function __construct(Request $request, Doc $doc, Attach $attach, DocAttach $docAttach)
    {
        $this->request   = $request;
        $this->doc       = $doc;
        $this->attach    = $attach;
        $this->docAttach = $docAttach;
    }

    public function api()
    {
        error_reporting(E_ERROR);
        $this->_config = config('upload.ueditor');
        $action = $_GET['action'];

        switch ($action) {
            case 'config':
                $result =  json_encode($this->_config);
                break;

            /* 上传图片 */
            case 'uploadimage':
            /* 上传涂鸦 */
            case 'uploadscrawl':
            /* 上传视频 */
            case 'uploadvideo':
            /* 上传文件 */
            case 'uploadfile':
                $result = $this->_actionUpload();
                break;

            /* 列出图片 */
            case 'listimage':
                $result = $this->_actionList();
                break;
            /* 列出文件 */
            case 'listfile':
                $result = $this->_actionList();
                break;

            /* 抓取远程文件 */
            case 'catchimage':
                $result = $this->_actionCrawler();
                break;

            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }

        /* 输出结果 */
        if(isset($_GET["callback"])) {
            if(preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
    }

    /**
     * 上传附件和上传视频
     * User: Jinqn
     * Date: 14-04-09
     * Time: 上午10:17
     */
    private function _actionUpload()
    {
        /* 上传配置 */
        $base64 = "upload";
        switch (htmlspecialchars($_GET['action'])) {
            case 'uploadimage':
                $config = array(
                    "pathFormat" => $this->_config['imagePathFormat'],
                    "maxSize" => $this->_config['imageMaxSize'],
                    "allowFiles" => $this->_config['imageAllowFiles']
                );
                $fieldName = $this->_config['imageFieldName'];
                $type = 1;
                break;
            case 'uploadscrawl':
                $config = array(
                    "pathFormat" => $this->_config['scrawlPathFormat'],
                    "maxSize" => $this->_config['scrawlMaxSize'],
                    "allowFiles" => $this->_config['scrawlAllowFiles'],
                    "oriName" => "scrawl.png"
                );
                $fieldName = $this->_config['scrawlFieldName'];
                $base64 = "base64";
                $type = 1;
                break;
            case 'uploadvideo':
                $config = array(
                    "pathFormat" => $this->_config['videoPathFormat'],
                    "maxSize" => $this->_config['videoMaxSize'],
                    "allowFiles" => $this->_config['videoAllowFiles']
                );
                $fieldName = $this->_config['videoFieldName'];
                $type = 3;
                break;
            case 'uploadfile':
            default:
                $config = array(
                    "pathFormat" => $this->_config['filePathFormat'],
                    "maxSize" => $this->_config['fileMaxSize'],
                    "allowFiles" => $this->_config['fileAllowFiles']
                );
                $fieldName = $this->_config['fileFieldName'];
                $type = 2;
                break;
        }

        /* 生成上传实例对象并完成上传 */
        $up = new Uploader($fieldName, $config, $base64);

        /**
         * 得到上传文件所对应的各个参数,数组结构
         * array(
         *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
         *     "url" => "",            //返回的地址
         *     "title" => "",          //新文件名
         *     "original" => "",       //原始文件名
         *     "type" => ""            //文件类型
         *     "size" => "",           //文件大小
         * )
         */
        $fileInfo = $up->getFileInfo();

        // 记录附件信息
        $attachment = $this->attach->save(
            $this->request->input('document_id'),   // 这里用 $this->request->document_id 报错找不到临时文件，不知道为啥
            $this->request->input('document_action'),   // 这里也一样
            [
                'type'           => $type,
                'description'    => $fileInfo['original'],
                'ext'            => $fileInfo['type'],
                'size'           => $fileInfo['size'],
                'uri'            => $fileInfo['url'],
                'user_id_create' => $this->request->user()->id,
                'user_id_modify' => $this->request->user()->id,
            ]
        );

        $fileInfo['url'] = $attachment->uri;

        // 记录数据
        $relationData = [
            'document_id'   => $this->request->input('document_id'),
            'attachment_id' => $attachment->id,
            'effective'     => 0,
        ];

        // 如果是更新操作，有效性给1
        if($this->request->input('document_action') == 'update') {
            $relationData['effective'] = 1;
        }
        $this->docAttach->store($relationData);

        /* 返回数据 */
        return json_encode($fileInfo);
    }

    /**
     * 获取已上传的文件列表
     * User: Jinqn
     * Date: 14-04-09
     * Time: 上午10:17
     */
    private function _actionList()
    {
        /* 判断类型 */
        switch ($this->request->input('action')) {
            /* 列出文件 */
            case 'listfile':
                $files = $this->doc->getAttachmentForUeditor($this->request->document_id, 2);
                break;
            /* 列出图片 */
            case 'listimage':
            default:
                $files = $this->doc->getAttachmentForUeditor($this->request->document_id, 1);
        }

        if (!count($files)) {
            return json_encode([
                "state" => "no match file",
                "list"  => array(),
                "start" => 0,
                "total" => 0,
            ]);
        }

        /* 返回数据 */
        $result = json_encode([
            "state" => "SUCCESS",
            "list"  => $files,
            "start" => 0,
            "total" => count($files),
        ]);

        return $result;
    }

    /**
     * 抓取远程图片
     * User: Jinqn
     * Date: 14-04-14
     * Time: 下午19:18
     */
    public function _actionCrawler()
    {
        set_time_limit(0);

        /* 上传配置 */
        $config = array(
            "pathFormat" => $this->_config['catcherPathFormat'],
            "maxSize" => $this->_config['catcherMaxSize'],
            "allowFiles" => $this->_config['catcherAllowFiles'],
            "oriName" => "remote.png"
        );
        $fieldName = $this->_config['catcherFieldName'];

        /* 抓取远程图片 */
        $list = array();
        if (isset($_POST[$fieldName])) {
            $source = $_POST[$fieldName];
        } else {
            $source = $_GET[$fieldName];
        }
        foreach ($source as $imgUrl) {
            $item = new Uploader($imgUrl, $config, "remote");
            $info = $item->getFileInfo();

            // 记录附件信息
            $attachment = $this->attach->save(
                $this->request->input('document_id'),   // 这里用 $this->request->document_id 报错找不到临时文件，不知道为啥
                $this->request->input('document_action'),   // 这里也一样
                [
                    'type'           => 1,
                    'description'    => htmlspecialchars($info["original"]),
                    'ext'            => strtolower(strrchr($info["title"], '.')),
                    'size'           => $info["size"],
                    'uri'            => $info["url"],
                    'user_id_create' => $this->request->user()->id,
                    'user_id_modify' => $this->request->user()->id,
                ]
            );

            // 记录数据
            $relationData = [
                'document_id'   => $this->request->input('document_id'),
                'attachment_id' => $attachment->id,
                'effective'     => 0,
            ];

            // 如果是更新操作，有效性给1
            if($this->request->input('document_action') == 'update') {
                $relationData['effective'] = 1;
            }
            $this->docAttach->store($relationData);


            array_push($list, array(
                "state"    => $info["state"],
                "url"      => $attachment->uri,
                "size"     => $info["size"],
                "title"    => htmlspecialchars($info["title"]),
                "original" => htmlspecialchars($info["original"]),
                "source"   => htmlspecialchars($imgUrl)
            ));
        }

        /* 返回抓取数据 */
        return json_encode(array(
            'state'=> count($list) ? 'SUCCESS':'ERROR',
            'list'=> $list
        ));
    }
}
