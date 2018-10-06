/**
 * DokuWiki Plugin rowmove (JavaScript Component) 
 *
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  lisps
 */
/* when ajax is working set working to true*/
var working = false;


function rowup(el,pageid) 
{
	var elTr = getParentTr(el);
	var elTbody = elTr.parentNode;
	var r_elRows = elTbody.rows;
	var iTr = elTr.rowIndex;
	var count = countRows(elTr);
	var offset_thead = r_elRows[0].rowIndex;
	
	iTr=iTr-offset_thead;
	
	if(iTr==0) return; // erste Zeile
	if(!checkRow(r_elRows[iTr-1])) return; //vorherige Zeile tauschbar
	
	if(working)return;
	working =true;
	
	elTbody.insertBefore(r_elRows[iTr],r_elRows[iTr-1]);
	
	var tnr = numberTable(elTbody);
	rowmovesend(pageid,iTr+offset_thead,iTr-1+offset_thead,tnr);
}

/*
* Verschiebt eine Zeile um eins nach unten
* el: Element unterhalb oder gleich tr Element
* pageid: ID
*/
function rowdown(el,pageid) 
{
	
	var elTr = getParentTr(el);
	var elTbody = elTr.parentNode;
	var r_elRows = elTbody.rows;
	var iTr = elTr.rowIndex;
	var count = countRows(elTr);
	var offset_thead = r_elRows[0].rowIndex;
	
	iTr=iTr-offset_thead;
	
	if(iTr+1 == count) return; //letzte Zeile
	if(!checkRow(r_elRows[iTr+1])) return; //nächste Zeile tauschbar
	
	if(working) return;
	var working =true;
	
	if(iTr+2 == count)
		elTbody.appendChild(r_elRows[iTr]);
	else 
		elTbody.insertBefore(r_elRows[iTr],r_elRows[iTr+2]);
	var tnr = numberTable(elTbody);
	rowmovesend(pageid,iTr+offset_thead,iTr+1+offset_thead,tnr);
}

//send ajax data    
function rowmovesend(pageid,idx_row,idx_row2,tablenr)
{
	ajaxedit_send2(
		'rowmove',
		tablenr,
		rowmovedone,
		{
			idx_row:idx_row,
			idx_row2:idx_row2,
		}
	);
}
    
function rowmovedone(data)
{
	working = false;
	var ret = ajaxedit_parse(data);
	ajaxedit_checkResponse(ret);
}  

function numberTable(elTbody) {
	var tables = jQuery('#dokuwiki__content table');
	for(ii=0;ii<tables.length;ii++) {
		if(elTbody.parentNode == tables[ii]) return ii;
	}
	return -1;
}

/* 
* zaehlt die zeilen
* benätigt das TBody Element
*/
function countRows(el)
{
	var count = 0;
	
	var elTr = getParentTr(el);
	var elTable = elTr.parentNode;
	var count = elTable.rows.length;
	return count;
}

/* 
* checkt ob Zeile zum tauschen makiert,
* d.h. ob ein rowmove tag in der Zeile vorhanden ist
*/
function checkRow(elTr) 
{
	var elCells = elTr.cells;
	
	for(ii = 0; ii<elCells.length; ii++) {
		for(kk=0; kk<elCells[ii].childNodes.length; kk++)
			if(elCells[ii].childNodes[kk].className=="rowmove") return true;
	}
	return false;
	
}

/*
* gibt das Tr Element eines darunterliegenden Elementes zurück
*/
function getParentTr(el) 
{
	while ( el.nodeName.toLowerCase() != "tr")
		el = el.parentNode;
	return el;
}
