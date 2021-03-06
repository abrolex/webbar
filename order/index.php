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
			if($_GET['s'] == "" || empty($_GET['ps']))
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Es wurden nicht alle Daten gesendet.</p>';
				$output .= '</div>';
			}
			else
			{
				if(preg_match('/[^0-9]/',$_GET['s']) == 0)
				{
					if(preg_match('/[^0-9]/',$_GET['ps']) == 0)
					{
						$ps = array('5','10','15');
						
						if(in_array($_GET['ps'],$ps))
						{
							$query = sprintf("
							SELECT order_id
							FROM orders
							WHERE order_user_id = '%s';",
							$sql->real_escape_string($_SESSION['user_id']));
							
							$result = $sql->query($query);
							
							$anzahl_gs = mysqli_num_rows($result);
							
							if($anzahl_gs > 0)
							{
								$output .= '<h4>'.$anzahl_gs.' Bestellung/en gefunden</h4>';
								
								$query = sprintf("
								SELECT order_id,order_time
								FROM orders
								WHERE order_user_id = '%s'
								ORDER BY order_id DESC
								LIMIT %s,%s;",
								$sql->real_escape_string($_SESSION['user_id']),
								$sql->real_escape_string($_GET['s']*$_GET['ps']),
								$sql->real_escape_string($_GET['ps']));
							
								$result = $sql->query($query);
								
								while($row = $result->fetch_array(MYSQLI_ASSOC))
								{
									$order_time = date('d.m.Y H:i:s',strtotime($row['order_time']));
									
									$output .= '<div class="w3-row w3-section">';
									$output .= '<div class="w3-col s9 m10 l10">';
									$output .= '<button class="w3-btn w3-block grey">'.$order_time.'</button>';
									$output .= '</div>';
									$output .= '<div class="w3-col s3 m2 l2">';
									$output .= '<a class="w3-btn w3-block blue" href="view.php?order_id='.$row['order_id'].'"><i class="fas fa-arrow-right"></i></a>';
									$output .= '</div>';
									$output .= '</div>';
								}
								
								if($anzahl_gs > $_GET['ps'])
								{
									$anzahl_s = ceil($anzahl_gs/$_GET['ps']);
										
									$output .= '<form action="/order/" method="get">';
									$output .= '<p><select onchange="document.forms[1].submit();" class="w3-select w3-border w3-white" name="s">';
									$output .= '<option value="">Seite ';
									$output .= $_GET['s']+1;
									$output .= ' von '.$anzahl_s.'</option>';
										
									for($i = 0; $i < $anzahl_s; $i++)
									{
										$output .= '<option value="'.$i.'">Seite ';
										$output .= $i+1;
										$output .= ' von '.$anzahl_s.'</option>';
									}
										
									$output .= '</select></p>';
									$output .= '<p><input type="hidden" name="ps" value="'.$_GET['ps'].'"/></p>';
									$output .= '</form>';
									
									$output .= '<form action="/order/" method="get">';
									$output .= '<p><input type="hidden" name="s" value="0"/></p>';
									$output .= '<p><select onchange="document.forms[2].submit();" class="w3-select w3-border w3-white" name="ps">';
									$output .= '<option value="">'.$_GET['ps'].' pro Seite</option>';
									$output .= '<option value="5">5 pro Seite</option>';
									$output .= '<option value="10">10 pro Seite</option>';
									$output .= '<option value="15">15 pro Seite</option>';
									$output .= '</select></p>';
									$output .= '</form>';
								}
							}
							else
							{
								$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
								$output .= '<p>Sie haben noch nichts bestellt.</p>';
								$output .= '</div>';
							}
						}
						else
						{
							$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
							$output .= '<p>Es k&ouml;nnen nur 5,10 oder 15 Elemente angezeigt werden.</p>';
							$output .= '</div>';
						}
					}
					else
					{
						$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
						$output .= '<p>Die Anzahl der anzuzeigenden Elemente besteht nur aus Zahlen.</p>';
						$output .= '</div>';
					}
				}
				else
				{
					$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
					$output .= '<p>Die Seitenzahl besteht nur aus Zahlen.</p>';
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
	
	$output .= '<p><a class="w3-btn w3-padding-large w3-block blue" href="/user/">Mein Account <i class="fas fa-user"></i></a></p>';
}
?>
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Meine Bestellungen</title>
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