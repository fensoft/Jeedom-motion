<?php
class MotionService
{
	private $proto = 'http://';
	private $cameras;
	public function __construct($host = 'localhost', $port = '8080'){
		$this->host = $host;
		$this->port = $port;
	}
	private function statusCheck(){
		self::AddCamera(0);
		foreach(array_slice($this->fetch('', ''),1) as $camera){
			self::AddCamera($camera);
		}
	}
	private function AddCamera($camera){
		if(is_numeric($camera)){
			$result = $this->fetch($camera, '/detection/status');
			if(stristr($result[0],'ACTIVE')){
				$this->cameras[$camera]['detection'] = true;
			}else{
				$this->cameras[$camera]['detection'] = false;
			}
			$result = $this->fetch($camera, '/config/list');
			$config='';
			foreach($result as $line){
				$conf = explode('=',$line);
				if(isset($conf[0]) && isset($conf[1]))
				$config[trim($conf[0])] = trim($conf[1]);
			}
			$this->cameras[$camera]['config'] = $config;
		}
	}
	public function getCameraId($target_dir){
		$this->statusCheck();
		if(count($this->cameras)>0)
		{
			foreach($this->cameras as $key => $value)
			{
				if(isset($value['config']['target_dir']))
					if($value['config']['target_dir']==$target_dir)
						return $key;
			}
			//return count($this->cameras);
		}
		return false;
	}	
	public function getCameraStatut($id){
		return $this->cameras[$id]['detection'];
	}
	public function detection($action = 'start',$camera = 0){
		if(is_numeric($camera)){
			$url = '/detection/'.$action;
			$result = $this->fetch($camera,$url);
		}else{
			if($camera == 'all'){
				foreach($this->cameras as $camera){
					$result = $this->fetch($camera, '/detection/'.$action);
				}
			}
		}
		if ($action=='detection')
		{
			if(stristr($result[0],'ACTIVE'))
				return true;
			else
				return false;
		}		
	}
	public function write($camera = 0){
		$url = '/config/writeyes';
		if(is_numeric($camera)){
			$result = $this->fetch($camera,$url);
		}else{
			if($camera == 'all'){
				foreach($this->cameras as $camera){
					$result = $this->fetch($camera, $url);
				}
			}
		}
		sleep (3);
	}
	public function getParam($camera = 0, $param){
		$url = '/config/get?'. $param;
		if(is_numeric($camera)){
			$result = $this->fetch($camera,$url);
		}else{
			if($camera == 'all'){
				foreach($this->cameras as $camera){
					$result = $this->fetch($camera, $url);
				}
			}
		}
		return $result[0];
	}
	public function setParam($camera = 0, $param, $value){
		$url = '/config/set?'. $param.'='.$value;
		if(is_numeric($camera)){
			$result = $this->fetch($camera,$url);
		}else{
			if($camera == 'all'){
				foreach($this->cameras as $camera){
					$result = $this->fetch($camera, $url);
				}
			}
		}
	}
	public function setRelativeTrack($camera = 0,$pan,$tilt){
		$url = '/track/set?pan='.$pan.'&tilt='.$tilt;
		if(is_numeric($camera)){
			$result = $this->fetch($camera,$url);
		}else{
			if($camera == 'all'){
				foreach($this->cameras as $camera){
					$result = $this->fetch($camera, $url);
				}
			}
		}
	}
	public function setAbsoluteTrack($camera = 0,$x,$y){
		$url = '/track/set?x='.$x.'&y='.$y;
		if(is_numeric($camera)){
			$result = $this->fetch($camera,$url);
		}else{
			if($camera == 'all'){
				foreach($this->cameras as $camera){
					$result = $this->fetch($camera, $url);
				}
			}
		}
	}
	public function setAutoTrack($camera = 0,$value){
		$url = '/track/auto?value='.$value;
		if(is_numeric($camera)){
			$result = $this->fetch($camera,$url);
		}else{
			if($camera == 'all'){
				foreach($this->cameras as $camera){
					$result = $this->fetch($camera, $url);
				}
			}
		}
	}
	public function MakeMovie($camera = 0){
		$url = '/action/makemovie';
		if(is_numeric($camera)){
			$result = $this->fetch($camera,$url);
		}else{
			if($camera == 'all'){
				foreach($this->cameras as $camera){
					$result = $this->fetch($camera, $url);
				}
			}
		}
	}
	public function Snapshot($camera = 0){
		$url = '/action/snapshot';
		if(is_numeric($camera)){
			$result = $this->fetch($camera,$url);
		}else{
			if($camera == 'all'){
				foreach($this->cameras as $camera){
					$result = $this->fetch($camera, $url);
				}
			}
		}
	}
	public function Restart($camera = 0){
		$url = '/action/restart';
		if(is_numeric($camera)){
			$result = $this->fetch($camera,$url);
		}else{
			if($camera == 'all'){
				foreach($this->cameras as $camera){
					$result = $this->fetch($camera, $url);
				}
			}
		}
		sleep(10);
	}
	public function Quit($camera = 0){
		$url = '/action/quit';
		if(is_numeric($camera)){
			$result = $this->fetch($camera,$url);
		}else{
			if($camera == 'all'){
				foreach($this->cameras as $camera){
					$result = $this->fetch($camera, $url);
				}
			}
		}
		sleep (3);
	}
	private function fetch($camera ='', $url=''){
		log::add('motion','debug','Exectution de la commande: '.$this->proto.$this->host.':'.$this->port.'/'.$camera.$url);
		$ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $this->proto.$this->host.':'.$this->port.'/'.$camera.$url); 
        curl_setopt($ch, CURLOPT_HEADER, FALSE); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        $result = curl_exec($ch); 
        curl_close($ch); 
		log::add('motion','debug','Retours de la commande: '.$result);
		return explode("\n", $result);
	}
}