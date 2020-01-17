<?php
	echo 'répondeur: ', basename(__FILE__);

	echo '<br/>dossier courant: ', getcwd();
	echo '<br/>remontée au dossier parent';
	chdir('..');
	echo '<br/>dossier courant: ', getcwd();
	$postdata = file_get_contents('php://input');
	if ($postdata==''){
		echo '<br/>rien recu';
		exit(0);
	}
	echo '<br/>reçu: $postdata='; var_dump($postdata);
	
	try{ /** convertit la chaine json en tableau associatif */
		$lesdatas = json_decode($postdata, true);
		if (!is_array($lesdatas)){
			echo '<br/>les datas pas array';
			exit(0);
		}
	}catch (Exception $e){
		echo '<br/>erreur json_decode<br/>';
		var_dump($e);
		exit(0);
	}
	echo '<br/>$lesdatas='; var_dump($lesdatas);
	
	require_once 'poo/si.php';
	require_once 'poo/imetier.php';
	require_once 'poo/element.php';
	require_once 'poo/elements.php';
	
	require_once 'poo/tools/memo.php';
	require_once 'outils/balise.php';
	require_once 'outils/controleur.php';

    $quoi = $lesdatas['type'];
    $lequel = $lesdatas['key'];

    $OBJ = null;
    switch ($quoi){
		case 'connex':
			require_once 'poo/utilisateur.php';
			$OBJ = Utilisateur::SQLConnex($lesdatas);
			break;
		case 'user':
			require_once 'poo/utilisateur.php';
			$OBJ = Utilisateur::findFromPK($lequel);
			break;
        case 'fourniture':
            require_once 'poo/fourniture.php';
            $OBJ = Fourniture::findFromPK($lequel);
            break;
        case 'categorie':
            require_once 'poo/categorie.php';
            $OBJ  = Categorie::findFromPK($lequel);
            break;
        default:
            exit(0);
    }

    if ($OBJ == null) exit(0);

    var_dump($OBJ);

    echo '<pre class="json">';
    echo json_encode($OBJ, JSON_PRETTY_PRINT);
    echo '</pre>';
?>