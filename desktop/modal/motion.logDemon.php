<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}

?>
<div class="btn btn-danger LogAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</div>
<div class='Log'></div>
<script>
getAlprdLog();
$('.LogAction[data-action=remove]').on('click', function() {
	$.ajax({
		type: "POST",
		timeout:8000, 
		url: "plugins/motion/core/ajax/motion.ajax.php",
		data: {
			action: "removeLog",
		},
		dataType: 'json',
		error: function(request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function(data) { 
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('#div_alert').showAlert({message: data.result, level: 'success'});
			getAlprdLog();
		}
	});	
});
function getAlprdLog(){
	$.ajax({
		type: "POST",
		timeout:8000, 
		url: "plugins/motion/core/ajax/motion.ajax.php",
		data: {
			action: "getLog",
		},
		dataType: 'json',
		error: function(request, status, error) {
			handleAjaxError(request, status, error);
		},
		success: function(data) { 
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('.Log').html(data.result);
		}
	});	
}
</script>