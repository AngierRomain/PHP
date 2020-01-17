<?php

	function myPgErrorHandler($errno, $errstr, $errfile, $errline){
		echo "avant clean";
		ob_end_clean();
		//echo $errno,
		//	', UW=',E_USER_WARNING , 
		//	', UE=', E_USER_ERROR,
		//	', UN=', E_USER_NOTICE,
		//	', --',$errstr;
		//exit(1);
		//return true;
	   if (!(error_reporting() & $errno)) {
			// Ce code d'erreur n'est pas inclus dans error_reporting()
			return;
		}

		switch ($errno) {
		case E_ERROR: // erreur fatale
		case E_USER_ERROR:
			echo '<div class="erreurfatale">',"Erreur fatale ligne <b>$errline</b> : <b>$errstr</b><br />\n";
			echo "Fichier : <b>$errfile</b>\n";
			echo "<br />PHP " , PHP_VERSION , " (" , PHP_OS , ")<br />\n";
			echo "</div>\n";
			exit(1);
			break;
			
		case E_COMPILE_ERROR :
			echo '<div class="erreurcompile">',"Erreur compilation ligne <b>$errline</b> : <b>$errstr</b><br />\n";
			echo "Fichier : <b>$errfile</b>\n";
			echo "<br />PHP " , PHP_VERSION , " (" , PHP_OS , ")<br />\n";
			echo "</div>\n";
			exit(1);
			
		

		case E_WARNING:
		case E_USER_WARNING:
			echo '<div class="erreurwarning">',"<b>Warning ligne $errline</b> : $errstr<br />\n";
			echo "Fichier : <b>$errfile</b>\n";
			echo "<br />PHP " , PHP_VERSION , " (" , PHP_OS , ")<br />\n";
			echo "</div>\n";
			break;

		case E_NOTICE:
		case E_USER_NOTICE:
			echo '<div class="erreurnotice">',"Notice ligne <b>$errline</b> : <b>$errstr</b><br />\n";
			echo "Fichier : <b>$errfile</b>\n";
			echo "<br />PHP " , PHP_VERSION , " (" , PHP_OS , ")<br />\n";
			echo "</div>\n";
			break;

		default:
			echo '<div class="erreur">',"Erreur n° <b>$errno</b> ligne <b>$errline</b> : <b>$errstr</b><br />\n";
			echo "Fichier : <b>$errfile</b>\n";
			echo "<br />PHP " , PHP_VERSION , " (" , PHP_OS , ")<br />\n";
			echo "</div>\n";
			echo "PG Erreur n°: $errno en ligne $errline dans fichier $errfile<br/>$errstr\n";
			break;
		}

		/* Ne pas exécuter le gestionnaire interne de PHP */
		return true;
	}
	
	//session_start();
	require_once 'outils/cookiemanager.php';

	
	/* comment déclencher une erreur
		trigger_error("Message", E_USER_WARNING);
	*/
	$tmp = file_get_contents('php://input');
	if (strlen($tmp) > 0) {
		$old_error_handler = set_error_handler("myPgErrorHandler");
		function pgSpecialShutdown()
		{
			// This is my Shutdown function, in 
			// here we can do any last operations
			// before the script is complete.
			$last_error = error_get_last();
			$tmp = $last_error['type'];
			//echo "coucoucou $tmp";
			switch ($tmp) {
				case E_ERROR:
				case E_COMPILE_ERROR :
				case E_USER_ERROR:
					myPgErrorHandler($tmp, 
								$last_error['message'], 
								$last_error['file'], 
								$last_error['line']);
			}
		}
		register_Shutdown_function('pgSpecialShutdown');
		$result = null;
		//ob_start(null, 100000,PHP_OUTPUT_HANDLER_REMOVABLE);
		ob_start();
		//echo '<div>hello</div>';
		try {
			$data = json_decode($tmp);
			require_once 'poo/pgitem.php';
			require_once 'poo/pgfichier.php';
			require_once 'poo/pgclassdetect.php' ;         
			//pgClassDetect::$ordrealpha = $data->alpha ;
			foreach ($data->fichiers as $f) {
				require_once $f;
			}
			//echo 'papou 3<br/>';
			$lstcl = new pgClassDetect();
			$lstcl->memoExtras($data->extras);
			$lstcl->rechercheDesClasses();
			$lstcl->memoFiles($data->fichiers);


			$pp1 = json_encode($lstcl, JSON_PRETTY_PRINT);
			$pp2 = json_encode($lstcl);
			ob_end_clean();
			//$lstcl->afficherUnSelect();
			echo '<div class="travail">Taille ',strlen($pp1),
			    ', compressé ',strlen($pp2),'<div>';
			echo '<div><pre class="enjson">';
			echo $pp1;
			echo '</pre></div>';
			$num = 0;
			foreach ($data->fichiers as $f) {
				echo '<div class="filesource"><span>', $f ,'</span>' ;
				echo '<div>';
				$fp = fopen($f,"r");
				while($line = fgets($fp)) {
					echo '<pre>',htmlspecialchars($line),'</pre>';
				}
				fclose($fp);
				//echo ']]>'; 
				echo '</div>';
				
				echo '</div>';
				$num++;
			}
			//$lstcl->afficherToutesLesClasses();
			ob_end_flush();
			//sleep(2);
			exit();
		} catch (Exception $e) {
			echo 'CATCH EXCEPTION';
			var_dump($e);
		}
		//ob_clean();
		var_dump($result);
		exit();
	}
	require_once 'poo/pgitem.php';
	require_once 'poo/pgfichier.php';
	require_once 'poo/pgdossier.php';
	require_once 'poo/pgexplorer.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv = "content-type" content = "text/html; charset=utf-8" />
