<?php
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 
require_once('./function/secureParam.php');

class c_akun
{
	var $strResults="";

	function getPilihanPeriode(){
		$now = date('mY');
		$prev = date('mY', strtotime('-5 months'));
		$next = date('mY', strtotime('+1 months'));

		$rs = mysql_query("SELECT idperiode, SUBSTRING(idperiode,1,2) as bulan, SUBSTRING(idperiode,3,4) as tahun FROM periode WHERE idperiode<='$next' AND idperiode>='$prev'");
		return $rs;
	}

	function getPeriodeAktif(){
		global $dbLink;

		$arr = array('idperiode'=>'','tahun'=>'','bulan'=>'');
		$cektb = mysql_fetch_array(mysql_query("SELECT * FROM periode WHERE status='tutup' ORDER BY idperiode desc", $dbLink));
        $sqladd = "";
        if($cektb)
        	$sqladd = " AND idperiode > ".$cektb["idperiode"];
                    
		$cekaktif = mysql_fetch_array(mysql_query("SELECT * FROM periode WHERE status='aktif' {$sqladd} ORDER BY idperiode", $dbLink));
        if($cekaktif){
        	$arr['idperiode'] = $cekaktif['idperiode'];
        	$arr['tahun'] = substr($arr['idperiode'], 2, 4);
            $arr['bulan'] = substr($arr['idperiode'], 0, 2);
        }                     
        return $arr;
	}

	function validateAkun(&$params)
	{
		$temp=TRUE;
		//Jika mode Add, root menu harus diisi
		if(strlen(trim($params["txtKodeAkun"]))== 0)
		{
			$this->strResults.="Kode Akun harus diisi!<br/>";
			$temp=FALSE;
		}
		if(strlen(trim($params["txtNama"]))== 0)
		{
			$this->strResults.="Nama Akun harus diisi!<br/>";
			$temp=FALSE;
		}
		if(strlen(trim($params["rdoPosisi"]))== 0)
		{
			$this->strResults.="Posisi harus diisi!<br/>";
			$temp=FALSE;
		}
		if(strlen(trim($params["rdoNormal"]))== 0)
		{
			$this->strResults.="Saldo Normal harus diisi!<br/>";
			$temp=FALSE;
		}
		return $temp;
	}

	function validatePeriode(&$params)
	{
		$temp=TRUE;
		//Jika mode Add, root menu harus diisi
		if($params["cbotahun"]=="0")
		{
			$this->strResults.="Periode Tahun harus dipilih !<br/>";
			$temp=FALSE;
		}
		if($params["cbobulan"]=="0")
		{
			$this->strResults.="Periode Bulan harus dipilih !<br/>";
			$temp=FALSE;
		}
		if(strlen(trim($params["rdoStatus"]))== 0)
		{
			$this->strResults.="Status harus diisi!<br/>";
			$temp=FALSE;
		}
		return $temp;
	}

	function validateDelete($kode) 
	{
		global $dbLink;
                
                $temp=TRUE;
		if(empty($kode))
		{
			$this->strResults.="Kode tidak ditemukan!<br/>";
			$temp=FALSE;
		}
		return $temp;

	}

