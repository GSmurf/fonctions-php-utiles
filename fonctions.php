<?php

/**
 * Affiche la variable ou la log dans error.log
 * @copyright : Salles stéphane gsmurf7@gmail.com - distribué librement pour le bien de tous ^^
 * @access public
 * 
 * @param type $variable Valeur de la variable à afficher
 * @param string $nom Labèle à ajouter
 * @param boolean $log doit on logger dans un fichier ? Si false alors on affiche
 * @param string $nomFichierLog nom du fichier dans lequel on log
 * @return void
 */
function test($variable, $nom=null, $log = false, $nomFichierLog = "error.log"){
	$liste=debug_backtrace();
	if (!function_exists("implode_with_keys")) {
		function implode_with_keys($glue, $array, $valwrap=''){
			foreach($array AS $key => $value) {
				$ret[] = $key."=".$valwrap.$value.$valwrap;
			}
			return implode($glue, $ret);
		}
	}//if
	$colorFond = array('#F5C1F1', '#D0C1F5', '#C1D6F5', '#C1F5F5', '#C2F5C1', '#ECF5C1', '#F5E1C1', '#F5C9C1');
	if ($log == false){
		
	switch(gettype($variable)){
	case "object":
		$nom = $type = get_class($variable)." Object";
		break;
	case "boolean":
		if($variable == TRUE){
			$nom = "TRUE";
		}else{
			$nom = "FALSE";
		}
		$type = gettype($variable);
		break;
	case "array":
		$type = gettype($variable)."[".count($variable)."]";
		break;
	case "string":
		$type = gettype($variable)."[".strlen($variable)."]";
		break;
	default:
		$type = gettype($variable);
	}
	if($nom == null)$nom = $variable;
	$uniqid = uniqid();
	?>
		<div style="background-color:<?= $colorFond[array_rand($colorFond)];?>;text-align: left;">
			<legend style="font-size: 16px;font-weight: bold;"><a href="#<?=$uniqid;?>"><?= $nom;?></a> ( <?= $type;?> ) <a href="javascript:void();" onclick="$('#calque_<?=$uniqid;?>').toggle();" style="font-size: 0.7em;">[toggle]</a></legend>
			<div id="calque_<?=$uniqid;?>">
				<p><?php echo $liste[0]["file"].":".$liste[0]["line"]; ?></p>
				<pre><?php print_r($variable); ?></pre>
			</div>
		</div>
		<a name="<?=$uniqid;?>"></a>
	<?php
	} else {
		$file = fopen($nomFichierLog, 'a');
		if (is_array($variable)) {
			$variable = implode_with_keys(",", $variable);
		}//if
		fputs($file, "\n".date("d/m/y H:i:s || ").$nom." : ".$variable);
		fclose($file);
	}//if
}


function ToString($val)
{
	if (is_object($val)) $ret="{Objet} ".get_class($val);
	else if (is_array($val))
	{
		$ret="";
		foreach ($val as $k=>$v) $ret="{$ret}".($ret?" , ":"")."{$k}=>".ToString($v);
		$ret="array({$ret})";
	}
	else $ret=$val;

	return($ret);
}
function GetValue(&$table,$key1,$key2=NULL,$default="")
{
	if($table && is_array($table))
	{
		if (is_array($table) && array_key_exists($key1,$table))
		{
			if ($key2!==NULL) return(GetValue($table[$key1],$key2,NULL,$default));
			else return($table[$key1]);
		}
		else return($default);
	}
	else return($default);
}
/*
 * si $niveau=0, tous les niveaux sont affichés,
* sinon, c'est seulement le niveau indiqué qui est affiché,
*/
function debug($complete=null,$html=true)
{
	$str="";
	$liste=debug_backtrace();

	foreach($liste as $key=>$value)
	{

		$oper=GetValue($value,"type");
		$file=basename(GetValue($value,"file"));
		$line=GetValue($value,"line");
		$fct=GetValue($value,"function");
		$cls=GetValue($value,"class");
		$args=GetValue($value,"args");
		$myargs=array();
		if( is_array($args)) {
			foreach ($args as $k=>$v) $myargs[$k]=ToString($v);
			$myargs=implode(" , ",$myargs);
		} else $myargs="";
		//$myargs="";
		$space="&nbsp;&nbsp;&nbsp;";
		if (!$oper && (in_array($fct,array("WriteLog","ErrorHandler","trigger_error","fDebug"))))
			continue;
		if ($html)
			$str="<tr><td width='200' nowrap>- {$file}</td><td width='100' nowrap>ligne: {$line}</td>
			<td nowrap><b>{$cls}{$oper}{$fct} ( </b>{$myargs}<b> )</b>
			</td></tr>{$str}";
		else
			$str = "\tFile: {$file}\tLine:{$line}\tClass:{$cls}{$oper}{$fct}({$myargs})\n{$str}";

	}
	if ($html) {
	if ($complete) {
	$str="{$str}<tr><td colspan='3' style='color:#770000'>{$complete}</td></tr>";
	}

	$str="<tr><td colspan='3'  bgcolor='#000000'>&nbsp;</td></tr>{$str}";
	$str="<table width='80%' cellspacing='0' cellpadding='1' style='font-size: 11px;'>{$str}</table>";
	} else {
	$debut = $complete?$complete:"Error";
	$str="-->{$debut}:\n{$str}";
	}
	echo $str;
	}
