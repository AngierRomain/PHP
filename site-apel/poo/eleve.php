<?php


class Eleve extends Element
{
    static function champPK(){return 'SELECT ELECode, ELECodeUTI, ELECodeNIV, ELENom, ELEPrenom, ELECivilite from eleve';}

    static function SQLSelect(){}
}