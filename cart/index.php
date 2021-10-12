<?php
require($_SERVER['DOCUMENT_ROOT'].'/include/config.inc.php');

$sql = mysqli_connect($app_sqlhost,$app_sqluser,$app_sqlpasswd,$app_sqldb);

if(!$sql)
{
	$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
	$output .= '<p>Es konnte keine Datenbankverbindung hergestellt werden.</p>';
	$output .= '</div>';
}
else
{
	session_start();

	session_regenerate_id();

	if(!empty($_SESSION['user_login']))
	{	
		require($_SERVER['DOCUMENT_ROOT'].'/include/user_cart.inc.php');
	}
	else
	{
		require($_SERVER['DOCUMENT_ROOT'].'/include/randomstr.inc.php');
		
		require($_SERVER['DOCUMENT_ROOT'].'/include/cookie_cart.inc.php');
	}

	$output = '';
	
	if(!empty($cart))
	{
		$output .= '<h4>'.$cart_count.' Artikel im Warenkorb</h4>';
		
		for($i = 0;$i < $cart_count;$i++)
		{
			$query = sprintf("
			SELECT article_name,article_variant,article_price
			FROM article
			WHERE article_id = '%s';",
			$sql->real_escape_string($cart[$i]['article_id']));
			
			$result = $sql->query($query);
			
			if($row = $result->fetch_array(MYSQLI_ASSOC))
			{
				/*$variant_arr = explode('/',$row['article_variant']);
				
				$price_arr = explode('/',$row['article_price']);
				
				$price = number_format($price_arr[$cart[$i]['article_variant']]*$cart[$i]['article_amount'],2,'.','.');
				
				$output .= '<p class="w3-large"><a href="del.php?article_id='.$i.'"><i class="fas fa-times"></i></a> '.$row['article_name'].' '.$variant_arr[$cart[$i]['article_variant']].'</p>';
				$output .= '<div class="w3-section">';
				$output .= 'Anzahl';
				$output .= '<div class="w3-row">';
				$output .= '<div class="w3-col s2 m2 l2">';
				$output .= '<a class="w3-btn w3-block w3-border border-blue blue"><i class="fas fa-minus"></i></a>';
				$output .= '</div>';
				$output .= '<div class="w3-col s2 m2 l2">';
				$output .= '<input class="w3-input w3-border" value="'.$cart[$i]['article_amount'].'"/>';
				$output .= '</div>';
				$output .= '<div class="w3-col s2 m2 l2">';
				$output .= '<a class="w3-btn w3-block w3-border border-blue blue"><i class="fas fa-plus"></i></a>';
				$output .= '</div>';
				$output .= '<div class="w3-col s6 m6 l6">';
				$output .= '<button class="w3-btn w3-block w3-border grey">Preis: '.$price.' &euro;</button>';
				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
			}
		}
	}
}		
?>
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Warenkorb anzeigen</title>
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
					<a class="w3-bar-item w3-btn active" href="#"><i class="fas fa-shopping-cart fa-2x"></i> 
					<?php
					if(!empty($cart_count))
					{
						echo $cart_count;
					}
					?>
					</a>
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