<title>PG - Analyse PHP</title>
<link href="link/style.css" rel="stylesheet" type="text/css" />
<script src="link/solution.js" type="text/javascript" ></script>
<script src="link/ajax.js" type="text/javascript" ></script>
<script src="link/analyse.js" type="text/javascript" ></script>
<script src="link/cookies.js" type="text/javascript" ></script>
<script>
var classevue = null;
function voir(nom) {
	try {
		if (classevue!=null) {
			classevue.classList.remove("visuyes");
			classevue.classList.add("visuno");
		}
		classevue = document.getElementById(nom);
		classevue.classList.add("visuyes");
		classevue.classList.remove("visuno");
		//document.getElementById("selclass").value = nom;
	}
	catch (e) {
	}
}
</script>
</head>
<body>
<div id="fichiersetdossiers" class="unselectable">
	<h3>Les fichiers sur le site</h3>
	<div class="fichsuff"><?php
	$obj=new pgExplorer();
	$tmp = $_SERVER['REQUEST_URI'];
	for ($i=1; $i<=2; $i++) {
		$pos = strrpos($tmp, '/');
		if ($pos!==false) {$tmp = substr($tmp, 0, $pos);}
	}
	echo "Recherche des fichiers dans '<span>$tmp</span>', ayant pour extension ";
	$nb = count(pgDossier::$extensions);
	$cpt = 0;
	foreach (pgDossier::$extensions as $ext) {
		$cpt++;
		switch ($cpt) {
			case 1:break;
			case $nb: echo ' ou '; break;
			default : echo ', ';
		}
		echo "<span>$ext</span>";
	}
	echo '. (trouvés : <span id="nbfichiers"></span>)';
?>
	</div>
	<div id="detailsfichiers">
	</div>
	<div id="divsel">
		<div>Fichiers choisis</div>

		<ol id="listsel">
		</ol>
			<div>Classes à ajouter</div>
			<ol id="listcla"></ol>
			<div><span>Nom : </span><input id="txtclasse" type="text" size="25" maxlength="25" /></div>
		<div id="divbtn"><button id="btnvalider" onClick="demander();">Valider</button>
				<button id="btnvider" onclick="viderListe();">Vider</button></div>
	</div>
	<div id="giorgi">
		<span>Analyse PHP Objet - Version 1.4 - &#169; P. Giorgi</span>
	</div>