	function add(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateAkun($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Tambah Data Akun - ".$this->strResults;
			return $this->strResults;
		}
		
		$kodeAkun   = secureParam($params["txtKodeAkun"], $dbLink);
		$nama   = secureParam($params["txtNama"], $dbLink);
		$posisi   = secureParam($params["rdoPosisi"], $dbLink);
		$normal   = secureParam($params["rdoNormal"], $dbLink);
		$parentKodeAkun   = secureParam($params["cboKodeParent"], $dbLink);
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
				
			$rsTemp=mysql_query("SELECT kodeAkun FROM akun WHERE kodeAkun='".$kodeAkun."';", $dbLink);
			if(mysql_num_rows($rsTemp))
				throw new Exception('Kode Akun sudah ada dalam database.');
				
			$q = "INSERT INTO akun (kodeAkun, nama, normal, parentKodeAkun, posisi) ";
			$q.= "VALUES ('".$kodeAkun."','".ucfirst($nama)."','".$normal."',".($parentKodeAkun?"'".$parentKodeAkun."'":"NULL").",'".$posisi."');";
		
			if (!mysql_query( $q, $dbLink))
			{	
				throw new Exception('Tidak dapat menambah data di database.');
			}
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Tambah Data Akun ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Tambah Data Akun - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
		}
		return $this->strResults;
	}

	function edit(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateAkun($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Ubah Data Akun - ".$this->strResults;
			return $this->strResults;
		}
		
		$kodeAkun  = secureParam($params["txtKodeAkun"], $dbLink);
		$nama   = secureParam($params["txtNama"], $dbLink);
		$posisi   = secureParam($params["rdoPosisi"], $dbLink);
		$normal   = secureParam($params["rdoNormal"], $dbLink);
		$parentKodeAkun   = secureParam($params["cboKodeParent"], $dbLink);
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			$q = "UPDATE akun SET nama='".ucfirst($nama)."', normal='".$normal."', parentKodeAkun=".($parentKodeAkun?"'".$parentKodeAkun."'":"NULL").", posisi='".$posisi."'  
			WHERE kodeAkun='".$kodeAkun."';";
			
			if (!mysql_query( $q, $dbLink))
			{	
				throw new Exception('Tidak dapat mengedit data di database.');
			}

			//insert data saldo awal
            $jumData = $params["jumAddSaldo"];
            $user = $_SESSION["my"]->id;
            for ($j = 0; $j < $jumData; $j++) {
                if (!empty($params["chkAddSaldo_" . $j])) {
                    if ($params["cbobulan_" . $j] == "0")
                        throw new Exception("Bulan harus dipilih !");
                    if ($params["cbotahun_" . $j] == "0")
                        throw new Exception("Tahun harus dipilih !");
                    if ($params["txtNominal_" . $j] == "")
                        throw new Exception("Saldo Awal Harus Diisi !");

                    $idperiode = secureParam($params["txtIdPeriode_" . $j], $dbLink);
                    $bulan = secureParam($params["cbobulan_" . $j], $dbLink);
                    $tahun = secureParam($params["cbotahun_" . $j], $dbLink);
                    $saldoAwal = secureParam($params["txtNominal_" . $j], $dbLink);
                    $saldoAwal = str_replace(".", "", $saldoAwal);
                    
                    if($bulan.$tahun != $idperiode)
                    	$idperiode = $bulan.$tahun;
            
					$q = "INSERT INTO periodeakun (idperiode, kodeAkun, saldoAwal, mutasiD, mutasiK, saldoAkhir, kodeUser) VALUES ('$idperiode', '$kodeAkun', ".$saldoAwal.", 0, 0, ".$saldoAwal.", '".$user."') ON DUPLICATE KEY UPDATE saldoAkhir=saldoAkhir-saldoAwal+".$saldoAwal.", saldoAwal=".$saldoAwal.", kodeUser='".$user."';";
					
					if (!mysql_query( $q, $dbLink))
					{	
						throw new Exception('Tidak dapat mengedit data saldo awal di database.');
					}
                }
            }
			        
            //update data saldo awal
            $jumData = $params["jumEditSaldo"];
            for ($a = 0; $a < $jumData; $a++) {
                if (!empty($params["chkEdit_" . $a])) {
                    if ($params["cbobulanE_" . $a] == "")
                       	throw new Exception("Bulan harus dipilih !!");
                    if ($params["cbotahunE_" . $a] == "")
                        throw new Exception("Tahun harus dipilih !!");

                    $idperiode = secureParam($params["chkEdit_" . $a], $dbLink);
                    $saldoAwal = secureParam($params["txtNominalE_" . $a], $dbLink);
                    $saldoAwal = str_replace(".", "", $saldoAwal);
                    
                    $q = "INSERT INTO periodeakun (idperiode, kodeAkun, saldoAwal, mutasiD, mutasiK, saldoAkhir, kodeUser) VALUES ('$idperiode', '$kodeAkun', ".$saldoAwal.", 0, 0, ".$saldoAwal.", '".$user."') ON DUPLICATE KEY UPDATE saldoAkhir=saldoAkhir-saldoAwal+".$saldoAwal.", saldoAwal=".$saldoAwal.", kodeUser='".$user."';";
					
					if (!mysql_query( $q, $dbLink))
					{	
						throw new Exception('Tidak dapat mengedit data saldo awal di database.');
					}
                    
                }
            }

			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Edit Data Akun ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Edit Data Akun - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
		}
		return $this->strResults;
	}

	function delete($kode)
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateDelete($kode))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Akun - ".$this->strResults;
			return $this->strResults;
		}
		$kode=secureParam($kode, $dbLink);
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			// cek apakah merupakan parent dari akun lain
			$rsTemp=mysql_query("SELECT kodeAkun FROM akun WHERE md5(parentKodeAkun)='".$kode."';", $dbLink);
			if(mysql_num_rows($rsTemp)){
				throw new Exception('Kode Akun parent tidak dapat dihapus.');
			}

			// cek apakah akun sudah digunakan di jurnal
			$rsTemp=mysql_query("SELECT kodeAkun FROM detailjurnal WHERE md5(kodeAkun)='".$kode."';", $dbLink);
			if(mysql_num_rows($rsTemp)){
				throw new Exception('Kode Akun sudah digunakan di Jurnal.');
			}		

			$q = "DELETE FROM periodeakun ";
			$q.= "WHERE md5(kodeAkun)='".$kode."';";
			
			if (!mysql_query( $q, $dbLink))
			{	
				throw new Exception('Tidak dapat menghapus data saldo awal akun di database.');
			}	

			$q = "DELETE FROM akun ";
			$q.= "WHERE md5(kodeAkun)='".$kode."';";
			
			if (!mysql_query( $q, $dbLink))
			{	
				throw new Exception('Tidak dapat menghapus data akun di database.');
			}

			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Hapus Data Akun ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Hapus Data Akun - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
		}
		
		return $this->strResults;
	}

	function addPeriode(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validatePeriode($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Tambah Data Periode - ".$this->strResults;
			return $this->strResults;
		}
		
		$idperiode = $params["cboBulan"].$params["cboTahun"];
		$idperiode   = secureParam($idperiode, $dbLink);
		$status   = secureParam($params["rdoStatus"], $dbLink);
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
				
			$rsTemp=mysql_query("SELECT idperiode FROM periode WHERE idperiode='".$idperiode."';", $dbLink);
			if(mysql_num_rows($rsTemp))
				throw new Exception('Periode sudah ada dalam database.');
				
			$q = "INSERT INTO periode (idperiode, status) ";
			$q.= "VALUES ('".$idperiode."','".$status."');";
		
			if (!mysql_query( $q, $dbLink))
			{	
				throw new Exception('Tidak dapat menambah data di database.');
			}
			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Tambah Data Periode ".$idperiode;
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Tambah Data Periode - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
		}
		return $this->strResults;
	}

	function editPeriode(&$params) 
	{
		global $dbLink;
		
		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validatePeriode($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Ubah Data Periode - ".$this->strResults;
			return $this->strResults;
		}
		
		$idperiode = $params["cboBulan"].$params["cboTahun"];
		$idperiode   = secureParam($idperiode, $dbLink);
		$status   = secureParam($params["rdoStatus"], $dbLink);

		if($idperiode != secureParam($params["idperiode"], $dbLink))
			$idperiode = secureParam($params["idperiode"], $dbLink);

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			$q = "UPDATE periode SET status='".$status."'  
			WHERE idperiode ='".$idperiode."';";
			
			if (!mysql_query( $q, $dbLink))
			{	
				throw new Exception('Tidak dapat mengedit data di database.');
			}

			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Edit Data Periode ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Edit Data Periode - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
		}
		return $this->strResults;
	}

	function deletePeriode($kode)
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateDelete($kode))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Periode - ".$this->strResults;
			return $this->strResults;
		}
		$kode=secureParam($kode, $dbLink);
		
		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			// cek apakah merupakan parent dari akun lain
			$rsTemp=mysql_query("SELECT idperiode FROM periodeakun WHERE md5(idperiode)='".$kode."';", $dbLink);
			if(mysql_num_rows($rsTemp)){
				throw new Exception('Periode sudah digunakan di Periode Akun.');
			}

			// cek apakah akun sudah digunakan di jurnal
			$rsTemp=mysql_query("SELECT idperiode FROM jurnal WHERE md5(idperiode)='".$kode."';", $dbLink);
			if(mysql_num_rows($rsTemp)){
				throw new Exception('Periode sudah digunakan di Jurnal.');
			}		

			$q = "DELETE FROM periode ";
			$q.= "WHERE md5(idperiode)='".$kode."';";
			
			if (!mysql_query( $q, $dbLink))
			{	
				throw new Exception('Tidak dapat menghapus data periode di database.');
			}

			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Hapus Data Periode ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Hapus Data Akun - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
		}
		
		return $this->strResults;
	}

	function addJurnal(&$params)
	{
		return;
	}

}