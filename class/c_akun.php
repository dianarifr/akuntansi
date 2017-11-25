<?php
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 
// require_once('./function/secureParam.php');

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

	function validateJurnal(&$params)
	{
		$temp=TRUE;
		if (strlen(trim($params["txtTanggal"]))==0) {
			$this->strResults.="Tanggal harus diisi!<br/>";
			$temp=FALSE;
		}
		if (strlen(trim($params["txtKeterangan"]))==0) {
			$this->strResults.="Keterangan harus diisi!<br/>";
			$temp=FALSE;
		}
		return $temp;
	}

	function validatePosting(&$params) 
	{
		$temp=TRUE;

		if($params["btn"]=='posting' || $params["btn"]=='batalposting'){
			if(strlen(trim($params["noJurnal"]))== 0)
			{
				$this->strResults.="Pilih data jurnal terlebih dahulu!<br/>";
				$temp=FALSE;
			}
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
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateJurnal($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Tambah Data Jurnal - ".$this->strResults;
			return $this->strResults;
		}

		$tanggal = datetomysql(secureParam($params["txtTanggal"], $dbLink));
		$keterangan = secureParam($params["txtKeterangan"], $dbLink);
		$noTahun = substr($tanggal, 0, 4);
        $noBulan = substr($tanggal, 5, 2);
		$user = $_SESSION["my"]->id;
		$idperiode = $noBulan.$noTahun;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}
			
			// insert/update periode
            $q = "INSERT INTO periode (idperiode, status) VALUES ('$idperiode', 'aktif') ON DUPLICATE KEY UPDATE idperiode='$idperiode';";
            if (!mysql_query( $q, $dbLink))
			{	
				throw new Exception('Tidak dapat mengedit data periode di database.');
			}

			//cek periode tutup/aktif
			$tutup = mysql_fetch_array(mysql_query("SELECT idperiode FROM periode WHERE status='tutup' AND idperiode='$idperiode'", $dbLink));
			if($tutup)
				throw new Exception("Tidak dapat menambah data jurnal karena proses Tutup Buku sudah dilakukan."); 

			//insert jurnal
			$thn = substr($tanggal, 2, 2);
			$qq = "SELECT CONCAT('JU$noBulan$thn', LPAD(IFNULL(MAX(RIGHT(noJurnal, 6)), 0)+1, 6, '0')) as kode FROM jurnal WHERE noJurnal LIKE 'JU".$noBulan.$thn."______'";
            $row = mysql_fetch_array(mysql_query($qq,$dbLink));
            $kode = $row["kode"];
            
			$q = "INSERT INTO jurnal (noJurnal, tgl, keterangan, kodeUser, idperiode, statusPosting) ";
			$q.= "VALUES ('".$kode."','".$tanggal."','".$keterangan."','".$user."','".$idperiode."',0);";
		
			if (!mysql_query( $q, $dbLink))
			{	
				throw new Exception('Tidak dapat menambah data jurnal di database.');
			}

			//insert detail jurnal
			$jumData = $params["jumAddJurnal"];
			$noUrut = 1;
            for ($j = 0; $j < $jumData; $j++) {
                if (!empty($params["chkAddJurnal_" . $j])) {
                    if ($params["cboAkun_" . $j] == "0")
                        throw new Exception("Nama Akun harus dipilih !");
                    if ($params["txtDebet_" . $j] == "")
                        throw new Exception("Nominal Debit harus diisi!");
                    if ($params["txtKredit_" . $j] == "")
                        throw new Exception("Nominal Kredit harus diisi!");

                    $kodeAkun = secureParam($params["cboAkun_" . $j], $dbLink);
                    $nominalD = str_replace(".", "", secureParam($params["txtDebet_" . $j], $dbLink));
                    $nominalK = str_replace(".", "", secureParam($params["txtKredit_" . $j], $dbLink));
                    if($nominalD>0){
						$normal = 'debet';
						$nominal = $nominalD;
					}elseif($nominalK>0){
						$normal = 'kredit';
						$nominal = $nominalK;
					}

                    $q = "INSERT INTO detailJurnal (kodeAkun, noJurnal, normal, nominal, noUrut) ";
					$q.= "VALUES ('".$kodeAkun."','".$kode."','".$normal."',".$nominal.",".$noUrut.");";
                    if (!mysql_query($q, $dbLink))
                        throw new Exception('Tidak dapat menambah detail jurnal di database.');
                    $noUrut++;
                }
            }

			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Tambah Data Jurnal ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Tambah Data Jurnal - ".$e->getMessage().'<br/>'.mysql_error();
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}

		return $this->strResults;
	}

	function editJurnal(&$params)
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateJurnal($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Edit Data Jurnal - ".$this->strResults;
			return $this->strResults;
		}

		$kode = secureParam($params["noJurnal"], $dbLink);
		$tanggal = datetomysql(secureParam($params["txtTanggal"], $dbLink));
		$keterangan = secureParam($params["txtKeterangan"], $dbLink);
		$noTahun = substr($tanggal, 0, 4);
        $noBulan = substr($tanggal, 5, 2);
		$user = $_SESSION["my"]->id;
		$idperiode = $noBulan.$noTahun;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			// cek status posting
			$cek = mysql_fetch_array(mysql_query("SELECT statusPosting FROM jurnal WHERE noJurnal='$kode'", $dbLink));
            if($cek["statusPosting"]==1)
            	throw new Exception("Jurnal yang sudah diposting tidak dapat diubah.");

            // insert/update periode
            $q = "INSERT INTO periode (idperiode, status) VALUES ('$idperiode', 'aktif') ON DUPLICATE KEY UPDATE idperiode='$idperiode';";
            if (!mysql_query( $q, $dbLink))
			{	
				throw new Exception('Tidak dapat mengedit data periode di database.');
			}

			//cek periode tutup/aktif
			$tutup = mysql_fetch_array(mysql_query("SELECT idperiode FROM periode WHERE status='tutup' AND idperiode='$idperiode'", $dbLink));
			if($tutup)
				throw new Exception("Tidak dapat menambah data jurnal karena proses Tutup Buku sudah dilakukan.");

			$q = "UPDATE jurnal SET tgl='".$tanggal."', keterangan='".$keterangan."', kodeUser='".$_SESSION["my"]->id."' 
			WHERE noJurnal='".$kode."';";
			
			if (!mysql_query( $q, $dbLink))
			{	
				throw new Exception('Tidak dapat mengedit data di database.');
			}

			//insert detail jurnal
			$jumData = $params["jumAddJurnal"];
            $cek = mysql_fetch_array(mysql_query("SELECT MAX(noUrut) as noUrut FROM detailjurnal WHERE noJurnal='$kode'", $dbLink));
            $noUrut = $cek['noUrut']; 
            for ($j = 0; $j < $jumData; $j++) {
                if (!empty($params["chkAddJurnal_" . $j])) {
                    $noUrut++;
                    if ($params["cboAkun_" . $j] == "0")
                        throw new Exception("Nama Akun harus dipilih !");
                    if ($params["txtDebet_" . $j] == "")
                        throw new Exception("Nominal Debit harus diisi!");
                    if ($params["txtKredit_" . $j] == "")
                        throw new Exception("Nominal Kredit harus diisi!");

                    $kodeAkun = secureParam($params["cboAkun_" . $j], $dbLink);
                    $nominalD = str_replace(".", "", secureParam($params["txtDebet_" . $j], $dbLink));
                    $nominalK = str_replace(".", "", secureParam($params["txtKredit_" . $j], $dbLink));
                    if($nominalD>0){
						$normal = 'debet';
						$nominal = $nominalD;
					}elseif($nominalK>0){
						$normal = 'kredit';
						$nominal = $nominalK;
					}

                    $q = "INSERT INTO detailJurnal (kodeAkun, noJurnal, normal, nominal, noUrut) ";
					$q.= "VALUES ('".$kodeAkun."','".$kode."','".$normal."',".$nominal.",".$noUrut.");";
                    if (!mysql_query($q, $dbLink))
                        throw new Exception('Tidak dapat menambah detail jurnal di database.');
                }
            }

            //update data jurnal
            $jumData = $params["jumEditJurnal"];
            for ($a = 0; $a < $jumData; $a++) {
                if (!empty($params["chkEdit_" . $a])) {
                    if ($params["cboAkunE_" . $a] == "0")
                        throw new Exception("Nama Akun harus dipilih !");
                    if ($params["txtDebetE_" . $a] == "")
                        throw new Exception("Nominal Debit harus diisi!");
                    if ($params["txtKreditE_" . $a] == "")
                        throw new Exception("Nominal Kredit harus diisi!");

                    $id = secureParam($params["chkEdit_" . $a], $dbLink);
                    
                    $kodeAkun = secureParam($params["cboAkunE_" . $a], $dbLink);
                    $nominalD = str_replace(".", "", secureParam($params["txtDebetE_" . $a], $dbLink));
                    $nominalK = str_replace(".", "", secureParam($params["txtKreditE_" . $a], $dbLink));
                    if($nominalD>0){
						$normal = 'debet';
						$nominal = $nominalD;
					}elseif($nominalK>0){
						$normal = 'kredit';
						$nominal = $nominalK;
					}
                    
                    $q = "UPDATE detailjurnal SET kodeAkun='" . $kodeAkun . "', normal='".$normal."', nominal='".$nominal."' 
                            WHERE noJurnal='".$kode."' AND id='".$id."' ";
                    if (!mysql_query($q, $dbLink))
                        throw new Exception("Tidak bisa edit Data detail jurnal di database.");
                }

                if (!empty($params["chkDel_" . $a])) {
                    $id = secureParam($params["chkDel_" . $a], $dbLink);
                    $q = "DELETE FROM detailjurnal WHERE id='" . $id . "' and noJurnal='".$kode."'";
                    if (!mysql_query($q, $dbLink))
                        throw new Exception("Tidak bisa hapus Data Pdetail jurnal di database.");
                }
            }

			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Edit Data Jurnal ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Edit Data Jurnal - ".$e->getMessage().'<br/>'.mysql_error();
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
			  return $this->strResults;
		}

		return $this->strResults;
	}

	function deleteJurnal($kode)
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validateDelete($kode))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Hapus Data Jurnal - ".$this->strResults;
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

			// cek status posting
			$cek = mysql_fetch_array(mysql_query("SELECT * FROM jurnal WHERE md5(noJurnal)='$kode'", $dbLink));
            if($cek["statusPosting"]==1)
            	throw new Exception("Jurnal yang sudah diposting tidak dapat dihapus.");  

			//delete detail jurnal
           	$q = "DELETE FROM detailJurnal WHERE noJurnal='".$cek["noJurnal"]."';";
			if (!mysql_query( $q, $dbLink))
			{	
				throw new Exception('Tidak dapat menghapus detail jurnal di database.');
			}

			//delete jurnal
			$q = "DELETE FROM jurnal WHERE noJurnal='".$cek["noJurnal"]."';";
            if (!mysql_query( $q, $dbLink))
			{	
				throw new Exception('Tidak dapat mengedit data jurnal di database.');
			}

			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Hapus Data Jurnal ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Hapus Data Jurnal - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
		}

		return $this->strResults;
	}

	function savePosting(&$params) 
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validatePosting($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Simpan Posting Jurnal - ".$this->strResults;
			return $this->strResults;
		}

		$noJurnal = secureParam($params["noJurnal"], $dbLink);
		$idperiode   = secureParam($params["idperiode"], $dbLink);
		$user = $_SESSION["my"]->id;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			$cektb = mysql_fetch_array(mysql_query("SELECT * FROM periode WHERE status='tutup' AND idperiode='$idperiode'", $dbLink));
			if($cektb)
				throw new Exception("Jurnal tidak dapat diposting karena proses Tutup Buku sudah dilakukan."); 			

			if($params["btn"]=="posting"){
				$arrNoJurnal = explode(",", $noJurnal);
				$sql = "SELECT d.*, a.normal as anormal FROM detailjurnal d JOIN jurnal j ON j.noJurnal=d.noJurnal JOIN akun a ON a.kodeAkun=d.kodeAkun WHERE j.noJurnal IN ('".implode("', '", $arrNoJurnal)."') AND j.statusPosting=0";
			}else{
				$sql = "SELECT d.*, a.normal as anormal FROM detailjurnal d JOIN jurnal j ON j.noJurnal=d.noJurnal JOIN akun a ON a.kodeAkun=d.kodeAkun WHERE j.idperiode='$idperiode' AND j.statusPosting=0";
			}
			$rs = mysql_query($sql, $dbLink);

			while ($row = mysql_fetch_array($rs)) {
				$q = "UPDATE jurnal SET statusPosting=1, kodeUser='$user' 
				WHERE noJurnal='".$row['noJurnal']."';";
				if (!mysql_query( $q, $dbLink))
				{	
					throw new Exception('Tidak dapat mengedit data jurnal di database.');
				}

				$q = mysql_fetch_array(mysql_query("SELECT saldoAwal FROM periodeakun WHERE idperiode='$idperiode' AND kodeAkun='{$row['kodeAkun']}'", $dbLink));
				if(mysql_num_rows($q)>0)
					$saldoAwal = $q["saldoAwal"];
				else
					$saldoAwal = 0;

				$mutasiD = ($row["normal"]=="debet"?$row["nominal"]:0);
				$mutasiK = ($row["normal"]=="kredit"?$row["nominal"]:0);
				// $saldoAkhir = ($row["anormal"]=="debet"?abs($saldoAwal+$mutasiD-$mutasiK):abs($saldoAwal-$mutasiD+$mutasiK));
				$saldoAkhir = ($row["anormal"]=="debet"?($mutasiD-$mutasiK):($mutasiK-$mutasiD));

				$q = "INSERT INTO periodeAkun (idperiode, kodeAkun, saldoAwal, mutasiD, mutasiK, saldoAkhir, kodeUser) VALUES ('$idperiode', '".$row["kodeAkun"]."', 0, ".$mutasiD.", ".$mutasiK.", ".$saldoAkhir.", '$user') ON DUPLICATE KEY UPDATE mutasiD=mutasiD+".$mutasiD.", mutasiK=mutasiK+".$mutasiK.", saldoAkhir=saldoAkhir+".$saldoAkhir.", kodeUser='".$user."';";
	            if (!mysql_query( $q, $dbLink))
				{	
					throw new Exception('Tidak dapat mengedit data saldo di database.');
				}
			}

			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Simpan Posting Jurnal ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Simpan Posting Jurnal - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
		}
		return $this->strResults;
	}

	function saveBatalPosting(&$params) 
	{
		global $dbLink;

		//Jika input tidak valid, langsung kembalikan pesan error ke user ($this->strResults)
		if(!$this->validatePosting($params))
		{	//Pesan error harus diawali kata "Gagal"
			$this->strResults="Gagal Simpan Pembatalan Posting Jurnal - ".$this->strResults;
			return $this->strResults;
		}

		$noJurnal = secureParam($params["noJurnal"], $dbLink);
		$idperiode   = secureParam($params["idperiode"], $dbLink);
		$user = $_SESSION["my"]->id;

		try
		{
			$result = @mysql_query('SET AUTOCOMMIT=0', $dbLink);
			$result = @mysql_query('BEGIN', $dbLink);
			if (!$result) {
				throw new Exception('Could not begin transaction');
			}

			$cektb = mysql_fetch_array(mysql_query("SELECT * FROM periode WHERE status='tutup' AND idperiode='$idperiode'", $dbLink));
			if($cektb)
				throw new Exception("Jurnal tidak dapat diposting karena proses Tutup Buku sudah dilakukan."); 			

			if($params["btn"]=="batalposting"){
				$arrNoJurnal = explode(",", $noJurnal);
				$sql = "SELECT d.*, a.normal as anormal FROM detailjurnal d JOIN jurnal j ON j.noJurnal=d.noJurnal JOIN akun a ON a.kodeAkun=d.kodeAkun WHERE j.noJurnal IN ('".implode("', '", $arrNoJurnal)."') AND j.statusPosting=1";
			}else{
				$sql = "SELECT d.*, a.normal as anormal FROM detailjurnal d JOIN jurnal j ON j.noJurnal=d.noJurnal JOIN akun a ON a.kodeAkun=d.kodeAkun WHERE j.idperiode='$idperiode' AND j.statusPosting=1";
			}
			$rs = mysql_query($sql, $dbLink);

			while ($row = mysql_fetch_array($rs)) {
				$q = "UPDATE jurnal SET statusPosting=0, kodeUser='$user' 
				WHERE noJurnal='".$row['noJurnal']."';";
				if (!mysql_query( $q, $dbLink))
				{	
					throw new Exception('Tidak dapat mengedit data jurnal di database.');
				}

				$q = mysql_fetch_array(mysql_query("SELECT saldoAwal FROM periodeakun WHERE idperiode='$idperiode' AND kodeAkun='{$row['kodeAkun']}'", $dbLink));
				if(mysql_num_rows($q)>0)
					$saldoAwal = $q["saldoAwal"];
				else
					$saldoAwal = 0;

				$mutasiD = ($row["normal"]=="debet"?$row["nominal"]:0);
				$mutasiK = ($row["normal"]=="kredit"?$row["nominal"]:0);
				$saldoAkhir = ($row["anormal"]=="debet"?($mutasiD-$mutasiK):($mutasiK-$mutasiD));

				$q = "INSERT INTO periodeAkun (idperiode, kodeAkun, saldoAwal, mutasiD, mutasiK, saldoAkhir, kodeUser) VALUES ('$idperiode', '".$row["kodeAkun"]."', 0, ".$mutasiD.", ".$mutasiK.", ".$saldoAkhir.", '$user') ON DUPLICATE KEY UPDATE mutasiD=mutasiD-".$mutasiD.", mutasiK=mutasiK-".$mutasiK.", saldoAkhir=saldoAkhir-".$saldoAkhir.", kodeUser='".$user."';";
	            if (!mysql_query( $q, $dbLink))
				{	
					throw new Exception('Tidak dapat mengedit data saldo di database.');
				}
			}

			@mysql_query("COMMIT", $dbLink);
			$this->strResults="Sukses Simpan Pembatalan Posting Jurnal ";
		}
		catch(Exception $e) 
		{
			  $this->strResults="Gagal Simpan Pembatalan Posting Jurnal - ".$e->getMessage().'<br/>';
			  $result = @mysql_query('ROLLBACK', $dbLink);
			  $result = @mysql_query('SET AUTOCOMMIT=1', $dbLink);
		}
		return $this->strResults;
	}

	function LapNeraca($idperiode)
	{
		global $dbLink;
		$arrNeraca = array('a'=>array(),'k'=>array());
		$arrTotalN = array('a'=>0,'k'=>0);
		$a_dataN = array();
		$q = "SELECT a.kodeAkun ,a.nama ,p.saldoAkhir ,a.normal ,a.parentKodeAkun , 
							CASE WHEN b.kodeAkun IS NULL THEN 0 ELSE 1 END as isParent 
						FROM akun a LEFT JOIN akun b on b.parentKodeAkun=a.kodeAkun 
						LEFT JOIN periodeakun p on a.kodeAkun=p.kodeAkun
						WHERE a.kodeAkun like '1%' OR a.kodeAkun like '3%' OR a.kodeAkun like '4%' OR a.kodeAkun like '5%' 
						OR (a.posisi = 'neraca' AND p.idperiode='$idperiode') ORDER BY a.kodeAkun";
		$rs = mysql_query($q, $dbLink);
		while ($rowa = mysql_fetch_array($rs)) {
			$a_dataN[$rowa['kodeAkun']]['nama'] = $rowa['nama'];
            $a_dataN[$rowa['kodeAkun']]['parent'] = $rowa["parentKodeAkun"];
            $a_dataN[$rowa['kodeAkun']]['isparent'] = $rowa["isParent"];
            $a_dataN[$rowa['kodeAkun']]['saldoAkhir'] = $rowa['saldoAkhir'];
            $a_dataN[$rowa['kodeAkun']]['normal'] = $rowa['normal'];
		}

		foreach ($a_dataN as $key => $value) {
			if($value["isparent"]){
				$parent =  $key;
			}
			if ($parent == $value["parent"]) {
				if(substr($key,0,1)=='1' or substr($key,0,1)=='3'){
					// echo $key." ".$value["nama"]."<br>";
					if ($value["normal"]=="debet"){
						if (!empty($value['saldoAkhir'])){
							$arrNeraca['a'][] = array("nama_rekening"=>$value['nama'],"nominal"=>$value['saldoAkhir']);
							$totA += $value['saldoAkhir']; 
						}
					}
					else{
						if (!empty($value['saldoAkhir'])){
							$arrNeraca['a'][] = array("nama_rekening"=>$value['nama'],"nominal"=>$value['saldoAkhir']);
							$totA -= $value['saldoAkhir']; 
						}
					}
					$arrTotalN['a'] = $totA;
				}
				else{
					if ($value["normal"]=="kredit"){
						if (!empty($value['saldoAkhir'])){
							$arrNeraca['k'][] = array("nama_rekening"=>$value['nama'],"nominal"=>$value['saldoAkhir']);
							$totK += $value['saldoAkhir']; 
						}
					}
					else{
						if (!empty($value['saldoAkhir'])){
							$arrNeraca['k'][] = array("nama_rekening"=>$value['nama'],"nominal"=>$value['saldoAkhir']);
							$totK -= $value['saldoAkhir']; 
						}
					}
					$arrTotalN['k'] = $totK;
				}
			}
		}

		$l = $this->LapRugiLaba($idperiode);
		$laba = $l[2];
		if ($arrNeraca['k']) {
            $arrTotalN['k'] += $laba;
            $arrNeraca['k'][] = array("nama_rekening"=>"Rugi / Laba","nominal"=>($laba?$laba:'0'));
        }
		
		return array($arrNeraca, $arrTotalN);
	}

	function LapRugiLaba($idperiode)
	{
		global $dbLink;

		$laba = 0;
		$a_dataRL = array();
		$arrRugiLaba = array('p'=>array(),'b'=>array());
		$arrTotalRL = array('p'=>0,'b'=>0);
		$q = "SELECT a.kodeAkun ,a.nama ,p.saldoAkhir ,a.normal ,a.parentKodeAkun , 
							CASE WHEN b.kodeAkun IS NULL THEN 0 ELSE 1 END as isParent 
						FROM akun a LEFT JOIN akun b on b.parentKodeAkun=a.kodeAkun 
						LEFT JOIN periodeakun p on a.kodeAkun=p.kodeAkun
						WHERE a.kodeAkun like '6%' OR a.kodeAkun like '8%' OR a.kodeAkun like '9%' AND a.kodeAkun not like '99%' 
						OR (a.posisi = 'rugilaba' AND p.idperiode='$idperiode') ORDER BY a.kodeAkun";
		$rs = mysql_query($q, $dbLink);
		while ($rowa = mysql_fetch_array($rs)) {
			$a_dataRL[$rowa['kodeAkun']]['nama'] = $rowa['nama'];
            $a_dataRL[$rowa['kodeAkun']]['parent'] = $rowa["parentKodeAkun"];
            $a_dataRL[$rowa['kodeAkun']]['isparent'] = $rowa["isParent"];
            $a_dataRL[$rowa['kodeAkun']]['saldoAkhir'] = $rowa['saldoAkhir'];
            $a_dataRL[$rowa['kodeAkun']]['normal'] = $rowa['normal'];
		}

		foreach ($a_dataRL as $key => $value) {
			if($value["isparent"]){
				$parent =  $key;
			}
			if ($parent == $value["parent"]) {
				//start laporan rugi laba
				if(substr($key,0,1)=='9' or substr($key,0,1)=='6'){
					if ($value["normal"]=="kredit"){
						if (!empty($value['saldoAkhir'])){
							$arrRugiLaba['p'][] = array("nama_rekening"=>$value['nama'],"nominal"=>$value['saldoAkhir']);
							$totP += $value['saldoAkhir']; 
						}
					}
					else{
						if (!empty($value['saldoAkhir'])){
							$arrRugiLaba['p'][] = array("nama_rekening"=>$value['nama'],"nominal"=>$value['saldoAkhir']);
							$totP -= $value['saldoAkhir']; 
						}
					}
					$arrTotalRL['p'] = $totP;
				}
				else{
					if ($value["normal"]=="debet"){
						if (!empty($value['saldoAkhir'])){
							$arrRugiLaba['b'][] = array("nama_rekening"=>$value['nama'],"nominal"=>$value['saldoAkhir']);
							$totB += $value['saldoAkhir']; 
						}
					}
					$arrTotalRL['b'] = $totB;
				}
				//end laporan rugi laba

				//start total rugi laba
				if ($value["normal"]=="kredit") {
					if (!empty($value['saldoAkhir'])) {
                        $laba += $value['saldoAkhir']; 
                    }
				}
				else{
					if (!empty($value['saldoAkhir'])) {
                        $laba -= $value['saldoAkhir']; 
                    }
				}
				//end total rugi laba

			}
		}
		return array($arrRugiLaba, $arrTotalRL, $laba);
	}

	function NeracaPercobaan($idperiode)
	{
		global $dbLink;

		$laba = 0;
		$a_dataNP = array();
		$arrNeracaPercobaan = array();
		$arrTotalNP = array();
		$q = "SELECT a.kodeAkun ,a.nama ,p.saldoAwal, p.mutasiD, p.mutasiK, p.saldoAkhir ,a.normal ,a.parentKodeAkun,
			CASE WHEN b.kodeAkun IS NULL THEN 0 ELSE 1 END as isParent 
			FROM akun a LEFT JOIN akun b on b.parentKodeAkun=a.kodeAkun 
			LEFT JOIN periodeakun p on a.kodeAkun=p.kodeAkun
			WHERE p.idperiode='$idperiode' ORDER BY a.kodeAkun";
		$rs = mysql_query($q, $dbLink);
		while ($rowa = mysql_fetch_array($rs)) {
			$arrNeracaPercobaan[$rowa['kodeAkun']]['nama'] = $rowa['nama'];
            if ($rowa["normal"]=="kredit") {
				$arrNeracaPercobaan[$rowa['kodeAkun']]["saldoAwalD"] = $rowa["saldoAwal"];
				$arrNeracaPercobaan[$rowa['kodeAkun']]["saldoAkhirD"] = $rowa["saldoAkhir"];
				$arrTotalNP["saldoAwalD"] += $rowa["saldoAwal"];
				$arrTotalNP["saldoAkhirD"] += $rowa["saldoAkhir"];
			}
			else{
				$arrNeracaPercobaan[$rowa['kodeAkun']]["saldoAwalK"] = $rowa["saldoAwal"];
				$arrNeracaPercobaan[$rowa['kodeAkun']]["saldoAkhirK"] = $rowa["saldoAkhir"];
				$arrTotalNP["saldoAwalK"] += $rowa["saldoAwal"];
				$arrTotalNP["saldoAkhirK"] += $rowa["saldoAkhir"];
			}
				$arrNeracaPercobaan[$rowa['kodeAkun']]["mutasiD"] = $rowa["mutasiD"];
				$arrNeracaPercobaan[$rowa['kodeAkun']]["mutasiK"] = $rowa["mutasiK"];
				$arrTotalNP["mutasiD"] += $rowa["mutasiD"];
				$arrTotalNP["mutasiK"] += $rowa["mutasiK"];
		}
		return array($arrNeracaPercobaan, $arrTotalNP);
	}

	function formatNumber($num,$ndec=0){
		if($num == '')
			return '0';
		
		if($num < 0) {
			$num = abs($num);
			$bracket = true;
		}

		$num = number_format($num,$ndec,',','.');
		
		if($bracket)
			return '('.$num.')';
		else
			return $num;
	}

}