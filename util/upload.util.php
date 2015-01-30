<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-11-26
 * Time: 上午8:49
 */
namespace MYB\UTIL;
class UploadUtil
{
    /**
     * 可上传的文件的后缀
     */
    protected  $UPLOAD_FILE_SUFFIX = array(
        'txt',
        'png',
        'jpg',
        'gif',
        'rar',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'pdf',
        'psd'
    );
    /*
     * 图片上传地址
     */
    protected $path = "/upload/photo/";
    /*
     * 图片后缀名
     */
    protected $suffix;
    /*
     * 图片大小
     *
     */
    protected $size = "200000";
    /*
     * 构造函数初始化可上传标准
     *
     * @param string $path 文件保存路径
     * @param array $suffix 图片可上传后缀名
     * @param int $size 图片可上传大小(B)1849921 默认20KB
     */
    public function __construct($path = "",$size='200000',array $suffix = array())
    {

        if($path)
            $this->path = $path;
        if(!empty($suffix))
            $this->UPLOAD_FILE_SUFFIX = $suffix;
        if($size>0)
            $this->size = $size;
    }
    /*
     * 获取图片后缀名
     *
     */
    public function GetSuffix()
    {
        return $this->suffix;
    }
    /**
     * 保存创建需求是上传的文件
     *
     * @param array $file_names 此需求下的附件的名称集合
     * @param array $upload_files 本次上传的附件信息
     * @return string 错误信息
     */
    public function SaveFile ( array &$file_names, array $upload_files)
    {
        $error_info = '';
        $absolute = dirname(dirname(__FILE__));
        $path = $this->path;
        foreach ($upload_files as $key=>$file)
        {
            if($file['size'] >   $this->size)
                $error_info .= "文件大小不能超过".($this->size*1000)."Kb;\r\n";
            $name = $file['name'];
            switch ($file['error']) {
                case 0:
                    if (! ($index = strrpos($name, '.')))
                    {
                        $error_info .= "文件【{$name}】后缀名错误;\\r\\n";
                        continue 2;
                    }
                    $this->suffix = strtolower(substr($name, $index + 1));
                    if (! in_array($this->suffix, $this->UPLOAD_FILE_SUFFIX))
                    {
                        $error_info .= "文件【{$name}】错误;\r\n";
                        continue 2;
                    }
                    break;
                case 1:
                    $error_info .= "文件【{$name}】过大;\r\n";
                    continue 2;
                case 2:
                    $error_info .= "文件【{$name}】超过建议大小;\r\n";
                    continue 2;
                case 3:
                    $error_info .= "文件【{$name}】上传不完整;\r\n";
                    continue 2;
                case 4:
                    continue 2;
                default:
                    $error_info .= "文件【{$name}】未知错误;\r\n";
                    continue 2;
            }
            $name = $this->GetNewFileName($file['name'], $file_names);
            @mkdir($path, 0777);
            $img_url = md5($name.time()) .".".$this->suffix;
            $file_name = $path . $img_url;
            move_uploaded_file($file['tmp_name'], $absolute.$file_name);
            $file_names['file_name'] = $absolute.$file_name;
            $file_names['img_url'] = $img_url;
            $file_names['size'] = $file['size'];
            $file_names['suffix'] = $this->suffix;
        }
        return $error_info;
    }

    /**
     * 获取文件的新名称
     *
     * @param string $name 上传时的附件名称
     * @param array $file_names 已有的附件名称
     * @return string 新名称
     */
    protected function GetNewFileName ($name, array &$file_names)
    {
        $new_name = $name;
        $i = 0;
        while (in_array($new_name, $file_names))
        {
            $i ++;
            $index = strrpos($name, '.');
            if ($index)
            {
                $new_name = substr($name, 0, $index) . "($i)" . substr($name, $index);
            }
            else
            {
                $new_name = $name . "_$i";
            }
        }
        return $new_name;
    }


}
