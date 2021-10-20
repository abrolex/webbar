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
		$session_status = 1;
		
		require($_SERVER['DOCUMENT_ROOT'].'/include/user_cart.inc.php');
	}
	else
	{
		require($_SERVER['DOCUMENT_ROOT'].'/include/randomstr.inc.php');
		
		require($_SERVER['DOCUMENT_ROOT'].'/include/cookie_cart.inc.php');
	}

	$output = '';
	
	if(!empty($_GET))
	{
		if(empty($_GET['article_id']) || $_GET['article_variant'] == "" || empty($_GET['article_amount']))
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Es wurde kein Artikel in den Warenkorb gelegt.</p>';
			$output .= '</div>';
		}
		else
		{
			if(preg_match('/[^0-9]/',$_GET['article_id']) == 0)
			{
				if(preg_match('/[^0-9]/',$_GET['article_variant']) == 0)
				{
					if(preg_match('/[^0-9]/',$_GET['article_amount']) == 0)
					{
						$query = sprintf("
						SELECT article_variant,article_price
						FROM article
						WHERE article_id = '%s';",
						$sql->real_escape_string($_GET['article_id']));
						
						$result = $sql->query($query);
						
						if($row = $result->fetch_array(MYSQLI_ASSOC))
						{
							$variant_arr = explode('/',$row['article_variant']);
							
							$price_arr = explode('/',$row['article_price']);
							
							if(array_key_exists($_GET['article_variant'],$variant_arr) && array_key_exists($_GET['article_variant'],$price_arr))
							{
								$article = array('article_id' => $_GET['article_id'],'article_variant' => $_GET['article_variant'],'article_amount' => $_GET['article_amount']);
								
								$break = 0;
								
								if(!empty($cart))
								{
									for($i = 0; $i < $cart_count; $i++)
									{
										$article_id = $cart[$i]['article_id'];
										
										$article_variant = $cart[$i]['article_variant'];
										
										$article_amount = $cart[$i]['article_amount'];
										
										if($_GET['article_id'] == $article_id && $_GET['article_variant'] == $article_variant)
										{
											$new_article_amount = $article_amount+$_GET['article_amount'];
											
											if($new_article_amount > 99)
											{
												$break = 1;
												
												break;
											}
											else
											{
												$cart[$i]['article_amount'] = $new_article_amount;
												
												$break = 2;
												
												break;
											}
										}
									}
								}
								
								switch($break)
								{
									case 0:
										
										array_push($cart,$article);
										
										$cart_count = count($cart);
										
										if(!empty($session_status))
										{
											$query = sprintf("
											UPDATE user
											set user_cart = '%s'
											WHERE user_id = '%s';",
											$sql->real_escape_string(json_encode($cart)),
											$sql->real_escape_string($_SESSION['user_id']));
											
											$sql->query($query);
											
											if($sql->affected_rows == 1)
											{
												$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
												$output .= '<p>Der Artikel wurde erfolgreich in ihren Warenkorb gelegt.</p>';
												$output .= '</div>';
											}
										}
										else
										{
											$query = sprintf("
											UPDATE cart
											set cart_content = '%s'
											WHERE cart_id = '%s';",
											$sql->real_escape_string(json_encode($cart)),
											$sql->real_escape_string($_COOKIE['wb_cart_id']));
											
											$sql->query($query);
											
											if($sql->affected_rows == 1)
											{
												setcookie('wb_cart_id',$_COOKIE['wb_cart_id'],time()+86400,'/');
												
												$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
												$output .= '<p>Der Artikel wurde erfolgreich in ihren Warenkorb gelegt.</p>';
												$output .= '</div>';
											}
										}
										
										break;
										
									case 1:
									
										$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
										$output .= '<p>Der Artikel kann maximal 99x in ihren Warenkorb gelegt werden.</p>';
										$output .= '</div>';
										
										break;
										
									case 2:
									
										if(!empty($session_status))
										{
											$query = sprintf("
											UPDATE user
											set user_cart = '%s'
											WHERE user_id = '%s';",
											$sql->real_escape_string(json_encode($cart)),
											$sql->real_escape_string($_SESSION['user_id']));
											
											$sql->query($query);
											
											if($sql->affected_rows == 1)
											{
												$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
												$output .= '<p>Der Artikel wurde erfolgreich in ihrem Warenkorb ge&auml;ndert.</p>';
												$output .= '</div>';
											}
										}
										else
										{
											$query = sprintf("
											UPDATE cart
											set cart_content = '%s'
											WHERE cart_id = '%s';",
											$sql->real_escape_string(json_encode($cart)),
											$sql->real_escape_string($_COOKIE['wb_cart_id']));
											
											$sql->query($query);
											
											if($sql->affected_rows == 1)
											{
												setcookie('wb_cart_id',$_COOKIE['wb_cart_id'],time()+86400,'/');
												
												$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
												$output .= '<p>Der Artikel wurde erfolgreich in ihrem Warenkorb ge&auml;ndert.</p>';
												$output .= '</div>';
											}
										}
										
										break;
								}
							}
							else
							{
								$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
								$output .= '<p>Der Artikel ist nicht in der gew&auml;hlten Variante vorhanden.</p>';
								$output .= '</div>';
							}
						}
						else
						{
							$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
							$output .= '<p>Es wurde kein Artikel mit der gesendeten ID gefunden.</p>';
							$output .= '</div>';
						}
					}
					else
					{
						$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
						$output .= '<p>Die Artikelanzahl darf nur aus Zahlen bestehen.</p>';
						$output .= '</div>';
					}
				}
				else
				{
					$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
					$output .= '<p>Die Artikelvariante besteht nur aus Zahlen.</p>';
					$output .= '</div>';
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
		$output .= '<p>Es wurden keine Daten gesendet.</p>';
		$output .= '</div>';							
	}
}

$output .= '<p><a class="w3-btn w3-padding-large w3-block blue" href="/">Startseite <i class="fas fa-home"></i></a></p>';	
?>										
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Warenkorb Artikel hinzuf&uuml;gen</title>
		<?php
		require($_SERVER['DOCUMENT_ROOT'].'/include/head.inc.php');
		?>
	</head>
	<body class="gradient-blue">
		<button class="w3-btn"><i class="fas fa-bars fa-2x"></i></button>
		<div class="w3-content" style="max-width:500px;margin-top:20vh;">
			<div class="w3-center">
				<a href="/"><h2>WebBar</h2></a>
				<div class="w3-bar">
					<a class="w3-bar-item w3-btn" href="/"><i class="fas fa-home fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="/user/"><i class="fas fa-user fa-2x"></i></a>
					<a class="w3-bar-item w3-btn active" href="/cart/"><i class="fas fa-shopping-cart fa-2x"></i> 
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