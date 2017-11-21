<?php
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 
require_once('./function/secureParam.php');

class c_user
{
               
	var $id=0;
	var $name="";
	var $groups="0";

	var $menus="0";
	var $strResults="";
	var $privilege="";
	
	//Constructor
	function c_user( $newId=0, $newName="", $newGroups="", $newMenus="", $newPrivilege="") 
	{
		$this->id=$newId;
		$this->name=$newName;
		$this->groups="'0'".$newGroups;

		$this->menus="'0'".$newMenus;	                
		$this->privilege = $newPrivilege;
	}
	
	// Fungsi untuk manajemen User
	function validate(&$params) 
	{
		$temp=TRUE;
		//kode grup harus diisi
		
		if($params["txtKodeUser"]=="" && $params["txtMode"]=="Add")
		{
			$this->strResults.="Kode Pengguna harus diisi!<br/>";
			$temp=FALSE;
		}
	
		//kode pengguna harus diisi
		if($params["txtNama"]=="")
		{
			$this->strResults.="Nama harus diisi!<br/>";
			$temp=FALSE;
		}
		
		if($params["txtMode"] == "add")
		{
			if($params["txtPassword"]=="")
			{
				$this->strResults.="Password harus diisi!<br/>";
				$temp=FALSE;
			}
		}
		
		return $temp;
	}
	
	function validateDelete($kode) 
	{
		global $dbLink;
		
		$temp=FALSE;
		if(empty($kode))
		{
			$this->strResults.="Kode tidak ditemukan!<br/>";
			$temp=FALSE;
		}
		
		$rsTemp=mysql_query("SELECT KodeUser FROM UserGroup WHERE md5(KodeUser)='".$kode."'", $dbLink);
		$rows = mysql_num_rows($rsTemp);

		if($rows==0)
		{
			return TRUE;
		}
		else
		{
			$temp = FALSE;
			$this->strResults="User masih terdaftar dalam salah satu Group di sistem ini. Hapus keanggotaan User dalam Group terlebih dahulu.<br>";
		}
		
		return $temp;
	}
	
	function add(&$params) 
	{
		global $dbLink;
                global $passSalt;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Tambah Data User - ".$this->strResults;
			return $this->strResults;
		}
		
		$kode= secureParam($params["txtKodeUser"],$dbLink);
		$nama = secureParam($params["txtNama"],$dbLink);
		$password = secureParam($params["txtPassword"],$dbLink);
		$status = secureParam($params["rdoStatus"],$dbLink);
			
		$rsTemp=mysql_query("SELECT kodeUser FROM user Where kodeUser = '".$kode."'", $dbLink);
		$query_data=mysql_fetch_row($rsTemp);
		if($query_data[0]==NULL)
		{
			$q = "INSERT INTO user (kodeUser, nama, aktif, password) VALUES ('".$kode."','".$nama."','".$status."','".HASH('SHA512',$passSalt.$password)."');";
		
			if (mysql_query( $q, $dbLink))
			{	
                            
                            $this->strResults="Sukses Tambah Data User ";
			}
			else
			{	//Pesan error harus diawali kata "Gagal"
				$this->strResults="Gagal Tambah Data User - ".mysql_error();
			}
		}
		else
		{
			$this->strResults="Gagal Tambah Data User - Kode Pengguna yang digunakan sudah terdaftar. ";
		}
		
		return $this->strResults;
	}
	
	function edit(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validate($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Ubah Data User - ".$this->strResults;
			return $this->strResults;
		}

		$kode= secureParam($params["kodeUser"],$dbLink);
		$nama = secureParam($params["txtNama"],$dbLink);
		$status = secureParam($params["rdoStatus"],$dbLink);
                		
		$q = "UPDATE user SET nama='".$nama."', aktif='".$status."' WHERE kodeUser='".$kode."';";
	
		if (mysql_query( $q, $dbLink))
		{	
                   $this->strResults="Sukses Ubah Data User ";
		}
		else
		{
			$this->strResults="Gagal Ubah Data User - ".mysql_error();
		}
		return $this->strResults;
	}
	
	function delete($kode)
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateDelete($kode))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data User - ".$this->strResults;
			return $this->strResults;
		}
		
               
                $kode=secureparam($kode,$dbLink);
		                           
		$q = "DELETE FROM user WHERE md5(kodeUser)='".$kode."';";
		
		if (mysql_query( $q, $dbLink))
		{	
			$this->strResults="Sukses Hapus Data User ";
		}
		else
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data User";
		}
		return $this->strResults;
	}
	
	function ChangeProfile(&$params)
	{
		global $dbLink;
                global $passSalt;

		$kode = secureParam($_SESSION["my"]->id, $dbLink);
		$nama = secureParam($params["txtNama"], $dbLink);
		$password = secureParam($params["txtPassword"], $dbLink);
		$newPassword = secureParam($params['txtPasswordBaru'], $dbLink);
		
		if($kode=='')
			return "Gagal Ubah Password User - Kode User tidak valid <br />";
		if($password=='' || $newPassword=='')
			return "Gagal Ubah Password User - Password tidak valid <br />";
			
		$rsTemp=mysql_query("SELECT kodeUser FROM user WHERE kodeUser='".$kode."' AND password='".HASH('SHA512',$passSalt.$password)."'", $dbLink);
		$rows = mysql_num_rows($rsTemp);

		if($rows == 0)
			$this->strResults.="Gagal Ubah Data Profil User - Password tidak cocok dengan database. ";
		else if($rows > 0)
		{
			$q = "UPDATE user SET nama='".$nama."', password='".HASH('SHA512',$passSalt.$newPassword)."'";
			$q.= "WHERE kodeUser='".$kode."';";

			if (mysql_query( $q, $dbLink))
			{	
				$this->strResults="Sukses Ubah Data Profil User ";
			}
			else
			{
				$this->strResults="Gagal Ubah Data Profil User - ".mysql_error();
			}
		}
		return $this->strResults;
	}
	
	function ChangePassword(&$params)
	{
		global $dbLink;
                global $passSalt;
		
		$kode = secureParam($params["txtKodeUser"], $dbLink);
		$password = secureParam($params["txtPasswordBaru"], $dbLink);
		$newPassword = secureParam($params['txtConfirmPassword'], $dbLink);
		
		if($kode=='')
			return "Gagal Ubah Password User - Kode User tidak valid <br />";
		if($password=='' || $newPassword=='' || $password!=$newPassword)
			return "Gagal Ubah Password User - Password tidak valid <br />";
		
		$q = "UPDATE user SET password='".HASH('SHA512',$passSalt.$newPassword)."' WHERE kodeUser='".$kode."';";
		if (mysql_query( $q, $dbLink))
			$this->strResults="Sukses Ubah Password User <br />";
		else
			$this->strResults="Gagal Ubah Password User - ".mysql_error()."<br />";
		return $this->strResults;
	}
}
?>