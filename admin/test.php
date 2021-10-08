<?php
require($_SERVER['DOCUMENT_ROOT'].'/include/functions.inc.php');

$password = 'Webbar_2021!';

$salt = randomstr(10);

$passwdhash = passwdhash($salt,$password);

echo $passwdhash.' '.$salt;
?>