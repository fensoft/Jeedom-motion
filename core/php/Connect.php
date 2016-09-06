<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
$Camera=eqLogic::byId($_REQUEST['id']);
if(is_object($Camera)){
	$url = parse_url($Camera->getUrl());
	set_time_limit(0); 
	$fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30); 
	if(!$fp) { 
		echo "/public/images/error.jpg"; 
	} else { 
		fputs($fp, "GET /".$url['path']."?".$url['query']." HTTP/1.0\r\n\r\n"); 
		header("Cache-Control: no-cache"); 
		header("Cache-Control: private"); 
		header("Pragma: no-cache"); 
		header('Content-type: image/jpeg'); 
		while($str = trim(fgets($fp, 4096))) 
		header($str); 
		fpassthru($fp); 
		fclose($fp); 
	}
}

?>