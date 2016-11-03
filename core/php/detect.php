<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
$Camera=eqLogic::byId($_REQUEST['id']);
if(is_object($Camera))
	$Camera->UpdateDetection($_REQUEST);
else
	log::add('motion','debug','Impossible de trouver la camera');
?>
