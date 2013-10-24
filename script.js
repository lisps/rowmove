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
	elTr = getParentTr(el);
	elTbody = elTr.parentNode;
	r_elRows = elTbody.rows;
	iTr = elTr.rowIndex;
	count = countRows(elTr);
	
	if(iTr==0) return; // erste Zeile
	if(!checkRow(r_elRows[iTr-1])) return; //vorherige Zeile tauschbar
	
	if(working)return;
	working =true;
	
	elTbody.insertBefore(r_elRows[iTr],r_elRows[iTr-1]);
	
	tnr = numberTable(elTbody);
	rowmovesend(pageid,iTr,iTr-1,tnr);
}

/*
* Verschiebt eine Zeile um eins nach unten
* el: Element unterhalb oder gleich tr Element
* pageid: ID
*/
function rowdown(el,pageid) 
{
	
	elTr = getParentTr(el);
	elTbody = elTr.parentNode;
	r_elRows = elTbody.rows;
	iTr = elTr.rowIndex;
	count = countRows(elTr);
	
	if(iTr+1 == count) return; //letzte Zeile
	if(!checkRow(r_elRows[iTr+1])) return; //nächste Zeile tauschbar
	
	if(working) return;
	working =true;
	
	if(iTr+2 == count)
		elTbody.appendChild(r_elRows[iTr]);
	else 
		elTbody.insertBefore(r_elRows[iTr],r_elRows[iTr+2]);
	tnr = numberTable(elTbody);
	rowmovesend(pageid,iTr,iTr+1,tnr);
}

//send ajax data    
function rowmovesend(pageid,idx_row,idx_row2,tablenr)
{
	ajaxedit_send(
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
	ret = ajaxedit_parse(data);
	ajaxedit_checkResponse(ret);
}  

function numberTable(elTbody) {
	tables = document.getElementsByTagName("tbody");
	for(ii=0;ii<tables.length;ii++) {
		if(elTbody == tables[ii]) return ii;
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
	
	elTr = getParentTr(el);
	elTable = elTr.parentNode;
	count = elTable.rows.length;
	return count;
}

/* 
* checkt ob Zeile zum tauschen makiert,
* d.h. ob ein rowmove tag in der Zeile vorhanden ist
*/
function checkRow(elTr) 
{
	elCells = elTr.cells;
	
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