</div>

<div id="jsondiv" class="travail">
	<div>Taille du JSON : <?php 
		pgFichier::$JSON_CONTENU = true;
		$tmp = json_encode($obj->o_Dossier, JSON_PRETTY_PRINT);
		$tmp2 = json_encode($obj->o_Dossier);
		echo strlen($tmp), ', compressé : ', strlen($tmp2);
	?></div>
	<pre id="jsonpre"><?php echo $tmp; ?></pre>
</div>
<div id="result" class="travail">rien</div>
<div id="analyse" class="etatoff">
	<div id="menuclasses">
		<!--<div><span class="btnnav unselectable" id="prevbtn">&#x21E4;</span><span class="btnnav unselectable" id="avantbtn">&#x21C7;</span></div>-->
		<div id="choixclasse"></div>
		<!--<div><span class="btnnav unselectable" id="apresbtn">&#x21C9;</span><span class="btnnav unselectable" id="nextbtn">&#x21E5;</span></div>-->
	</div>
	<div id="lstclasses"></div>
	<div id="visumethode">
		<table>
			<tr><td><span id="methodename"></span></td></tr>
			<tr><td><pre id="methodelines"></pre></td></tr>
		</table>
	</div>
	<div id="closeanalyse">
		<div><button onclick="fermerAnalyse();">Fermer</button></div>
				<div>
		<input type="checkbox" id="visuherit" name="visuherit" value="1">
			<label for="visuherit">Voir membres hérités</label><br/>
		<div style="margin-left:15px">
		<input type="checkbox" id="visuprivate" name="visuprivate" value="1">
			<label for="visuprivate"><i>membres 'private'</i></label></div>
		<input type="checkbox" id="visustatic" name="visustatic" value="1">
			<label for="visustatic">Voir membres static</label><br/>
		<input type="checkbox" id="visunonstatic" name="visunonstatic" value="1">
			<label for="visunonstatic">Voir membres non static</label><br/>		</div>
		<div>
			<input type="checkbox" id="voirresume" name="voirresume" value="1">
			<label for="voirresume">Mode 'Résumé'</label><br/>
			<input type="checkbox" id="voircouleur" name="voircouleur" value="1">
			<label for="voircouleur">Mode 'Normal' en couleurs</label><br/>
		</div>
		<div id="criteretri">
		<span>Affichage : tri</span><br/>
		<input type="radio" name="choixtri" id ="rb0" value="0">
		<label for="rb0"><i>aucun</i></label><br/>
		<input type="radio" name="choixtri" id="rb1" value="1">
		<label for="rb1"><i>alpha, par classe</i></label><br/>
		<input type="radio" name="choixtri" id="rb2" value="2">
		<label for="rb2"><i>alphabétique</i></label><br/>
		<input type="radio" name="choixtri" id="rb3" value="3">
		<label for="rb3"><i>accès, alpha</i></label><br/>
		<input type="radio" name="choixtri" id="rb4" value="4">
		<label for="rb4"><i>static, alpha</i></label>
		</div>
	</div>
</div>
<div id="pginformation" class="visuno">
	<div id="pgdialogue" >
		<table>
			<tr>
				<td><div id="dialtexte">Texte du dialogue</div></td>
			</tr>
			<tr>
				<td><button id="dialbtnok">OK</button></td>
			</tr>
		</table>
	</div>
</div>
</div>
</body>
</html>
<?php
/************************************** TRASH
	function joliEntier($n) {
		$m = $n;
		$tmp = '';
		$l = strlen($n);
		while ($l>3) {
			$l-=3;
			$tmp = ' ' . substr($m, $l,3) . $tmp;
		}
		$tmp = substr($m, 0, $l) . $tmp;
		return $tmp;
	}
*/?>