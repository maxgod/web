<?php
namespace SERVICE;
use SERVICE\LexicalService;
use SERVICE\templateService;
define('FILE_NAME','index');
class DisplayService
{
    private $cache_file;
    private $template_file;
    private $header_file;
    private $footer_file;
    private $Suffix = ".htm";
    public function __construct()
    {
        include_once "expand.php";
        //自己配置 模板文件  缓存文件
        $this->template_file = strtolower(PATH_TEMPLATE .VERSION.DIRECTORY_SEPARATOR."html".DIRECTORY_SEPARATOR);
        $this->cache_file = strtolower(PATH_PUBLIC .DIRECTORY_SEPARATOR."cache" .DIRECTORY_SEPARATOR."html".DIRECTORY_SEPARATOR);
        $this->header_file = strtolower(PATH_TEMPLATE .VERSION.DIRECTORY_SEPARATOR."header.htm");
        $this->footer_file = strtolower(PATH_TEMPLATE .VERSION.DIRECTORY_SEPARATOR."footer.htm");
    }
    /*
     * 读取模板进行解析处理
     *
     * @param string $file_name 文件地址
     * @param bool $is_cache 是否开启静态缓存
     * @return string $html
     */
    public function display($file_name = "",$is_cache = false)
    {
        if(!$is_cache)
        {
            if($file_name)
            {
                $cache_file = $this->cache_file.md5($file_name).$this->Suffix;
                $file = strtolower($this->template_file.$file_name.$this->Suffix);
            }
            else
            {
                $cache_file = $this->cache_file.md5(FILE_NAME).$this->Suffix;
                $file = strtolower($this->template_file.FILE_NAME.$this->Suffix);
            }
            $this->template($file,$cache_file);
            include_once $cache_file;
        }
    }
    /*
     * 模板生成处理
     *
     * @param string $file_name 文件地址
     * @param string $cache_file 缓存文件
     * @param bool $is_pubic 是否开启缓存
     * @return string $file_name
     */
    public function template($file_name,$cache_file,$is_pubic = true)
    {
        if(file_exists($cache_file))
        {
            if($is_pubic)
            {
                $header = $this->ReadFile($this->header_file);
                $footer_file = $this->ReadFile($this->footer_file);
            }
            //读取文件地址
            $html = $this->ReadFile($file_name);
            $lexical = new LexicalService();
            $html = $lexical->template($html);
            $this->fWriteFile($cache_file,$html);
            return $cache_file;
        }
    }
    /*
     * 读取文件方式
     * @param string $file_name 文件地址
     */
    public function ReadFile($file_name)
    {
        $html = "";
        if(@$fp = fopen($file_name, 'r'))
        {
            $html = @fread($fp, filesize($file_name));
            flock($fp, 2);
            fclose($fp);
        }
        return $html;
    }
    /*
     * 写内容文件方式
     * @param string $file_name 文件地址
     * @param string $template 内容
     */
    public function fWriteFile($cache_file,$template)
    {
        if(@$fp = fopen($cache_file, 'w'))
        {
            flock($fp, 2);
            fwrite($fp, $template);
            fclose($fp);
            return true;
        }
        return false;
    }

}