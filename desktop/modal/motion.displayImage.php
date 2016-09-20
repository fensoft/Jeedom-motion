<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
//echo init('src');
if(strpos(init('src'),'.avi')>0){
?>
<script>
	$('video').hide();
	$('progress').show();
	getVideoConvertionStat("<php echo init('src');?>");
	function getVideoConvertionStat(filename){
		var timeoutConvertion = setTimeout(getVideoConvertionStat, 1000);
	        $.ajax({// fonction permettant de faire de l'ajax
			type: "POST", // methode de transmission des données au fichier php
			url: "plugins/motion/core/ajax/motion.ajax.php", // url du fichier php
			data: {
				action: "getVideoConvertionStat",
				src: filename
			},
			dataType: 'json',
			global: false,
			error: function(request, status, error) {
				handleAjaxError(request, status, error,$('#div_cameraRecordAlert'));
			},
			success: function(data) { // si l'appel a bien fonctionné
				if (data.state != 'ok') {
					$('#div_cameraRecordAlert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				$('progress').text(data.result.time+' / '+data.result.duration);
				$('progress').val(data.result.progress);
				if(data.result.progress==100){ 
					$('video').show();
					$('progress').hide();
					clearTimeout(timeoutConvertion);
				}
				 timeoutConvertion = setTimeout(getVideoConvertionStat, 1000);
			}
		});
	}
</script>
<center>
	<progress value="0" max="100"></progress>
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
