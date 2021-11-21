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

			$output  = '<div class="w3-panel w3-white">'; 
			$output .= '<h4>Zuletzt angesehen <i class="fas fa-arrow-right"></i></h4>';
			$output .= '<div class="w3-section scroll-h">';

			for($i = 0; $i < count($last_view); $i++)
			{
				$query = sprintf("
				SELECT article_name
				FROM article
				WHERE article_id = '%s';",
				$sql->real_escape_string($last_view[$i]));

				$result = $sql->query($query);

				if($row = $result->fetch_array(MYSQLI_ASSOC))
				{
					if($i == 3)
					{
						$output .= '</div>';
						$output .= '<div class="w3-section scroll-h">';
					}

					$output .= '<div class="scroll-h-container" style="width:100%;">';
					$output .= '<div class="w3-row">';
					$output .= '<div class="w3-col s9 m9 l9">';
					$output .= '<button class="w3-btn w3-block grey">'.$row['article_name'].'</button>';
					$output .= '</div>';
					$output .= '<div class="w3-col s3 m3 l3">';
					$output .= '<a class="w3-btn w3-block blue" href="/view/?article_id='.$last_view[$i].'"><i class="fas fa-arrow-right"></i></a>';
					$output .= '</div>';
					$output .= '</div>';
					$output .= '</div>';
				}
			}

			$output .= '</div>';
			$output .= '</div>';
		}
	}
}
?>
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Home</title>
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
				<h2>WebBar</h2>
				<div class="w3-bar">
					<a class="w3-bar-item w3-btn active" href="/"><i class="fas fa-home fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="user/"><i class="fas fa-user fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="cart/"><i class="fas fa-shopping-cart fa-2x"></i> 
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
					<form action="search/" method="get">
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
					<p><a class="w3-btn w3-block w3-padding-large blue" href="register/">Account erstellen <i class="fas fa-user-plus"></i></a></p>
				</div>
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