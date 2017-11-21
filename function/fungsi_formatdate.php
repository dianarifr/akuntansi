<?php
	function formatDate_id($value="01/01/1970", $pemisah="-", $hurufbesar=false, $formatbulan="MM", $format="dd/mm/yyyy")
	{
		$tgl = substr($value, 0, 2);
		$bulan = substr($value, 3, 2);
		$tahun = substr($value, 6, 4);
		
		if($formatbulan=="MM")
		{
			$bulan = namaBulan_id($bulan);
			if($hurufbesar)
				$bulan=strtoupper($bulan);
		}
		elseif($formatbulan=="M")
		{
			$bulan = substr(namaBulan_id($bulan), 0, 3);
			if($hurufbesar)
				$bulan=strtoupper($bulan);
		}
		
		if($format=='d/mm/yyyy')
		{
			if(substr($tgl, 0, 1)=="0")
				$tgl = substr($tgl, 1, 1);
			return $tgl.$pemisah.$bulan.$pemisah.$tahun;
		}
		elseif($format=="mm/yyyy")
			return $bulan.$pemisah.$tahun;
		elseif($format=="mm/dd/yyyy")
			return $bulan.$pemisah.$tgl.$tahun;
		elseif($format=="m/d/yyyy")
		{
			if(substr($tgl, 0, 1)=="0")
				$tgl = substr($tgl, 1, 1);
			if(substr($bulan, 0, 1)=="0")
				$tgl = substr($bulan, 1, 1);
			return $bulan.$pemisah.$tgl.$tahun;
		}
		else
			return $tgl.$pemisah.$bulan.$pemisah.$tahun;
		
	}
	
	function formatDate_en($value="01/01/1970", $pemisah="-", $hurufbesar=false, $formatbulan="MM", $format="dd/mm/yyyy")
	{
		$tgl = substr($value, 0, 2);
		$bulan = substr($value, 3, 2);
		$tahun = substr($value, 6, 4);
		
		if($formatbulan=="MM")
			$bulan = namaBulan_en($bulan);
		elseif($formatbulan=="M")
			$bulan = substr(namaBulan_en($bulan), 0, 3);
		
		if($hurufbesar)
			$bulan=strtoupper($bulan);
		
		if($format=='y/m/d')		
			return $tahun.$pemisah.$bulan.$pemisah.$tgl;
		elseif($format=='d/m/yyyy')
		{
			if(substr($tgl, 0, 1)=="0")
				$tgl = substr($tgl, 1, 1);
			
			if(substr($bulan, 0, 1)=="0")
				$tgl = substr($bulan, 1, 1);	
			return $tahun.$pemisah.$bulan.$pemisah.$tgl;
		}
		elseif($format=="MM d, yyyy")
			return $bulan." ".$tgl.", ".$tahun;
		elseif($format=='m/d/y')
		{
			if($formatbulan=='MM' || $formatbulan=='M')
				return $bulan.$pemisah.$tgl.", ".$tahun;
			else
				return $bulan.$pemisah.$tgl.$pemisah.$tahun;
		}
		else
			return $tgl.$pemisah.$bulan.$pemisah.$tahun;		
	}
	
	function namaBulan_id($bulan="01")
	{
		switch($bulan)
		{
			case '01':
				$nama = "Januari";
				break;
			case '02':
				$nama = "Februari";
				break;
			case '03':
				$nama = "Maret";
				break;
			case '04':
				$nama = "April";
				break;				
			case '05':
				$nama = "Mei";
				break;
			case '06':
				$nama = "Juni";
				break;
			case '07':
				$nama = "Juli";
				break;
			case '08':
				$nama = "Agustus";
				break;
			case '09':
				$nama = "September";
				break;
			case '10':
				$nama = "Oktober";
				break;
			case '11':
				$nama = "November";
				break;
			case '12':
				$nama = "Desember";
				break;			
			default:
				$nama = "Error";
				break;																						
		}
		return $nama;
	}
	
	function namaBulan_en($bulan="01")
	{
		switch($bulan)
		{
			case '01':
				$nama = "January";
				break;
			case '02':
				$nama = "February";
				break;
			case '03':
				$nama = "March";
				break;
			case '04':
				$nama = "April";
				break;				
			case '05':
				$nama = "May";
				break;
			case '06':
				$nama = "June";
				break;
			case '07':
				$nama = "July";
				break;
			case '08':
				$nama = "August";
				break;
			case '09':
				$nama = "September";
				break;
			case '10':
				$nama = "October";
				break;
			case '11':
				$nama = "November";
				break;
			case '12':
				$nama = "December";
				break;			
			default:
				$nama = "Error";
				break;																						
		}
		return $nama;
	}
	
	function namaHari($hari = '0')
	{
		switch($hari)
		{
			case '0':
				$nama = "Minggu";
				break;
			case '1':
				$nama = "Senin";
				break;
			case '2':
				$nama = "Selasa";
				break;
			case '3':
				$nama = "Rabu";
				break;				
			case '4':
				$nama = "Kamis";
				break;
			case '5':
				$nama = "Jumat";
				break;
			case '6':
				$nama = "Sabtu";
				break;		
			default:
				$nama = "Error";
				break;																						
		}
		return $nama;
	}
	
	function cekFormatTanggal( $tanggal, $format= "ind" )
	{
		if(substr_count($tanggal,"-") > 0 )
		{
			$delimiter = "-";
		}
		else if(substr_count($tanggal,"/") > 0 )
		{
			$delimiter = "/";
		}
		else
		{
			return false;
		}
		$data = explode($delimiter,$tanggal,3);
		$tgl = $data[0];
		$bln = $data[1];
		$thn = $data[2];
		
		if( $bln == "1" || $bln == "3" || $bln == "5" || $bln == "7" || $bln == "8" || $bln == "10" || $bln == "12")
		{ $maxHari = 31; }
		else if( $bln == "4" || $bln == "6" || $bln == "9" || $bln == "11" )
		{ $maxHari = 30; }
		else if( $bln == "2" )
		{
			if( $thn%4 == 0 )
			{ $maxHari = 29; }
			else
			{ $maxHari = 28; }
		}
		else
		{ return false; }
		 
		if( $tgl > $maxHari )
		{ return false; }
		
		if( strlen($thn) == 4 )
		{
			if(substr($thn,0,1) <= 0 )
			{ return false; }
		}
		else
		{ return false; }
		
		if(strlen($tgl) < 2)
		{ $tgl = "0".$tgl; }
		if(strlen($bln) < 2)
		{ $bln = "0".$bln; }
		
		if( $format == "en" )
		{
			$returnTanggal = $thn.$delimiter.$bln.$delimiter.$tgl;
		}
		else if($format == "ind" )
		{
			$returnTanggal = $tgl.$delimiter.$bln.$delimiter.$thn;
		}
		else
		{
			$returnTanggal = $thn.$delimiter.$bln.$delimiter.$tgl;
		}
		
		return $returnTanggal;
	}
	
	function bulanRomawi($bulan = '1')
	{
		if($bulan=='1' || $bulan=='01')
			$romawi = "I";
		elseif($bulan=='2' || $bulan=='02')
			$romawi = "II";
		elseif($bulan=='3' || $bulan=='03')
			$romawi = "III";
		elseif($bulan=='4' || $bulan=='04')
			$romawi = "IV";
		elseif($bulan=='5' || $bulan=='05')
			$romawi = "V";
		elseif($bulan=='6' || $bulan=='06')
			$romawi = "VI";
		elseif($bulan=='7' || $bulan=='07')
			$romawi = "VII";
		elseif($bulan=='8' || $bulan=='08')
			$romawi = "VIII";
		elseif($bulan=='9' || $bulan=='09')
			$romawi = "IX";
		elseif($bulan=='10')
			$romawi = "X";
		elseif($bulan=='11')
			$romawi = "XI";
		elseif($bulan=='12')
			$romawi = "XII";
		else
			$romawi = "ERR";
			
		return $romawi;
	}
        
        function datetomysql($datecheck) {

            list($Day, $Month, $Year) = split("-", $datecheck);

            $stampeddate = mktime(12, 0, 0, (int) $Month, (int) $Day, (int) $Year);
            if (checkdate((int) $Month, (int) $Day, (int) $Year)) {
                return date("Y-m-d", $stampeddate);
            } else {
                return 0; //not ok
            }
        }
        
        function datetoind($datecheck) {

            list($Year, $Month, $Day) = split("-", $datecheck);

            $stampeddate = mktime(12, 0, 0, (int) $Month, (int) $Day, (int) $Year);
            if (checkdate((int) $Month, (int) $Day, (int) $Year)) {
                return date("d-m-Y", $stampeddate);
            } else {
                return 0; //not ok
            }
        }
?>
