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
	
	require($_SERVER['DOCUMENT_ROOT'].'/include/randomstr.inc.php');
	
	require($_SERVER['DOCUMENT_ROOT'].'/include/passwdhash.inc.php');

	$showform = 1;
	
    $output = '';
	
	if(!empty($_POST))
	{
		if(empty($_POST['old_password']) || empty($_POST['new_password']))
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
            $output .= '<p>F&uuml;llen Sie alle Felder aus.</p>';
            $output .= '</div>';
		}
		else
		{
			if(strlen($_POST['old_password']) >= 8)
			{
				if(preg_match('/[A-Z]{1,}/',$_POST['old_password']) != 0 && preg_match('/[0-9]{1,}/',$_POST['old_password']) != 0)
				{
					if(strlen($_POST['new_password']) >= 8)
					{
						if(preg_match('/[A-Z]{1,}/',$_POST['new_password']) != 0 && preg_match('/[0-9]{1,}/',$_POST['new_password']) != 0)
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
								SELECT user_email,user_password,user_salt
								FROM user
								WHERE user_id = '%s';",
								$sql->real_escape_string($_SESSION['user_id']));
								
								$result = $sql->query($query);
								
								if($row = $result->fetch_array(MYSQLI_ASSOC))
								{
									if($row['user_password'] == passwdhash($row['user_salt'],$_POST['old_password']))
									{
										$user_email = $row['user_email'];
										
										$user_salt = randomstr(10);
										
										$passwdhash = passwdhash($user_salt,$_POST['new_password']);
										
										$query = sprintf("
										UPDATE user
										SET user_password = '%s',
										user_salt = '%s'
										WHERE user_id = '%s';",
										$sql->real_escape_string($passwdhash),
										$sql->real_escape_string($user_salt),
										$sql->real_escape_string($_SESSION['user_id']));
										
										$sql->query($query);
										
										if($sql->affected_rows == 1)
										{
											$showform = 0;
											
											$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
											$output .= '<p>Ihr Passwort wurde erfolgreich ge&auml;ndert.</p>';
											$output .= '</div>';
										}
										else
										{
											$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
											$output .= '<p>Ihr Passwort konnte nicht ge&auml;ndert werden.</p>';
											$output .= '</div>';
										}
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
									$output .= '<p>Es wurde kein User gefunden.</p>';
									$output .= '</div>';
								}
							}
						}
						else
						{
							$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
							$output .= '<p>Ihr neues Passwort ben&ouml;tigt mind. eine Zahl und einen Gro&szlig;buchstaben.</p>';
							$output .= '</div>';
						}
					}
					else
					{
						$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
						$output .= '<p>Ihr neues Passwort muss mind. 8 Zeichen lang sein.</p>';
						$output .= '</div>';
					}
				}
				else
				{
					$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
					$output .= '<p>Ihr altes Passwort beinhaltet mind. eine Zahl und einen Gro&szlig;buchstaben.</p>';
					$output .= '</div>';
				}
			}
			else
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Ihr altes Passwort ist mind. 8 Zeichen lang.</p>';
				$output .= '</div>';
			}
		}
	}
	
	if($showform)
	{
		$output .= '<form action="/user/change_passwd.php" method="post">';
		$output .= '<p><label for="old_password">altes Passwort</label><input class="w3-input w3-border" type="password" name="old_password" placeholder="altes Passwort"/></p>';
		$output .= '<p><label for="new_password">neues Passwort</label><input class="w3-input w3-border" type="password" name="new_password" placeholder="neues Passwort"/></p>';
		$output .= '<p><button class="w3-btn w3-block w3-padding-large blue" type="submit">Passwort setzen <i class="fas fa-key"></i></button></p>';
		$output .= '</form>';
	}
	
	$output .= '<p><a href="/user/"><i class="fas fa-arrow-left"></i> zur&uuml;ck</a></p>';
}							
?>		
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Passwort &auml;ndern</title>
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
						<h3>Passwort &auml;ndern</h3>
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