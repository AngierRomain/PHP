<?php
	class Vueutilisateur {
		private $oListe;
		
		public function __construct($theListe){
			$this->oListe = $theListe;
		}
		
		public function getDomSelect(){
			$DOM = new Balise('select');
			foreach($this->oListe as $KM => $OMAT){
				$OPT = new Balise('option');
				$DOM->add($OPT);
				$OPT->add($OMAT->getNP());
				$OPT->addAttribut('value', $KM);
			}
			return $DOM;
		}
	
		public function getDomTbody($cols, $itemClickable=false){
			$DOM = new Balise('tbody');
			if ($itemClickable) {
				$DOM->addAttribut('data-nature', 'user');
				$DOM->addAttribut('onclick', 'gestu_Demande(event)');
			}
			$TBCOLS = explode(';', $cols);
			$cpt = 0;
			foreach ($this->oListe as $KEY => $OBJ){
				$cpt++;
				$TR = new Balise('tr'); $DOM->add($TR);
				if ($itemClickable) {
					$TR->addAttribut('data-key', $KEY);
				}
				foreach ($TBCOLS as $codecol) {
					$TD = new Balise ('td'); $TR->add($TD);
					switch ($codecol) {
						case 'CPT':
							$TD->add($cpt);
							$TD->addClass('numeric');
							break;
						case 'COD':
							$TD->add($OBJ->getIdUtilisateur());
							break;
						case 'PNM':
							$TD->add($OBJ->getPrenom());
							break;
						case 'NOM':
							$TD->add($OBJ->getNom());
							break;
						case 'CIV':
							$TD->add($OBJ->getstrCivilite());
							break;
						case 'DTN':
							$TD->add($OBJ->getDateN());
							break;
						case 'ADR':
							$TD->add($OBJ->getAdresse());
							break;	
						case 'TEL':
							$TD->add($OBJ->getTel());
							break;
						case 'MAI':
							$TD->add($OBJ->getMail());
							break;
						case 'NAT':
							$TD->add($OBJ->getNatureLibelle());
							break;						
						default:
							$TD->add($codecol);
					}
				}
			}
			return $DOM;
		}
		
		
	}
?>