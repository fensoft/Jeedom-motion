<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
if (config::byKey('Host', 'motion')=='')
	config::save('Host', network::getNetworkAccess('internal','ip'),'motion');
?>


<form class="form-horizontal">
    <fieldset>
		<div class="form-group">
			<label class="col-lg-4 control-label">Adresse ou motion est installé :</label>
			<div class="col-lg-4">
				<input class="configKey form-control" data-l1key="Host" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-4 control-label">Port ou motion est installé :</label>
			<div class="col-lg-4">
				<input class="configKey form-control" data-l1key="Port" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-4 control-label">Emplacement du dossier Snapshot :</label>
			<div class="col-lg-4">
				<input class="configKey form-control" data-l1key="SnapshotFolder" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-4 control-label">Taille du dossier Snapshot de chaque camera (Mo) :</label>
			<div class="col-lg-4">
				<input class="configKey form-control" data-l1key="SnapshotFolderSeize" />
			</div>
		</div>
    </fieldset>	
</form>
<script>
function motion_postSaveConfiguration(){
	$.ajax({
		type: 'POST',
		url: 'plugins/motion/core/ajax/motion.ajax.php',
		data: {
			action: 'ChangeMotionConfiguration'
		},
		dataType: 'json',
		global: false,
		error: function (request, status, error) {
			handleAjaxError(request, status, error, $('#div_updatepreRequisAlert'));
		},
		success: function () {
		}
	});
};
</script>