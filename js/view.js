const startEdit = (x) => {

var fieldTypes = ['text','email'];

var fieldType = document.forms[x].attr_value.type;

if(fieldTypes.indexOf(fieldType) != -1)
{
	document.forms[x].attr_value.readOnly = false;
}
else
{
	document.forms[x].attr_value.disabled = false;
}

document.forms[x].edit_btn.style.display = 'none';

document.forms[x].cancel_btn.style.display = 'block';

document.forms[x].save_btn.style.display = 'block';

}

const cancelEdit = (x) => {

var fieldTypes = ['text','email'];

var fieldType = document.forms[x].attr_value.type;

if(fieldTypes.indexOf(fieldType) != -1)
{
	document.forms[x].attr_value.readOnly = true;
}
else
{
	document.forms[x].attr_value.disabled = true;
}

document.forms[x].edit_btn.style.display = 'block';

document.forms[x].cancel_btn.style.display = 'none';

document.forms[x].save_btn.style.display = 'none';

}

function getKeyCode(event)
{
	var key = event.keyCode;
				
	return key;
}

document.body.onkeydown = function() {
	
	if(getKeyCode(event) == 13)
	{
		return false;
	}
	
}