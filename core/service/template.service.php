<?php
/*token_get_all
 * 第一版本
 *
 */
namespace SERVICE;

class templateService
{
    //开始标签
    public $labLeft = "{";
    //结束标签
    public $labRight = "}";
    //开始标签
    public $LexLeft = "<?php";
    //结束标签
    public $LexRight = "?>";
    //字段开头
    public $variable  = "$";
    //关键词
    public $Keyword  = "if|else|elseif|for|foreach|switch|case|default|echo";
    //关键词结尾
    public $end  = "/if|/for|/foreach|/switch|/break";
    /**
     * 词法分析
     *
     * @param string $html 内容
     * @param bool $is_cache 是否开启缓存
     * @return string $html
     */
    public function Lexical($html = "",$is_cache = false)
    {
        @preg_match_all("/{$this->labLeft}(.*?){$this->labRight}/is",$html,$result);
        if(empty($result[0]))
        {
            return $html;
        }
        $result[0] = array_unique($result[0]);
        $result[1] = array_unique($result[1]);
        $Replace = $Keywords = array();
        $start = explode('|',$this->Keyword);
        $end = explode('|',$this->end);
        foreach($result[1] as $key=>$val)
        {
            $val = trim($val);
            $keyword = explode(" ",$val);
            //系统的方法会把0去掉所以只能自己写
            $keyword = $this->arrayFilter($keyword);//array_filter($keyword,array($this,"filter"));
            if(in_array($keyword[0],$start))
            {
                $words = $keyword[0];
                unset($keyword[0]);
                //从新对键值从新排列
                $keyword = array_values($keyword);
                $fun_name = "Replace{$words}";
                $Replace[$key] = $this->$fun_name($keyword,$words);
            }
            elseif(in_array($keyword[0],$end))
            {
                $Replace[$key] = $this->ReplaceEnd($keyword);
            }
            else
                $Replace[$key] = $this->reDefault($keyword,$val);
        }
        $html = str_replace($result[0],$Replace,$html);
        echo $html;
        return $html;
    }
    public function filter($val)
    {
        if(is_numeric($val) || is_string($val) || $val>=0)
            return $val;
    }
    public function arrayFilter($keyword)
    {
        foreach($keyword as  $val)
        {
            if(is_numeric($val) || is_string($val))
                $string[] =  $val;
        }
        return $string;
    }
    /**
     * 处理IF关键字
     * //日后在做复杂语法错误处理
     * @param string $keyword 关键字
     * @param string $words 关键词语
     * @return string $html
     */
    public function ReplaceIf($keyword,$words)
    {

        $search = array('eq','gt','gte','ge','lt','lie','le','ne','neq','mod','not','by','and','or');
        $replace = array('==','>','>=','>=','<','<=','<=','<>','<>','%','~','/','&&','||');
        $conditions = "";
        if(!empty($keyword))
        {
            if(count($keyword) > 1)
            {
                foreach($keyword as $val)
                {
                    if(in_array($val,$search)  && !is_numeric($val))
                    {
                        $key = array_search($val,$search);
                        if($replace[$key])
                            $conditions .= $replace[$key];
                    }
                    else
                    {
                        if(substr_count($val,"\$") || is_numeric($val) )
                            $conditions .= " {$val} ";
                        else
                            $conditions .= " '{$val}' ";
                    }
                    //必须应对情况 $k=$a  &&  $k = 1
                }
            }
            else
            {
                $conditions = implode(" ",$keyword);
            }
            return "{$this->LexLeft} {$words}( {$conditions} ) { {$this->LexRight}";
        }
        else
        {
            return "<!-- error :  if 语法错误 if 必须要有 判断条件 -->";
            //抛出错误
        }
    }

    /**
     * 处理else关键字
     * //日后在做复杂语法错误处理
     * @param string $keyword 条件
     * @param string $words 关键词
     * @return string $html
     */
    public function ReplaceElse($keyword,$words)
    {

        if($words && empty($keyword))
        {
            return "{$this->LexLeft} }{$words}{ {$this->LexRight}";
        }
        else
        {
            return "<!-- error :  else 语法错误 -->";
            //抛出错误
        }
    }

