<?php
session_start();

session_regenerate_id();

if(empty($_SESSION['user_login']))
{
	header('location:http://'.$_SERVER['HTTP_HOST'].'/login.php');
	exit;
}
else
{
	require($_SERVER['DOCUMENT_ROOT'].'/include/config.inc.php');

	$output = '';

	if(!empty($_GET))
	{
		if(empty($_GET['article_id']))
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Es wurde keine ArtikelID gesendet.</p>';
			$output .= '</div>';
		}
		else
		{
			if(preg_match('/[^0-9]/',$_GET['article_id']) == 0)
			{
				$sql = mysqli_connect($app_sqlhost,$app_sqluser,$app_sqlpasswd,$app_sqldb);
				
				if(!$sql)
				{
					$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
					$output .= '<p>Es wurde keine ArtikelID gesendet.</p>';
					$output .= '</div>';
				}
				else
				{
					$query = sprintf("
					SELECT article_id,article_name,article_variant,article_price
					FROM article
					WHERE article_id = '%s';",
					$sql->real_escape_string($_GET['article_id']));
					
					$result = $sql->query($query);
					
					if($row = $result->fetch_array(MYSQLI_ASSOC))
					{
						$output .= '<h3>'.$row['article_name'].'</h3>';
						$output .= '<form action="/cart/add.php" method="get">';
						$output .= '<p><input type="hidden" name="article_id" value="'.$row['article_id'].'"/></p>';
						$output .= '<div class="w3-row-padding w3-section">';
						$output .= '<div class="w3-col s6 m6 l6">';
						$output .= '<label for"article_variant">Variante</label>';
						$output .= '<select class="w3-select w3-border w3-white" name="article_variant">';
						
						for($i = 0; $i < count($variant_arr); $i++)
						{
						  $output .= '<option value="'.$i.'">'.$variant_arr[$i].' '.$price_arr[$i].'</option>';
						}
						
						$output .= '</select></div>';
						$output .= '<div class="w3-col s6 m6 l6">';
						$output .= '<label for"article_amount">Anzahl</label>';
						$output .= '<select class="w3-select w3-border w3-white" name="article_amount">';
						
						for($i = 1; $i <= 99; $i++)
						{
						  $output .= '<option value="'.$i.'">'.$i.'x</option>';
						}
						
						$output .= '</select></div></div>';
						$output .= '<p><button class="w3-btn w3-block w3-padding-large blue" type="submit">in den Warenkorb <i class="fas fa-plus"></i></button></p>';
						$output .= '</form>';
					}
				}
			}
			else
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Die ArtikelID besteht nur aus Zahlen.</p>';
				$output .= '</div>';
			}
		}
	}
	else
	{
		$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
		$output .= '<p>Es wurde keine ArtikelID gesendet.</p>';
		$output .= '</div>';
	}
}
?>
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Artikel anzeigen</title>
		<?php
		require($_SERVER['DOCUMENT_ROOT'].'/include/head.inc.php');
		?>
	</head>
	<body class="gradient-blue">
		<button class="w3-btn"><i class="fas fa-bars fa-2x"></i></button>
		<div class="w3-content" style="max-width:500px;margin-top:20vh;">
			<div class="w3-center">
				<h2>WebBar</h2>
				<div class="w3-bar">
					<a class="w3-bar-item w3-btn" href="/"><i class="fas fa-home fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="/user/"><i class="fas fa-user fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="/cart/"><i class="fas fa-shopping-cart fa-2x"></i></a>
				</div>
			</div>
			<div class="w3-container">
				<div class="w3-panel w3-white">
					<form action="/search/" method="get">
						<div class="w3-row w3-section">
							<div class="w3-col s8 m8 l8">
								<input class="w3-input w3-border" type="text" name="search" placeholder="Artikel suchen"/>
								<input type="hidden" name="s" value="0"/>
								<input type="hidden" name="ps" value="5"/>
							</div>
							<div class="w3-col s4 m4 l4">
								<button class="w3-btn w3-block w3-border border-blue blue" type="submit"><i class="fas fa-search"></i></button>
							</div>
						</div>
					</form>
				</div>
				<div class="w3-panel w3-white">
				<?php
				if(!empty($output))
				{
					echo $output;
				}
				?>
				</div>
			</div>
		</div>
	</body>
</html>