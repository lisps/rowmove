<?php
/**
 * DokuWiki Plugin rowmove (Ajax Component) 
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  lisps
 */
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
require_once(DOKU_INC.'inc/init.php');
require_once(DOKU_INC.'inc/common.php'); //changelog.php=>addLogEntry(), io.php=>io_writeWikiPage(), pageinfo
require_once(DOKU_INC.'inc/auth.php');

#Variables  
$tablenr = intval($_POST["index"]);
$index   = intval($_POST["idx_row"]); 
$index2  = intval($_POST["idx_row2"]); 

$Hajax = plugin_load('helper', 'ajaxedit');
$data=$Hajax->getWikiPage();

$zeilen = explode("\n",$data);

$itable = -1; //Tabllen zähler
$irow = 0;	//Zeilenzähler
$key1=false;
$key2=false;
foreach($zeilen as $key=>$zeile) { //durchlaufen der Zeilen
	if( preg_match("/^[\|\^].*/",$zeile)) { //Am Anfang der Zeile ein "|" oder "^" heist Tablle
		if($irow == 0) $itable++; //Tabellenanfang gefunden
		if($itable == $tablenr && $irow == $index) {$key1=$key;if($key2!==false)break;} //1. Zeile gefunden
		if($itable == $tablenr && $irow == $index2){$key2=$key;if($key1!==false)break;} //2. Zeile gefunden
		$irow++;
	} else 
		$irow=0; //Tabllenende
}

//Vertauschen der Zeilen
$copy =$zeilen[$key1];
$zeilen[$key1] = $zeilen[$key2];
$zeilen[$key2] = $copy;

//Zusammenfügen
$data = implode("\n",$zeilen);


$summary ="Row Change Table:".$tablenr." Row:".$index." to Row:".$index2." ";
$Hajax->saveWikiPage($data,$summary,true);

?>
