<?php
	$hrefsite = new Balise('a');
	$title = new Balise('h1');

	$title->add('APEL - Champagnat');
	$title->setID('mastertitle');
	$hrefsite->addAttribut('href','http://127.0.0.1/PPE/site-apelv2/');
	
	$hrefsite->add($title);
	$hrefsite->display();
?>