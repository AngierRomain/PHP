<?php
	class Elements implements Iterator {
		
		
		/*
		stockage interne dans tableau associatif
		*/
		private $arr;

		/*nom de la classe des objets stocké des ojets dans la liste*/
		private $classItem;
		
		protected function __CONSTRUCT(){
			$this->arr = array();
			$this->classItem = $this->getClassOfItem();
		}
		
	/*renvoi par default de nom de la classe plusriel sans le "s" final
	peut être réécrite
	*/
		protected function getClassOfItem(){
			$clapp = get_called_class();
			$taille = strlen($clapp);
			return substr($clapp, 0, $taille-1);
			
		}
		/*public function rangerFromDB($curseur){
			$classdesitems = $this->get_classeOfItem();
			
			foreach($curseur as $ligne){
				$tmp = new $classdesitems($ligne);
				$this->arr[$tmp->getID()] = $tmp;
				
			}			
		}*/
		
		/*renvoi l'élément si il existe
		@$id : id de l'élément recherché*/
		public function getObject($id){
			if (!array_key_exists($id, $this->arr)) return null;
			return $this->arr[$id];
		}
		
		/*instencie une listeen la remplissant depuis la BDD*/
		public static function makeFromBDD($whereOrder = null, $params = null){
			$classList = get_called_class(); //recup class
			$TMP = new $classList();
			$clobj = $TMP->classItem;
			$req = $clobj::SQLselect();
			if ($whereOrder != null) {
				$req.= ' ' .$whereOrder;
			}
			// etude des parametres
			if ($params != null) {
				if (! is_array($params)){
					//supression du 1ere argument<s
					$nbargs = func_num_args();
					if ($nbargs > 2){
						$params = func_get_args();
						array_shift($params);
					}
				}
				
			}
			//echo '</br>', $req;
			$curs = SI::getSI()->SGBDgetCursorReady($req, $params);
			foreach($curs as $ligne){
				/**recherche de l'objet avec l'ID dans le raw */
				$IDROW = $clobj::getIDinRaw($ligne);
				/**recherche objet avec cette ID */
				$obj = Memo::find($clobj, $IDROW);
				if ($obj == null) $obj = new $clobj($ligne);
				$TMP->arr[$IDROW] = $obj;
								
			}
			return $TMP;
		}
		
		private $myIterator;

		public final function rewind(){
			$this->myIterator = new ArrayIterator($this->arr);
			$this->myIterator->rewind();
		}

		public final function current()	{ return $this->myIterator->current(); }
		public final function key() 	{ return $this->myIterator->key(); }
		public final function next() 	{ return $this->myIterator->next(); }
		public final function valid()	{ return $this->myIterator->valid(); }
		

	}


?>