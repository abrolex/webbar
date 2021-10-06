<?php
session_start();

session_regenerate_id();

if(empty($_SESSION['admin_login']))
{
	header('location:http://'.$_SERVER['HTTP_HOST'].'/admin/login.php');
	exit;
}
?>
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Admin | Home</title>
		<?php
		require($_SERVER['DOCUMENT_ROOT'].'/include/head.inc.php');
		?>
	</head>
	<body class="gradient-blue">
		<button class="w3-btn"><i class="fas fa-bars fa-2x"></i></button>
		<div class="w3-content" style="max-width:500px;margin-top:20vh;">
			<div class="w3-center">
				<div class="w3-bar">
					<a class="w3-bar-item w3-btn active" href="index.php"><i class="fas fa-home fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="user/"><i class="fas fa-user fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="article/"><i class="fas fa-cube fa-2x"></i></a>
				</div>
			</div>
			<div class="w3-container">
				<div class="w3-panel w3-center w3-white">
					<h2>WebBarAdmin</h2>
				</div>
			</div>
		</div>
	</body>
</html>