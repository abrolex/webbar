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

	if(!empty($_GET))
	{
		if(empty($_GET['csrf_token']))
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Es wurde kein Token gesendet.</p>';
			$output .= '</div>';
		}
		else
		{
			if(preg_match('/[^a-zA-Z0-9]/',$_GET['csrf_token']) == 0)
			{
				if($_SESSION['user_csrf_token'] == $_GET['csrf_token'])
				{
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
						SELECT user_credit,user_cart,user_location_id
						FROM user
						WHERE user_id = '%s';",
						$sql->real_escape_string($_SESSION['user_id']));
						
						$result = $sql->query($query);
						
						if($row = $result->fetch_array(MYSQL_ASSOC))
						{
							$user_credit = $row['user_credit'];
							
							$location_id = $row['user_location_id'];

							$cart = json_decode($row['user_cart'],true);
							
							$cart_count = count($cart);
							
							if(!empty($cart))
							{
								$price_g = 0.00;
								
								$order_cart = array();
								
								for($i = 0; $i < $cart_count; $i++)
								{	
									$article_id = $cart[$i]['article_id'];
									
									$variant_id = $cart[$i]['variant_id'];
									
									$amount = $cart[$i]['amount'];
									
									$query = sprintf("
									SELECT article_name,article_variant,article_price
									FROM article
									WHERE article_id = '%s';",
									$sql->real_escape_string($article_id));
									
									$result = $sql->query($query);
									
									if($row = $result->fetch_array(MYSQLI_ASSOC))
									{
										$article_name = $row['article_name'];
										
										$variant_arr = explode('/',$row['article_variant']);
										
										$price_arr = explode('/',$row['article_price']);

										$variant = $variant_arr[$variant_id];
									
										$price = $price_arr[$variant_id];
										
										$article = array('id' => $article_id,'name' => $article_name,'variant' => $variant,'price' => $price,'amount' => $amount);
										
										$price_g = number_format($amount*$price+$price_g,2,'.','.');
										
										array_push($order_cart,$article);
									}
									else
									{
										$output  = '<div class="w3-panel w3-border w3-border-red w3-text-red">';
										$output .= '<p>Einige Artikel wurden aus ihrem Warenkorb entfernt.</p>';
										$output .= '</div>';
									}
									
									unset($cart[$i]);
								}
								
								$cart_count = count($cart);
								
								$credit_after = $user_credit-$price_g;
								
								if($credit_after >= 0.00)
								{
									$query = sprintf("
									UPDATE user
									set user_credit = '%s',
									user_cart = '%s'
									WHERE user_id = '%s';",
									$sql->real_escape_string($credit_after),
									$sql->real_escape_string(json_encode($cart)),
									$sql->real_escape_string($_SESSION['user_id']));
									
									$sql->query($query);
									
									if($sql->affected_rows == 1)
									{
										$query = sprintf("
										INSERT INTO orders
										(order_user_id,order_location_id,order_cart)
										VALUES
										('%s','%s','%s');",
										$sql->real_escape_string($_SESSION['user_id']),
										$sql->real_escape_string($location_id),
										$sql->real_escape_string(json_encode($order_cart)));
										
										$sql->query($query);
										
										if($sql->affected_rows == 1)
										{
											$output .= '<div class="w3-section w3-center">';
											$output .= '<div class="w3-circle w3-border w3-border-green w3-text-green" style="display:inline-block;width:80px;height:80px;line-height:90px;">';
											$output .= '<i class="fas fa-check fa-2x"></i>';
											$output .= '</div>';
											$output .= '</div>';
											$output .= '<div class="w3-panel w3-center w3-border w3-border-green w3-text-green">';
											$output .= '<p>Ihre Bestellung wurde erfolgreich entgegen genommen und wird bald bearbeitet :)</p>';
											$output .= '</div>';
											$output .= '<p><a class="w3-btn w3-block w3-padding-large blue" href="/order/?s=0&ps=5">Meine Bestellungen <i class="fas fa-list"></i></a></p>';
										}
										else
										{
											$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
											$output .= '<p>Ihre Bestellung konnte nicht entgegen genommen werden.</p>';
											$output .= '</div>';
										}
									}
									else
									{
										$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
										$output .= '<p>Ihre Bestellung konnte nicht entgegen genommen werden.</p>';
										$output .= '</div>';
									}
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
						else
						{
							$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
							$output .= '<p>Es wurde kein Useraccount gefunden.</p>';
							$output .= '</div>';
						}
					}
				}
				else
				{
					$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
					$output .= '<p>Ung&uuml;ltiger Token.</p>';
					$output .= '</div>';
				}
			}
			else
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Ung&uuml;ltiger Token.</p>';
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
	
	$output .= '<p><a class="w3-btn w3-block w3-padding-large blue" href="/">Startseite <i class="fas fa-home"></i></a></p>';
}
?>	
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Checkout</title>
		<?php
		require($_SERVER['DOCUMENT_ROOT'].'/include/head.inc.php');
		?>
	</head>
	<body class="gradient-blue">
		<div id="sidebar-overlay" class="overlay">
			<div class="w3-sidebar w3-animate-left dark">
				<button onclick="w3.addStyle('#sidebar-overlay','display','none');" class="w3-btn"><i class="fas fa-times fa-2x"></i></button>
				<div class="w3-container">
					<p><a class="w3-btn w3-block w3-padding-large" href="/admin/">Admin</a></p>
					<p><a class="w3-btn w3-block w3-padding-large active" href="#">User</a></p>
				</div>
			</div>
		</div>
		<button onclick="w3.addStyle('#sidebar-overlay','display','block');" class="w3-btn"><i class="fas fa-bars fa-2x"></i></button>
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
		<script src="https://www.w3schools.com/lib/w3.js"></script>
	</body>
</html>