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
				$query = sprintf("
				SELECT article_id,article_name,article_variant,article_price
				FROM article
				WHERE article_id = '%s';",
				$sql->real_escape_string($_GET['article_id']));
					
				$result = $sql->query($query);
					
				if($row = $result->fetch_array(MYSQLI_ASSOC))
				{
					if(!empty($_COOKIE['wb_last_view']))
					{
						$last_view = json_decode($_COOKIE['wb_last_view'],true);
						
						if(is_array($last_view))
						{
							$last_view_error = 0;
						
							for($i = 0; $i < count($last_view); $i++)
							{
								if(preg_match('/[^0-9]/',$last_view[$i]) != 0)
								{
									$last_view_error = 1;
									
									unset($last_view[$i]);
								}
							}
							
							if(!empty($last_view_error))
							{
								$last_view_new = array();
									
								foreach($last_view as $val)
								{
									array_push($last_view_new,$val);
								}

								$last_view = $last_view_new;
							}

							if(!in_array($_GET['article_id'],$last_view))
							{
								if(count($last_view) == 6)
								{
									unset($last_view[0]);

									$last_view_new = array();

									foreach($last_view as $val)
									{
										array_push($last_view_new,$val);
									}
								
									$last_view = $last_view_new;

									array_push($last_view,$_GET['article_id']);
								}
								else
								{
									array_push($last_view,$_GET['article_id']);
								}
							}
						}
						else
						{
							$last_view = array();

							array_push($last_view,$_GET['article_id']);
						}

						setcookie('wb_last_view',json_encode($last_view),time()+86400,'/');
					}
					else
					{
						$last_view = array();

						array_push($last_view,$_GET['article_id']);

						setcookie('wb_last_view',json_encode($last_view),time()+86400,'/');
					}

					$variant_arr = explode('/',$row['article_variant']);
						
					$price_arr = explode('/',$row['article_price']);
						
					$output .= '<h3>'.$row['article_name'].'</h3>';
					$output .= '<form action="/cart/add.php" method="get">';
					$output .= '<p><input type="hidden" name="article_id" value="'.$row['article_id'].'"/></p>';
					$output .= '<div class="w3-row-padding w3-section" style="padding:0;">';
					$output .= '<div class="w3-col s6 m6 l6" style="padding-left:0;">';
					$output .= '<label for"variant">Variante</label>';
					$output .= '<select class="w3-select w3-border w3-white" name="variant_id">';
						
					for($i = 0; $i < count($variant_arr); $i++)
					{
						$output .= '<option value="'.$i.'">'.$variant_arr[$i].' '.$price_arr[$i].' &euro;</option>';
					}
						
					$output .= '</select></div>';
					$output .= '<div class="w3-col s6 m6 l6" style="padding-right:0;">';
					$output .= '<label for"amount">Anzahl</label>';
					$output .= '<select class="w3-select w3-border w3-white" name="amount">';
						
					for($i = 1; $i <= $app_max_amount; $i++)
					{
						$output .= '<option value="'.$i.'">'.$i.'x</option>';
					}
						
					$output .= '</select></div></div>';
					$output .= '<p><button class="w3-btn w3-block w3-padding-large blue" type="submit">in den Warenkorb <i class="fas fa-plus"></i></button></p>';
					$output .= '</form>';
				}
			}
			else
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Die ArtikelId besteht nur aus Zahlen.</p>';
				$output .= '</div>';
			}
		}
	}
	else
	{
		$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
		$output .= '<p>Es wurde keine ArtikelId gesendet.</p>';
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