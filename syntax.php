<?php
/**
 * DokuWiki Plugin rowmove (Syntax Component) 
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  lisps    
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/*
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_rowmove extends DokuWiki_Syntax_Plugin{

    var $idcount = 0;

    /*
     * What kind of syntax are we?
     */
    function getType() {return 'substition';}

    /*
     * Where to sort in?
     */
    function getSort() {return 155;}

    /*
     * Paragraph Type
     */
    function getPType() {return 'normal';}

    /*
     * Connect pattern to lexer
     */
    function connectTo($mode) {
		$this->Lexer->addSpecialPattern("<rowmove>",$mode,'plugin_rowmove');
	}

    /*
     * Handle the matches
     */
    function handle($match, $state, $pos, Doku_Handler $handler){
		return ($opts);
    }
        
    function iswriter() {
		global $conf;
		global $INFO;
		
		return($conf['useacl'] && $INFO['perm'] > AUTH_READ);
	}
    
    /*
     * Create output
     */
  function render($mode, Doku_Renderer $renderer, $opt){
		global $INFO;

		if($mode == 'metadata') return false;
		if($mode == 'xhtml') {
			$Hajax = plugin_load('helper', 'ajaxedit');
			if(!$Hajax){
				msg('Plugin ajaxedit is missing');
			} 
			
			//insert selector if writable
			if ($this->iswriter()==TRUE && $Hajax) {
			    $image = DOKU_URL."lib/plugins/rowmove/";
				$renderer->cdata("\n");
				$renderer->doc .= "<span class='rowmove'>";
				$renderer->doc .= "<img src=\"".$image."arrow_up.gif\" alt='up'  class=\"rowmove\" style=\"cursor:pointer;\" onclick=\"rowup(this,'".base64_encode($INFO["id"])."');\" />";
				$renderer->doc .= "<img src=\"".$image."arrow_down.gif\" alt='down' class=\"rowmove\" style=\"cursor:pointer;\" onclick=\"rowdown(this,'".base64_encode($INFO["id"])."');\" />";
				$renderer->doc .= "</span>";
			}
		}	
		return true;
	}

}

