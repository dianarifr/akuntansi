<?php
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 

class c_setting
{
	var $strResults="";
	
	function validateMenu(&$params) 
	{
		$temp=TRUE;
		//Jika mode Add, root menu harus diisi
		if($params["cboRootMenu"]=="" && $params["txtMode"]=="Add")
		{
			$this->strResults.="Root Menu harus diisi!<br/>";
			$temp=FALSE;
		}
		if($params["txtSubKode"]=="" && $params["txtMode"]=="Add")
		{
			$this->strResults.="Sub Menu harus diisi!<br/>";
			$temp=FALSE;
		}
		//Jika mode Add, judul menu harus diisi dan tidak dapat berupa angka
		if((strlen(trim($params["txtJudul"]))=="" || is_numeric($params["txtJudul"])))
		{
			$this->strResults.="Judul Menu tidak valid!<br/>";
			$temp=FALSE;
		}
		return $temp;
	}
	
	function validateDeleteMenu($kode) 
	{
		global $dbLink;
		
		$temp=FALSE;
		if(empty($kode))
		{
			$this->strResults.="Kode tidak ditemukan!<br/>";
			$temp=FALSE;
		}
		
		$rsTemp=mysql_query("SELECT kodeMenu FROM groupPrivilege WHERE md5(kodeMenu)='".$kode."'", $dbLink);
		$rows = mysql_num_rows($rsTemp);

		if($rows==0)
		 	$temp = TRUE;
		else
		{
			$temp = FALSE;
			$this->strResults="Menu masih memiliki aturan Group Privilege. Hapus Group Privilege yang terkait dengan Menu ini terlebih dahulu.<br>";
		}
		
		return $temp;
	}
	
	function checkDeleteMenu($kode)
	{
		global $dbLink;
		
		$q = "SELECT kodeMenu FROM menu WHERE md5(kodeMenu)='".$kode."'";
		$result=mysql_query($q, $dbLink);
		while($query_data=mysql_fetch_row($result))
		{ $menu =  $query_data[0]; }
		
		$rsTemp=mysql_query("SELECT COUNT(kodeMenu) FROM menu Where kodeMenu LIKE '".$menu.".%'");
		if($query_data=mysql_fetch_row($rsTemp))
		{
			if ($query_data[0]==0)
				$temp = TRUE;
			else
				$temp = FALSE;
		}
		return $temp;
	}
	
