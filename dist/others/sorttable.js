/*
How to use this script:
 1. Put <script src="sorttable.js"></script> in your html page <head> section.
 2. Add class="sortable" to the tables you want sorted (if your table html tag was previously <table class="old_style">, now it'd become <table class="old_style sortable">)
 That's it! Now you can already sort any "sortable" table on your page!
 Please also note that by default you could sort a table by simply pressing 
 "alt"+"ctrl" and clicking inside any table cells of the table.
 If prefered, you could set the variable sort_only_sortable to false to have
 all the tables on your page sortable by default!

 For advanced usage, read on!

Authors and feature lists:
 This script by me (Mingyi Liu) was initially based upon an excellent script by
 Stuart Langridge. But now this script is so much beyond what that original
 script offered.  I made very significant improvements and bug fixes and
 developed it into the most advanced and feature-rich table sorting script on
 the net (and even created a firefox extension out of it!).

 Mingyi's script's feature list:
   1. Ease of use - just include the script in your html and add class "sortable"
      to the tables you want sorted. If prefered, you could modify one variable
      sort_only_sortable to false to have all tables on page sortable by default!
   2. Fast performance (fast resorting of same column).
   3. Automatically detects (US-styled) date, number (negatives, scientific 
      notations), text, multiple currency types, IP addresses and sort them. 
      More date style can be specified (see below)
   4. X-browser support for most browsers.
   5. Support multiple tables on same html page.
   6. Works with thead, tbody, th, etc. html tags and will not break on colspan.
   7. Allow user to specify tables that should be sorted right after
      the html page is loaded.
   8. Sort US, European and user-specified dates (and time)! Simply specify the 
      type in table head cells (see advanced usage for details).
   9. Allow force sorting of unrecognized currencies too.
   10. Support various levels of style preserving (row-level, cell-level or 
      table-level (default)). Particularly useful for table rows that're
      alternatively highlighted.
   11. Support for user-supplied sorting function for custom data type.
   12. Allow customization of the style of the table heading clickable links.
   13. Improved reliability over offending data (like non-number in a number
       column).
   14. Works with tfoot html tag, Allows multi-row header and footer.
   15. Has a FireFox extension made out of this script!
   16. Allow user to press 'ctrl'+'alt' and click inside anywhere in the table
       to sort!
       
License: 
 You can do whatever you want with the script, but it'd be 
 tradition-honoring if you give authors credit or simply keep this 
 header comment section intact. BTW, also observe the tradition of 
 most freewares - no warranty provided and use it at your own risk! ;)

Advanced usage:
 1. You can change the variables in the sections marked as modify as needed.
 2. Currency detection is limited to a few common ones (¥£€$), so if you have,
    say, YMB, just add ts_type="money" to the column heading, e.g., 
    <td ts_type="money"> or <th ts_type="money">. That's it! It'll be sorted
    correctly. In fact, it's always a good idea to specify money columns as
    type "money", even if it's US $, euro, etc.
 3. You could also force data type to be "number", "ip", "date" the same way
    as "money". You could even specify ts_type to be "custom", but then you
    must supply valid javascript code that accesses the values a and b and
    evaluates to negative, positive or 0 (like any comparison function). e.g.:
    implement numeric sorting:
      <td ts_type="custom" ts_sortfn="aa=parseFloat(a);bb=parseFloat(b); if(isNaN(aa)) aa=0; if(isNaN(bb)) bb=0; aa-bb;">My number column header cell</td>
    implement your own IP sorting:
      <th ts_type="custom" ts_sortfn="aa=a.split('.');bb=b.split('.');(aa[0] - bb[0] || aa[1] - bb[1] || aa[2] - bb[2] || aa[3] - bb[3]);">My IP column header cell</th>
    Note that:
    a. You must know what you're doing!
    b. Due to the use of eval, the custom function is always slower than 
       defining function in this script directly (but using custom function 
       keeps code here cleaner during updates).
 4. (this feature is temporarily disabled after my recent upgrade)
    Add 'sortdir="desc"'' on any table header column to indicate that the 
    table should be sorted right after page is loaded. Use 'asc' for ascending
    sorting order. Example: <td sortdir="desc">the default sorting column</td>
 5. Use <table preserve_style="cell"> on any table to indicate that the table 
    redrawing after sorting should preserve cell style (override the global
    preserve_style variable that you can set below). The other valid preserve_style
    is "row", which is very useful if you use alternating colors for table rows.
    Note that row style is slower than default, cell style is even slower due to
    more update needed.
    IE has a "design" (read:bug) that prevents setting innerHTML for rows ("design"
    explained in http://msdn.microsoft.com/workshop/author/tables/buildtables.asp#TOM_Create, 
    bug documented for selects at http://support.microsoft.com/default.aspx?scid=kb;en-us;276228#appliesto).
    Therefore for IE, "row" is actually equivalent with "cell" because it is implemented
    the same way (blame IE for the speed!).         
 6. Use <td ts_type="euro_date"> to indicate date format is DD/MM/YY or DD/MM/YYYY
    Using <td ts_type="other_date" ts_date_format="D/M/Y"> you can also indicate 
    it's european dates. <td ts_type="other_date" ts_date_format="D-M-Y"> is 
    also european dates separated by '-' not '/'. Use the formatting you 
    could specify other date format, like Y-M-D. If Y is double digit, any number
    smaller than 32 is deemed 20xx, otherwise it's 19xx.  Note that D/M/Y (or any
    user-specified format) could just part of the time string (the date part),
    time conversion will be done correctly. Also note that if you choose custom
    date format, due to the time consumed during date conversion, the sorting
    would be much slower (2-3 times as slow).
    The date/time formats accepted are: 
     1. Anything accepted directly by Javascript's Date.parse() function 
       (format best explained at "http://java.sun.com/j2se/1.3/docs/api/java/util/Date.html#parse(java.lang.String)");
     2. Any combination of a date type specified by you and a time (hour, min, 
        seconds) format accepted by Date.parse(). Check the demo html page (http://www.mingyi.org/other/ts_demo.html) for 
        examples.
  7. Remember that there are a number of parameters that you could customize
     in the script!
*/
var ts_version = "1.21";
var ts_browser_agt = navigator.userAgent.toLowerCase();   
var ts_browser_is_ie = ((ts_browser_agt.indexOf("msie") != -1) && (ts_browser_agt.indexOf("opera") == -1));

