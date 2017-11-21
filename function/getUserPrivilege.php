<?php
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

function getUserPrivilege($namaFile)
{
	//kembalikan level hak user
	//Level default untuk semua modul adalah 0=NONE; 10=READ ONLY; .... ; 90=ALL
	//Level antara 10 dan 90 dapat digunakan apabila ada modul dengan kebutuhan level hak user lebih dari 3 level default di atas.
	
	global $dbLink;
//        global $dbLinkMy;

	//Periksa apakah user tergabung sebagai group ADMIN. 
	//Jika Ya, maka langsung kembalikan 90 -> ALL ACCESS
	//Jika Tidak, periksa level hak di database
	
	$pieces = explode("_", $namaFile);
	$namaModul=$pieces[0];
        $namaModul='view'.SUBSTR($namaModul,-(STRLEN($namaModul)-4));
	
	if(in_array("'ADMIN'",explode(',',$_SESSION['my']->groups)))
	{
		return 90;
	}
	else
	{	
		$q = "SELECT GP.level ";
		$q.= "FROM groupPrivilege GP, menu M ";
		$q.= "WHERE GP.kodeMenu=M.kodeMenu AND GP.kodeGroup IN (".$_SESSION['my']->groups.") AND ";
		$q.= " 		(M.link LIKE '".$namaModul."_list%' OR M.link LIKE '".$namaModul."_detail%')";

                                
		$result=mysql_query($q, $dbLinkMy);
		$level=0;
		while($queryData=mysql_fetch_row($result))
		{
			if($queryData[0]>$level)
			{
				$level=$queryData[0];
			}
		}
		
		if ($level==0)
		{
			session_unregister("my");
			die( 'Authorization Failed' ); 
		}
		else
		{
			return $level;
		}
	}
}
?>