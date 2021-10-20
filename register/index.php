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
	
	require($_SERVER['DOCUMENT_ROOT'].'/include/randomstr.inc.php');
	
	require($_SERVER['DOCUMENT_ROOT'].'/include/passwdhash.inc.php');
	
	$output = '';
	
	$showform = 1;
	
	if(!empty($_POST))
	{
		if(empty($_POST['user_username']) || empty($_POST['user_email']) || empty($_POST['user_password']))
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>F&uuml;llen Sie alle Felder aus.</p>';
			$output .= '</div>';
		}
		else
		{
			if(strlen($_POST['user_username']) <= 10)
			{
				if(preg_match('/[^a-zA-Z0-9\_\-]/',$_POST['user_username']) == 0)
				{
					$user_username = $_POST['user_username'];
					
					if(preg_match('/[^a-zA-Z0-9\-\_\.\@]/',$_POST['user_email']) == 0)
					{
						$pos = strpos($_POST['user_email'],'@');
				
						if($pos !== false)
						{
							$email_provider = substr($_POST['user_email'],$pos+1);
					
							if(in_array($email_provider,$app_email_provider))
							{
								$user_email = $_POST['user_email'];
								
								if(strlen($_POST['user_password']) >= 8)
								{
									if(preg_match('/[A-Z]{1,}/',$_POST['user_password']) != 0 && preg_match('/[0-9]{1,}/',$_POST['user_password']) != 0)
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
											SELECT user_id
											FROM user
											WHERE user_username = '%s';",
											$sql->real_escape_string($user_username));
											
											$result = $sql->query($query);
											
											if($row = $result->fetch_array(MYSQLI_ASSOC))
											{
												$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
												$output .= '<p>Der gew&auml;hlte Username ist bereits vorhanden.</p>';
												$output .= '</div>';
											}
											else
											{
												$query = sprintf("
												SELECT user_id
												FROM user
												WHERE user_email = '%s';",
												$sql->real_escape_string($user_email));
											
												$result = $sql->query($query);
												
												if($row = $result->fetch_array(MYSQLI_ASSOC))
												{
													$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
													$output .= '<p>Es ist bereits ein Account mit der eingegebenen E-Mail-Adresse vorhanden.</p>';
													$output .= '</div>';
												}
												else
												{
													$user_salt = randomstr(10);
													
													$user_password = passwdhash($user_salt,$_POST['user_password']);
													
													$user_cart = array();
													
													$user_atime = date("Y.m.d H:i:s",strtotime("now"));
													
													$user_acode = randomstr(10);
													
													$query = sprintf("
													INSERT INTO user
													(user_email,user_username,user_password,user_salt,user_cart,user_activationtime,user_activationcode,user_keywords)
													VALUES
													('%s','%s','%s','%s','%s','%s','%s','%s');",
													$sql->real_escape_string($user_email),
													$sql->real_escape_string($user_username),
													$sql->real_escape_string($user_password),
													$sql->real_escape_string($user_salt),
													$sql->real_escape_string(json_encode($user_cart)),
													$sql->real_escape_string($user_atime),
													$sql->real_escape_string(hash('sha256',$user_acode)),
													$sql->real_escape_string($user_email.' '.$user_username));
													
													$sql->query($query);
													
													if($sql->affected_rows == 1)
													{
														$query = sprintf("
														SELECT user_id
														FROM user
														WHERE user_email = '%s';",
														$sql->real_escape_string($user_email));
														
														$result = $sql->query($query);
														
														if($row = $result->fetch_array(MYSQLI_ASSOC))
														{
															$showform = 0;
															
															$receiver = $user_email;
															
															$subject = 'Aktivieren Sie ihren Account';
															
															$txt  = 'Guten Tag '.$user_username."\n\n";
															$txt .= 'Vielen Dank fuer ihre Registrierung.'."\n\n";
															$txt .= 'Aktivieren Sie ihren Account mit folgendem Link.'."\n\n";
															$txt .= 'http://'.$_SERVER['HTTP_HOST'].'/activation/?user_id='.$row['user_id'].'&user_code='.$user_acode."\n\n";
															$txt .= 'Ihr WebBar Team :)';
															
															$header  = 'MIME-Version: 1.0'."\r\n";
															$header .= 'Content-type: text/plain; charset=UTF-8'."\r\n";
															$header .= 'From:WebBar<'.$app_email_account.'>';
																
															//mail($receiver,$subject,$txt,$header);
															
															$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
															$output .= '<p>Ihr Account wurde erfolgreich angelegt.</p>';
															$output .= '<p>Sie erhalten eine Aktivierungsmail an die eingegebene E-Mail-Adresse.</p>';
															$output .= '</div>';
														}
														else
														{
															$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
															$output .= '<p>Es ist ein Fehler aufgetreten.</p>';
															$output .= '</div>';
														}
													}
													else
													{
														$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
														$output .= '<p>Es konnte kein Account angelegt werden.</p>';
														$output .= '</div>';
													}
												}
											}
										}
									}
									else
									{
										$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
										$output .= '<p>Ihr Passwort ben&ouml;tigt mind. einen Gro&szlig;buchstaben und eine Zahl.</p>';
										$output .= '</div>';
									}
								}
								else
								{
									$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
									$output .= '<p>Ihr Passwort ben&ouml;tigt mind. 8 Zeichen.</p>';
									$output .= '</div>';
								}
							}
							else
							{
								$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
								$output .= '<p>Sie verwenden einen nicht zul&auml;ssigen E-Mail-Provider.</p>';
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
						$output .= '<p>Verwenden Sie nur folgende Zeichen in ihrer E-Mail-Adresse: a-z, A-Z, 0-9, -_.@ </p>';
						$output .= '</div>';
					}
				}
				else
				{
					$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
					$output .= '<p>Verwenden Sie nur folgende Zeichen in ihrem Username: a-z, A-Z, 0-9, -_</p>';
					$output .= '</div>';
				}
			}
			else
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Ihr Username darf max. 10 Zeichen lang sein.</p>';
				$output .= '</div>';
			}
		}
	}
	
	if($showform)
	{
		$output .= '<form action="/register/" method="post">';
		$output .= '<p><label for="user_username">Username</label><input class="w3-input w3-border" type="text" name="user_username" placeholder="Username" ';
		
		if(!empty($user_username))
		{
			$output .= 'value="'.$user_username.'"';
		}
		
		$output .= '/></p>';
		$output .= '<p><label for="user_email">E-Mail-Adresse</label><input class="w3-input w3-border" type="email" name="user_email" placeholder="E-Mail-Adresse" ';
		
		if(!empty($user_email))
		{
			$output .= 'value="'.$user_email.'"';
		}
		
		$output .= '/></p>';
		$output .= '<p><label for="user_password">Passwort</label><input class="w3-input w3-border" type="password" name="user_password" placeholder="Passwort"/></p>';
		$output .= '<p><button class="w3-btn w3-block w3-padding-large blue" type="submit">registrieren <i class="fas fa-arrow-right"></i></button></p>';
		$output .= '</form>';
	}
}			
?>		
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Registrierung</title>
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
				<div class="w3-row">
					<div class="w3-col s6 m6 l6">
						<a class="w3-btn w3-block w3-padding-large grey" href="/login/">Login</a>
					</div>
					<div class="w3-col s6 m6 l6">
						<a class="w3-card w3-btn w3-block w3-padding-large blue" href="#">Registrierung</a>
					</div>
				</div>
				<div class="w3-container w3-white">
					<div class="w3-center">
						<h3>Registrierung</h3>
						<p><i class="fas fa-user-plus fa-3x"></i></p>
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