var ml_tsort = {
  ///////////////////////////////////////////////////
  // configurable constants, modify as needed!
  sort_col_title : "Click here to Sort!",
  sort_col_class : "", // whichever class you want the heading to be
  sort_col_style : "text-decoration:none; font-weight:bold; color:black", // whichever style you want the link to look like
  sort_col_mouseover : "this.style.color='blue'", // what style the link should use onmouseover?
  sort_col_mouseout : "this.style.color='black'", // what style the link should use onmouseover?
  use_ctrl_alt_click : true, // allow ctrl-alt-click anywhere in table to activate sorting?
  sort_only_sortable : true, // make all tables sortable by default or just make the tables with "sortable" class sortable?
  
  //////////////////////////////////////////////////
  // speed related constants, modify as needed!
  table_content_might_change : false, // table content could be changed by other JS on-the-fly? if so, some speed improvements cannot be used.
  preserve_style : "", // (row, cell) preserves style for row or cell e.g., row is useful when the table highlights rows alternatively. cell is much slower while no preservation's the fastest by far!
  tell_me_time_consumption : false, // give stats about time consumed during sorting and table update/redrawing.
  
  //////////////////////////////////////////////////////////
  // anything below this line, modify at your own risk! ;)
  set_vars : function(event)
  {
    var e = (event)? event : window.event;
    var element = (event)? ((event.target)? event.target : event.srcElement) : window.event.srcElement;
    var clicked_td = ml_tsort.getParent(element,'TD') || ml_tsort.getParent(element,'TH');
    var table = ml_tsort.getParent(element,'TABLE');
    if(!table || table.rows.length < 1 || !clicked_td) return;
    var column = clicked_td.cellIndex;
    if (e.altKey && e.ctrlKey && ml_tsort.use_ctrl_alt_click) ml_tsort.resortTable(table.rows[0].cells[column]);
  },

  makeSortable: function(table) 
  {
      if (table.rows && table.rows.length > 0) {
          var firstRow = table.rows[0];
      }
      if (!firstRow) return;
      var sortCell;
      // We have a first row: assume it's the header (it works for <thead> too), 
      // and make its contents clickable links
      for (var i=0;i<firstRow.cells.length;i++) {
          var cell = firstRow.cells[i];
          var txt = this.getInnerText(cell);
          if(cell.getAttribute("sortdir")) sortCell = cell;
          cell.innerHTML = '<a style="'+this.sort_col_style+'" onMouseOver="'+this.sort_col_mouseover+'" onMouseOut="'+this.sort_col_mouseout+'" class="'+this.sort_col_class+'" href="#" title="'+this.sort_col_title+'" onclick="javascript:ml_tsort.resortTable(this.parentNode);return false">'+txt+'</a>';
      }
      if(sortCell) this.resortTable(sortCell);
  },
  
  sortables_init : function() 
  {
      // Find all tables with class sortable and make them sortable
      if (!document.getElementsByTagName) return;
      var tbls = document.getElementsByTagName("table");
      for (var ti=0;ti<tbls.length;ti++) {
          thisTbl = tbls[ti];
          if(!ml_tsort.sort_only_sortable || thisTbl.className.match(/sortable/i))
            ml_tsort.makeSortable(thisTbl);
      }
  },
  
  getParent : function(el, pTagName) 
  {
  	if (el == null) return null;
  	else if (el.nodeType == 1 && el.tagName.toLowerCase() == pTagName.toLowerCase())	// Gecko bug, supposed to be uppercase
  		return el;
  	else
  		return this.getParent(el.parentNode, pTagName);
  },
  
  getInnerText : function(el) 
  {
  	if (typeof el == "string") return el;
  	if (typeof el == "undefined") { return el };
  	if (el.innerText) return el.innerText;	//Not needed but it is faster
  	var str = "";
  	
  	var cs = el.childNodes;
  	var l = cs.length;
  	for (var i = 0; i < l; i++) {
  		switch (cs[i].nodeType) {
  			case 1: //ELEMENT_NODE
  				str += this.getInnerText(cs[i]);
  				break;
  			case 3:	//TEXT_NODE
  				str += cs[i].nodeValue;
  				break;
  		}
  	}
  	return str;
  },
  
  match_date_format : function(value, format)
  {
    this.set_date_array(format);
    if(format == 'M/D/Y' && !isNaN(Date.parse(this.getInnerText(value.cells[this.sort_column_index]))))
      return true;
    else if(!isNaN(this.convert_date(value))) return true;
    this.set_date_array(format.replace(/\//g, '-'));
    if(!isNaN(this.convert_date(value))) return true;
    this.set_date_array(format.replace(/\//g, '.'));
    if(!isNaN(this.convert_date(value))) return true;
    this.set_date_array(format.replace(/\//g, ' '));
    if(!isNaN(this.convert_date(value))) return true;
    return false;
  },
    
  resortTable : function(td) 
  {
    if(td == null) return;
    var column = td.cellIndex;
    var table = this.getParent(td,'TABLE');
    this.sort_column_index = column;
    if(table == null || table.rows.length <= 2) return;

    // now let's do a lot just to save a little time, if possible at all. ;)
    var lastSortCell = table.getAttribute("ts_sortcell") || 0;
    lastSortCell--; // the processing is used for IE, which treats no attribute as 0, while FF treats 0 as still true.
    var lastSortDir = (table == this.last_sorted_table && column == lastSortCell)? table.getAttribute("ts_sortdir") : 'desc';

    var newRows = new Array();
    var headcount = 1;
    for (var i=0,j=1;j<table.rows.length;j++)
    {
      var t = table.rows[j].parentNode.tagName.toLowerCase();
      if(t == 'tbody' && table.rows[j].cells.length >= column + 1) newRows[i++] = table.rows[j];
      else if(t == 'thead') headcount++;
    }
    if(newRows.length == 0) return;
    var time2 = new Date();
  
    // check if we really need to sort
    if(!this.table_content_might_change && table == this.last_sorted_table && column == lastSortCell)
      newRows.reverse();
    else
    {
      // Work out a type for the column
      var sortfn, type = td.getAttribute("ts_type");
      this.replace_pattern = '';
      var itm, i;
      for(i = 0; i < newRows.length; i++)
      {
        itm = this.getInnerText(newRows[i].cells[column]);
        if(itm.match(/\S/)) break;
      }
      if(i == newRows.length) return;
      if(!type)
      {
        sortfn = this.sort_caseinsensitive;

        if (this.match_date_format(newRows[i], 'M/D/Y')) sortfn = this.sort_date;
        else if (itm.match(/^[¥£€$]/)) sortfn = this.sort_currency;
        else if (itm.match(/^\d{1,3}(\.\d{1,3}){3}$/)) sortfn = this.sort_ip;
        else if (itm.match(/^[+-]?\s*[0-9]+(?:\.[0-9]+)?(?:\s*[eE]\s*[+-]?\s*\d+)?$/))
          sortfn = this.sort_numeric;
      }
      else if(type == 'date' && this.match_date_format(newRows[i], 'M/D/Y')) sortfn = this.sort_date;
      else if(type == 'euro_date' && this.match_date_format(newRows[i], 'D/M/Y')) sortfn = this.sort_date;
      else if(type == 'other_date' && this.match_date_format(newRows[i], td.getAttribute("ts_date_format"))) sortfn = this.sort_date; 
      else if(type == 'number') sortfn = this.sort_numeric;
      else if(type == 'ip') sortfn = this.sort_ip;
      else if(type == 'money') sortfn = this.sort_currency;
  //       else if(type == 'custom') sortfn = function(aa,bb) { a = this.getInnerText(aa.cells[this.sort_column_index]); b = this.getInnerText(bb.cells[this.sort_column_index]); eval(td.getAttribute("ts_sortfn")) }; // the coding here is shorter but interestingly it's also slower
      else if(type == 'custom') { this.custom_code = td.getAttribute("ts_sortfn"); sortfn = this.custom_sortfn }
      else { alert("unsupported sorting type or data not matching indicated type!"); return; }
  
      table.setAttribute("ts_sortcell", column+1);
      newRows.sort(sortfn);
      if (lastSortDir == 'asc') newRows.reverse();
    }
    if (lastSortDir == 'desc') table.setAttribute('ts_sortdir','asc');
    else table.setAttribute('ts_sortdir','desc');
  
    this.last_sorted_table = table;
       
    var time3 = new Date();
    
    var ps = table.getAttribute("preserve_style") || this.preserve_style;
    if(ps == 'row' && !ts_browser_is_ie) 
    {
      var tmp = new Array(newRows.length);
      for (var i = 0; i < newRows.length; i++) tmp[i] = newRows[i].innerHTML;
      for (var i = 0; i < newRows.length; i++) table.rows[i+headcount].innerHTML = tmp[i];
    }
    else if(ps == 'cell' || (ps == 'row' && ts_browser_is_ie)) 
    {
      var tmp = new Array(newRows.length);
      for (var i = 0; i < newRows.length; i++)
        for (var j = 0; j < newRows[i].cells.length; j++)
        {
          if(!tmp[i]) tmp[i] = new Array(newRows[i].cells.length);
          tmp[i][j] = newRows[i].cells[j].innerHTML;
        }
      for (var i = 0; i < newRows.length; i++)
        for (var j = 0; j < newRows[i].cells.length; j++)
          table.rows[i+headcount].cells[j].innerHTML = tmp[i][j];
    }
    else
    {
      for (var i=0;i<newRows.length;i++) // We appendChild rows that already exist to the tbody, so it moves them rather than creating new ones
        table.tBodies[0].appendChild(newRows[i]);
    } 
    var time4 = new Date();
    if(this.tell_me_time_consumption)
    {
      alert('it took ' + this.diff_time(time3, time2) + ' seconds to do sorting!');
      alert('it took ' + this.diff_time(time4, time3) + ' seconds to do redrawing!');
    }
    return false;
  },
  
  diff_time : function(time2, time1) 
  {
    return (time2.getTime() - time1.getTime())/1000;
  },
  
  // Mingyi Note: it seems ridiculous to do so much processing for
  // customizable date conversion, should try to find a zbetter way
  // of doing it.
  set_date_array : function(f) 
  {
    var tmp = [['D', f.indexOf('D')], ['M', f.indexOf('M')], ['Y', f.indexOf('Y')]];
    tmp.sort(function(a,b){ return a[1] - b[1]});
    this.date_order_array = new Array(3);
    for(var i = 0; i < 3; i++) this.date_order_array[tmp[i][0]] = '$' + (i + 2);
    this.replace_pattern = f.replace(/[DMY]([^DMY]+)[DMY]([^DMY]+)[DMY]/, '^(.*?)(\\d+)\\$1(\\d+)\\$2(\\d+)(.*)$');
  },
  
  process_year : function(y) 
  {
    var tmp = parseInt(y);
    if(tmp < 32) return '20' + y; 
  	else if(tmp < 100) return '19' + y;
  	else return y;
  },
  
  // convert to MM/DD/YYYY (or M/D/YYYY) format
  convert_date : function(a) 
  {
    var aa = this.getInnerText(a.cells[this.sort_column_index]);
    var re = 'RegExp.$1+RegExp.'+this.date_order_array['M']+'+\'/\'+RegExp.'+this.date_order_array['D']+'+\'/\'+this.process_year(RegExp.'+this.date_order_array['Y']+')+RegExp.$5';
    var code = 'if(aa.match(/'+this.replace_pattern+'/)) (' + re + ')';
    return Date.parse(eval(code));
  },
  
  sort_date : function(a,b) 
  {
    var aa, bb;
    // basically I have to do the conversion due to the potential usage of double digit years
    aa = ml_tsort.convert_date(a);
    if(isNaN(aa)) aa = Date.parse(ml_tsort.getInnerText(a.cells[ml_tsort.sort_column_index]));
    bb = ml_tsort.convert_date(b);
    if(isNaN(bb)) bb = Date.parse(ml_tsort.getInnerText(b.cells[ml_tsort.sort_column_index]));
    if(isNaN(aa)) aa = 0;
    if(isNaN(bb)) bb = 0;
    return aa - bb;
  },
  
  // assume no scientific number in currency (if assumption incorrect, just use
  // same code for this.sort_numeric will do)
  sort_currency : function(a,b) 
  { 
      return ml_tsort.sort_num(ml_tsort.getInnerText(a.cells[ml_tsort.sort_column_index]).replace(/[^-0-9.+]/g,''),
                         ml_tsort.getInnerText(b.cells[ml_tsort.sort_column_index]).replace(/[^-0-9.+]/g,''));
  },
  
  // let's allow scientific notation but also be strict on number format
  sort_num : function(a, b) 
  {
      var aa, bb;
      if(!isNaN(a)) aa = a;
      else if(a && a.match(/^[^0-9.+-]*([+-]?\s*[0-9]+(?:\.[0-9]+)?(?:\s*[eE]\s*[+-]?\s*\d+)?)/))
        aa = parseFloat(RegExp.$1.replace(/\s+/g, ''));
      else aa = 0;
      if(!isNaN(b)) bb = b;
      else if(b && b.match(/^[^0-9.+-]*([+-]?\s*[0-9]+(?:\.[0-9]+)?(?:\s*[eE]\s*[+-]?\s*\d+)?)/))
        bb = parseFloat(RegExp.$1.replace(/\s+/g, ''));
      else bb = 0;
      return aa - bb;
  },
  
  sort_numeric : function(a,b) 
  {
      return ml_tsort.sort_num(ml_tsort.getInnerText(a.cells[ml_tsort.sort_column_index]),
                         ml_tsort.getInnerText(b.cells[ml_tsort.sort_column_index])); 
  },
  
  sort_ip : function(a,b) 
  {
      var aa = ml_tsort.getInnerText(a.cells[ml_tsort.sort_column_index]).split('.');
      var bb = ml_tsort.getInnerText(b.cells[ml_tsort.sort_column_index]).split('.');
      return ml_tsort.sort_num(aa[0], bb[0]) || ml_tsort.sort_num(aa[1], bb[1]) || 
             ml_tsort.sort_num(aa[2], bb[2]) || ml_tsort.sort_num(aa[3], bb[3]);
  },
   
  sort_caseinsensitive : function(a,b) 
  {
      var aa = ml_tsort.getInnerText(a.cells[ml_tsort.sort_column_index]).toLowerCase();
      var bb = ml_tsort.getInnerText(b.cells[ml_tsort.sort_column_index]).toLowerCase();
      if (aa==bb) return 0;
      if (aa<bb) return -1;
      return 1;
  },
  
  custom_sortfn : function(aa,bb) 
  {
    var a = ml_tsort.getInnerText(aa.cells[ml_tsort.sort_column_index]);
    var b = ml_tsort.getInnerText(bb.cells[ml_tsort.sort_column_index]);
    return eval(ml_tsort.custom_code);
  }
};

function ts_addEvent(elm, evType, fn, useCapture)
// addEvent and removeEvent
// cross-browser event handling for IE5+,  NS6 and Mozilla
// By Scott Andrew
{
  if (elm.addEventListener){
    elm.addEventListener(evType, fn, useCapture);
    return true;
  } else if (elm.attachEvent){
    var r = elm.attachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Handler could not be removed");
  }
}
ts_addEvent(document, "click", ml_tsort.set_vars);
ts_addEvent(window, "load", ml_tsort.sortables_init);
