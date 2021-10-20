<?php
session_start();

session_regenerate_id();

if(!empty($_SESSION['user_login']))
{
	header('location:http://'.$_SERVER['HTTP_HOST'].'/');
	exit;
}
else
{
	require($_SERVER['DOCUMENT_ROOT'].'/include/config.inc.php');
	
	require($_SERVER['DOCUMENT_ROOT'].'/include/randomstr.inc.php');
	
	require($_SERVER['DOCUMENT_ROOT'].'/include/passwdhash.inc.php');
	
	$output = '';
	
	$showform = 1;
	
	if(!empty($_POST))
	{
		if(empty($_POST['user_email']) || empty($_POST['user_password']))
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Geben Sie eine E-Mail-Adresse und ein Passwort ein.</p>';
			$output .= '</div>';
		}
		else
		{
			if(preg_match('/[^a-zA-Z0-9\-\_\.\@]/',$_POST['user_email']) == 0)
			{
				$pos = strpos($_POST['user_email'],'@');
				
				if($pos !== false)
				{
					$email_provider = substr($_POST['user_email'],$pos+1);
					
					if(in_array($email_provider,$app_email_provider))
					{
						$email = $_POST['user_email'];
						
						if(strlen($_POST['user_password']) >= 8)
						{
							if(preg_match('/[A-Z]{1,}/',$_POST['user_password']) != 0 && preg_match('/[0-9]{1,}/',$_POST['user_password']) != 0)
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
									SELECT user_id,user_password,user_salt,user_active
									FROM user
									WHERE user_email = '%s';",
									$sql->real_escape_string($email));
									
									$result = $sql->query($query);
									
									if($row = $result->fetch_array(MYSQLI_ASSOC))
									{
										if($row['user_active'])
										{
											if($row['user_password'] == passwdhash($row['user_salt'],$_POST['user_password']))
											{	
												$user_csrf_token = randomstr(10);
												
												session_start();
												
												$_SESSION = array('user_login' => true,'user_id' => $row['user_id'],'user_csrf_token' => $user_csrf_token);
												
												require($_SERVER['DOCUMENT_ROOT'].'/include/cookie_cart.inc.php');
												
												if(!empty($cart))
												{
													$cookie_cart = $cart;
													
													$cookie_cart_count = count($cookie_cart);
													
													require($_SERVER['DOCUMENT_ROOT'].'/include/user_cart.inc.php');
													
													if(!empty($cart))
													{
														$user_cart = $cart;
														
														$user_cart_count = count($user_cart);
														
														for($i = 0; $i < $cookie_cart_count; $i++)
														{
															$in_user_cart = 0;
															
															$cookie_article_id = $cookie_cart[$i]['article_id'];
															
															$cookie_article_variant = $cookie_cart[$i]['article_variant'];
															
															$cookie_article_amount = $cookie_cart[$i]['article_amount'];
															
															for($j = 0; $j < $user_cart_count; $j++)
															{
																$user_article_id = $user_cart[$j]['article_id'];
															
																$user_article_variant = $user_cart[$j]['article_variant'];
															
																$user_article_amount = $user_cart[$j]['article_amount'];
																
																if($cookie_article_id == $user_article_id && $cookie_article_variant == $user_article_variant)
																{
																	$in_user_cart = 1;
																	
																	$user_article_amount_new = $user_article_amount+$cookie_article_amount;
																	
																	if($user_article_amount_new > 99)
																	{
																		$user_article_amount_new = 99;
																	}
																	
																	$user_cart[$j]['article_amount'] = $user_article_amount_new;
																}
															}
															
															if(empty($in_user_cart))
															{
																array_push($user_cart,$cookie_cart[$i]);
															}
														}
														
														$cart = $user_cart;
													}
													else
													{
														$cart = $cookie_cart;
													}
													
													$query = sprintf("
													UPDATE cart
													SET cart_content = '%s'
													WHERE cart_id = '%s';",
													$sql->real_escape_string(json_encode(array())),
													$sql->real_escape_string($_COOKIE['wb_cart_id']));
													
													$sql->query($query);
													
													$query = sprintf("
													UPDATE user
													SET user_cart = '%s'
													WHERE user_id = '%s';",
													$sql->real_escape_string(json_encode($cart)),
													$sql->real_escape_string($_SESSION['user_id']));
													
													$sql->query($query);
												}					
												
												header('location:http://'.$_SERVER['HTTP_HOST'].'/');
												exit;
											}
											else
											{
												$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
												$output .= '<p>Sie haben ein falsches Passwort eingegeben.</p>';
												$output .= '</div>';
											}
										}
										else
										{
											$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
											$output .= '<p>Ihr Account ist deaktiviert.</p>';
											$output .= '</div>';
										}
									}
									else
									{
										$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
										$output .= '<p>Die eingegebene E-Mail-Adresse ist nicht vorhanden.</p>';
										$output .= '</div>';
									}
								}
							}
							else
							{
								$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
								$output .= '<p>Ihr Passwort ben&ouml;tigt mind. ein Gro&szlig;buchstaben und eine Zahl.</p>';
								$output .= '</div>';
							}
						}
						else
						{
							$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
							$output .= '<p>Ihr Passwort muss mind. 8 Zeichen lang sein.</p>';
							$output .= '</div>';
						}
					}
					else
					{
						$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
						$output .= '<p>Sie verwenden einen nicht zul&auml;ssigen E-Mail-Provider.</p>';
						$output .= '</div>';
					}
				}
				else
				{
					$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
					$output .= '<p>In ihrer E-Mail-Adresse fehlt das @-Zeichen.</p>';
					$output .= '</div>';
				}
			}
			else
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Ihre E-Mail-Adresse darf nur folgende Zeichen enthalten: a-z, A-Z, 0-9, -_.@</p>';
				$output .= '</div>';
			}
		}
	}
	
	if($showform)
	{
		$output .= '<form action="/login/" method="post">';
		$output .= '<p><label for="user_email">E-Mail-Adresse</label><input class="w3-input w3-border" type="email" name="user_email" placeholder="E-Mail-Adresse" ';
		
		if(!empty($email))
		{
			$output .= 'value="'.$email.'"';
		}
		
		$output .= '/></p>';
		$output .= '<p><label for="user_password">Passwort</label><input class="w3-input w3-border" type="password" name="user_password" placeholder="Passwort"/></p>';
		$output .= '<p><button class="w3-btn w3-block w3-padding-large blue" type="submit">einloggen <i class="fas fa-sign-in-alt"></i></button></p>';
		$output .= '</form>';
	}
}
?>
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Login</title>
		<?php
		require($_SERVER['DOCUMENT_ROOT'].'/include/head.inc.php');
		?>
	</head>
	<body class="gradient-blue">
		<div class="w3-content" style="max-width:500px;margin-top:20vh;">
			<div class="w3-container">
				<div class="w3-center">
					<a href="/"><h2>WebBar</h2></a>
				</div>
				<div class="w3-row">
					<div class="w3-col s6 m6 l6">
						<a class="w3-card w3-btn w3-block w3-padding-large blue" href="#">Login</a>
					</div>
					<div class="w3-col s6 m6 l6">
						<a class="w3-btn w3-block w3-padding-large grey" href="/register/">Registrierung</a>
					</div>
				</div>
				<div class="w3-container w3-white">
					<div class="w3-center">
						<h3>Login</h3>
						<p><i class="fas fa-user fa-3x"></i></p>
					</div>
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