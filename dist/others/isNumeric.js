function isNumeric(sText)
{
var ValidChars = "0123456789.,-";
var IsNumber=true;
var Char;

var i;
for (i = 0; i < sText.length && IsNumber == true; i++) 
  { 
  Char = sText.charAt(i); 
  if (ValidChars.indexOf(Char) == -1) 
	 {
	 IsNumber = false;
	 }
  }
return IsNumber;
}

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? ',' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + '.' + '$2');
	}
	return x1 + x2;
}

//Added By Ng Kho Kim Fang, May 06, 2010
function formatNumberSeparator(obj){
var str = obj.value;
//remove non-digits
var neg = false;
if(str.substring(0,1)=="-")
	neg = true;
var reg1 = /[^\d]/g;
str = str.replace(reg1,"");
//insert thousand separators
var reg2=/(-?\d+)(\d{3})/;
while(reg2.test(str)){
str=str.replace(reg2,'$1.$2')
}
if(neg)
	str = "-"+str;
obj.value= str;
}