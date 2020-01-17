<?php
	$tbclasses = array();
	function enclair($truc) {
		if ($truc==null) {return 'NULL'; }
		if (is_string($truc)) {return '"'.$truc.'" (string)'; }
		if (is_bool($truc)) {
			return ($truc) ? 'true' : 'false';
		}
		if (is_int($truc)) {return $truc.' (int)';}
		if (is_numeric($truc)) {return $truc;}
		
		if (is_array($truc)) {return 'tableau';}
		return $truc;
	}
	
	function getSubclassesOf($parent) {
	global $tbclasses;
		$result = array();
		$np = $parent->getName();
		foreach ($tbclasses as $K => $class) {
			if (is_subclass_of($K, $np))
				$result[] = $K;
		}
		return $result;
	}

	function trierN($obj1, $obj2) {
		if ($obj1==$obj2) {return 0;}
		$n1 = $obj1->getName();
		$n2 = $obj2->getName();
		return strcasecmp ( $n1 , $n2 );
	}
	function trierSN($obj1, $obj2) {
		if ($obj1==$obj2) {return 0;}
		if ($obj1->isStatic()) {
			if (!$obj2->isStatic()) {return -1;}
		} elseif ($obj2->isStatic()) {return 1;}
		return trierN($obj1, $obj2);
	}
	function plop($P) {
		$tmp='';
		if ($P->isPrivate()) {$tmp .= 'private ';}
		elseif ($P->isProtected()) {$tmp .= 'protected ';}
		elseif ($P->isPublic()) {$tmp .= 'public ';}
		if ($P->isStatic()) {$tmp .= 'static ';}
		return $tmp;
	}

	function commentaire($P) {
		$tmp = '';
		$c = $P->getDocComment();
		$c = str_replace(array('/**', '*/'), '', $c);
		$c=trim($c);
		return nl2br(htmlspecialchars($c)); 
	}

	function makeLiaison($txt) {
		$tmp = '<span class="liaison" onclick="voir('."'".$txt."');".'">'. $txt . '</span>';
		return $tmp;
	}
	?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv = "content-type" content = "text/html; charset=utf-8" />
<style>
.liaison {border:1px black solid; 
	border-radius:9px;
	padding:0px 7px;
	background-color:white; 
	color:red;
	user-select: none;
	cursor:pointer;
}
table {
	border-spacing : 0;
	border-collapse : collapse;
	background-color:transparent;
}
td {vertical-align:top;}

table td:nth-child(1) {text-align:right;}
table tr:nth-child(odd) {background-color:white;}
table tr td span {background-color:yellow; padding-left:2px;}
.methode {font-weight:bold;}
.static {text-decoration: underline ;}
.comment {font-style:italic; color:blue; text-decoration: none;}
.classe {
	position:absolute;
	top:30px;
	bottom:30px;
	border:1px black solid;
	border-radius:8px;
	padding:3px 10px;
	background-color:#cccccc;
	margin-top:12px;
	transform-origin: 0% 0%;
	transition:all 0.5s ease-in-out;
	overflow: scroll;
}
.visuno {
	opacity:0.3;
	visibility:collapse;
	transform:scale(0.5, 0.5);
}
.visuyes {
	opacity:1;
	visibility:visible;
	transform:scale(1, 1);
}

.defclasse {
	border:2px white solid;
	border-radius:8px;
	margin:5px;
	padding:3px 10px;
	background-color:black;
	color:white;
	font-size:125%;
	font-weight:bold;
}

.porteeclasse {
	text-decoration: underline wavy red;
}


</style>
<script>
var classevue = null;
function voir(nom) {
	try {
		if (classevue!=null) {
			classevue.classList.remove("visuyes");
			classevue.classList.add("visuno");
		}
		classevue = document.getElementById(nom);
		classevue.classList.add("visuyes");
		classevue.classList.remove("visuno");
		document.getElementById("selclass").value = nom;
	}
	catch (e) {
	}
}

