<?php
	$DIV = new Balise('div');
	$DIV->setID('rightNav');
	$form = new Balise('form');
		$table = new Balise('table');
		$tr = '<tr>';
		$ntr = '</tr>';
		$form->addAttribut('name','frmconnex');
			$table->add($tr);
			$table->add('<td><label>Login </label></td>');
			$table->add('<td><input type="text" id="login" placeholder="votre login" required="required"/></td>');
			$table->add($ntr.$tr);
			$table->add('<td><label>Password </label></td>');
			$table->add('<td><input type="text" id="pass" placeholder="mot de passe" required/></td>');
			$table->add($tr);
			$table->add('<td><span class="bouton" onclick="gestc_Connex();">Connexion<span></td>');
			$table->add($ntr);
		$form->add($table);
	$DIV->add($form);
	$DIV->display();
?>