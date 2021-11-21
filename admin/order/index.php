<?php
session_start();

session_regenerate_id();

if(empty($_SESSION['admin_login']))
{
	header('location:http://'.$_SERVER['HTTP_HOST'].'/admin/login/');
	exit;
}
else
{
	require($_SERVER['DOCUMENT_ROOT'].'/include/config.inc.php');
	
	$output = '';
	
	if(!empty($_GET))
	{
		if($_GET['s'] == "" || empty($_GET['ps']) || $_GET['state'] == "")
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Es konnten keine Bestellungen angezeigt werden.</p>';
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
						if(preg_match('/[^0-9]/',$_GET['state']) == 0)
						{
							$order_states = array(0,1,2);
								
							if(in_array($_GET['state'],$order_states))
							{
								$sql = mysqli_connect($app_sqlhost,$app_sqluser,$app_sqlpasswd,$app_sqldb);
						
								if(!$sql)
								{
									$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
									$output .= '<p>Es konnte keine Datenbankverbingung hergestellt werden.</p>';
									$output .= '</div>';
								}
								else
								{
									$query = sprintf("
									SELECT order_id
									FROM orders
									WHERE order_state = '%s';",
									$sql->real_escape_string($_GET['state']));
									
									$result = $sql->query($query);
									
									$anzahl_gs = mysqli_num_rows($result);
									
									if($anzahl_gs > 0)
									{
										$output .= '<h4>'.$anzahl_gs.' Bestellung/en gefunden</h4>';
										$output .= '<div class="w3-section w3-row-padding" style="padding:0;">';
										
										$i = 0;
										
										$query = sprintf("
										SELECT order_id,user_username,location_name,order_cart,order_time
										FROM orders
										INNER JOIN user ON user_id = order_user_id
										INNER JOIN location ON location_id = order_location_id
										WHERE order_state = '%s'
										ORDER BY order_id ASC
										LIMIT %s,%s;",
										$sql->real_escape_string($_GET['state']),
										$sql->real_escape_string($_GET['s']*$_GET['ps']),
										$sql->real_escape_string($_GET['ps']));
										
										$result = $sql->query($query);
									
										while($row = $result->fetch_array(MYSQLI_ASSOC))
										{
											$price_g = 0.00;
											
											$order_cart = json_decode($row['order_cart'],true);
											
											$cart_count = count($order_cart);
											
											$order_time = date('d.m.Y H:i:s',strtotime($row['order_time']));
											
											if($i == 0)
											{
												$output .= '<div class="w3-col s12 m6 l6" style="padding-left:0;">';
											}
											
											if($i == 1)
											{
												$output .= '<div class="w3-col s12 m6 l6" style="padding-right:0;">';
											}
												
											if($i == 2)
											{
												$output .= '</div>';
												$output .= '<div class="w3-section w3-row-padding" style="padding:0;">';
												$output .= '<div class="w3-col s12 m6 l6" style="padding-left:0;">';
												
												$i = 0;
											}
											
											
											$output .= '<div class="w3-container w3-white">';
											$output .= '<h4>Bestellnummer #'.$row['order_id'].'</h4>';
											
											$output .= '<div class="w3-row w3-section">';
											$output .= '<div class="w3-col s3 m2 l2">';
											$output .= '<button class="w3-btn w3-block blue"><i class="fas fa-clock"></i></button>'; 
											$output .= '</div>';
											$output .= '<div class="w3-col s9 m10 l10">';
											$output .= '<button class="w3-btn w3-block w3-left-align grey">'.$order_time.'</button>';
											$output .= '</div>';
											$output .= '</div>';
											
											$output .= '<div class="w3-section w3-row-padding" style="padding:0;">';
											$output .= '<div class="w3-col s6 m6 l6" style="padding-left:0;">';
											
											$output .= '<div class="w3-row">';
											$output .= '<div class="w3-col s4 m4 l4">';
											$output .= '<button class="w3-btn w3-block blue"><i class="fas fa-user"></i></button>'; 
											$output .= '</div>';
											$output .= '<div class="w3-col s8 m8 l8">';
											$output .= '<button class="w3-btn w3-block w3-left-align grey">'.$row['user_username'].'</button>';
											$output .= '</div>';
											$output .= '</div>';
											
											$output .= '</div>';
											$output .= '<div class="w3-col s6 m6 l6" style="padding-right:0;">';
											
											$output .= '<div class="w3-row">';
											$output .= '<div class="w3-col s4 m4 l4">';
											$output .= '<button class="w3-btn w3-block blue"><i class="fas fa-map-marker-alt"></i></button>'; 
											$output .= '</div>';
											$output .= '<div class="w3-col s8 m8 l8">';
											$output .= '<button class="w3-btn w3-block w3-left-align grey">'.$row['location_name'].'</button>';
											$output .= '</div>';
											$output .= '</div>';
											
											$output .= '</div>';
											$output .= '</div>';
											
											$output .= '<h4>'.$cart_count.' Artikel</h4>';
											
											$output .= '<div class="w3-section scroll-h">';
						
											for($j = 0; $j < $cart_count; $j++)
											{
												$article_name = $order_cart[$j]['name'];
												
												$variant = $order_cart[$j]['variant'];
												
												$price_e = $order_cart[$j]['price'];
												
												$amount = $order_cart[$j]['amount'];
												
												$price = number_format($price_e*$amount,2,'.','.');
												
												$output .= '<div class="scroll-h-container w3-border">';
												$output .= '<p class="w3-large">'.$article_name.'</p>';
												$output .= '<p>'.$amount.'x '.$variant.' '.$price_e.' &euro;</p>';
												$output .= '<p><button class="w3-btn w3-padding-large w3-border grey">Preis: '.$price.' &euro;</button></p>';
												$output .= '</div>';
												
												$price_g = number_format($price_g+$price,2,'.','.');
											}
											
											$output .= '</div>';
											$output .= '<p><button class="w3-btn w3-padding-large w3-block w3-border grey">Summe: '.$price_g.' &euro;</button></p>';
											
											switch($_GET['state'])
											{
												case $order_states[0]:
												
													$output .= '<p><a href="/admin/order/change.php?order_id='.$row['order_id'].'&attr=state&attr_value=1&csrf_token='.$_SESSION['user_csrf_token'].'" class="w3-btn w3-padding-large w3-block blue">in Bearbeitung nehmen</a>';
													
													break;
													
												case $order_states[1]:
												
													$output .= '<p><a href="/admin/order/change.php?order_id='.$row['order_id'].'&attr=state&attr_value=2&csrf_token='.$_SESSION['user_csrf_token'].'" class="w3-btn w3-padding-large w3-block blue">Bestellung fertigstellen</a>';
													
													break;
											}
											
											$output .= '</div>';
											
											$output .= '</div>';
											
											$i++;
										}
										
										$output .= '</div>';
									}
									else
									{
										$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
										$output .= '<p>Es wurden keine Bestellungen mit dem gesendeten Status gefunden.</p>';
										$output .= '</div>';
									}
								}
							}
							else
							{	
								$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
								$output .= '<p>Der Status einer Bestellung kann nur 0,1 oder 2 betragen.</p>';
								$output .= '</div>';
							}
						}
						else
						{
							$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
							$output .= '<p>Der Status einer Bestellung wird nur in Zahlen ausgedr&uuml;ckt.</p>';
							$output .= '</div>';
						}
					}
					else
					{
						$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
						$output .= '<p>Es konnen nur 5,10 oder 15 Elemente angezeigt werden.</p>';
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
				$output .= '<p>Die Seitenanzahl besteht nur aus Zahlen.</p>';
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
?>
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Admin | Order</title>
		<?php
		require($_SERVER['DOCUMENT_ROOT'].'/include/head.inc.php');
		?>
	</head>
	<body class="gradient-blue">
		<div id="sidebar-overlay" class="overlay">
			<div class="w3-sidebar w3-animate-left dark">
				<button onclick="w3.addStyle('#sidebar-overlay','display','none');" class="w3-btn"><i class="fas fa-times fa-2x"></i></button>
				<div class="w3-container">
					<p><a class="w3-btn w3-block w3-padding-large active" href="#">Admin</a></p>
					<p><a class="w3-btn w3-block w3-padding-large" href="/">User</a></p>
				</div>
			</div>
		</div>
		<button onclick="w3.addStyle('#sidebar-overlay','display','block');" class="w3-btn"><i class="fas fa-bars fa-2x"></i></button>
		<div class="w3-content" style="max-width:800px;margin-top:15vh;">
			<div class="w3-center">
				<div class="w3-bar">
					<a class="w3-bar-item w3-btn" href="/admin/"><i class="fas fa-home fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="/admin/user/?s=0&ps=5"><i class="fas fa-user fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="/admin/article/?s=0&ps=5"><i class="fas fa-cube fa-2x"></i></a>
					<a class="w3-bar-item w3-btn active" href="/admin/order/?s=0&ps=5&state=0"><i class="fas fa-list fa-2x"></i></a>
				</div>
			</div>
			<div class="w3-container">
			<?php
			if(!empty($output))
			{
				echo $output;
			}
			?>
			</div>
		</div>
	</body>
</html>