    /**
     * 处理elseif关键字
     * //日后在做复杂语法错误处理
     * @param string $keyword 关键字
     * @param string $words 关键词语
     *  && == and  || == or
     *  eq:相等                          neq:不相等                              mod:求模
        gt:大于                          is even:是否为偶数                      not:非
        gte:大于等于                     is not even:是否不为偶数                ==:相等
        ge:大于等于                      is odd:是否为奇数                       !=:不相等
        lt:小于                          is not odd:是否不为奇数                 >:大于
        lie:小于等于                     div by:是否能被整除                     <:小于
        le:小于等于                      even by:商是否为偶数                    <=:小于等于
        ne:不相等                        odd by:商是否为奇数                     >=:大于等于
     *
     * @return string $html
     */
    public function ReplaceElseIf($keyword,$words)
    {
        $search = array('eq','gt','gte','ge','lt','lie','le','ne','neq','mod','not','by','and','or');
        $replace = array('==','>','>=','>=','<','<=','<=','<>','<>','%','~','/','&&','||');
        $conditions = "";
        if(!empty($keyword))
        {
            if(count($keyword) > 1)
            {
                foreach($keyword as $val)
                {
                    if(in_array($val,$search))
                    {
                       $key = array_search($val,$search);
                       if($replace[$key])
                           $conditions .= $replace[$key];
                    }
                    else
                    {
                        if(substr_count($val,"\$") || is_numeric($val))
                            $conditions .= " {$val} ";
                        else
                            $conditions .= " '{$val}' ";
                    }
                    //必须应对情况 $k=$a  &&  $k = 1
                }
            }
            else
            {
                $conditions = implode(" ",$keyword);
            }
            return "{$this->LexLeft} } {$words}( {$conditions} ) { {$this->LexRight}";
        }
        else
        {
            return "<!-- error :  elseif 语法错误 elseif 必须要有 判断条件 -->";
            //抛出错误
        }
    }


    /**
     * 处理Foreach关键字
     * //日后在做复杂语法错误处理
     * @param string $keyword 关键字
     * @param string $words 关键词语
     * @return string $html
     */
    public function ReplaceForeach($keyword,$words)
    {
        $count = count($keyword);
        if($count <= 3 && $count >= 2)
        {
            if(isset($keyword[2]))
            {
                $conditions = array($keyword[1],$keyword[2]);
                $conditions = implode(chr(61).chr(62),$conditions);
            }
            else
            {
                $conditions = $keyword[1];
            }
            if(!$conditions)
                return "<!-- error :  Foreach 语法错误 Foreach 必须要有 value 变量 -->";
            if(!$keyword[0])
                return "<!-- error :  Foreach 语法错误 Foreach 必须要有主体数组 -->";
            return "{$this->LexLeft} if (!empty({$keyword[0]})){ {$words}( {$keyword[0]} as {$conditions} ) { {$this->LexRight}";
        }
        else
        {
            return "<!-- error :  Foreach 语法错误  语法例子：Foreach ( \$k \$b \$c ) -->";
            //抛出错误
        }
    }

    /**
     * 处理For关键字  for $i = 0 $i < 1 $i ++
     * //日后在做复杂语法错误处理
     * @param string $keyword 关键字
     * @param string $words 关键词语
     * @return string $html
     */
    public function ReplaceFor($keyword,$words)
    {
        $keyword = implode('',$keyword);
        $count = substr_count($keyword,"\$");
        foreach(str_split($keyword) as $key=>$val)
        {
            $ascii = ord($val);
            if($ascii == 36 && $key)
            {
                $chr[] = chr('59');
            }
            $chr[] = $val;
        }
        if($count <= 3 && $count > 1)
        {
            if($count == 2)
                $chr[] = chr('59');
            return "{$this->LexLeft} {$words}( ".implode('',$chr)." ) { {$this->LexRight}";
        }
        else
        {
            return "<!-- error :  for 语法错误 -->";
            //错误处理
        }
    }
    /**
     * 处理switch关键字  switch $i
     * //日后在做复杂语法错误处理
     * @param string $keyword 关键字
     * @param string $words 关键词语
     * @return string $html
     */
    public function ReplaceSwitch($keyword,$words)
    {
        $keyword = implode('',$keyword);
        $count = substr_count($keyword,"\$");
        if($count == 1)
        {
            return "{$this->LexLeft} {$words}( {$keyword} ) { {$this->LexRight}";
        }
        else
        {
            return "<!-- error :  switch 语法错误 -->";
            //错误处理
        }
    }
    /**
     * 处理switch关键字  case $i
     * //日后在做复杂语法错误处理
     * @param string $keyword 关键字
     * @param string $words 关键词语
     * @return string $html
     */
    public function ReplaceCase($keyword,$words)
    {
        $keyword = implode('',$keyword);
        if($words && $keyword)
        {
            if(!substr_count($keyword,"\$"))
                return "{$this->LexLeft} {$words} '{$keyword}': {$this->LexRight}";
            else
                return "{$this->LexLeft} {$words} {$keyword}: {$this->LexRight}";

        }
        else
        {
            return "<!-- error :  case 语法错误 -->";
            //错误处理
        }
    }
    /**
     * 处理switch关键字  Default $i
     * //日后在做复杂语法错误处理
     * @param string $keyword 关键字
     * @param string $words 关键词语
     * @return string $html
     */
    public function ReplaceDefault($keyword,$words)
    {
        if($words && empty($keyword))
        {
            return "{$this->LexLeft} {$words}: {$this->LexRight}";
        }
        else
        {
            return "<!-- error :  Default 语法错误 -->";
            //错误处理
        }
    }

