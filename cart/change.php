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
	
	if(!empty($cart))
	{
		if(!empty($_GET))
		{
			if($_GET['article_id'] == "" || empty($_GET['attr']) || $_GET['attr_value'] == "")
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Es konnte kein Artikel ge&auml;ndert werden.</p>';
				$output .= '</div>';
			}
			else
			{
				if(preg_match('/[^0-9]/',$_GET['article_id']) == 0)
				{
					$article_id = $_GET['article_id'];
					
					if(!empty($cart[$article_id]))
					{
						if(preg_match('/[^a-z]/',$_GET['attr']) == 0)
						{
							$aktions = array('amount','variant');
					
							if(in_array($_GET['attr'],$aktions))
							{
								$break = 0;
								
								switch($_GET['attr'])
								{
									case $aktions[0]:
									
										if(preg_match('/[^a-z0-9]/',$_GET['attr_value']) == 0)
										{
											$amount = $cart[$article_id]['amount'];
											
											if($amount == $_GET['attr_value'])
											{
												$break = 1;
											}
											else
											{
												if($_GET['attr_value'] == 'reduce')
												{
													$amount--;
												}
												else if($_GET['attr_value'] == 'rise')
												{
													$amount++;
												}
												else
												{
													$amount = $_GET['attr_value'];
												}
												
												if($amount <= 0)
												{
													$new_cart = array();
													
													unset($cart[$article_id]);
													
													foreach($cart as $val)
													{
														array_push($new_cart,$val);
													}
													
													$cart = $new_cart;
												}
												else if($amount > $app_max_amount)
												{
													$break = 1;
												}
												else
												{
													$cart[$article_id]['amount'] = $amount;
												}
											}
										}
										else
										{
											$break = 1;
										}
										
										break;
									
									case $aktions[1]:
									
										if(preg_match('/[^0-9]/',$_GET['attr_value']) == 0)
										{
											$variant_id = $cart[$article_id]['variant_id'];
											
											if($variant_id == $_GET['attr_value'])
											{
												$break = 1;
											}
											else
											{
												$query = sprintf("
												SELECT article_variant,article_price
												FROM article
												WHERE article_id = '%s';",
												$sql->real_escape_string($cart[$article_id]['article_id']));
												
												$result = $sql->query($query);
												
												if($row = $result->fetch_array(MYSQLI_ASSOC))
												{
													$variant_arr = explode('/',$row['article_variant']);
													
													$price_arr = explode('/',$row['article_price']);
													
													if(array_key_exists($_GET['attr_value'],$variant_arr) && array_key_exists($_GET['attr_value'],$price_arr))
													{
														$in_cart = 0;
														
														for($i = 0; $i < $cart_count; $i++)
														{
															$article_id_cart = $cart[$i]['article_id'];
													
															$variant_id_cart = $cart[$i]['variant_id'];
													
															$amount_cart = $cart[$i]['amount'];
													
															if($article_id_cart == $cart[$article_id]['article_id'] && $variant_id_cart == $_GET['attr_value'])
															{
																$in_cart = 1;
																
																$new_amount_cart = $amount_cart+$cart[$article_id]['amount'];
											
																if($new_amount_cart > $app_max_amount)
																{	
																	$break = 1;
																}
																else
																{
																	$cart[$i]['amount'] = $new_amount_cart;
																	
																	unset($cart[$article_id]);
																	
																	$new_cart = array();
												
																	foreach($cart as $val)
																	{
																		array_push($new_cart,$val);
																	}
																	
																	$cart = $new_cart;
																}
															}
														}
														
														if(empty($in_cart))
														{
															$cart[$article_id]['variant_id'] = $_GET['attr_value'];
														}
													}
													else
													{
														$break = 1;
													}
												}
												else
												{
													$break = 1;
												}
											}
										}
										else
										{
											$break = 1;
										}
										
										break;
								}
								
								if(empty($break))
								{
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
											header('location:http://'.$_SERVER['HTTP_HOST'].'/cart/');
											exit;
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
											
											header('location:http://'.$_SERVER['HTTP_HOST'].'/cart/');
											exit;
										}
									}
								}
								else
								{
									$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
									$output .= '<p>Es konnte kein Artikel ge&auml;ndert werden.</p>';
									$output .= '</div>';
								}
							}
							else
							{
								$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
								$output .= '<p>Sie k&ouml;nnen nur die Anzahl und Variante &auml;ndern.</p>';
								$output .= '</div>';
							}
						}
						else
						{
							$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
							$output .= '<p>Es konnte keine Aktion durchgef&uuml;hrt werden.</p>';
							$output .= '</div>';
						}
					}
					else
					{
						$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
						$output .= '<p>ArtikelId existiert nicht in ihrem Warenkorb.</p>';
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
	else
	{
		$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
		$output .= '<p>Ihr Warenkorb ist leer :(</p>';
		$output .= '</div>';
	}
}
?>				
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Warenkorb | Artikel &auml;ndern</title>
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
		<script src="https://www.w3schools.com/lib/w3.js"></script>
	</body>
</html>