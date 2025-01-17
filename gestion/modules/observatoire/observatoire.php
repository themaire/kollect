<?php
$titre = 'Configuration observatoires';
$description = 'Configuration des observatoires';
$script = '<script type="text/javascript" src="../dist/js/jquery.js" defer></script>
<script src="../dist/js/bootstrap.min.js" defer></script>
<script type="text/javascript" src="dist/js/bootstrap-multiselect.js" defer></script>
<script type="text/javascript" src="dist/js/configobser.js" defer></script>';
$css = '<link rel="stylesheet" type="text/css" href="dist/css/bootstrap-multiselect.css">';
if ($_SESSION['droits'] >= 3)
{
	include CHEMIN_MODELE.'observatoire.php';
	if ($_SESSION['droits'] == 3)
	{
		$id = $_SESSION['idmembre'];
		$gestionobs = gestion($id);
		$disc = explode(", ", $gestionobs['gestionobs']);
		$nbdisc = count($disc);
		if ($nbdisc >= 1)
		{
			foreach($rjson_site['observatoire'] as $n)
			{
				foreach ($disc as $n1)
				{
					if($n['nomvar'] == $n1)
					{
						$menuobservatoire[] = array("nom"=>$n['nom'],"nomvar"=>$n['nomvar']);
					}
				}
			}
			$nbobservatoire = count($menuobservatoire);
			$libnbobser = ($nbobservatoire >1) ? 'observatoires' : 'observatoire';
		}
		else
		{
			$nbobservatoire = 0;
		}
	}
	else
	{
		if (isset ($rjson_site['observatoire']))
		{
			foreach ($rjson_site['observatoire'] as $n)
			{
				$menuobservatoire[] = array('nom'=>$n['nom'],'nomvar'=>$n['nomvar']);	
			}
			$nbobservatoire = count($menuobservatoire);
			$libnbobser = ($nbobservatoire >1) ? 'observatoires' : 'observatoire';
		}
		else
		{
			$nbobservatoire = 0;
		}	
	}
		
	include CHEMIN_VUE.'observatoire.php';	
}