	function addMenu(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateMenu($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Tambah Data Menu - ".$this->strResults;
			return $this->strResults;
		}

		$root = $params["cboRootMenu"];
		$subKode = $params["txtSubKode"];
		if($root=="M")
			$kode = $subKode;
		else
			$kode = $root.".".$subKode;
		$judul = $params["txtJudul"];
		if($params["txtLinkedit"]=="")
			$link = "";
		else
			$link = $params["txtLinkedit"];
		$status=$params["rdoStatus"];
	
		//Secure parameter from SQL injection
		if(get_magic_quotes_gpc()) 
		{
			$kode = stripslashes($kode);
			$judul = stripslashes($judul);
			$link = stripslashes($link);
			$status = stripslashes($status);
		}
		$kode=mysql_real_escape_string($kode, $dbLink);
		$judul=mysql_real_escape_string($judul, $dbLink);
		$link=mysql_real_escape_string($link, $dbLink);
		$status=mysql_real_escape_string($status, $dbLink);		
		
		$kode = strip_html_tags($kode);
		$judul = strip_html_tags($judul);
		$link = strip_html_tags($link);
		$status = strip_html_tags($status);	
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			
			$rsTemp=mysql_query("SELECT kodeMenu FROM menu WHERE kodeMenu='".$kode."'", $dbLink);
			if(mysql_num_rows($rsTemp)>0)
			{
				throw new Exception('Kode Menu sudah digunakan. Silakan gunakan kode menu yang belum terpakai.');
			}
			else
			{ 
				$q = "INSERT INTO menu (kodeMenu, judul, link, aktif) ";
				$q.= "VALUES ('".$kode."','".$judul."','".$link."','".$status."');";
			
				if (!mysql_query( $q, $dbLink))
				{	throw new Exception('Gagal menambah database'); }
			}
			$this->strResults="Sukses Tambah Data Menu ";
			@mysql_query("COMMIT", $dbLink);
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Tambah Data Menu - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
		}
		return $this->strResults;
	}
	
	function editMenu(&$params)  
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateMenu($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Ubah Data Menu - ".$this->strResults;
			return $this->strResults;
		}
				
		$root = $params["cboRootMenu"];
		$subKode = $params["txtSubKode"];
		$kodeBaru = $root.".".$subKode;
		
		$kode = $params["kodeMenu"];
		$judul = $params["txtJudul"];
		$link = $params["txtLinkedit"];
		$status=$params["rdoStatus"];
		
		//echo $kodeBaru;
		//Secure parameter from SQL injection
		if(get_magic_quotes_gpc()) 
		{
			$kode = stripslashes($kode);
			$judul = stripslashes($judul);
			$link = stripslashes($link);
			$status = stripslashes($status);
			$kodeBaru = stripslashes($kodeBaru);
		}
		$kode=mysql_real_escape_string($kode, $dbLink);
		$judul=mysql_real_escape_string($judul, $dbLink);

		if($link=="0")
			$link = "";
		else
			$link=mysql_real_escape_string($link, $dbLink);
			
		$status=mysql_real_escape_string($status, $dbLink);
		$kodeBaru=mysql_real_escape_string($kodeBaru, $dbLink);
		
		$kode = strip_html_tags($kode);
		$judul = strip_html_tags($judul);
		$link = strip_html_tags($link);
		$status = strip_html_tags($status);	
		$kodeBaru=strip_html_tags($kodeBaru);
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
		
			$child = $this->getChildMenu($kode);
	
			if(count($child)>1) //Menu yang di-non aktifkan memiliki sub menu, sehingga semua sub menu yang berkaitan juga harus dinon-aktifkan
			{
				for($i=0; $i<count($child); $i++)
				{
					$q = "UPDATE menu SET aktif='".$status."' ";
					$q.= "WHERE kodeMenu='".$child[$i]."';";
					if(!mysql_query( $q, $dbLink))
					{
						throw new Exception('Tidak dapat mengubah status child menu.');
					}
				}
			}
			
			$q = "UPDATE menu SET judul='".$judul."', link='".$link."', aktif='".$status."' ";
			$q.= "WHERE kodeMenu='".$kode."';";
			if(!mysql_query( $q, $dbLink))
			{
				throw new Exception('Tidak dapat mengubah data menu di database.');
			}
			$this->strResults="Sukses Ubah Data Menu ";
			@mysql_query("COMMIT", $dbLink);
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Ubah Data Menu - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
		}
		
		return $this->strResults;
	}
	
	function deleteMenu($kode)
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateDeleteMenu($kode))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Menu - ".$this->strResults;
			return $this->strResults;
		}
		
		if(!$this->checkDeleteMenu($kode))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Menu - ".$this->strResults;
			$this->strResults.="Menu masih memiliki sub menu. Hapus sub menu lebih dulu!<br/>";
			return $this->strResults;
		}
		
		//Secure parameter from SQL injection
		if(get_magic_quotes_gpc()) 
		{
			$kode = stripslashes($kode);
		}
		$kode=mysql_real_escape_string($kode, $dbLink);
		
		$q = "DELETE FROM menu ";
		$q.= "WHERE md5(kodeMenu)='".$kode."';";
		
		if (mysql_query( $q, $dbLink))
		{	
			$this->strResults="Sukses Hapus Data Menu " .$q;
		}
		else
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Menu - ".mysql_error();
		}
		return $this->strResults;
	}
	
	function getChildMenu($kode)
	{
		global $dbLink;
		global $SITUS;
		$menu = array();
		$i = 0;
		$q = "SELECT kodeMenu FROM menu WHERE kodeMenu LIKE '".$kode."%'";
		$result=mysql_query($q, $dbLink);
		while($query_data=mysql_fetch_row($result))
		{ $menu[$i] =  $query_data[0];  $i++;}
		
		return $menu;
	}
        
        function validateUserGroup(&$params) 
	{
		$temp=TRUE;
		//kode grup harus diisi
		if($params["cboKodeGrup"]=='0')
		{
			$this->strResults.="Kode User Group harus diisi!<br/>";
			$temp=FALSE;
		}
		
		//kode pengguna harus diisi
		if($params["cboKodeUser"]=='0')
		{
			$this->strResults.="Kode User harus diisi!<br/>";
			$temp=FALSE;
		}
		return $temp;
	}
	
	function validateDeleteUserGroup($kode) 
	{
		$temp=TRUE;
		if(empty($kode))
		{
			$this->strResults.="Kode tidak ditemukan!<br/>";
			$temp=FALSE;
		}
		
		return $temp;
	}
	
	function addUserGroup(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateUserGroup($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Tambah Data User Group - ".$this->strResults;
			return $this->strResults;
		}

		$kodeGrup = $params["cboKodeGrup"];
		$kodeUser = $params["cboKodeUser"];
		
		//Secure parameter from SQL injection
		if(get_magic_quotes_gpc()) 
		{
			$kodeGrup = stripslashes($kodeGrup);
			$kodeUser = stripslashes($kodeUser);
		}
		$kodeGrup=mysql_real_escape_string($kodeGrup, $dbLink);
		$kodeUser=mysql_real_escape_string($kodeUser, $dbLink);
		
		$kodeGrup = strip_html_tags($kodeGrup);
		$kodeUser = strip_html_tags($kodeUser);
			
		$q = "INSERT INTO userGroup ( kodeGroup, kodeUser ) ";
		$q.= "VALUES ('".$kodeGrup."','".$kodeUser."');";
		
		if (mysql_query( $q, $dbLink))
		{	
			$this->strResults="Sukses Tambah Data User Group ";
		}
		else
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Tambah Data User Group - ".mysql_error().$q;
		}
		return $this->strResults;
	}
	
	function editUserGroup(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateUserGroup($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Ubah Data User Group - ".$this->strResults;
			return $this->strResults;
		}
		$id = $params["ID"];
		$kodeGrup = $params["cboKodeGrup"];
		$kodeUser = $params["cboKodeUser"];
		
		
		//Secure parameter from SQL injection
		if(get_magic_quotes_gpc()) 
		{
			$id = stripslashes($id);
			$kodeGrup = stripslashes($kodeGrup);
			$kodeUser = stripslashes($kodeUser);
		}
		$id=mysql_real_escape_string($id, $dbLink);
		$kodeGrup=mysql_real_escape_string($kodeGrup, $dbLink);
		$kodeUser=mysql_real_escape_string($kodeUser, $dbLink);
		
		$id = strip_html_tags($id);
		$kodeGrup = strip_html_tags($kodeGrup);
		$kodeUser = strip_html_tags($kodeUser);
		
		$q = "UPDATE userGroup SET kodeGroup='".$kodeGrup."', kodeUser='".$kodeUser."'";
		$q.= "WHERE iduserGroup='".$id."';";

		if (mysql_query( $q, $dbLink))
		{	
			$this->strResults="Sukses Ubah Data User Group ";
		}
		else
		{
			$this->strResults="Gagal Ubah Data User Group - ".mysql_error();
		}
		return $this->strResults;
	}
	
	function deleteUserGroup($kode)
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateDeleteUserGroup($kode))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data User Group - ".$this->strResults;
			return $this->strResults;
		}
		
		//Secure parameter from SQL injection
		if(get_magic_quotes_gpc()) 
		{
			$kode = stripslashes($kode);
		}
		$kode=mysql_real_escape_string($kode, $dbLink);
		
		$q = "DELETE FROM userGroup ";
		$q.= "WHERE md5(iduserGroup)='".$kode."';";
		
		if (mysql_query( $q, $dbLink))
		{	
			$this->strResults="Sukses Hapus Data User Group ".$q;
		}
		else
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data User Group - ".mysql_error();
		}
		return $this->strResults;
	}
        
        function validateGroup(&$params) 
	{
		$temp=TRUE;
		//kode grup harus diisi
		if($params["txtKodeGroup"]=="" && $params['txtMode'] == "Add")
		{
			$this->strResults.="Kode Group harus diisi!<br/>";
			$temp=FALSE;
		}
		
		//kode pengguna harus diisi
		if($params["txtNamaGroup"]=="")
		{
			$this->strResults.="Nama harus diisi!<br/>";
			$temp=FALSE;
		}
		return $temp;
	}
	
	function validateDeleteGroup($kode) 
	{
		global $dbLink;
		
		$temp=FALSE;
		if(empty($kode))
		{
			$this->strResults.="Kode tidak ditemukan!<br/>";
			$temp=FALSE;
		}
		
		$rsTemp=mysql_query("SELECT kodeGroup FROM userGroup WHERE md5(kodeGroup)='".$kode."'", $dbLink);
		$rows = mysql_num_rows($rsTemp);

		if($rows==0)
		{
			$rsTemp=mysql_query("SELECT kodeGroup FROM groupPrivilege WHERE md5(kodeGroup)='".$kode."'", $dbLink);
			$count = mysql_num_rows($rsTemp);
			if($count==0)
				$temp = TRUE;
			else
			{
				$temp = FALSE;
				$this->strResults="Group masih memiliki Group Privilege. Hapus Group Privilege yang terkait dengan Group ini terlebih dahulu.<br>";
			}
		}
		else
		{
			$temp = FALSE;
			$this->strResults="Group masih memiliki User yang terdaftar dalam Group ini. Hapus keanggotaan User dalam Group ini terlebih dahulu.<br>";
		}
		
		return $temp;
	}
	
	function addGroup(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateGroup($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Tambah Data Group - ".$this->strResults;
			return $this->strResults;
		}

		$kode = $params["txtKodeGroup"];
		$nama = $params["txtNamaGroup"];
			
		//Secure parameter from SQL injection
		if(get_magic_quotes_gpc()) 
		{
			$kode = stripslashes($kode);
			$nama = stripslashes($nama);
		}
		$kode=mysql_real_escape_string($kode, $dbLink);
		$nama=mysql_real_escape_string($nama, $dbLink);
		
		$kode=strip_html_tags($kode);
		$nama=strip_html_tags($nama);
		
		$rsTemp=mysql_query("SELECT kodeGroup FROM groups WHERE kodeGroup='".strtoupper($kode)."'", $dbLink);
		$rows = mysql_num_rows($rsTemp);
		
		if($rows == 0)
		{ 
			$q = "INSERT INTO groups ( kodeGroup, nama ) ";
			$q.= "VALUES ('".strtoupper($kode)."','".strtoupper($nama)."');";
			
			if (mysql_query( $q, $dbLink))	
			{
				$this->changePrivilegeGroup($kode, $params["access"], $params["noAccess"], $params["cboLevelAccess"], $params["cboLevelNoAccess"]);
				$this->strResults="Sukses Tambah Data Group ";
			}
			else
				//Pesan error harus diawali kata "Gagal"
				$this->strResults="Gagal Tambah Data Group - ".mysql_error();
		}
		else
		{
			$this->strResults="Gagal Tambah Data Group - ";
			$this->strResults.="Kode Group sudah digunakan. Silakan gunakan kode group yang belum terpakai. ";
		}
		return $this->strResults;
	}
	
	function editGroup(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateGroup($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Ubah Data Group - ".$this->strResults;
			return $this->strResults;
		}
		
		$kode = $params["txtKodeGroup"];
		$nama = $params["txtNamaGroup"];
		 
		//Secure parameter from SQL injection
		if(get_magic_quotes_gpc()) 
		{
			$kode = stripslashes($kode);
			$nama = stripslashes($nama);
		}
		$kode=mysql_real_escape_string($kode, $dbLink);
		$nama=mysql_real_escape_string($nama, $dbLink);
		
		$kode=strip_html_tags($kode);
		$nama=strip_html_tags($nama);
		
		
		$q = "UPDATE groups SET nama='".strtoupper($nama)."' ";
		$q.= "WHERE kodeGroup='".$kode."';";

		if (mysql_query( $q, $dbLink))
		{	
			$this->changePrivilegeGroup($kode, $params["access"], $params["noAccess"], $params["cboLevelAccess"], $params["cboLevelNoAccess"]);
			$this->strResults="Sukses Ubah Data Group ";
		}
		else
		{
			$this->strResults="Gagal Ubah Data Group - ".mysql_error();
		}
		return $this->strResults;
	}
	
	function changePrivilegeGroup($kode, $access, $noaccess, $levelAkses, $levelNoAkses)
	{
		global $dbLink;
		
		// ARRAY CHECKBOX AKSES
		$cbAccess = array();
		$cbnoAccess = array();
		$cbAccess = $access;
		$cbNoAccess = $noaccess;
		// ARRAY COMBOBOX LEVEL
		$lvAkses = array();
		$lvNoAkses = array();
		$lvAkses = $levelAkses;
		$lvNoAkses = $levelNoAkses;
		
		$arrID = $this->getSelectedMenuGroup($kode, $cbAccess);
		
		// Memiliki hak akses menjadi tidak memiliki hak akses atau mengubah status level aksesnya
		if($cbAccess&&$lvAkses)
		{
			$ct=0;
			for($i=0; $i<count($cbAccess); $i++)
			{
				$id = $arrID[$ct];
				if($lvAkses[$id]==0)
				{
					$arrMenu = array();
					$arrMenu = $this->getChildMenuGroup($cbAccess[$i]);
					for($a=0; $a<count($arrMenu); $a++)
					{
						$q = "SELECT kodeMenu FROM groupPrivilege WHERE kodeMenu = '".$arrMenu[$a]."'";
						$rsTemp=mysql_query($q, $dbLink);
						$rows = mysql_num_rows($rsTemp);
						if($rows!=0)
						{
							$q = "DELETE FROM groupPrivilege ";
							$q.= "WHERE kodeGroup='".strtoupper($kode)."' AND kodeMenu='".$arrMenu[$a]."';";
							mysql_query( $q, $dbLink);
						}
					}
					$q = "DELETE FROM groupPrivilege ";
					$q.= "WHERE kodeGroup='".strtoupper($kode)."' AND kodeMenu='".$cbAccess[$i]."';";
					mysql_query( $q, $dbLink);
				}
				else
				{
					$arrMenu = array();
					$arrMenu = $this->getChildMenuGroup($cbAccess[$i]);
					
					for($a=0; $a<count($arrMenu); $a++)
					{
						$q = "SELECT kodeMenu FROM groupPrivilege WHERE kodeMenu = '".$arrMenu[$a]."'";
						$rsTemp=mysql_query($q, $dbLink);
						$rows = mysql_num_rows($rsTemp);
						if($rows!=0)
						{
							$q = "UPDATE groupPrivilege SET level='".$lvAkses[$id]."' ";
							$q.= "WHERE kodeGroup='".strtoupper($kode)."' AND kodeMenu='".$arrMenu[$a]."';";
							mysql_query( $q, $dbLink);
						}
					}
					$q = "UPDATE groupPrivilege SET level='".$lvAkses[$id]."' ";
					$q.= "WHERE kodeGroup='".strtoupper($kode)."' AND kodeMenu='".$cbAccess[$i]."';";
					mysql_query( $q, $dbLink);
				}
				$ct++;
			}
		}
		
		// Tidak memiliki hak akses menjadi memiliki hak akses
		$arrID = $this->getSelectedNoMenuGroup($kode, $cbNoAccess);
		if($cbNoAccess&&$lvNoAkses)
		{
			$ct=0;
			for($i=0; $i<count($cbNoAccess); $i++)
			{
				$id = $arrID[$ct];
				if($lvNoAkses[$id]!=0)
				{
					$q = "INSERT INTO groupPrivilege ( kodeGroup, kodeMenu, level ) ";
					$q.= "VALUES ('".strtoupper($kode)."','".$cbNoAccess[$i]."', '".$lvNoAkses[$id]."');";
					mysql_query( $q, $dbLink);
				}
				$ct++;
			}
		}
	}
	
	function getChildMenuGroup($kode)
	{
		global $dbLink;
		$menu = array();
		$i = 0;
		$kode = $kode . ".";
		$q = "SELECT kodeMenu FROM menu WHERE kodeMenu LIKE '".$kode."%'";
		$result=mysql_query($q, $dbLink);
		while($query_data=mysql_fetch_row($result))
		{ $menu[$i] =  $query_data[0];  $i++;}
		
		return $menu;
	}
	
	function getSelectedMenuGroup($kode, $arrAccess)
	{
		global $dbLink;
		
		$q = "SELECT gp.kodeMenu FROM groupPrivilege gp ";
		$q.= "WHERE gp.kodeGroup = '".$kode."' ORDER BY gp.kodeMenu";
	
		$rsTemp=mysql_query($q, $dbLink);
		while($row=mysql_fetch_array($rsTemp))
		{ $access[] = $row; }
		
		$arrID = array();
		$ct=0;
		for($i=0; $i<count($access); $i++)
		{
			if(strcmp($arrAccess[$ct],$access[$i][0])==0)
			{ $arrID[$ct] = $i; $ct++; }
		}
		
		return $arrID;
	}
	
	function getSelectedNoMenuGroup($kode, $arrNoAccess)
	{
		global $dbLink;
		
		$allowedGroups="'0'";
		$q = "SELECT gp.kodeMenu FROM groupPrivilege gp ";
		$q.= "WHERE gp.kodeGroup = '".$kode."' ORDER BY gp.kodeMenu";
		$rsTemp=mysql_query($q, $dbLink);
		while($row=mysql_fetch_array($rsTemp))
		{  
			$allowedGroups.=",'".$row[0]."'";
		}
			
		$q = "SELECT kodeMenu,judul FROM menu WHERE kodeMenu NOT IN (".$allowedGroups.") ORDER BY kodeMenu";
		$rsTemp=mysql_query($q, $dbLink);
		$akhirNoAccess = mysql_num_rows($rsTemp);
		while($row=mysql_fetch_array($rsTemp))
		{   $noAccess[] = $row; }

		$arrID = array();
		$ct=0;
		for($i=0; $i<count($noAccess); $i++)
		{
			if(strcmp($arrNoAccess[$ct],$noAccess[$i][0])==0)
			{
				$arrID[$ct] = $i;
				$ct++;
			}
		}
		
		return $arrID;
	}
	
	function deleteGroup($kode)
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateDeleteGroup($kode))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Group - ".$this->strResults;
			return $this->strResults;
		}
		
		//Secure parameter from SQL injection
		if(get_magic_quotes_gpc()) 
		{
			$kode = stripslashes($kode);
		}
		$kode=mysql_real_escape_string($kode, $dbLink);
		
		$q = "DELETE FROM groups ";
		$q.= "WHERE md5(kodeGroup)='".$kode."';";
		
		if (mysql_query( $q, $dbLink))
		{	
			$this->strResults="Sukses Hapus Data Group ";
		}
		else
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Group - ".mysql_error();
		}
		return $this->strResults;
	}
}
?>
