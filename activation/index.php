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
	
	$output = '';
	
	if(!empty($_GET))
	{
		if(empty($_GET['user_id']) || empty($_GET['user_code']))
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Es wurden nicht alle Daten gesendet.</p>';
			$output .= '</div>';
		}
		else
		{
			if(preg_match('/[^0-9]/',$_GET['user_id']) == 0)
			{
				if(preg_match('/[^a-zA-Z0-9]/',$_GET['user_code']) == 0)
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
						SELECT user_activationtime
						FROM user
						WHERE user_id = '%s';",
						$sql->real_escape_string($_GET['user_id']));
						
						$result = $sql->query($query);
						
						if($row = $result->fetch_array(MYSQLI_ASSOC))
						{
							if($row['user_activationtime'] != NULL)
							{
								if(strtotime($row['user_activationtime'])+3600 > strtotime('now'))
								{
									$query = sprintf("
									UPDATE user
									SET user_active = 1,
									user_activationtime = NULL,
									user_activationcode = NULL
									WHERE user_id = '%s'
									AND user_activationcode = '%s';",
									$sql->real_escape_string($_GET['user_id']),
									$sql->real_escape_string(hash('sha256',$_GET['user_code'])));
									
									$sql->query($query);
									
									if($sql->affected_rows == 1)
									{
										$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
										$output .= '<p>Ihr Account wurde erfolgreich aktiviert.</p>';
										$output .= '<p>Sie k&ouml;nnen sich nun einloggen.</p>';
										$output .= '</div>';
										$output .= '<p><a class="w3-btn w3-block w3-padding-large blue" href="/login/">zum Login <i class="fas fa-sign-in-alt"></i></a></p>';
									}
									else
									{
										$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
										$output .= '<p>Es konnte kein Account aktiviert werden.</p>';
										$output .= '</div>';
									}
								}
								else
								{
									$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
									$output .= '<p>Der Aktivierungszeitraum wurde &uuml;berschritten.</p>';
									$output .= '<p>Kontaktieren Sie einen Administrator um ihren Account zu aktivieren.</p>';
									$output .= '</div>';
								}
							}
							else
							{
								$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
								$output .= '<p>Ihr Account wurde bereits aktiviert.</p>';
								$output .= '</div>';
							}
						}
						else
						{
							$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
							$output .= '<p>Es wurde kein Account gefunden.</p>';
							$output .= '</div>';
						}
					}
				}
				else
				{
					$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
					$output .= '<p>Ung&uuml;ltiger Aktivierungscode.</p>';
					$output .= '</div>';
				}
			}
			else
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Die UserId besteht nur aus Zahlen.</p>';
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
		<title>WebBar | Account aktivieren</title>
		<?php
		require($_SERVER['DOCUMENT_ROOT'].'/include/head.inc.php');
		?>
	</head>
	<body class="gradient-blue">
		<div class="w3-content" style="max-width:500px;margin-top:15vh;">
			<div class="w3-container">
				<div class="w3-center">
					<a href="/"><h2>WebBar</h2></a>
				</div>
				<div class="w3-container w3-white">
					<div class="w3-center">
						<h3>Account aktivieren</h3>
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