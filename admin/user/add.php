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
	
	require($_SERVER['DOCUMENT_ROOT'].'/include/functions.inc.php');

	$output = '';

	$showform = 1;

	if(!empty($_GET))
	{
		if(empty($_GET['user_email']) || empty($_GET['user_username']) || empty($_GET['csrf_token']))
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Es konnte kein User angelegt werden.</p>';
			$output .= '</div>';
		}
		else
		{
			if(preg_match('/[^a-zA-Z0-9\-\_\.\@]/',$_GET['user_email']) == 0)
			{
				$pos = strpos($_GET['user_email'],'@');
				
				if($pos !== false)
				{
					$email_provider = substr($_GET['user_email'],$pos+1);
					
					if(in_array($email_provider,$app_email_provider))
					{
						$user_email = $_GET['user_email'];
						
						if(strlen($_GET['user_username']) <= 10)
						{
							if(preg_match('/[^a-zA-Z0-9\_\-]/',$_GET['user_username']) == 0)
							{
								$user_username = $_GET['user_username'];
								
								if(preg_match('/[^a-zA-Z0-9]/',$_GET['csrf_token']) == 0)
								{
									if($_SESSION['user_csrf_token'] == $_GET['csrf_token'])
									{
										$sql = mysqli_connect($app_sqlhost,$app_sqluser,$app_sqlpasswd,$app_sqldb);
										
										if(!$sql)
										{
											$output .= '<div class="w3-panel w3-text-red">';
											$output .= '<p>Es konnte keine Datenbankverbindung hergestellt werden.</p>';
											$output .= '</div>';
										}
										else
										{
											/*
											$query = sprintf("
											SELECT user_id
											FROM user
											WHERE user_email = '%s'
											OR user_username = '%s';",
											$sql->real_escape_string($user_email),
											$sql->real_escape_string($user_username));
													
											$result = $sql->query($query);
													
											if($row = $result->fetch_array(MYSQLI_ASSOC))
											{
												$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
												$output .= '<p>Username oder E-Mail-Adresse bereits vorhanden.</p>';
												$output .= '</div>';
											}
											else
											{
												$user_salt = randomstr(10);
														
												$user_password = passwdhash($user_salt,$app_default_password);
														
												$query = sprintf("
												INSERT INTO user
												(user_email,user_username,user_password,user_salt,user_active,user_keywords)
												VALUES
												('%s','%s','%s','%s','%s','%s');",
												$sql->real_escape_string($user_email),
												$sql->real_escape_string($user_username),
												$sql->real_escape_string($user_password),
												$sql->real_escape_string($user_salt),
												$sql->real_escape_string(1),
												$sql->real_escape_string($user_email.' '.$user_username));
														
												$sql->query($query);
														
												if($sql->affected_rows == 1)
												{
													$showform = 0;
															
													$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
													$output .= '<p>Der User wurde erfolgreich angelegt.</p>';
													$output .= '</div>';
													$output .= '<p><a class="w3-btn w3-block w3-padding-large blue" href="add.php">User erstellen <i class="fas fa-user-plus"></i></a></p>';
												}
												else
												{
													$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
													$output .= '<p>Der User konnte nicht angelegt werden.</p>';
													$output .= '</div>';
												}
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
								$output .= '<p>Verwenden Sie nur folgende Zeichen f&uuml;r den Username: a-z, A-Z, 0-9, -_</p>';
								$output .= '</div>';
							}		
						}
						else
						{
							$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
							$output .= '<p>Der Username darf max. 10 Zeichen lang sein.</p>';
							$output .= '</div>';
						}
					}
					else
					{
						$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
						$output .= '<p>Sie verwenden einen unzul&auml;ssigen E-Mail-Provider.</p>';
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
				$output .= '<p>Verwenden Sie nur folgende Zeichen in ihrer E-Mail-Adresse: a-z, A-Z, 0-9, -_.@</p>';
				$output .= '</div>';
			}
		}				
	}

	if($showform)
	{
		$output .= '<form action="add.php" method="get">';
		$output .= '<p><label for="user_email">E-Mail-Adresse</label><input class="w3-input w3-border" type="email" name="user_email" placeholder="E-Mail-Adresse"';
		
		if(!empty($user_email))
		{
			$output .= ' value="'.$user_email.'"';
		}
		
		$output .= '/></p>';
		$output .= '<p><label for="user_username">Username</label><input class="w3-input w3-border" type="text" name="user_username" placeholder="Username"';
		
		if(!empty($user_username))
		{
			$output .= ' value="'.$user_username.'"';
		}
		
		$output .= '/></p>';
		$output .= '<p><input type="hidden" name="csrf_token" value="'.$_SESSION['user_csrf_token'].'"/></p>';
		$output .= '<p><button class="w3-btn w3-block w3-padding-large blue" type="submit">User erstellen <i class="fas fa-user-plus"></i></button></p>';
		$output .= '</form>';
	}
}
?>
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Admin | User erstellen</title>
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