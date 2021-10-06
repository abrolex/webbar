<?php
function randomstr($length) 
{
	$str = '';
	
	$z = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	
	for($i = 0; $i < $length; $i++)
	{
		$str .= substr($z,mt_rand(0,strlen($z)-1),1);
	}
	
	return $str;
}

function passwdhash($salt,$password)
{
	$passwdhash = hash('sha256',$salt.$password);
	
	return $passwdhash;
}
?>
	
