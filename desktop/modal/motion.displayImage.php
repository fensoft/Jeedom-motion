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
	shell_exec('sudo ffmpeg -i '.init('src').' -vcodec libx264 '.$dir.'video.mp4 1>'.$dir.'block.txt 2>&1');
	//exec('sudo ffmpeg -i '.init('src').' -vcodec libtheora '.$dir.'video.ogv');
	//exec('sudo ffmpeg -i '.init('src').'  -b 1000k '.$dir.'video.webm');
?>
<script>
	var _progress = function(i){
		i++;
		var logfile = '<php echo $dir."block.txt";';
		request.post(logfile).then( function(content){
			// AJAX success
			var duration = 0, time = 0, progress = 0;
			var result = {};
			
			// get duration of source
			var matches = (content) ? content.match(/Duration: (.*?), start:/) : [];
			if( matches.length>0 ){
				var rawDuration = matches[1];
				// convert rawDuration from 00:00:00.00 to seconds.
				var ar = rawDuration.split(":").reverse();
				duration = parseFloat(ar[0]);
				if (ar[1]) duration += parseInt(ar[1]) * 60;
				if (ar[2]) duration += parseInt(ar[2]) * 60 * 60;
				
				// get the time 
				matches = content.match(/time=(.*?) bitrate/g);
				console.log( matches );
				
				if( matches.length>0 ){
					var rawTime = matches.pop();
					// needed if there is more than one match
					if (lang.isArray(rawTime))
						rawTime = rawTime.pop().replace('time=','').replace(' bitrate',''); 
					else
						rawTime = rawTime.replace('time=','').replace(' bitrate','');
					
					// convert rawTime from 00:00:00.00 to seconds.
					ar = rawTime.split(":").reverse();
					time = parseFloat(ar[0]);
					if (ar[1]) time += parseInt(ar[1]) * 60;
					if (ar[2]) time += parseInt(ar[2]) * 60 * 60;
					
					//calculate the progress
					progress = Math.round((time/duration) * 100);
				}
				
				result.status = 200;
				result.duration = duration;
				result.current  = time;
				result.progress = progress;
				
				console.log(result);
				
				$('progress').text(time+' / '+duration);
				$('progress').val(progress);
				
				if(progress==0 && i>20){
					// TODO err - giving up after 8 sec. no progress - handle progress errors here
					console.log('{"status":-400, "error":"there is no progress while we tried to encode the video" }'); 
					return;
				} else if(progress<100){ 
					$('video').hide();
					$('progress').show();
					setTimeout(function(){ _progress(i); }, 400);
				}else if(progress>100){ 
					$('video').show();
					$('progress').hide();
				}
			} else if( content.indexOf('Permission denied') > -1) {
			// TODO - err - ffmpeg is not executable ...
			console.log('{"status":-400, "error":"ffmpeg : Permission denied, either for ffmpeg or upload location ..." }');    
			} 
		},
		function(err){
			// AJAX error
			if(i<20)
				setTimeout(function(){ _progress(0); }, 400);
			else {
				console.log('{"status":-400, "error":"there is no progress while we tried to encode the video" }');
				console.log( err ); 
			}
			return; 
		});
	}
	setTimeout(function(){ _progress(0); }, 800);
</script>
<center>
	<progress max="100"></progress>
	<video width="400" height="222" controls autoplay>
		<source src="'.$dir.'video.mp4" type="video/mp4" />
		<source src="'.$dir.'video.webm" type="video/webm" />
		<source src="'.$dir.'video.ogv" type="video/ogg" />
	</video>
</center>
<?php
}
else
echo '<center><img class="img-responsive" src="' . init('src') . '" /></center>';


?>
