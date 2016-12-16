<?php
namespace App\Libraries;

class Uploader {
    // 通用文件上传
    static public function upFile($fileObjName) {
        $file = request()->file($fileObjName);    // fileObjName: 文件对象名称（就是html里input的name属性）

        if(!$file->isValid()) {
            return array('status' => '0', 'data' => '上传失败');
        }

        // 文件信息
        $md5          = md5_file($file->path());
        $ext          = $file->extension();
        $filePathInfo = attachmentUri($md5, $ext, 'set');

        $description  = $file->getClientOriginalName();
        $size         = $file->getClientSize();
        $uri          = $filePathInfo['path'];
        $path         = '.' . $uri;

        // 移动到上传目录并重命名
        $file->move('.' . $filePathInfo['dir'], $filePathInfo['filename']);

        return [
            'status' => 1,
            'info'    => [
                'path'     => $filePathInfo['dir'],
                'ext'      => $ext,
                'fullName' => $uri,
                'filePath' => $path,
                'fileName' => $filePathInfo['filename'],
                'md5'      => $md5,
                'size'     => $size,
            ],
        ];
    }
}
