<?php
    /*
    System d'information : UNE SEULE INSTANCE POSSIBLE : SINGLETON
    */
class SI {
    const BDD_SERVER = '127.0.0.1';
	const BDD_NAME = 'apelv2';
	const BDD_LOGIN = 'root';
	const BDD_PWD = '';
    const BDD_SETNAMES = 'SET NAMES utf8';

    /* CONNEXION à la BDD*/
    private $o_Cnx;

    const MAP = true;
	
	//le controleur avec le SI
	private $oCTRL;
    
    /* stockage interne DU SI. */
    private static $theSI;

    private function __CONSTRUCT() {
        //echo '</br> instenciation SI'; 
        
        $this->o_Cnx = new PDO('mysql:host='.static::BDD_SERVER.';dbname='.static::BDD_NAME, static::BDD_LOGIN, static::BDD_PWD, 
            array(PDO::MYSQL_ATTR_INIT_COMMAND => static::BDD_SETNAMES));
        $this->o_Cnx->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        if(static::MAP)
            $this->o_Cnx->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		
		$this->oCTRL = new Controleur();
            
    }

	public function getControleur() {
		return $this->oCTRL;
	}

    /* renvoie LE SI, en l'instanciant si néscessaire */
    public static function getSI() {
        /* si pas theSI alors creer*/
        if(static::$theSI == null) static::$theSI = new SI();
        return static::$theSI;
    }

    private function getCursor($req) {
        $curs = $this->o_Cnx->prepare($req);
        // MAP (mise au point, $curs peut etre un boolean)
        if (!$curs){
            echo '</br> Erreur prepare de : ', $req, '</br>';
            echo '<pre>', var_export($this->o_Cnx->errorInfo()), '</pre>';
            echo '</br><pre>';
            debug_print_backtrace();
            echo '</pre></br>';
            exit(0);
        }
        return $curs;
    }


    public function SGBDgetOneRow($req, $params=null){
        
        $curs = $this->getCursor($req);
        if($params == null) {
            $ok = $curs->execute();
        }
        else if(is_array($params)){
            $ok = $curs->execute($params);
        }
        else {
            $curs->bindParam(1, $params);
            $ok =$curs->execute();
        }

        if (!$ok) {
            echo '</br> Erreur execute de : ', $req, '</br>';
            echo '<pre>', var_export($curs->errorInfo()), '</pre>';
            echo '</br><pre>';
            debug_print_backtrace();
            echo '</pre></br>';
            exit(0);
        }
        $lig = $curs->fetch();
        return ($lig) ? $lig : null; //if compacté
		
    }

    /*renvoi un curseur pret a etre balayé
    @req requete complete SQL 
    @params peut etre nul, atomique (élémentaire) ou array
    */

    public function SGBDgetCursorReady($req, $params){
        $curs = $this->getCursor($req);
        if($params == null) {
            $ok = $curs->execute();
        }
        else if(is_array($params)){
            $ok = $curs->execute($params);
        }
        else {
            $curs->bindParam(1, $params);
            $ok = $curs->execute();
        }
        if (!$ok) {
            echo '</br> Erreur execute de : ', $req, '</br>';
            echo '<pre>', var_export($curs->errorInfo()), '</pre>';
            echo '</br><pre>';
            debug_print_backtrace();
            echo '</pre></br>';
            exit(0);
        }
        return $curs;
    }
	
	public function SGBDexecuteQuery($requete, $valeurs) {
		$stmt = $this->o_Cnx->prepare($requete);
		$nbargs = func_num_args();
		if($nbargs == 2) {
			$params = array($valeurs);
		} else {
			$params = func_get_args();
			array_shift($params); // suppression 1er argument
		}
		$R = array();
		var_dump($params);
		try {
			$stmt->execute($params);
			$tberr = $stmt->errorInfo();
			if ($tberr[0]=='0000') {
				$tmp = $stmt->rowCount();
				if ($tmp==0) {
					$R = array( 'pgstatus' => 0, 'pgerror' => 0,
								'pgComment' => 'aucune information modifie');
				} else {
					$R = array( 'pgstatus' => $tmp, 'pgerror' => 0,
								'pgComment' => "l'operation a affecté $tmp occurences");
				}
			} else {
				$R = array( 'pgstatus' => -1, 'pgerror' => $tberr[0],
								'pgComment' => $tberr[2]);
			}
		} catch (exception $e) {
			$R = array( 'pgstatus' => -3, 'pgerror' => 0,
								'pgComment' => $e->getMessage());
		}
		return $R;
	}
}

?>