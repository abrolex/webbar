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
			if($_GET['article_id'] == "")
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Es konnte kein Artikel gel&ouml;scht werden.</p>';
				$output .= '</div>';
			}
			else
			{
				if(preg_match('/[^0-9]/',$_GET['article_id']) == 0)
				{
					if(!empty($cart[$_GET['article_id']]))
					{
						$new_cart = array();
													
						unset($cart[$_GET['article_id']]);
													
						foreach($cart as $article)
						{
							array_push($new_cart,$article);
						}
													
						$cart = $new_cart;
						
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
						$output .= '<p>ArtikelIndex existiert nicht.</p>';
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
		<title>WebBar | Warenkorb Artikel l&ouml;schen</title>
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