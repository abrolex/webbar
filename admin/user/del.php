<?php
session_start();

session_regenerate_id();

if(empty($_SESSION['admin_login']))
{
	header('location:http://'.$_SERVER['HTTP_HOST'].'/admin/login.php');
	exit;
}
else
{
	require($_SERVER['DOCUMENT_ROOT'].'/include/config.inc.php');

	$output = '';

	if(!empty($_GET))
	{
		if(empty($_GET['user_id']) || empty($_GET['csrf_token']))
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Es konnte kein User gel&ouml;scht werden.</p>';
			$output .= '</div>';
		}
		else
		{
			if(preg_match('/[^0-9]/',$_GET['user_id']) == 0)
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
							DELETE FROM user
							WHERE user_id = '%s';",
							$sql->real_escape_string($_GET['user_id']));
							
							$sql->query($query);
							
							if($sql->affected_rows == 1)
							{
								$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
								$output .= '<p>Der User wurde erfolgreich gel&ouml;scht.</p>';
								$output .= '</div>';
							}
							else
							{
								$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
								$output .= '<p>Es konnte kein User gel&ouml;scht werden.</p>';
								$output .= '</div>';
							}
						}
					}
					else
					{
						$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
						$output .= '<p>Es wurde ein falscher Token gesendet.</p>';
						$output .= '</div>';
					}
				}
				else
				{
					$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
					$output .= '<p>Invalid Token.</p>';
					$output .= '</div>';
				}	
			}
			else
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Die UserID besteht nur aus Zahlen.</p>';
				$output .= '</div>';
			}
		}
	}
	else
	{
		$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
		$output .= '<p>Es konnte kein User gel&ouml;scht werden.</p>';
		$output .= '</div>';
	}
}
?>	
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Admin | User l&ouml;schen</title>
		<?php
		require($_SERVER['DOCUMENT_ROOT'].'/include/head.inc.php');
		?>
	</head>
	<body class="gradient-blue">
		<button class="w3-btn"><i class="fas fa-bars fa-2x"></i></button>
		<div class="w3-content" style="max-width:500px;margin-top:20vh;">
			<div class="w3-center">
				<div class="w3-bar">
					<a class="w3-bar-item w3-btn" href="/admin/"><i class="fas fa-home fa-2x"></i></a>
					<a class="w3-bar-item w3-btn active" href="index.php"><i class="fas fa-user fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="../article/"><i class="fas fa-cube fa-2x"></i></a>
				</div>
			</div>
			<div class="w3-container">
				<div class="w3-panel w3-white">
					<form action="search.php" method="get">
						<div class="w3-row w3-section">
							<div class="w3-col s8 m8 l8">
								<input class="w3-input w3-border" type="text" name="search" placeholder="User suchen"/>
								<input type="hidden" name="s" value="0"/>
								<input type="hidden" name="ps" value="5"/>
							</div>
							<div class="w3-col s4 m4 l4">
								<button class="w3-btn w3-block w3-border border-blue blue" type="submit"><i class="fas fa-search"></i></button>
							</div>
						</div>
					</form>
					<p><a class="w3-btn w3-block w3-padding-large blue" href="add.php">User erstellen <i class="fas fa-user-plus"></i></a></p>
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