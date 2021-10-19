<?php
session_start();

session_regenerate_id();

if(empty($_SESSION['user_login']))
{
	header('location:http://'.$_SERVER['HTTP_HOST'].'/login/');
	exit;
}
else
{
	require($_SERVER['DOCUMENT_ROOT'].'/include/config.inc.php');

	$output = '';

	$sql = mysqli_connect($app_sqlhost,$app_sqluser,$app_sqlpasswd,$app_sqldb);

	if(!$sql)
	{
		$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
		$output .= '<p>Es konnte keine Datenbankverbindung hergestellt werden.</p>';
		$output .= '</div>';
	}
	else
	{
		$query = sprintf("
		SELECT user_username,user_email,user_credit,user_cart,user_location_id,location_name
		FROM user
		INNER JOIN location ON user_location_id = location_id
		WHERE user_id = '%s';",
		$sql->real_escape_string($_SESSION['user_id']));
		
		$result = $sql->query($query);
		
		if($row = $result->fetch_array(MYSQLI_ASSOC))
		{
			$user_credit = $row['user_credit'];
			
			$cart = json_decode($row['user_cart'],true);
				
			$output .= '<h4>Account</h4>';
			$output .= '<p>Username<button class="w3-btn w3-block w3-padding-large w3-left-align w3-border grey">'.$row['user_username'].'</button></p>';
			$output .= '<p>E-Mail-Adresse<button class="w3-btn w3-block w3-padding-large w3-left-align w3-border grey">'.$row['user_email'].'</button></p>';
			
			if($row['user_location_id'] != 1)
			{
				$output .= '<p>Lokation<button class="w3-btn w3-block w3-padding-large w3-left-align w3-border grey">'.$row['location_name'].'</button></p>';
			}
			else
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Sie haben noch keinen Tisch gew&auml;hlt.</p>';
				$output .= '</div>';
			}
			
			$output .= '<p><a class="w3-btn w3-padding-large blue" href="/user/">&auml;ndern <i class="fas fa-edit"></i></a></p>';
			
			if(!empty($cart))
			{
				$cart_count = count($cart);
				
				$price_g = 0.00;
				
				$output .= '<h4>'.$cart_count.' Artikel im Warenkorb <a href="/cart/"><i class="fas fa-edit"></i></a></h4>';
				
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
						$variant_arr = explode('/',$row['article_variant']);
				
						$price_arr = explode('/',$row['article_price']);
				
						$price = number_format($price_arr[$cart[$i]['article_variant']]*$cart[$i]['article_amount'],2,'.','.');
				
						$output .= '<p class="w3-large">'.$row['article_name'].'</p>';
						$output .= '<div class="w3-section">';
						$output .= 'Anzahl & Variante';
						$output .= '<div class="w3-row-padding" style="padding:0;">';
						$output .= '<div class="w3-col s6 m6 l6" style="padding-left:0;">';
						$output .= '<button class="w3-btn w3-block w3-border">'.$cart[$i]['article_amount'].'x</button>';
						$output .= '</div>';
						$output .= '<div class="w3-col s6 m6 l6" style="padding-right:0;">';
						$output .= '<button class="w3-btn w3-block w3-border">'.$variant_arr[$cart[$i]['article_variant']].' '.$price_arr[$cart[$i]['article_variant']].' &euro;</button>';
						$output .= '</div>';
						$output .= '</div>';
						$output .= '<p><button class="w3-btn w3-block w3-padding-large w3-border grey">Artikelpreis: '.$price.' &euro;</button></p>';
						
						$price_g = number_format($price+$price_g,2,'.','.');
					}
				}
				
				$output .= '<p><button class="w3-btn w3-block w3-padding-large w3-border grey">Summe: '.$price_g.' &euro;</button></p>';
				
				$credit_after = $user_credit-$price_g;
				
				if($credit_after >= 0.00)
				{
					$output .= '<p><a class="w3-btn w3-block w3-padding-large blue" href="/order/checkout.php?csrf_token='.$_SESSION['user_csrf_token'].'">Bestellung absenden <i class="fas fa-arrow-right"></i></a></p>';
				}
				else
				{
					$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
					$output .= '<p>Ihr Guthaben reicht nicht aus.</p>';
					$output .= '</div>';
				}
			}
			else
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Ihr Warenkorb ist leer :(</p>';
				$output .= '</div>';
			}
		}
	}
}
?>		
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Bestellung pr&uuml;fen</title>
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
					<a class="w3-bar-item w3-btn" href="/cart/"><i class="fas fa-shopping-cart fa-2x"></i> 
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