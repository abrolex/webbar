<?php
session_start();

session_regenerate_id();

if(empty($_SESSION['admin_login']))
{
	header('location:http://'.$_SERVER['HTTP_HOST'].'/admin/login/');
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
		<div id="sidebar-overlay" class="overlay">
			<div class="w3-sidebar w3-animate-left dark">
				<button onclick="w3.addStyle('#sidebar-overlay','display','none');" class="w3-btn"><i class="fas fa-times fa-2x"></i></button>
				<div class="w3-container">
					<p><a class="w3-btn w3-block w3-padding-large active" href="#">Admin</a></p>
					<p><a class="w3-btn w3-block w3-padding-large" href="/">User</a></p>
				</div>
			</div>
		</div>
		<button onclick="w3.addStyle('#sidebar-overlay','display','block');" class="w3-btn"><i class="fas fa-bars fa-2x"></i></button>
		<div class="w3-content" style="max-width:500px;margin-top:15vh;">
			<div class="w3-center">
				<div class="w3-bar">
					<a class="w3-bar-item w3-btn active" href="/admin/"><i class="fas fa-home fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="/admin/user/?s=0&ps=5"><i class="fas fa-user fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="/admin/article/?s=0&ps=5"><i class="fas fa-cube fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="/admin/order/?s=0&ps=5&state=0"><i class="fas fa-list fa-2x"></i></a>
				</div>
			</div>
			<div class="w3-container">
				<div class="w3-panel w3-center w3-white">
					<h2>Admin</h2>
				</div>
			</div>
		</div>
	</body>
</html>