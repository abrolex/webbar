const startEdit = (x) => {

document.forms[x].attr_value.readOnly = false;

document.forms[x].attr_value.focus();

document.forms[x].edit_btn.style.display = 'none';

document.forms[x].cancel_btn.style.display = 'block';

document.forms[x].save_btn.style.display = 'block';

}

const cancelEdit = (x) => {

document.forms[x].attr_value.readOnly = true;

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