<?php
if(isset($_SESSION['prenom']) && isset($_SESSION['nom']))
{
	$titre = 'Consultation';
	$description = 'Consultation sur le site '.$rjson_site['titre'];
	$script = '<script src="dist/js/jquery.js" defer></script>
	<script src="dist/js/bootstrap.min.js" defer></script>
	<script src="dist/js/jquery-saisie.js" defer></script>
	<script src="dist/js/leafletpj4.js"></script>
	<script src="dist/js/leaflet.draw.js" defer></script>
	<script src="dist/js/popup-image.js" defer></script>
	<script type="text/javascript" src="dist/js/jquery.dataTables.min.js" defer></script>
	<script type="text/javascript" src="dist/js/datatables/dataTables.scroller.min.js" defer></script>
	<script type="text/javascript" src="dist/js/datatables/dataTables.buttons.min.js" defer></script>
	<script type="text/javascript" src="dist/js/datatables/jszip.min.js" defer></script>
	<script type="text/javascript" src="dist/js/datatables/buttons.html5.min.js" defer></script>
	<script type="text/javascript" src="dist/js/datatables/buttons.colVis.min.js" defer></script>
	<script src="dist/js/consultation1.js?'.filemtime('dist/js/consultation1.js').'" defer></script>';
	$css = '<link rel="stylesheet" href="dist/css/jquery-ui.css" />
	<link rel="stylesheet" href="dist/css/leaflet.css" />
	<link rel="stylesheet" href="dist/css/leaflet.draw.css" />
	<link rel="stylesheet" type="text/css" href="dist/css/dataTables.bootstrap4.css">
	<link rel="stylesheet" type="text/css" href="dist/css/buttons.bootstrap4.min.css">
	<link rel="stylesheet" href="dist/css/popup.css" type="text/css">';
	//<script src="dist/js/consultation1.js?'.filemtime('dist/js/consultation1.js').'" defer></script><script src="src/js/consultation1.js" defer></script>
	include CHEMIN_MODELE.'consultation.php';

	if(!isset($_SESSION['virtobs']))
	{
		$obser = chercheobmembre($_SESSION['idmembre']);
		$idobser = $obser['idobser'];
	}
	else
	{
		$idobser = $_SESSION['idmembre'];			
	}
	
	if($idobser != false)
	{
		$observateur = (!isset($_SESSION['virtobs'])) ? $obser['nom'].' '.$obser['prenom'] : $_SESSION['nom'].' '.$_SESSION['prenom'];
		$type = recherche_typeobs($idobser);
		$nbtotal = 0;
		foreach($type as $n)
		{
			$nbtotal += $n['nb'];
			if($n['floutage'] == 0)
			{
				$nbnonflou = $n['nb'];
			}
		}
		if(isset($nbnonflou))
		{
			$caldroit = round(($nbnonflou / $nbtotal) * 100);
			$voir = ($caldroit >= 85) ? 'oui' : 'non';
			$perso = (isset($_GET['perso']) && $_GET['perso'] == 'oui') ? 'oui' : 'non';
		}
		else
		{
			$voir = 'non';
			$perso = 'non';
		}
		$droit = (isset($_SESSION['droits']) && $_SESSION['droits'] >= 1 && $voir == 'oui' && !isset($_SESSION['virtobs'])) ? 'oui' : 'non';
	}
	else
	{
		$voir = 'non';
		$pasobs = 'oui';	
	}
	$json_emprise = file_get_contents('emprise/emprise.json');
	$rjson_emprise = json_decode($json_emprise, true);

	$dep = ($rjson_emprise['emprise'] == 'fr' || $rjson_emprise['contour2'] == 'oui' ) ? 'oui' : 'non';
	$etude = etude();	
	$org = organisme();
	
	include CHEMIN_VUE.'consultation1.php';
}
else
{
	header('location:index.php?module=connexion&action=connexion&s=o');
}