<?php
/**
 * DokuWiki Plugin rowmove (Ajax Component)
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author lisps
 * @author peterfromearth
 */
if (!defined('DOKU_INC')) die();
class action_plugin_rowmove extends DokuWiki_Action_Plugin {

    /**
     * Register the eventhandlers
     */
    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('TOOLBAR_DEFINE', 'AFTER', $this, 'insert_button', array ());
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE',  $this, '_ajax_call');
    }
    
    /**
     * Inserts the toolbar button
     */
    function insert_button(Doku_Event $event, $param) {
        $event->data[] = array(    
            'type'   => 'format',
            'title' => 'Rowmove',
            'icon'   => '../../plugins/rowmove/images/toolicon.png',
            'sample' => 'CHECK HELP',
            'open' => '<rowmove>',
            'close'=>'',
            'insert'=>'',
        );
    }
    
    function _ajax_call(Doku_Event $event, $param) {
        if ($event->data !== 'plugin_rowmove') {
            return;
        }
        //no other ajax call handlers needed
        $event->stopPropagation();
        $event->preventDefault();
        
        /* @var $INPUT \Input */
        global $INPUT;
        
        #Variables
        $tablenr = $INPUT->int('index');
        $index   = $INPUT->int('idx_row');
        $index2  = $INPUT->int('idx_row2'); 
        
        /* @var $Hajax \helper_plugin_ajaxedit */
        $Hajax = $this->loadHelper('ajaxedit');
                
        $data=$Hajax->getWikiPage(); 
        
        $zeilen = explode("\n",$data);
        
        $itable = -1; //Tabllen zähler
        $irow = 0;	//Zeilenzähler
        $key1=array();
        $key2=array();
        $col_notclosed=0;
        $pagemod = 0;
        foreach($zeilen as $key=>$zeile) { //durchlaufen der Zeilen
            if( preg_match("/^[\|\^].*/",$zeile) || $col_notclosed) { //Am Anfang der Zeile ein "|" oder "^" heist Tablle
                if(preg_match("/^[\|\^].*/",$zeile) && !$pagemod && $col_notclosed) { //Doch am Anfang einer Zeile, aber nur wenn pagemod=aus und zeile nicht geschlossen
                    $col_notclosed = 0;
                    $irow++;
                }
                
                if($irow == 0 && !$col_notclosed) $itable++; //Tabellenanfang gefunden
                if(($itable == $tablenr && $irow == $index)) {
                    $key1[]=$key;
                } //1. Zeile gefunden
                if($itable == $tablenr && $irow == $index2){
                    $key2[]=$key;
                } //2. Zeile gefunden
                
                if(!in_array(substr(trim($zeile),-1),array('|','^'))){ //kein Zeilenendezeichen vorhanden
                    $col_notclosed = 1;
                    
                    if(substr_count($zeile,'pagemod')%2 == 1 && (strpos($zeile,'<pagemod') !== false || strpos($zeile,'</pagemod') !== false)) {
                        if($pagemod) { //ende von pagemod
                            $pagemod = 0;
                            $irow++;
                            $col_notclosed = 0;
                            
                        } else { //pagemod beginnt
                            $pagemod = 1;
                        }
                    }
                } else { //normales Zeilenende
                    $col_notclosed = 0;
                    $irow++;
                }
                
                
            } else { //Tabllenende
                $col_notclosed = 0;
                $irow=0;
            }
        }
        
        //Vertauschen der Zeilen
        $copy1 = array_slice($zeilen,reset($key1),count($key1));
        $copy2 = array_slice($zeilen,reset($key2),count($key2));
        array_splice($zeilen,reset($key1),count($key1),$copy2);
        
        array_splice($zeilen,reset($key2),count($key2),$copy1);
        
        //Zusammenfügen
        $data = implode("\n",$zeilen);
        
        $summary ="Row Change Table:".$tablenr." Row:".$index." to Row:".$index2." ";
        $Hajax->saveWikiPage($data,$summary,true);
    }
}
