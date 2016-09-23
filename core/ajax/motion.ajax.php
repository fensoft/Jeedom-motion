<?php
	try {
		require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
		include_file('core', 'authentification', 'php');
		
		if (!isConnect('admin')) {
			throw new Exception(__('401 - Accès non autorisé', __FILE__));
		}
		if (init('action') == 'SearchCamera') {
			$EqLogic = eqLogic::byType('camera');
			/*    if (!is_object($EqLogic)) {
			// ajax::success(false);
			}*/
			$return=array();
			foreach($EqLogic as $Camera)
			$return[]=array('Nom'=>$Camera->getName(),'Id'=>$Camera->getID());
			ajax::success($return);
		}
		if (init('action') == 'SearchUSBCamera') {
			$USBCamera=array();
			foreach (motion::getUsbMapping() as $name => $value) {
			$USBCamera[]=array('name'=>$name,'value'=>$value);
		}
		ajax::success($USBCamera);
		}
		if (init('action') == 'removeRecord') {
			$result;
			$file = init('file');
			$Camera=eqLogic::byId(init('cameraId'));
			if (is_object($Camera)){
				$result=motion::removeRecord($file);
				ajax::success($result);
			}
			ajax::success(false);
		}
		if (init('action') == 'WidgetHtml') {
			$MotionCamera=eqLogic::byId(init('cameraId'));
			if (is_object($MotionCamera))
				ajax::success($MotionCamera->toHtml('dashboard'));
			ajax::success(false);
		}
		if (init('action') == 'RefreshFlux') {
			$MotionCamera=eqLogic::byId(init('cameraId'));
			if (is_object($MotionCamera))
				ajax::success($MotionCamera->getSnapshot());
		ajax::success(false);
		}
		if (init('action') == 'getCamera') {
			if (init('object_id') == '') {
				$object = object::byId($_SESSION['user']->getOptions('defaultDashboardObject'));
			} else {
				$object = object::byId(init('object_id'));
			}
			if (!is_object($object)) {
				$object = object::rootObject();
			}
			$return = array();
			$return['eqLogics'] = array();
			if (init('object_id') == '') {
				foreach (object::all() as $object) {
					foreach ($object->getEqLogic(true, false, 'motion') as $camera) {
						$return['eqLogics'][] = $camera->toHtml(init('version'));
					}
				}
			} else {
				foreach ($object->getEqLogic(true, false, 'motion') as $camera) {
					$return['eqLogics'][] = $camera->toHtml(init('version'));
				}
				foreach (object::buildTree($object) as $child) {
					$cameras = $child->getEqLogic(true, false, 'motion');
					if (count($cameras) > 0) {
						foreach ($cameras as $camera) {
							$return['eqLogics'][] = $camera->toHtml(init('version'));
						}
					}
				}
			}
			ajax::success($return);
		}
		if (init('action') == 'getLog') {
			ajax::success("<pre>".file_get_contents('/etc/motion/motion.log')."</pre>");
		}
		if (init('action') == 'removeLog') {
			exec('sudo rm /etc/motion/motion.log > /dev/null 2>/dev/null &');
			ajax::success("Suppression faite");
		}
		if (init('action') == 'getVideoConvertionStat') {
			$dir=dirname(__FILE__) . '/../../../../tmp/';
			$result = array();
			$result['duration'] = 0;
			$result['time'] = 0;
			$result['progress'] = 0;
			$result['video']=$dir.'video.mp4';
			$result['videoType']="video/mp4";
			if(file_exists($result['video']))
				exec('sudo rm '.$result['video']);
			$LogFile=$dir.'convert.txt';
			if(!file_exists($LogFile)){
				$cmd = 'sudo ffmpeg -i '.init('src').' -vcodec libx264 '.$result['video'];
				$cmd .= ' >> ' . $LogFile . ' 2>&1 &';
				exec($cmd);
			}
			//exec('sudo ffmpeg -i '.init('src').' -vcodec libtheora '.$dir.'video.ogv');
			//exec('sudo ffmpeg -i '.init('src').'  -b 1000k '.$dir.'video.webm');
			if(!$log=@fopen($LogFile,"r")){
				ajax::error('Impossible d\'ouvrir le fichier : '.$LogFile);
			}
			else {
				$content=fgets($log);
				preg_match('/Duration: (.*?), start:/',$content,$matches) ;
				if(count($matches)>0 ){
					$rawDuration = $matches[1];
					// convert rawDuration from 00:00:00.00 to seconds.
					$ar = array_reverse(explode(":",$rawDuration));
					$result['duration'] = floatval($ar[0]);
					if ($ar[1]) $result['duration'] += intval($ar[1]) * 60;
					if ($ar[2]) $result['duration'] += intval($ar[2]) * 60 * 60;
					
					// get the time 
					
					preg_match('/time=(.*?) bitrate/g',$content,$matches) ;
					if(count($matches)>0 ){
						$rawTime = array_pop($matches);
						$rawTime = str_replace('time=','',$rawTime);
						$rawTime = str_replace(' bitrate','',$rawTime);
						
						// convert rawTime from 00:00:00.00 to seconds.
						$ar = array_reverse(explode(":",$rawTime));
						$result['time'] = floatval($ar[0]);
						if ($ar[1]) $result['time'] += intval($ar[1]) * 60;
						if ($ar[2]) $result['time'] += intval($ar[2]) * 60 * 60;
						
						//calculate the progress
						$result['progress'] = round(($result['time']/$result['duration']) * 100);
					}
					if($result['progress'] == 100)
						exec('sudo rm '.$LogFile);
				}
				ajax::success($result);
			}
		}	
		throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
		/*     * *********Catch exeption*************** */
	} catch (Exception $e) {
		ajax::error(displayExeption($e), $e->getCode());
	}
?>
