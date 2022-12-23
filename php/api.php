<?php

@header('Content-Type: text/html; charset=UTF-8');

$ticket = $_POST['ticket'];
$randstr = $_POST['randstr'];

if(!$ticket || !$randstr) exit('参数不能为空');

$result = check_ticket($ticket, $randstr);
if($result === 1){
	exit('验证通过');
}elseif($result === -1){
	exit('接口已失效');
}elseif($result === 0){
	exit('验证不通过');
}


function check_ticket($ticket, $randstr)
{
	$url = 'https://cgi.urlsec.qq.com/index.php?m=check&a=gw_check&callback=url_query&url=https%3A%2F%2Fwww.qq.com%2F'.rand(111111,999999).'&ticket='.urlencode($ticket).'&randstr='.urlencode($randstr);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$httpheader[] = "Accept: application/json";
	$httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
	$httpheader[] = "Connection: close";
	curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
	curl_setopt($ch, CURLOPT_REFERER, 'https://urlsec.qq.com/check.html');
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $ret = curl_exec($ch);
    curl_close($ch);

	$arr = jsonp_decode($ret, true);
	if(isset($arr['reCode']) && $arr['reCode'] == 0){ //验证通过
		return 1;
	}elseif($arr['reCode'] == -109){ //验证码错误
		return 0;
	}else{ //接口已失效
		return -1;
	}
}
function jsonp_decode($jsonp, $assoc = false)
{
	$jsonp = trim($jsonp);
	if(isset($jsonp[0]) && $jsonp[0] !== '[' && $jsonp[0] !== '{') {
		$begin = strpos($jsonp, '(');
		if(false !== $begin)
		{
			$end = strrpos($jsonp, ')');
			if(false !== $end)
			{
				$jsonp = substr($jsonp, $begin + 1, $end - $begin - 1);
			}
		}
	}
	return json_decode($jsonp, $assoc);
}