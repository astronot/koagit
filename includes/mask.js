// Copyright 2001, 2003 InterAKT Online. All rights reserved.

function formatCurrency(num) {
num = num.toString().replace(/\$|\,/g,'');
if(isNaN(num))
num = "0";
sign = (num == (num = Math.abs(num)));
num = Math.floor(num*100+0.50000000001);
cents = num%100;
num = Math.floor(num/100).toString();
if(cents<10)
cents = "0" + cents;
for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
num = num.substring(0,num.length-(4*i+3))+','+
num.substring(num.length-(4*i+3));
return (((sign)?'':'-') + '$' + num + '.' + cents);
}

function editMaskPre(obj, mask, evt) {
	var keyCode = evt.keyCode;
	if (keyCode == 0) {
		keyCode = evt.charCode;
	}
	if (obj.value.length == 0 && keyCode != 8 && keyCode != 0 && keyCode!= 9) {
		completeMask(obj, mask);
	}
}

function toregexp(txt) {
	txt = txt.replace(/([-\/\[\]()\*\+])/g, '\\$1');
	txt = txt.replace(/N/g, '\\d');
	txt = txt.replace(/\?/g, '.');
	txt = txt.replace(/A/g, '\\w');
	txt = txt.replace(/C/g, '[A-Za-z]');
	return txt;
}

function editMask(obj, mask, evt) {
	var tmVal = getFirstMatch(obj.value, mask);
	if (obj.value != tmVal) {
		obj.value = tmVal;
	}
	if(evt.keyCode != 8 && obj.value.length != 0) { // backspace and tab
		completeMask(obj, mask);
	}
}

function getFirstMatch(value, mask) {
	var size = value.length;
	if(size == 0) {
		return "";
	}
	var re = new RegExp('^' + toregexp(mask.substr(0, size)) + '$');
	if (!value.match(re)) { 
		return getFirstMatch(value.substr(0, size-1), mask);
	} else {
		return value;
	}
}


function completeMask(obj, mask) {
	var size = obj.value.length;
	var sw=true;
	var tmp = obj.value;
	while (sw) {
		if (mask.length<=size) {
			break;
		}
		switch (mask.charAt(size)) {
			case 'N':
			case 'A':
			case 'C':
			case '?':
				sw = false;
				break;
			default:
				tmp += mask.charAt(size);
		}
		size++;
	}
	if (obj.value != tmp) {
		obj.value = tmp;
		obj.lastMatched = obj.value;
	}
	return;
}





