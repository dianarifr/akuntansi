<?php
/* 
Addition : show Prev 10 dan Next 10 for more than 10 pages paging
*/


class MySQLPagedResultSet
{

  var $results;
  var $pageSize;
  var $page;
  var $row;
  
  function MySQLPagedResultSet($query,$pageSize,$cnx)
  {
    $resultpage = $_GET['resultpage'];
    
    $this->results = @mysql_query($query,$cnx);
    $this->pageSize = $pageSize;
    if ((int)$resultpage <= 0) $resultpage = 1;
    if ($resultpage > $this->getNumPages())
      $resultpage = $this->getNumPages();
    $this->setPageNum($resultpage);
  }
  
  function getNumPages()
  {
    if (!$this->results) return FALSE;
    
    return ceil(mysql_num_rows($this->results) /
                (float)$this->pageSize);
  }
  
  function setPageNum($pageNum)
  {
    if ($pageNum > $this->getNumPages() or
        $pageNum <= 0) return FALSE;
  
    $this->page = $pageNum;
    $this->row = 0;
    mysql_data_seek($this->results,($pageNum-1) * $this->pageSize);
  }
  
  function getPageNum()
  {
    return $this->page;
  }
  
  function isLastPage()
  {
    return ($this->page >= $this->getNumPages());
  }
  
  function isFirstPage()
  {
    return ($this->page <= 1);
  }
  
  function fetchArray()
  {
    if (!$this->results) return FALSE;
    if ($this->row >= $this->pageSize) return FALSE;
    $this->row++;
    return mysql_fetch_array($this->results);
  }
  
  function getPageNav($queryvars = '')
  {
	
	if (stripos($queryvars,'resultpage=')!==FALSE) {
		$startposition = stripos($queryvars,'resultpage=');
		$endposition = stripos(substr($queryvars, $startposition,-1),'&');
		$queryvars = substr_replace($queryvars,'',$startposition,$endposition-$startposition+1);
	}
	
	
    $nav = '';
	$nav.="Jumlah: ".number_format(mysql_num_rows($this->results), 0, ',', '.')." records. Halaman: ".$this->page."/".$this->getNumPages()." &nbsp; ";
      
   
   if (!$this->isFirstPage())
    {
		 $nav .= "<a href=\"?resultpage=1&".$queryvars."\">First</a> ";
  
	       if ($this->page > 10) {
		   $nav .= "<a href=\"?resultpage=".
              ($this->getPageNum()-10)."&".$queryvars."\">Prev 10</a> ";
   }
 
		 $nav .= "<a href=\"?resultpage=".
              ($this->getPageNum()-1)."&".$queryvars."\">Prev</a> ";
    }
		
    if ($this->getNumPages() > 1){
	
	 if  ($this->getNumPages()<=10) { // kalau halaman kurang dari 10
		  for ($i=1; $i<=$this->getNumPages(); $i++)
		  {
			if ($i==$this->page)
			  $nav .= "$i ";
			else
			  $nav .= "<a href=\"?resultpage={$i}&".
					  $queryvars."\">{$i}</a> ";
		  }
	  }else{
		  if ($this->page > 10) {
			  $start = $this->page -5;
			  
		  }else{
			  if ($this->page - 5 < 1) {
			   	$start =1;
			  }else{
				$start = $this->page - 5;
			  }
		  }
		 // echo "start =".$start;
		  if ($this->getNumPages() - $this->page <5) {
			  $end = $this->getNumPages();
		  }else{
			  if ($this->page +5 <10) {
				  $end=10;
			  }else{
				  $end = $this->page + 5;
			  }
		 }
		  //echo "end =".$end;
		  
		  for ($i=$start; $i<=$end; $i++)
		  {
			if ($i==$this->page)
			  $nav .= "$i ";
			else
			  $nav .= "<a href=\"?resultpage={$i}&".
					  $queryvars."\">{$i}</a> ";
		  }
	  }
	}
	  
    if (!$this->isLastPage())
    {
      $nav .= "<a href=\"?resultpage=".
              ($this->getPageNum()+1).'&'.$queryvars.'">Next</a> ';
			  
			  
			  
			  
	if ($this->page + 5 <  $this->getNumPages() ) {
	      $nav .= "<a href=\"?resultpage=".
              ($this->getPageNum()+10).'&'.$queryvars.'">Next 10</a> ';
   	}
	
	  $nav .= "<a href=\"?resultpage=".
              ($this->getNumPages()).'&'.$queryvars.'">Last</a> ';
			  
	
	
    }
	
	
    
    return $nav;
  }
}

?>
