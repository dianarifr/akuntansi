function handleEnterEmptyField (field, event) 
{
	var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
	if (keyCode == 13) {
		var i;
		for (i = 0; i < field.form.elements.length; i++)
			if (field == field.form.elements[i])
				for(j = i+1; j< field.form.elements.length; j++)
					if(field.form.elements[j].type!="hidden")
						break;
		j = (j) % field.form.elements.length;
		field.form.elements[j].focus();
		if( field.form.elements[j].value == "0")
		{
			field.form.elements[j].value="";
		}
		
		return false;
	} 
	else
	return true;
}
