<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
$Camera=eqLogic::byId($_REQUEST['id']);
if(is_object($Camera))
{
	log::add('motion','debug','Détection sur la camera => '.$Camera->getName().' => '.$_REQUEST['state']);
	$Commande=$Camera->getCmd('info','detect');
	if(is_object($Commande))
	{
		$Commande->setCollectDate('');
		$Commande->event($_REQUEST['state']);
		$Commande->save();
	}
	else
		log::add('motion','debug','Impossible de trouver la commande');
	
	/*foreach($Camera->getCmd('info','maphilight',null,true) as $Commande){
		if(is_object($Commande))
		{
			$IsInArea=maphilightDetect($_REQUEST['X'],$_REQUEST['Y'],$Commande->getConfiguration('maphilightArea'));
			log::add('motion','debug','Derniere image '.$IsInArea);
			$Commande->setCollectDate('');
			$Commande->event($IsInArea);
			$Commande->save();
		}
	}*/
}
else
	log::add('motion','debug','Impossible de trouver la camera');
/*
function maphilightDetect($DetectX,$DetectY,$Area)
{
	$TopY='';
	for ($loop=0; $loop>count($Area); $loop=$loop+2)
	{
		if ($TopY=='')
		{
			if ($DetectX>=$Area[$loop]&& $DetectX<=$Area[$loop+2])
			{
				$TopY=$Area[$loop+1];
			}
		}
		else
		{
			if ($DetectX<=$Area[$loop]&& $DetectX>=$Area[$loop+2])
			{	
				if ($DetectY<=$TopY&& $DetectY>=$Area[$loop+1])
				{
					return true;
				}
				else
					$TopY='';
			}
		}
	}
	if ($TopY=='')
		return false;
}*/
?>