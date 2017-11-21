// JavaScript Document
function isDate(dateStr) {

var datePat = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
var matchArray = dateStr.match(datePat); // is the format ok?

if (matchArray == null) {
alert("Harap masukkan data tanggal dengan format dd/mm/yyyy atau dd-mm-yyyy.");
return false;
}

day = matchArray[1]; // p@rse date into variables
month = matchArray[3];
year = matchArray[5];

if (month < 1 || month > 12) { // check month range
alert("Bulan harus antara 1 dan 12.");
return false;
}

if (day < 1 || day > 31) {
alert("Tanggal harus antara 1 dan 31.");
return false;
}

if ((month==4 || month==6 || month==9 || month==11) && day==31) {
alert("Bulan "+month+" tidak memiliki 31 hari!")
return false;
}

if (month == 2) { // check for february 29th
var isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
if (day > 29 || (day==29 && !isleap)) {
alert("Bulan February " + year + " tidak memiliki " + day + " hari!");
return false;
}
}
return true; // date is valid
}