<?php
/** système d'information : 
UNE SEULE INSTANCE POSSIBLE : SINGLETON */
class SI {
	
	const BDD_SERVER	= '127.0.0.1';
	const BDD_NAME 	= 's5bd1';
	const BDD_LOGIN 	= 'root';
	const BDD_PWD 	= '';
	const BDD_SETNAMES = 'SET NAMES utf8';
/** Mise Au Point SQL . Mettre true pour stopper
tout si une requête se passe mal.*/
	const MAP = true; 

	/** LE CONTROLEUR AVEC LE SI */
	private $oCTRL;

/** connexion à la BDD */
	private $o_Cnx;
	
/** stockage interne DU SI.*/
	private static $theSI;
	
/** constructeur privé, contrôlé par le getter static */
	private function __construct() {
		//echo '<br/>Instanciation SI';
		$this->o_Cnx = new PDO('mysql:host='.static::BDD_SERVER.
			';dbname='.static::BDD_NAME, 
		static::BDD_LOGIN,
		static::BDD_PWD,
		array(PDO::MYSQL_ATTR_INIT_COMMAND => static::BDD_SETNAMES));
		$this->o_Cnx->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		// si on veut que 'prepare' genere une erreur
		if (static::MAP) 
			$this->o_Cnx->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$this->oCTRL = new Controleur();
	}

	public function getControleur(){return $this->oCTRL;}
	
/** renvoie LE SI, en l'instanciant si nécessaire */
	public static function getSI() {
		// si pas theSI alors créer
		if (static::$theSI == null) 
			static::$theSI = new SI();
		return static::$theSI;
	}
	
/** génère un curseur (PDOStatement) à partir de la requête */
	private function getCursor($req) {
		$curs = $this->o_Cnx->prepare($req);
		// en MAP, $curs peut être un boolean (false) si erreur
		if (!$curs) {
			echo '<br/>erreur prepare de : ',$req,'<br/>';
			echo '<pre>', var_export($this->o_Cnx->errorInfo()), '</pre>';
			echo '<br/><pre>'; 
			debug_print_backtrace();
			echo '</pre><br/>';
			exit(0);
		}
		return $curs;
	}
	
/** recupère une seule ligne en fonction de la requête
@req : requete de type SELECT
@params : optionnel, peut être NULL, atomique, array, ou une série linéaire */
	public function SGBDgetOneRow($req, $params=null) {
		$curs= $this->getCursor($req);
		
		if ($params==null) { // pas de paramètre après la requète
			$ok = $curs->execute();
		} elseif (is_array($params)) { 
			$ok = $curs->execute($params);
		} elseif ( func_num_args() > 2) {
			$params = func_get_args(); // recup en array de ts les arguments
			array_shift($params); // suppression 1er argument (req)
			$ok = $curs->execute($params);
		} else { // un seul paramètre, atomique
			$curs->bindParam(1, $params); 
			$ok = $curs->execute() ;
		}
		//echo '<br/>après execute<br/>';
		//var_dump($curs->errorInfo());
		//echo '<br/>';
		
		if (!$ok) {
			echo '<br/>erreur execute de : ',$req,'<br/>';
			echo '<pre>', var_export($curs->errorInfo()), '</pre>';
			echo '<br/><pre>'; 
			debug_print_backtrace();
			echo '</pre><br/>';
			exit(0);			
		}
		$lig = $curs->fetch();
		return ($lig) ? $lig : null;  // if compacté
	}
/** Renvoie un curseur pret à être balayé
@req : requete complete SQL
@params : peut être null, atomique ou array */
	public function SGBDgetCursorReady($req, $params) {
		$curs = $this->getCursor($req);

		if ($params==null) {
			$ok = $curs->execute();
		} elseif (is_array($params)) {
			$ok = $curs->execute($params);
		} else {
			$curs->bindParam(1, $params); 
			$ok = $curs->execute() ;
		}
		if (!$ok) {
			echo '<br/>erreur execute de : ',$req,'<br/>';
			echo '<pre>', var_export($curs->errorInfo()), '</pre>';
			echo '<br/><pre>'; 
			debug_print_backtrace();
			echo '</pre><br/>';
			exit(0);			
		}
		return $curs;
	}
    /** Execution paramétrée : INSERT, etc ... */
    public function SGBDexecuteQuery($requete, $valeurs){
        $stmt = $this->o_Cnx->prepare($requete);
        $nbargs = func_num_args();
        if ($nbargs==2){
            $params = $valeurs;
        }else{
            $params = func_get_args();
            array_shift($params); //Enlever le 1er parametre
        }
        $R = array();
        try{
            $stmt->execute($params);
            $tberr = $stmt->errorInfo();
            if ($tberr[0] == '00000'){
                $tmp = $stmt->rowCount();
                if ($tmp==0){
                    $R = array('pgstatus' => 0, 'pgerror' => 0, 'pgcomment' => 'aucune information modifiée');
                }else{
                    $R = array('pgstatus' => $tmp, 'pgerror' => 0, 'pgcomment' => "L'opération a affecté $tmp occurrence(s)");
                }
            }else{
                $R = array('pgstatus' => -1, 'pgerror' => $tberr[0], 'pgcomment' => $tberr[2]);
            }
        }catch (Exception $e){
            $R = array('pgstatus' => -3, 'pgerror' => 0, 'pgcomment' => $e->getMessage());
        }return $R;
    }
}




?>