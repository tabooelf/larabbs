<?php

namespace App\Handlers;

use Intervention\Image\ImageManagerStatic as Image;

class ImageUploadHandler{
    protected $allowed_ext = ['png', 'jpg', 'gif', 'jpeg'];

    public function save($file, $folder, $file_prefix, $max_width = false){
        // 构建存储文件夹规则
        $folder_name = "uploads/images/$folder/" . date("Ym/d", time());
        // 文件实际存储位置
        $upload_path = public_path() . '/' . $folder_name;
        // 获取文件后缀名
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';
        // 拼接文件名
        $filename = $file_prefix . '_' . time() . '_' . str_random(10) . '.' . $extension;
        // 如果上传的不是图片将终止操作
        if( ! in_array($extension, $this->allowed_ext)){
            return false;
        }

        $file->move($upload_path, $filename);

        if($max_width && $extension != 'gif'){
            $this->reduceSize($upload_path . '/' .$filename, $max_width);
        }

        return [
            'path' => "/$folder_name/$filename"
        ];
    }

    public function reduceSize($file_path, $max_width){
        // 读取文件初始化image
        $image = Image::make($file_path);
        // 调整大小
        $image->resize($max_width, null, function($constraint){
            // 等比例缩放
            $constraint->aspectRatio();
            // 防止图片尺寸变大
            $constraint->upsize();
        });
        // 保存
        $image->save();
    }
}