<?php
/**
 * @group plugin_rowmove
 * @group plugins
 */
class plugin_rowmove_ajax_test extends DokuWikiTest {

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
        
        $this->assertEquals(pq("td",$doc)->get(0)->textContent, 'row1');
        $this->assertEquals(pq("td",$doc)->get(2)->textContent, 'row2');
        
        $request = new TestRequest();
        $request->post([
            'call'   => 'plugin_rowmove', 
            'pageid' => 'test:plugin_rowmove:syntax',
            'id'     => 0,
            'index'  => 0,
            'idx_row' => 0,
            'idx_row2' => 1,
            
            'lastmod' => @filemtime(wikiFN('test:plugin_rowmove:syntax')),
            
        ], '/lib/exe/ajax.php');
               
        $xhtml = p_wiki_xhtml('test:plugin_rowmove:syntax');
        $doc = phpQuery::newDocument($xhtml);
        
        $selector = pq("span.rowmove",$doc);
        $this->assertTrue($selector->length === 2);
        $this->assertTrue(pq("img",$selector)->length === 4);  

        $this->assertEquals(pq("td",$doc)->get(0)->textContent, 'row2');
        $this->assertEquals(pq("td",$doc)->get(2)->textContent, 'row1');
    }
}
