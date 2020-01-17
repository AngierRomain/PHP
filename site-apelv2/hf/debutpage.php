<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8"/>
	<title>APEL</title>
	<link rel="stylesheet" href="styles/styles.css" type="text/css" />
	<script src="scripts/ajax.js" type="text/javascript"></script>
	<script src="scripts/gestconnex.js" type="text/javascript"></script>
	<script src="scripts/gestutilisateur.js" type="text/javascript"></script>
    <script src="scripts/gestfourniture.js" type="text/javascript"></script>
    <script src="scripts/gestcategorie.js" type="text/javascript"></script>
</head>
<body>
<?php
	require_once('vues/vtop.php');
	SI::getSI()->getControleur()->displayMenu();
?>
<div id="contenu">
<div id="details"></div>