// exécuté quand le body a été intégralement reçu
window.onload = function() {
	try {
		voir(document.getElementById("selclass").value)
	}
	catch (e) {
		alert(e);
	}
}
</script>
</head>
<body>
<div>
<?php
	
	foreach (get_declared_classes() as $cl) {
		$obj = new ReflectionClass($cl);
		if ($obj->isUserDefined()) {
			$tbclasses[$cl] = $obj;
		} elseif (substr($cl,0,3)=='PDO') {
			$tbclasses[$cl] = $obj;		
		}
	}
	
	foreach ($tbclasses as $cl) {
		$herite = $cl->getParentClass(); 
		if ($herite!==false) {$tbclasses[$herite->getName()] = $herite;} 
		foreach ($cl->getInterfaces() as $I) {
			$tbclasses[$I->getName()] = $I;
		}
	}
	uasort($tbclasses, 'trierN');

	echo 'Choisir une classe : ';
	echo '<select id="selclass" onchange="voir(this.value);">';
	foreach ($tbclasses as $nom => $cl) {
			echo '<option value="',$nom,'">';
			echo $nom;
			echo '</option>';
	}
	echo '</select><br/>';
	
	foreach ($tbclasses as $classe) {
		//$classe = new ReflectionClass('JsonSerializable');
		//$classe = new ReflectionClass('Element');
		//$classe = new ReflectionClass('SI');
		//$classe = new ReflectionClass('Categorie');
		$n = $classe->getName();
		echo "\n", '<div id="', $n, '" class="classe visuno">';
		echo '<div class="defclasse">';
		$estinterface = $classe->isInterface();
		if ($estinterface) {echo 'Interface : ';}
		else {echo 'Classe : ';}
		echo '<span>', $n, '</span> ';
		$estecrit = $classe->isUserDefined();
		echo ($estecrit ? ' (définie dans la solution)' : '(Interne)' );
		echo '<br/>';
		
		if ($estecrit) {
			$deb = $classe->getStartLine();
			$fin = $classe->getEndLine();
			echo 'Source : ',$classe->getFileName(), 
				'<br/>',($fin-$deb+1), ' lignes (',$deb,', ',$fin,')<br/>';
		}

		$herite = $classe->getParentClass(); 
		if ($herite!==false) {
			echo ' --- Hérite de ', makeLiaison($herite->getName()),'<br/>';}
		if ((!$estinterface) && $classe->isAbstract()) {echo 'classe Abstraite', "<br/>";}
		$Interf = $classe->getInterfaces();
		$nbi=count($Interf);
		if ($nbi==1) {$nbi=" --- Implémente l'interface ";} 
		else {$nbi=" --- Implémente les $nbi interfaces ";}
		$ilya=false;
		
		foreach ($Interf as $K => $I) {
			if (!$ilya) {echo "\n$nbi";}
			else {echo ', ';}
			echo makeLiaison($K);
			//echo $K;
			$ilya=true;
		}
		
		// recherche des classe dérivées
		$ilya=false;
		foreach (getSubclassesOf($classe) as $CD) {
			if (!$ilya) {
				echo '<br/> --- ';
				echo  (($estinterface) ? 'Est implémenté par ' : 'Est dérivée par ');
				$ilya = true;
			}
			else {echo ', ';}
			echo makeLiaison($CD);
		}
		echo '</div>';
		echo "\n",'<table>';
		
		foreach ($classe->getConstants() as $K => $V){
			echo "\n<tr><td>const $K</td><td>";
			echo enclair($V);
			echo '</td></tr>';
		}  

		$Prop_Stat = $classe->getStaticProperties();
		$Prop_DP = $classe->getDefaultProperties();
		$Prop = $classe->getProperties();
		uasort($Prop_Stat, 'trierN') ;
		
		//echo "\n", '<tr><td colspan="4">Membres STATIC</td></tr>';
		foreach ($Prop_Stat as $K => $V) {
			$P = $classe->getProperty($K);
			$c = commentaire($P);
			echo "\n", '<tr class="membre"><td class="static">';


			echo '<span>',plop($P),'</span>';
			echo '</td><td class="static"><span>$',$K,'</span></td><td>';
			var_dump($V);
			echo "</td><td class='comment'>$c</td></tr>"; 

		}
		$i=0;
		//echo "\n", '<tr><td colspan="4">Membres</td></tr>';
		$lesProps = $classe->getProperties();
		uasort($lesProps, 'trierN') ;
		foreach ($lesProps as $K => $P) {
			if (!array_key_exists($P->getName(), $Prop_Stat)) {
				$i++;
				echo "\n",'<tr class="membre objet"><td>';
				echo plop($P);
				echo '</td><td>$';
				echo $P->getName(), '</td><td>';
				var_dump($Prop_DP[$P->name]);
				$c = commentaire($P);
				echo "</td><td class='comment'>$c</td></tr>"; 
			}
		}
		
		$tmp = $classe->getMethods();
		uasort($tmp, 'trierSN') ;
		foreach ($tmp as $M) {
			if ($classe==$M->getDeclaringClass()) {
				// récup des arguments
				$par = $M->getParameters();
				$signat = '(';
				$plus='';
				foreach ($par as $kp => $p1) {
					if ($kp>0) {$signat .= ', ';}
					$signat .= $p1->getName();
					if ($classe->isUserDefined()) {
						if ($p1->isOptional()) {
							$signat .= '='.enclair($p1->getDefaultValue());
						} else {
												
						}
					}
				}
				$signat .= ')';
				$cla = '';
				$declarer = plop($M);
				if ($M->isFinal()) {$declarer .= ' final ';}

				if ($M->isStatic()) {$cla .= ' class="static"';}
				echo "\n",'<tr class="methode"><td',$cla,'>', 
						$declarer,' function','</td><td',$cla,'>',
						$M->getName(),$signat, '</td><td>';
				if (!$estinterface && $estecrit) {
					$deb = $M->getStartLine();
					$fin = $M->getEndLine();
					if ($deb==$fin) {echo "ligne $deb";}
					else {echo "lignes $deb à $fin";}
				}
				
				//if ($M->hasReturnType()) { PHP 7
				//	echo $M->getReturnType()->__toString();
				//}
				//echo get_class($M);
				echo '</td><td class="comment">', commentaire($M);
				echo '</td></tr>';
			}
		}
		echo "\n</table>";		
		echo "\n</div>";
	}
	


	//if (count($tmp)!=0) { }
?>

</div>
</body>
</html>


