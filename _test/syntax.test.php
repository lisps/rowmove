<?php
/**
 * @group plugin_rowmove
 * @group plugins
 */
class plugin_rowmove_syntax_test extends DokuWikiTest {

    public function setup() {
        $this->pluginsEnabled[] = 'rowmove';
        $this->pluginsEnabled[] = 'ajaxedit';
        parent::setup();
    }

    
    public function test_basic_syntax() {
        global $INFO;
        $INFO['id'] = 'test:plugin_rowmove:syntax';
        $INFO['perm'] = AUTH_EDIT;
        saveWikiText('test:plugin_rowmove:syntax',"|row1|<rowmove>|\n|row2|<rowmove>|\n",'test');
        
        $xhtml = p_wiki_xhtml('test:plugin_rowmove:syntax');
        $doc = phpQuery::newDocument($xhtml);
        
        $selector = pq("span.rowmove",$doc);
        $this->assertTrue($selector->length === 2);
        $this->assertTrue(pq("img",$selector)->length === 4);  
    }
    

}
