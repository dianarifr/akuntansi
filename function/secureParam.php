<?php	
	function secureParam($strParam, $dbLinkMy)
	{
		require_once('./function/strip_html_tags.php');
		//Secure parameter from SQL injection
		$cleanString = $strParam;
		if(get_magic_quotes_gpc()) 
		{
			$cleanString = stripslashes($strParam);
		}
		$cleanString = mysql_real_escape_string($cleanString, $dbLinkMy);
		$cleanString = strip_html_tags($cleanString);
		return $cleanString;
	}
	
	function secureParamAjax($strParam, $dbLinkMy)
	{
		require_once('strip_html_tags.php');
		//Secure parameter from SQL injection
                $cleanString = $strParam;
		if(get_magic_quotes_gpc()) 
		{
			$cleanString = stripslashes($strParam);
		}
		$cleanString = mysql_real_escape_string($cleanString, $dbLinkMy);
		$cleanString = strip_html_tags($cleanString);
		return $cleanString;
	}
?>
