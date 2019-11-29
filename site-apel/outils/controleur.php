<?php
class Controleur{
    //tableau associatif des menus
    private $TB_MENUS;

    public function __construct()
    {
        $this->TB_MENUS = array(
            'ACC' => 'rempAccueil.php');
            session_start();
    }

    /** renvoie la valeur d'une variable de session */
    private function getVarSess($name, $defaultvalue=null){
        if (array_key_exists($name, $_SESSION))
            return $_SESSION[$name];
        return $defaultvalue;
    }

    public function setCodeMenu($value){
        return $_SESSION['CODEMENU'] = $value;
    }

    public function getCodeMenu(){
        return $this->getVarSess('CODEMENU', 'ACC');
    }

    public function displayMenu(){
        foreach ($this->TB_MENUS as $K => $fich){
            echo '<button data-key="'.$K.'"
            onclick="mnu_select(this);">',$K,'</button>';
        }
    }

    public function getRemplisseur(){
        $fichier = $this->TB_MENUS [$this->getCodeMenu()];
        require_once 'contenus/'.$fichier;
        return new Remplisseur();
    }

    public function DEMARRER(){
        $postdata = file_get_contents('php://input');
        if ($postdata==''){
            $pageEntiere = true;
            include 'htmltemplates/debutpage.php';
            $REMP = $this->getRemplisseur();
            $REMP->fullDisplay();
            //echo date('H:i:s');
            include 'htmltemplates/finpage.php';
        }else{
            $pageEntiere = false;
            echo '<br/>reçu dans controleur: $postdata='; var_dump($postdata);

            try{
                /** convertit la chaine json en tableau associatif */
                $lesdatas = json_decode($postdata, true);
                if (!is_array($lesdatas)){
                    echo '<br/>les datas pas array';
                    exit(0);
                }
                $dem = $lesdatas['demand'];
                switch ($dem){
                    case 'choixmenu':
                    $this->setCodeMenu($lesdatas['key']);
                    $REMP = $this->getRemplisseur();
                    $REMP->fullDisplay($dem);
                    exit(0);
                }
                $REMP = $this->getRemplisseur();
                $REMP->actualiser($dem, $lesdatas);

            }catch (Exception $e){
                echo '<br/>erreur json_decode<br/>';
                var_dump($e);
                exit(0);
            }
        }

    }
}


?>