<!-- Script untuk validasi waktu -->

function IsNumeric(strString) 
{ 
    var strValidChars = "0123456789"; 
    var strChar; 
    var blnResult = true; 

    if (strString.length == 0) 
        return false; 
    for (i = 0; i < strString.length && blnResult == true; i++) 
    { 
        strChar = strString.charAt(i); 
        if (strValidChars.indexOf(strChar) == -1) 
        { 
            blnResult = false; 
        } 
     } 
return blnResult; 
}
function trimString(str) 
{ 
     var str1 = ''; 
     var i = 0; 
     while ( i != str.length) 
     { 
         if(str.charAt(i) != ' ') str1 = str1 + str.charAt(i); i++; 
     }
     var retval = IsNumeric(str1); 
     if(retval == false) 
         return -100; 
     else 
         return str1; 
}
function trimAllSpace(str) 
{ 
    var str1 = ''; 
    var i = 0; 
    while(i != str.length) 
    { 
        if(str.charAt(i) != ' ') 
            str1 = str1 + str.charAt(i); i ++; 
    } 
    return str1; 
}
function isTime(strval)
{
  var strval1;
  // TIME FORMAT HH:MM
  
  if(strval.length < 5)
  {
   alert("Format jam tidak valid. Format jam HH:MM.");
   return false;
  }

  if(strval.length > 5)
  {
   alert("Format jam tidak valid. Format jam HH:MM.");
   return false;
  }
  
  //Removing all space
  strval = trimAllSpace(strval); 
      
  var pos1 = strval.indexOf(':');

  if(pos1 < 0 )
  {
   alert("Format waktu tidak valid. Format waktu yang sesuai = HH:MM.");
   return false;
  }
  else if(pos1 > 2 || pos1 < 1)
  {
   alert("Format waktu tidak valid. Format waktu yang sesuai = HH:MM.");
   return false;
  }
   
  //Checking hours
  var horval =  trimString(strval.substring(0,pos1));

  if(horval == -100)
  {
   alert("Format waktu tidak valid. Format waktu yang sesuai = HH:MM. Nilai jam (HH) antara 0-24.");
   return false;
  }
      
  if(horval > 24)
  {
   alert("Format waktu tidak valid. Format waktu yang sesuai = HH:MM. Nilai jam (HH) tidak dapat lebih besar dari 24.");
   return false;
  }
  
  else if(horval < 0)
  {
   alert("Format waktu tidak valid. Format waktu yang sesuai = HH:MM. Nilai jam (HH) tidak dapat lebih kecil dari 0.");
   return false;
  }
  
  //Checking minutes.
  var minval =  trimString(strval.substring(pos1+1,pos1 + 3));

  if(minval == -100)
  {
   alert("Format waktu tidak valid. Format waktu yang sesuai = HH:MM. Nilai menit (MM) antara 0-59.");
   return false;
  }
    
  if(minval > 59)
  {
     alert("Format waktu tidak valid. Format waktu yang sesuai = HH:MM. Nilai menit (MM) tidak dapat lebih besar dari 59.");
     return false;
  }   
  
  else if(minval < 0)
  {
   alert("Format waktu tidak valid. Format waktu yang sesuai = HH:MM. Nilai menit (MM) tidak dapat lebih kecil dari 0.");
   return false;
  }
  
  if(horval==24 && minval >0)
  {
  	alert("Format waktu tidak valid. Format waktu yang sesuai = HH:MM. Waktu maksimal adalah 24:00.");
   return false;
  }
  
  return true;
}
function compareTime(strval1, strval2)
{
	strval1 = trimAllSpace(strval1);
	var pos1 = strval1.indexOf(':');
	var horval1 =  trimString(strval1.substring(0,pos1));
	var minval1 =  trimString(strval1.substring(pos1+1,pos1 + 3));
	
	strval2 = trimAllSpace(strval2);
	var pos2 = strval2.indexOf(':');
	var horval2 =  trimString(strval2.substring(0,pos2));
	var minval2 =  trimString(strval2.substring(pos2+1,pos2 + 3));
	
	
	// TRUE CONDITION IF strval1 IS GREATER THAN strval2
	if(parseInt(horval1.replace(/^0+/g, '')) > parseInt(horval2.replace(/^0+/g, '')))
	{ 	
		return true;
	}
	else if(parseInt(horval1.replace(/^0+/g, '')) == parseInt(horval2.replace(/^0+/g, '')))
	{
		if(parseInt(minval1.replace(/^0+/g, '')) > parseInt(minval2.replace(/^0+/g, '')))
		{ return true; }
		else
		{ return false; }
	}
	else
	{
		return false;
	}
}