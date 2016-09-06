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
		switch (init('cameraAnalyse'))
		{
			case 'local':
				$USB=motion::getUsbMapping();
			break;
			case 'camera':	
			break;
			default:
				/*$jeeNetwork=jeeNetwork::byId(init('cameraAnalyse'));
				$jsonrpc = $jeeNetwork->getJsonRpc();
				if (!$jsonrpc->sendRequest('SearchUSBCamera', array('plugin' => 'motion'))) {
					throw new Exception($jsonrpc->getError());//, $jsonrpc->getErrorCode());
				}
				$USB=$jsonrpc->getResult();*/
			break;
		}
		foreach ($USB as $name => $value) {
			$USBCamera[]=array('name'=>$name,'value'=>$value);
		}
		ajax::success($USBCamera);
	}
	if (init('action') == 'removeRecord') {
		$result;
        $file = init('file');
		$Camera=eqLogic::byId(init('cameraId'));
		if (is_object($Camera))
		{
			$result=motion::removeRecord($file);
			ajax::success($result);
		}
		ajax::success(false);
    }
	if (init('action') == 'WidgetHtml') {
		$MotionCamera=eqLogic::byId(init('cameraId'));
		if (is_object($MotionCamera))
		{
			ajax::success($MotionCamera->toHtml('dashboard'));
		}
		ajax::success(false);
    }
	if (init('action') == 'RefreshFlux') {
		$MotionCamera=eqLogic::byId(init('cameraId'));
		if (is_object($MotionCamera))
		{
			ajax::success($MotionCamera->getSnapshot());
		}
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

    throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>