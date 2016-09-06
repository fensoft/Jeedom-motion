<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
//echo init('src');
if(strpos(init('src'),'.avi')>0){
 	 exec('sudo rm '.$dir.'video.mp4');
	exec('sudo rm '.$dir.'video.ogv');
	exec('sudo rm '.$dir.'video.webm');
	exec('sudo ffmpeg -i '.init('src').' -vcodec libx264 '.$dir.'video.mp4');
	//exec('sudo ffmpeg -i '.init('src').' -vcodec libtheora '.$dir.'video.ogv');
	//exec('sudo ffmpeg -i '.init('src').'  -b 1000k '.$dir.'video.webm');
	
  	echo '<center><video width="400" height="222" controls autoplay>
	  <source src="'.$dir.'video.mp4" type="video/mp4" />
	  <source src="'.$dir.'video.webm" type="video/webm" />
	  <source src="'.$dir.'video.ogv" type="video/ogg" />
	</video></center>';
	$dir=dirname(__FILE__) . '/../../../../tmp/';
}
else
echo '<center><img class="img-responsive" src="' . init('src') . '" /></center>';
?>