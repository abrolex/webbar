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
		require($_SERVER['DOCUMENT_ROOT'].'/include/user_cart.inc.php');
		
		if(!empty($_GET))
		{
			if(empty($_GET['order_id']))
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Es wurde keine Bestellnummer gesendet.</p>';
				$output .= '</div>';
			}
			else
			{
				if(preg_match('/[^0-9]/',$_GET['order_id']) == 0)
				{
					$query = sprintf("
					SELECT order_id,order_cart,order_time,order_status,location_name
					FROM orders
					INNER JOIN location ON order_location_id = location_id
					WHERE order_id = '%s'
					AND order_user_id = '%s';",
					$sql->real_escape_string($_GET['order_id']),
					$sql->real_escape_string($_SESSION['user_id']));
					
					$result = $sql->query($query);
					
					if($row = $result->fetch_array(MYSQLI_ASSOC))
					{
						$price_g = 0.00;
						
						$order_cart = json_decode($row['order_cart'],true);
						
						$order_time = date('d.m.Y H:i:s',strtotime($row['order_time']));
						
						$output .= '<div class="w3-section" style="width:100%;">';
						
						switch($row['order_status'])
						{
							case 0: $output .= '<div class="w3-red" style="width:33.3%;height:10px;"></div>'; break;
							case 1: $output .= '<div class="w3-amber" style="width:66.6%;height:10px;"></div>'; break;
							case 2: $output .= '<div class="w3-green" style="width:100%;height:10px;"></div>'; break;
						}
						
						$output .= '</div>';
						
						$output .= '<h4>Bestellnummer #'.$row['order_id'].'</h4>';
						$output .= '<div class="w3-row w3-section">';
						$output .= '<div class="w3-col s3 m2 l2">';
						$output .= '<button class="w3-btn w3-block blue"><i class="fas fa-clock"></i></button>'; 
						$output .= '</div>';
						$output .= '<div class="w3-col s9 m10 l10">';
						$output .= '<button class="w3-btn w3-block w3-left-align grey">'.$order_time.'</button>';
						$output .= '</div>';
						$output .= '</div>';
						$output .= '<div class="w3-row w3-section">';
						$output .= '<div class="w3-col s3 m2 l2">';
						$output .= '<button class="w3-btn w3-block blue"><i class="fas fa-map-marker-alt"></i></button>'; 
						$output .= '</div>';
						$output .= '<div class="w3-col s9 m10 l10">';
						$output .= '<button class="w3-btn w3-block w3-left-align grey">'.$row['location_name'].'</button>';
						$output .= '</div>';
						$output .= '</div>';
						$output .= '<div class="w3-section scroll-h">';
						
						for($i = 0; $i < count($order_cart); $i++)
						{
							$article_name = $order_cart[$i]['name'];
							
							$variant = $order_cart[$i]['variant'];
							
							$price_e = $order_cart[$i]['price'];
							
							$amount = $order_cart[$i]['amount'];
							
							$price = number_format($price_e*$amount,2,'.','.');
							
							$output .= '<div class="w3-border">';
							$output .= '<p class="w3-large">'.$article_name.'</p>';
							$output .= '<p>'.$amount.'x '.$variant.' '.$price_e.' &euro;</p>';
							$output .= '<p><button class="w3-btn w3-padding-large w3-border grey">Preis: '.$price.' &euro;</button></p>';
							$output .= '</div>';
							
							$price_g = number_format($price_g+$price,2,'.','.');
						}
						
						$output .= '</div>';
						$output .= '<p><button class="w3-btn w3-padding-large w3-block w3-border grey">Summe: '.$price_g.' &euro;</button></p>';
						$output .= '<p><a class="w3-btn w3-padding-large w3-block blue" href="/order/?s=0&ps=5">Meine Bestellungen <i class="fas fa-list"></i></a></p>';
					}
					else
					{
						$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
						$output .= '<p>Es wurde keine Bestellung gefunden.</p>';
						$output .= '</div>';
					}	
				}
				else
				{
					$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
					$output .= '<p>Die Bestellnummer besteht nur aus Zahlen.</p>';
					$output .= '</div>';
				}
			}
		}
		else
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Es wurden keine Daten gesendet.</p>';
			$output .= '</div>';
		}
	}
}
?>						
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Bestellung anzeigen</title>
		<?php
		require($_SERVER['DOCUMENT_ROOT'].'/include/head.inc.php');
		?>
	</head>
	<body class="gradient-blue">
		<button class="w3-btn"><i class="fas fa-bars fa-2x"></i></button>
		<div class="w3-content" style="max-width:500px;margin-top:15vh;">
			<div class="w3-center">
				<a href="/"><h2>WebBar</h2></a>
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
				