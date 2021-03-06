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
	
	require($_SERVER['DOCUMENT_ROOT'].'/include/randomstr.inc.php');
	
	require($_SERVER['DOCUMENT_ROOT'].'/include/passwdhash.inc.php');

	$output = '';

	if(!empty($_GET))
	{
		if(empty($_GET['user_id']) || empty($_GET['attr']) || $_GET['attr_value'] == "" || empty($_GET['csrf_token']))
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Es konnte kein User ge&auml;ndert werden.</p>';
			$output .= '</div>';
		}
		else
		{
			if(preg_match('/[^0-9]/',$_GET['user_id']) == 0)
			{
				$aktions = array('email','username','credit','active','admin','password','location');
				
				if(in_array($_GET['attr'],$aktions))
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
								switch ($_GET['attr'])
								{
									case $aktions[0]:	
								
										if(preg_match('/[^a-zA-Z0-9\-\_\.\@]/',$_GET['attr_value']) == 0)
										{
											$pos = strpos($_GET['attr_value'],'@');
						
											if($pos !== false)
											{
												$email_provider = substr($_GET['attr_value'],$pos+1);
							
												if(in_array($email_provider,$app_email_provider))
												{
													$query = sprintf("
													SELECT user_id
													FROM user
													WHERE user_email = '%s';",
													$sql->real_escape_string($_GET['attr_value']));
													
													$result = $sql->query($query);
													
													if($row = $result->fetch_array(MYSQLI_ASSOC))
													{
														$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
														$output .= '<p>Die gew&auml;hlte E-Mail-Adresse ist bereits vorhanden.</p>';
														$output .= '</div>';
													}
													else
													{
														$query = sprintf("
														SELECT user_keywords
														FROM user
														WHERE user_id = '%s';",
														$sql->real_escape_string($_GET['user_id']));
														
														$result = $sql->query($query);
														
														if($row = $result->fetch_array(MYSQLI_ASSOC))
														{
															$user_keywords = explode(' ',$row['user_keywords']);
															
															$user_keywords[0] = $_GET['attr_value'];
															
															$query = sprintf("
															UPDATE user
															SET user_email = '%s',
															user_keywords = '%s'
															WHERE user_id = '%s';",
															$sql->real_escape_string($_GET['attr_value']),
															$sql->real_escape_string(implode(' ',$user_keywords)),
															$sql->real_escape_string($_GET['user_id']));
															
															$sql->query($query);
															
															if($sql->affected_rows == 1)
															{
																$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
																$output .= '<p>Die E-Mail-Adresse wurde erfolgreich ge&auml;ndert.</p>';
																$output .= '</div>';
															}
															else
															{
																$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
																$output .= '<p>Die E-Mail-Adresse konnte nicht ge&auml;ndert werden.</p>';
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
													$output .= '<p>Sie verwenden einen unzul&auml;ssigen E-Mail-Provider.</p>';
													$output .= '</div>';
												}
											}
											else
											{
												$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
												$output .= '<p>In der E-Mail-Adresse fehlt das @-Zeichen.</p>';
												$output .= '</div>';
											}
										}
										else
										{
											$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
											$output .= '<p>Verwenden Sie nur folgende Zeichen in der E-Mail-Adresse: a-z, A-Z, 0-9, -_.@</p>';
											$output .= '</div>';
										}
										
										break;
										
									case $aktions[1]:
									
										if(strlen($_GET['attr_value']) <= 10)
										{
											if(preg_match('/[^a-zA-Z0-9\-\_]/',$_GET['attr_value']) == 0)
											{
												$query = sprintf("
												SELECT user_id
												FROM user
												WHERE user_username = '%s';",
												$sql->real_escape_string($_GET['attr_value']));
												
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
													SELECT user_keywords
													FROM user
													WHERE user_id = '%s';",
													$sql->real_escape_string($_GET['user_id']));
														
													$result = $sql->query($query);
														
													if($row = $result->fetch_array(MYSQLI_ASSOC))
													{
														$user_keywords = explode(' ',$row['user_keywords']);
															
														$user_keywords[1] = $_GET['attr_value'];
													
														$query = sprintf("
														UPDATE user
														SET user_username = '%s',
														user_keywords = '%s'
														WHERE user_id = '%s';",
														$sql->real_escape_string($_GET['attr_value']),
														$sql->real_escape_string(implode(' ',$user_keywords)),
														$sql->real_escape_string($_GET['user_id']));
												
														$sql->query($query);
														
														if($sql->affected_rows == 1)
														{
															$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
															$output .= '<p>Der Username wurde erfolgreich ge&auml;ndert.</p>';
															$output .= '</div>';
														}
														else
														{
															$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
															$output .= '<p>Der Username konnte nicht ge&auml;ndert werden.</p>';
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
										
										break;
										
									case $aktions[2]:
									
										if(strlen($_GET['attr_value']) > 3 && strlen($_GET['attr_value']) < 6)
										{
											if(preg_match('/[0-9]{1,2}+\.+[0-9]{2}/',$_GET['attr_value']) != 0)
											{
												$query = sprintf("
												UPDATE user
												SET user_credit = '%s'
												WHERE user_id = '%s';",
												$sql->real_escape_string($_GET['attr_value']),
												$sql->real_escape_string($_GET['user_id']));
												
												$sql->query($query);
												
												if($sql->affected_rows == 1)
												{
													$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
													$output .= '<p>Das Guthaben wurde erfolgreich ge&auml;ndert.</p>';
													$output .= '</div>';
												}
												else
												{
													$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
													$output .= '<p>Das Guthaben konnte nicht ge&auml;ndert werden.</p>';
													$output .= '</div>';
												}
											}
											else
											{
												$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
												$output .= '<p>Geben Sie das Guthaben in folgender Form ein: 00.00</p>';
												$output .= '</div>';
											}
										}
										else
										{
											$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
											$output .= '<p>Geben Sie das Guthaben in folgender Form ein: 00.00</p>';
											$output .= '</div>';
										}
											
										break;
										
									case $aktions[3]:
									
										if(preg_match('/[^01]/',$_GET['attr_value']) == 0)
										{
											$query = sprintf("
											UPDATE user
											SET user_active = '%s'
											WHERE user_id = '%s';",
											$sql->real_escape_string($_GET['attr_value']),
											$sql->real_escape_string($_GET['user_id']));
											
											$sql->query($query);
											
											if($sql->affected_rows == 1)
											{
												$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
												$output .= '<p>Das Attribut Aktiv wurde auf '.$_GET['attr_value'].' gesetzt.</p>';
												$output .= '</div>';
											}
											else
											{
												$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
												$output .= '<p>Das Attribut Aktiv konnte nicht ge&auml;ndert werden.</p>';
												$output .= '</div>';
											}
										}
										else
										{
											$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
											$output .= '<p>Der User kann nur aktiviert oder deaktivert werden.</p>';
											$output .= '</div>';
										}
										
										break;
									
									case $aktions[4]:
									
										if(preg_match('/[^01]/',$_GET['attr_value']) == 0)
										{
											$query = sprintf("
											UPDATE user
											SET user_admin = '%s'
											WHERE user_id = '%s';",
											$sql->real_escape_string($_GET['attr_value']),
											$sql->real_escape_string($_GET['user_id']));
											
											$sql->query($query);
											
											if($sql->affected_rows == 1)
											{
												$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
												$output .= '<p>Das Attribut Admin wurde auf '.$_GET['attr_value'].' gesetzt.</p>';
												$output .= '</div>';
											}
											else
											{
												$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
												$output .= '<p>Das Attribut Admin konnte nicht ge&auml;ndert werden.</p>';
												$output .= '</div>';
											}
										}
										else
										{
											$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
											$output .= '<p>Einem User k&ouml;nnen nur die Adminrechte gegeben oder entzogen werden.</p>';
											$output .= '</div>';
										}
										
										break;
										
									case $aktions[5]:
									
										if($_GET['attr_value'] == 'reset')
										{
											$salt = randomstr(10);
											
											$passwdhash = passwdhash($salt,$app_default_password);
												
											$query = sprintf("
											UPDATE user
											SET user_password = '%s',
											user_salt = '%s'
											WHERE user_id = '%s';",
											$sql->real_escape_string($passwdhash),
											$sql->real_escape_string($salt),
											$sql->real_escape_string($_GET['user_id']));
												
											$sql->query($query);
											
											if($sql->affected_rows == 1)
											{
												$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
												$output .= '<p>Das Passwort wurde erfolgreich zur&uuml;ckgesetzt.</p>';
												$output .= '</div>';
											}
											else
											{
												$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
												$output .= '<p>Das Passwort konnte nicht zur&uuml;ckgesetzt werden.</p>';
												$output .= '</div>';
											}
										}
										else
										{
											$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
											$output .= '<p>Das Passwort kann nur zur&uuml;ckgesetzt werden.</p>';
											$output .= '</div>';
										}
										
										break;

									case $aktions[6]:

										if(preg_match('/[^0-9]/',$_GET['attr_value']) == 0)
										{
											$query = sprintf("
											SELECT location_name
											FROM location
											WHERE location_id = '%s';",
											$sql->real_escape_string($_GET['attr_value']));
											
											$result = $sql->query($query);
											
											if($row = $result->fetch_array(MYSQLI_ASSOC))
											{
												$query = sprintf("
												SELECT user_location_id
												FROM user
												WHERE user_id = '%s';",
												$sql->real_escape_string($_GET['user_id']));
												
												$result = $sql->query($query);
												
												if($row = $result->fetch_array(MYSQLI_ASSOC))
												{
													if($row['user_location_id'] != $_GET['attr_value'])
													{
														$query = sprintf("
														UPDATE user
														SET user_location_id = '%s'
														WHERE user_id = '%s';",
														$sql->real_escape_string($_GET['attr_value']),
														$sql->real_escape_string($_GET['user_id']));
														
														$sql->query($query);
														
														if($sql->affected_rows == 1)
														{
															$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
															$output .= '<p>Die Lokation wurde erfolgreich ge&auml;ndert.</p>';
															$output .= '</div>';
														}
														else
														{
															$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
															$output .= '<p>Die Lokation konnte nicht ge&auml;ndert werden.</p>';
															$output .= '</div>';
														}
													}
													else
													{
														$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
														$output .= '<p>Der User befindet sich bereits an der gew&auml;hlten Lokation.</p>';
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
											else
											{
												$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
												$output .= '<p>Die gesendete LokationsId ist nicht vorhanden.</p>';
												$output .= '</div>';
											}
										}
										else
										{
											$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
											$output .= '<p>Die LokationsId besteht nur aus Zahlen.</p>';
											$output .= '</div>';
										}
										
										break;
								}
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
						$output .= '<p>Invalid Token.</p>';
						$output .= '</div>';
					}
				}
				else
				{
					$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
					$output .= '<p>Es konnte keine Aktion durchgef&uuml;hrt werden.</p>';
					$output .= '</div>';
				}
				
				$output .= '<p><a class="w3-btn w3-block w3-padding-large blue" href="view.php?user_id='.$_GET['user_id'].'"><i class="fas fa-arrow-left"></i> zur&uuml;ck</a></p>';
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
		<title>WebBar | Admin | User &auml;ndern</title>
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
		<div class="w3-content" style="max-width:500px;margin-top:15vh;">
			<div class="w3-center">
				<div class="w3-bar">
					<a class="w3-bar-item w3-btn" href="/admin/"><i class="fas fa-home fa-2x"></i></a>
					<a class="w3-bar-item w3-btn active" href="/admin/user/?s=0&ps=5"><i class="fas fa-user fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="/admin/article/?s=0&ps=5"><i class="fas fa-cube fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="/admin/order/?s=0&ps=5&state=0"><i class="fas fa-list fa-2x"></i></a>
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
					<p><a class="w3-btn w3-block w3-padding-large blue" href="add.php">Use erstellen <i class="fas fa-user-plus"></i></a></p>
				</div>
				<div class="w3-panel w3-white">
					<h4>User &auml;ndern</h4>
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