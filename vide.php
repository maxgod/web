<?php



$contents = file_get_contents('http://match.pcgames.com.cn/table_1236_700_2840.html');
preg_match("/<tbody>(.*?)<\/tbody>/is",$contents,$box);
preg_match_all("/<tr class=\"upto\">(.*?)<\/tr>/is",$box[1],$dz);
preg_match_all("/<tr>(.*?)<\/tr>/is",$box[1],$ss);

unset($ss[1][0]);
unset($ss[1][1]);
//print_r($ss[1]);
foreach($ss[1] as $key=>$val)
{
    preg_match("/<td class=\"classG\" id=\"td.*?\">(.*?)<\/td>/is",$val,$bf);
    preg_match_all("/<a.*?>.*?<span>(.*?)<\/span>.*?<\/a>/is",$val,$names);
    if(!is_numeric($bf[1]))
    {
        preg_match("/<em class=\"vshow\">(.*?)<\/em>/is",$val,$date);
    }
    $data[$key]['name'] = $names[1][0];//战队名字
    $data[$key]['bname'] = $names[1][1];//战队名字
    $data[$key]['bf'] = $bf[1];//比分
    $data[$key]['date'] = $date[1];
    $data[$key]['date'] = str_replace(array(iconv("UTF-8", "GB2312//IGNORE", '月'),iconv("UTF-8", "GB2312//IGNORE",'日')),array('2015-',' '),$date[1]);//比分
    //$data[$key]['bfbox'] = $val;//比分
}
print_r($data);
exit;
//获取MP4http://vv.video.qq.com/geturl?vid=g0145k0zzi5&otype=json
for($i=4;$i>=1;$i++)
{

$box = file_get_contents('http://lol.15w.com/zt/2015lpl/video/index_1.html');

preg_match("/<div class=\"videos videolistpad\">(.*?)<\/div></is",$box,$page);
$page[0];

preg_match_all("/<a href=\"(.*?)\".*?title=\"(.*?)\">.*?<div class=\"t\">.*?<img src=\"(.*?)\" alt=\".*?\">/is",$page[0],$video);

foreach($video[1] as $key=>$val)
{
    $contents = file_get_contents($val);
    preg_match("/<div class=\"videoshow\" flash=\"(.*?)\" flashvars=\"(.*?)\">/is",$contents,$a);
    $data['swf'] = $a[1];
    $data['flashvars'] = $a[2];
    preg_match("/vid=(.*?)\&/is",$a[2],$cs);
    $data['url'] = $cs[1];
    $data['title'] = $video[2][$key];
    $data['images'] = $video[3][$key];
    $data['platform'] = 'LOL';
    $keyword = array_keys($data);
    $values = array_values($data);
    echo $sql = "insert into pyc_video_events (`".implode("`,`",$keyword)."`)values('".implode("','",$values)."');";
exit;
}
}

