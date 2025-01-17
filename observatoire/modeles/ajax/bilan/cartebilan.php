<?php 
include '../../../../global/configbase.php';
include '../../../lib/pdo2.php';

function nbespece($nomvar)
{
	$bdd = PDO2::getInstance();
	$bdd->query("SET NAMES 'UTF8'");
	$req = $bdd->query("SELECT COUNT(DISTINCT obs.cdref) AS Nb FROM obs.obs
						INNER JOIN $nomvar.liste ON liste.cdnom = obs.cdref
						WHERE rang = 'ES' ") or die(print_r($bdd->errorInfo()));
	$nbobs = $req->fetchColumn();
	$req->closeCursor();
	return $nbobs;
}
function cartodep($nomvar)
{
	$bdd = PDO2::getInstance();
	$bdd->query("SET NAMES 'UTF8'");
	$req = $bdd->query("WITH sel AS (SELECT DISTINCT(obs.cdref), iddep FROM obs.obs
									INNER JOIN obs.fiche USING(idfiche)
									INNER JOIN $nomvar.liste ON liste.cdnom = obs.cdref
									WHERE (rang = 'ES' OR rang = 'SSES')
						)
                        SELECT sel.iddep AS id, COUNT(DISTINCT cdref) AS nb FROM sel
                        GROUP BY sel.iddep ") or die(print_r($bdd->errorInfo()));
	$carto = $req->fetchAll(PDO::FETCH_ASSOC);
	$req->closeCursor();
	return $carto;
}
function departement()
{
	$bdd = PDO2::getInstance();
	$bdd->query("SET NAMES 'UTF8'");
	$req = $bdd->query("SELECT iddep AS id, departement AS emp, poly, geojson FROM referentiel.departement") or die(print_r($bdd->errorInfo()));
	$commune = $req->fetchAll(PDO::FETCH_ASSOC);
	$req->closeCursor();
	return $commune;
}	
function cartocommune($nomvar)
{
	$bdd = PDO2::getInstance();
	$bdd->query("SET NAMES 'UTF8'");
	$req = $bdd->query("WITH sel AS (SELECT DISTINCT(obs.cdref), codecom FROM obs.obs
									INNER JOIN obs.fiche USING(idfiche)
									INNER JOIN $nomvar.liste ON liste.cdnom = obs.cdref
									WHERE rang = 'ES' OR rang = 'SSES'
						)
                        SELECT sel.codecom AS id, COUNT(DISTINCT cdref) AS nb FROM sel
                        GROUP BY sel.codecom ") or die(print_r($bdd->errorInfo()));
	$carto = $req->fetchAll(PDO::FETCH_ASSOC);
	$req->closeCursor();
	return $carto;
}
function commune($iddep='%') // Liste des commune de l'emprise avec filtre par département
{
	$bdd = PDO2::getInstance();
	$bdd->query("SET NAMES 'UTF8'");
    $req = $bdd->prepare("SELECT codecom AS id, commune AS emp, iddep, poly, geojson FROM referentiel.commune where geojson IS NOT NULL AND iddep like :iddep");
    $req->execute([':iddep' => $iddep]);
	$commune = $req->fetchAll(PDO::FETCH_ASSOC);
	$req->closeCursor();
	return $commune;
}
function cartoutm($nomvar)
{
	$bdd = PDO2::getInstance();
	$bdd->query("SET NAMES 'UTF8'");
	$req = $bdd->query("SELECT utm, COUNT(DISTINCT obs.cdref) AS nb, geo FROM obs.obs
						INNER JOIN obs.fiche USING(idfiche)
						INNER JOIN obs.coordonnee ON coordonnee.idcoord = fiche.idcoord
						INNER JOIN referentiel.mgrs10 ON mgrs10.mgrs = coordonnee.utm
						INNER JOIN $nomvar.liste ON liste.cdnom = obs.cdref
						WHERE rang = 'ES' OR rang = 'SSES'
						GROUP BY utm, geo ") or die(print_r($bdd->errorInfo()));
	$carto = $req->fetchAll(PDO::FETCH_ASSOC);
	$req->closeCursor();
	return $carto;
}	
function mgrs()
{
	$bdd = PDO2::getInstance();
	$bdd->query("SET NAMES 'UTF8'");
	$req = $bdd->query("SELECT mgrs, geo FROM referentiel.mgrs10 ") or die(print_r($bdd->errorInfo()));
	$utm = $req->fetchAll(PDO::FETCH_ASSOC);
	$req->closeCursor();
	return $utm;
}
function cartol93($nomvar, $iddep='%')
{
	$bdd = PDO2::getInstance();
	$bdd->query("SET NAMES 'UTF8'");
	$req = $bdd->prepare("WITH sel AS (SELECT DISTINCT(obs.cdref), codel93, fiche.idcoord, iddep FROM obs.obs
									INNER JOIN obs.fiche USING(idfiche)
									INNER JOIN obs.coordonnee ON coordonnee.idcoord = fiche.idcoord
									INNER JOIN $nomvar.liste ON liste.cdnom = obs.cdref
									WHERE (rang = 'ES' OR rang = 'SSES') AND codel93 != ''
						)
                        SELECT sel.codel93, COUNT(DISTINCT cdref) AS nb FROM sel
                        INNER JOIN obs.coordonnee ON coordonnee.idcoord = sel.idcoord
                        WHERE iddep LIKE :iddep
                        GROUP BY sel.codel93, iddep ") or die(print_r($bdd->errorInfo()));
	$req->execute([':iddep'=>$iddep]);
	$carto = $req->fetchAll(PDO::FETCH_ASSOC);
	$req->closeCursor();
	return $carto;
}
function maillel93($iddep='%')
{
	$bdd = PDO2::getInstance();
	$bdd->query("SET NAMES 'UTF8'");
    $req = $bdd->prepare("SELECT codel93 FROM referentiel.maillel93 where iddep like :iddep");
    $req->execute([':iddep' => ("%" . $iddep . "%")]);
	$l93 = $req->fetchAll(PDO::FETCH_ASSOC);
	$req->closeCursor();
	return $l93;
}	
function carto5l93($nomvar, $iddep='%')
{
	$bdd = PDO2::getInstance();
	$bdd->query("SET NAMES 'UTF8'");
	$req = $bdd->prepare("WITH sel AS (SELECT DISTINCT(obs.cdref), codel935, fiche.idcoord, iddep FROM obs.obs
									INNER JOIN obs.fiche USING(idfiche)
									INNER JOIN obs.coordonnee ON coordonnee.idcoord = fiche.idcoord
									INNER JOIN $nomvar.liste ON liste.cdnom = obs.cdref
									WHERE (rang = 'ES' OR rang = 'SSES') AND codel935 != ''
						)
                        SELECT sel.codel935, COUNT(DISTINCT cdref) AS nb FROM sel
                        INNER JOIN obs.coordonnee ON coordonnee.idcoord = sel.idcoord
                        WHERE iddep LIKE :iddep
                        GROUP BY sel.codel935, iddep ") or die(print_r($bdd->errorInfo()));
	$req->execute([':iddep'=>$iddep]);
	$carto = $req->fetchAll(PDO::FETCH_ASSOC);
	$req->closeCursor();
	return $carto;
}

if(isset($_POST['choixcarte'])) 
{
	$choix = $_POST['choixcarte'];
	$nomvar = $_POST['nomvar'];
	$iddep = $_POST['iddep'];
	
	$nbsp = nbespece($nomvar);
	if($choix == 'commune' || $choix == 'dep')
	{
		$tabobs = ($choix == 'commune') ? cartocommune($nomvar) : cartodep($nomvar);
		$tabref = ($choix == 'commune') ? commune($iddep) : departement();
		
		foreach ($tabobs as $n)
		{
			$code[] = $n['id'];
		}
		$code = array_flip($code);
		foreach ($tabref as $n)
		{
			if(isset($code[$n['id']]))
			{
				$cle = $code[$n['id']];
				$info = 'Nombre d\'espèces : '.$tabobs[$cle]['nb'];
				$feature = array('type' => 'Feature', 'properties' => Null, 'geometry' => Null);
				$feature['properties']['id'] = $n['id'];
				$feature['geometry'] = array('type' => $n['poly'], 'coordinates' => $n['geojson']);
				$resultats['features'][] = $feature;
				$carte[] = array("nom"=>$n['emp'], "id"=>$n['id'], "value"=>$tabobs[$cle]['nb'], "info"=>$info);				
			}
			else
			{
				$info = 'Aucune espèce';
				$feature = array('type' => 'Feature', 'properties' => Null, 'geometry' => Null);
				$feature['properties']['id'] = $n['id'];
				$feature['geometry'] = array('type' => $n['poly'], 'coordinates' => $n['geojson']);
				$resultats['features'][] = $feature;
				$carte[] = array("nom"=>$n['emp'], "id"=>$n['id'], "value"=>0, "info"=>$info);	
			}
		}
		unset($commune);
		$tmpcarto = json_encode($resultats, JSON_NUMERIC_CHECK);
		$tmpcarto = str_replace('"[','[',$tmpcarto);
		$tmpcarto = str_replace(']"',']',$tmpcarto);
		$resultats = json_decode($tmpcarto);
		$retour['carto'] = $resultats;
		$retour['data'] = $carte;
		$retour['nbsp'] = $nbsp;
		$retour['maille'] = 'non';
		$retour['dep'] = 'non';
		$retour['statut'] = 'Oui';
	}	
	elseif($choix == 'maille')
	{
		$utm = $_POST['utm'];
		$emp = $_POST['emp'];
		if ($utm == 'oui')
		{
			$cartoutm = cartoutm($nomvar);
			foreach ($cartoutm as $n)
			{
				$codeutm[] = $n['utm'];
				$info = 'Nombre d\'espèces : '.$n['nb'];
				$feature = array('type' => 'Feature', 'properties' => Null, 'geometry' => Null);
				$feature['properties']['id'] = $n['utm'];
				$feature['geometry'] = array('type' => 'Polygon', 'coordinates' => $n['geo']);
				$resultats['features'][] = $feature;
				$carte[] = array("nom"=>$n['utm'], "id"=>$n['utm'], "value"=>$n['nb'], "info"=>$info);
			}
			unset($cartoutm);
			if($emp != 'fr')
			{
				$utm = mgrs();
				foreach ($utm as $n)
				{
					$info = 'Aucune donnée';
					if (!in_array($n['mgrs'], $codeutm))
					{
						$feature = array('type' => 'Feature', 'properties' => Null, 'geometry' => Null);
						$feature['properties']['id'] = $n['mgrs'];
						$feature['geometry'] = array('type' => 'Polygon', 'coordinates' => $n['geo']);
						$resultats['features'][] = $feature;
						$carte[] = array("nom"=>$n['mgrs'], "id"=>$n['mgrs'], "value"=>0, "info"=>$info);
					}
				}
				unset($utm);
			}
			$tmpcarto = json_encode($resultats, JSON_NUMERIC_CHECK);
			$tmpcarto = str_replace('"[','[',$tmpcarto);
			$tmpcarto = str_replace(']"',']',$tmpcarto);
			$resultats = json_decode($tmpcarto);
			$retour['carto'] = $resultats;
			$retour['data'] = $carte;
			$retour['nbsp'] = $nbsp;
			$retour['maille'] = 'oui';
			$retour['statut'] = 'Oui';
		}
		else
		{
			$cartol93 = cartol93($nomvar, $iddep);
			foreach ($cartol93 as $n)
			{
				$codel93[] = $n['codel93'];
				$info = 'Nombre d\'espèces : '.$n['nb'];
				$xg = substr($n['codel93'], 1, -4)*10000;
				$yb = substr($n['codel93'], 5)*10000;
				$xd = $xg + 10000;
				$yh = $yb + 10000;
				$feature = array('type' => 'Feature', 'properties' => Null, 'geometry' => Null);
				$feature['properties']['id'] = $n['codel93'];
				$feature['geometry'] = array('type' => 'Polygon', 'coordinates' => array([[intval($xg), intval($yb)],[intval($xg), intval($yh)],[intval($xd), intval($yh)],[intval($xd), intval($yb)]]));
				$resultats['features'][] = $feature;
				$carte[] = array("nom"=>$n['codel93'], "id"=>$n['codel93'], "value"=>$n['nb'], "info"=>$info);				
			}
			unset($cartol93);
			if($emp != 'fr')
			{
				$l93 = maillel93($iddep);
				foreach ($l93 as $n)
				{
					$info = 'Aucune donnée';
					if (!in_array($n['codel93'], $codel93))
					{
						$xg = substr($n['codel93'], 1, -4)*10000;
						$yb = substr($n['codel93'], 5)*10000;
						$xd = $xg + 10000;
						$yh = $yb + 10000;
						$feature = array('type' => 'Feature', 'properties' => Null, 'geometry' => Null);
						$feature['properties']['id'] = $n['codel93'];
						$feature['geometry'] = array('type' => 'Polygon', 'coordinates' => array([[intval($xg), intval($yb)],[intval($xg), intval($yh)],[intval($xd), intval($yh)],[intval($xd), intval($yb)]]));
						$resultats['features'][] = $feature;
						$carte[] = array("nom"=>$n['codel93'], "id"=>$n['codel93'], "value"=>0, "info"=>$info);
					}
				}
				unset($l93);
			}
			$retour['carto'] = $resultats;
			$retour['data'] = $carte;
			$retour['nbsp'] = $nbsp;
			$retour['maille'] = 'oui';
			$retour['statut'] = 'Oui';
		}		
	}
	elseif($choix == 'maille5')
	{
		$cartol93 = carto5l93($nomvar, $iddep);
		foreach ($cartol93 as $n)
		{
			$info = 'Nombre d\'espèces : '.$n['nb'];
			$xg = substr($n['codel935'], 1, -5) * 1000;
			$yb = substr($n['codel935'], 6) * 1000;
			$xd = $xg + 5000;
			$yh = $yb + 5000;
			$feature = array('type' => 'Feature', 'properties' => Null, 'geometry' => Null);
			$feature['properties']['id'] = $n['codel935'];
			$feature['geometry'] = array('type' => 'Polygon', 'coordinates' => array([[intval($xg), intval($yb)],[intval($xg), intval($yh)],[intval($xd), intval($yh)],[intval($xd), intval($yb)]]));
			$resultats['features'][] = $feature;
			$carte[] = array('nom'=>$n['codel935'], 'id'=>$n['codel935'], 'value'=>$n['nb'], 'info'=>$info);	
		}
		unset($cartol93);
		$l93 = maillel93($iddep);
		foreach ($l93 as $n)
		{
			$xg = substr($n['codel93'], 1, -4)*10000;
			$yb = substr($n['codel93'], 5)*10000;
			$xd = $xg + 10000;
			$yh = $yb + 10000;
			$feature = array('type' => 'Feature', 'properties' => Null, 'geometry' => Null);
			$feature['properties']['cd'] = $n['codel93'];
			$feature['geometry'] = array('type' => 'MultiLineString', 'coordinates' => array([[intval($xg), intval($yb)],[intval($xg), intval($yh)],[intval($xd), intval($yh)],[intval($xd), intval($yb)]]));
			$resultats['features'][] = $feature;					
		}		
		$retour['carto'] = $resultats;
		$retour['maille5'] = 'oui';
		$retour['data'] = $carte;
		$retour['nbsp'] = $nbsp;
		$retour['maille'] = 'oui';
		$retour['statut'] = 'Oui';
	}	
}
else
{
	$retour['statut'] = 'Non';
}	
echo json_encode($retour, JSON_NUMERIC_CHECK);
?>