    /**
     * 处理switch关键字  Default $i
     * //日后在做复杂语法错误处理
     * @param string $keyword 关键字
     * @param string $words 关键词语
     * @return string $html
     */
    public function ReplaceEcho($keyword,$words)
    {
        $key = $keyword[0];
        $count = substr_count($keyword[0],"\$");
        if(!$key)
        {
            return "<!-- error :  echo 没有给输出内容 -->";
        }
        if(!$count)
        {
            unset($keyword[0]);
            if(!empty($keyword))
            {
                $string = "";
                foreach($keyword as $val)
                {
                    if(substr_count($val,"\$"))
                        $string .= $val;
                    else
                        $string .= "'{$val}'";
                    $string .= chr(44);
                }
                $string = rtrim($string,chr(44));
                $keyword = "{$key}({$string})";
            }
            else
            {
                $keyword = "'{$key}'";
            }
        }
        elseif($count == 1)
        {
            if(strstr($keyword[0],"\$this->"))
            {
                unset($keyword[0]);
                if(!empty($keyword))
                {
                    $string = "";
                    foreach($keyword as $val)
                    {
                        if(substr_count($val,"\$"))
                            $string .= $val;
                        else
                            $string .= "'{$val}'";
                        $string .= chr(44);
                    }
                    $string = rtrim($string,chr(44));
                    $keyword = "{$key}({$string})";
                }
                else
                {
                    $keyword = $key;
                }
            }
            else
            {
                if(count($keyword)>1)
                    return "<!-- error :  输出只能一个变量 -->";
                else
                    $keyword = implode('',$keyword);
            }
        }
        else
        {
            return "<!-- error :  echo 输出只能一个变量  -->";
        }
        if($words && !empty($keyword))
        {
            return "{$this->LexLeft} {$words} {$keyword}; {$this->LexRight}";
        }
        else
        {
            //错误处理
            return "<!-- error :  关键字错误或者没有输出内容  -->";
        }
    }
    /**
     * 处理结束关键字
     *
     * @param string $keyword 关键字
     * @return string $html
     */
    public function ReplaceEnd($keyword)
    {
        switch($keyword[0])
        {
            case '/foreach':
                return "{$this->LexLeft} }} {$this->LexRight}";
                break;
            case '/break':
                return "{$this->LexLeft} break; {$this->LexRight}";
                break;
            default:
                return "{$this->LexLeft} } {$this->LexRight}";
                break;
        }
    }
    /**
     * 非关键字默认处理
     *
     * @param string $keyword 关键字
     * @param string $original 词法源
     * @return string $html
     */
    public function reDefault($keyword,$original)
    {
        $count = substr_count($keyword[0],"\$");
        $key = $keyword[0];
        unset($keyword[0]);
        if($key)
        {
            if(!count($keyword))
                $count = 1;
            switch($count)
            {
                case '0':
                    $conditions = implode(chr(44),$keyword);
                    if(!empty($keyword))
                    {
                        return "{$this->LexLeft} {$key}({$conditions}); {$this->LexRight}";
                    }
                    else
                    {
                        return '<!--- error  : 请传递参数 -->';
                    }
                    break;
                case '1':
                    if(!empty($keyword))
                        $string = implode(' ',$keyword);
                    else
                        $string = "";
                    if(substr_count($key,"\$"))
                    {
                        if(!substr_count($string,chr(44)))
                        {
                            $str = "";
                            foreach($keyword as $val)
                            {
                                if(substr_count($val,"\$"))
                                    $str .= chr(44)." ";
                                $str .= "{$val} ";
                            }
                        }
                        else
                        {
                            return '<!--- error  : 语法错误声明多个字段以空格隔开 -->';
                        }
                        return "{$this->LexLeft} {$key} {$str}; {$this->LexRight}";
                    }
                    else
                    {
                        return "{$this->LexLeft} '{$key}{$string}'; {$this->LexRight}";
                    }
                    break;
                default:
                    return '<!--- error  : 暂时无法识别 -->';
                    break;
            }
        }
        else
        {
            return '<!--- error  : 暂时无法识别 -->';
        }
        //进行替换
    }

}
