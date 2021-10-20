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
					session_destroy();
						
					$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
					$output .= '<p>Sie wurden erfolgreich ausgeloggt.</p>';
					$output .= '</div>';
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
		<title>WebBar | Logout</title>
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
				<div class="w3-container w3-white">
					<div class="w3-center">
						<h3>Logout</h3>
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