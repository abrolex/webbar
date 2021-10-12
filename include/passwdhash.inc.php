<?php
function passwdhash($salt,$password)
{
	$passwdhash = hash('sha256',$salt.$password);
	
	return $passwdhash;
}
?>
	
