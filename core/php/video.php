<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
$Camera=eqLogic::byId($_REQUEST['id']);
if(is_object($Camera)){
  	header('Content-Type: multipart/x-mixed-replace; boundary=myboundary');
  	ob_end_flush();
  	if($Camera->getConfiguration('stream_motion')){
		if($Camera->getConfiguration('stream_port')!=''){
			$urlStream='http://';
			if($Camera->getConfiguration('stream_auth_method')!=0 && $Camera->getConfiguration('stream_authentication')!='')
				$urlStream.=urlencode($Camera->getConfiguration('stream_authentication')).'@';
			$urlStream.=config::byKey('Host', 'motion').':'.$Camera->getConfiguration('stream_port');
			$urlStream.='/stream.mjpg';
		}
	}else {
		switch ($Camera->getConfiguration('cameraType'))
		{
			case 'ip':
				if($Camera->getConfiguration('cameraLogin')!='' && $Camera->getConfiguration('cameraPass')!='')
					$urlStream=split('://',$Camera->getConfiguration('cameraUrl'))[0].'://'.$Camera->getConfiguration('cameraLogin').':'.$Camera->getConfiguration('cameraPass').'@'.split('://',$Camera->getConfiguration('cameraUrl'))[1];
				else
					$urlStream=$Camera->getConfiguration('cameraUrl');
			break;
		}
	}
  readfile($urlStream);
}
?>
