<?php
	interface IMetier {
	/** MÉTHODE DEVANT ÊTRE OBLIGATOIREMENT RÉÉCRITE
(définition dans les classes dérivées de Element)
Il s'agit de renvoyer le (ou les) champ(s) constitutif(s) de la clé primaire (PK)
Exemple 1 : PK simple    : return 'CATCode' 
Exemple 2 : PK composée  : return ['LIGNumFact', 'LIGnumseq'] */
	static function champPK();
/** MÉTHODE DEVANT ÊTRE OBLIGATOIREMENT RÉÉCRITE 
(définition dans les classes dérivées de Element) 
@return : (string) 'SELECT champ1, champ2, ..., champn FROM nom_table' */
	static function SQLSelect();
	}

?>