<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
//echo init('src');
if(strpos(init('src'),'.avi')>0){
	$dir=dirname(__FILE__) . '/../../../../tmp/';
	if(file_exists($dir.'video.mp4'))
 		exec('sudo rm '.$dir.'video.mp4');
	if(file_exists($dir.'video.ogv'))
		exec('sudo rm '.$dir.'video.ogv');
	if(file_exists($dir.'video.webm'))
		exec('sudo rm '.$dir.'video.webm');
	exec('sudo ffmpeg -i '.init('src').' -vcodec libx264 '.$dir.'video.mp4 1> '.$dir.'block.txt 2>&1');
	$content = @file_get_contents('../block.txt');

if($content){
    //get duration of source
    preg_match("/Duration: (.*?), start:/", $content, $matches);

    $rawDuration = $matches[1];

    //rawDuration is in 00:00:00.00 format. This converts it to seconds.
    $ar = array_reverse(explode(":", $rawDuration));
    $duration = floatval($ar[0]);
    if (!empty($ar[1])) $duration += intval($ar[1]) * 60;
    if (!empty($ar[2])) $duration += intval($ar[2]) * 60 * 60;

    //get the time in the file that is already encoded
    preg_match_all("/time=(.*?) bitrate/", $content, $matches);

    $rawTime = array_pop($matches);

    //this is needed if there is more than one match
    if (is_array($rawTime)){$rawTime = array_pop($rawTime);}

    //rawTime is in 00:00:00.00 format. This converts it to seconds.
    $ar = array_reverse(explode(":", $rawTime));
    $time = floatval($ar[0]);
    if (!empty($ar[1])) $time += intval($ar[1]) * 60;
    if (!empty($ar[2])) $time += intval($ar[2]) * 60 * 60;

    //calculate the progress
    $progress = round(($time/$duration) * 100);

    echo "Duration: " . $duration . "<br>";
    echo "Current Time: " . $time . "<br>";
    echo "Progress: " . $progress . "%";

}
	//exec('sudo ffmpeg -i '.init('src').' -vcodec libtheora '.$dir.'video.ogv');
	//exec('sudo ffmpeg -i '.init('src').'  -b 1000k '.$dir.'video.webm');
  	echo '<center><video width="400" height="222" controls autoplay>
	  <source src="'.$dir.'video.mp4" type="video/mp4" />
	  <source src="'.$dir.'video.webm" type="video/webm" />
	  <source src="'.$dir.'video.ogv" type="video/ogg" />
	</video></center>';
	
}
else
echo '<center><img class="img-responsive" src="' . init('src') . '" /></center>';
?>
