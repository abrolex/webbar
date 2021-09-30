<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Admin | Artikel erstellen</title>
		<?php
		require($_SERVER['DOCUMENT_ROOT'].'/include/head.inc.php');
		?>
	</head>
	<body class="gradient-blue">
		<button class="w3-btn"><i class="fas fa-bars fa-2x"></i></button>
		<div class="w3-content" style="max-width:500px;margin-top:20vh;">
			<div class="w3-center">
				<div class="w3-bar">
					<a class="w3-bar-item w3-btn" href="/admin/"><i class="fas fa-home fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="user/"><i class="fas fa-user fa-2x"></i></a>
					<a class="w3-bar-item w3-btn active" href="index.php"><i class="fas fa-cube fa-2x"></i></a>
				</div>
			</div>
			<div class="w3-container">
				<div class="w3-panel w3-white">
					<form action="add.php" method="get">
						<p><label for="article_name">Artikelname</label><input class="w3-input w3-border" type="text" name="article_name" placeholder="Cola"/></p>
						<p><label for="article_variant">Varianten</label><input class="w3-input w3-border" type="text" name="article_variant" placeholder="groß/klein"/></p>
						<p><label for="article_price">Preise in &euro;</label><input class="w3-input w3-border" type="text" name="article_price" placeholder="5.00/3.00"/></p>
						<p><button class="w3-btn w3-block w3-padding-large blue" type="submit">Artikel erstellen <i class="fas fa-plus"></i></button></p>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>