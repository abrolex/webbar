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

    $output = '';

    if(!empty($_POST))
    {
        if(empty($_POST['attr']) || empty($_POST['attr_value']) || empty($_POST['csrf_token']))
        {
            $output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
            $output .= '<p>Es wurden nicht alle Daten gesendet.</p>';
            $output .= '</div>';
        }
        else
        {  
            if(preg_match('/[^a-z]/',$_POST['attr']) == 0)
            {
                $aktions = array('username','location','guthaben','password');

                if(in_array($_POST['attr'],$aktions))
                {
                    if(preg_match('/[^a-zA-Z0-9]/',$_POST['csrf_token']) == 0)
                    {
                        if($_SESSION['user_csrf_token'] == $_POST['csrf_token'])
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
                                switch($_POST['attr'])
                                {
                                    case $aktions[0]:
                                        
                                        if(strlen($_POST['attr_value']) <= 10)
                                        {
                                            if(preg_match('/[^a-zA-Z0-9\-\_]/',$_POST['attr_value']) == 0)
                                            {
                                                $query = sprintf("
                                                SELECT user_id
                                                FROM user
                                                WHERE user_username = '%s';",
                                                $sql->real_escape_string($_POST['attr_value']));

                                                $result = $sql->query($query);

                                                if($row = $result->fetch_array(MYSQLI_ASSOC))
                                                {
                                                    $query = '';

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
                                                    $sql->real_escape_string($_SESSION['user_id']));

                                                    $result = $sql->query($query);

                                                    if($row = $result->fetch_array(MYSQLI_ASSOC))
                                                    {
                                                        $user_keywords = explode(" ",$row['user_keywords']);

                                                        $user_keywords[1] = $_POST['attr_value'];

                                                        $query = sprintf("
                                                        UPDATE user
                                                        SET user_username = '%s',
                                                        user_keywords = '%s'
                                                        WHERE user_id = '%s';",
                                                        $sql->real_escape_string($_POST['attr_value']),
                                                        $sql->real_escape_string(implode(" ",$user_keywords)),
                                                        $sql->real_escape_string($_SESSION['user_id']));
														
														$sql->query($query);
														
														if($sql->affected_rows == 1)
														{
															$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
															$output .= '<p>Ihr Username wurde erfolgreich ge&auml;ndert.</p>';
															$output .= '</div>';
														}
                                                    }
                                                    else
                                                    {
                                                        $query = '';

                                                        $output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
                                                        $output .= '<p>Es ist ein Fehler aufgetreten.</p>';
                                                        $output .= '</div>';
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                $output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
                                                $output .= '<p>Verwenden Sie nur folgende Zeichen f&uuml;r ihren Username: a-z, A-Z, 0-9, -_</p>';
                                                $output .= '</div>';
                                            }
                                        }
                                        else
                                        {
                                            $output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
                                            $output .= '<p>Ihr Username darf max. 10 Zeichen lang sein.</p>';
                                            $output .= '</div>';
                                        }

                                        break;
                                    
                                    case $aktions[1]:

                                        if(preg_match('/[^0-9]/',$_POST['attr_value']) == 0)
                                        {
                                            $query = sprintf("
                                            SELECT location_name
                                            FROM location
                                            WHERE location_id = '%s';",
                                            $sql->real_escape_string($_POST['attr_value']));

                                            $result = $sql->query($query);

                                            if($row = $result->fetch_array(MYSQLI_ASSOC))
                                            {
                                                $query = sprintf("
                                                SELECT user_location_id
                                                FROM user
                                                WHERE user_id = '%s';",
                                                $sql->real_escape_string($_SESSION['user_id']));

                                                $result = $sql->query($query);

                                                if($row = $result->fetch_array(MYSQLI_ASSOC))
                                                {
                                                    if($row['user_location_id'] != $_POST['attr_value'])
                                                    {
                                                        $query = sprintf("
                                                        UPDATE user
                                                        SET user_location_id = '%s'
                                                        WHERE user_id = '%s';",
                                                        $sql->real_escape_string($_POST['attr_value']),
                                                        $sql->real_escape_string($_SESSION['user_id']));
														
														$sql->query($query);
														
														if($sql->affected_rows == 1)
														{
															$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
															$output .= '<p>Ihre Lokation wurde erfolgreich ge&auml;ndert.</p>';
															$output .= '</div>';
														}
                                                    }
                                                    else
                                                    {
                                                        $query = '';

                                                        $output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
                                                        $output .= '<p>Bitte w&auml;hlen Sie eine andere Lokation.</p>';
                                                        $output .= '</div>';
                                                    }
                                                }
                                                else
                                                {
                                                    $query = '';

                                                    $output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
                                                    $output .= '<p>Es ist ein Fehler aufgetreten.</p>';
                                                    $output .= '</div>';
                                                }
                                            }
                                            else
                                            {
                                                $query = '';

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
										
									case $aktions[2]:
									
										if(preg_match('/[^a-z]/',$_POST['attr_value']) == 0)
										{
											if($_POST['attr_value'] == 'info')
											{
												$output .= '<div class="w3-panel">';
												$output .= '<p>Sie k&ouml;nnen ihr Guthaben nur durch Einzahlung von Bargeld aufstocken.</p>';
												$output .= '</div>';
											}
											else
											{
												$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
												$output .= '<p>Sie k&ouml;nnen nur eine Info erhalten.</p>';
												$output .= '</div>';
											}
										}
										else
										{
											$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
											$output .= '<p>Sie k&ouml;nnen nur eine Info erhalten.</p>';
											$output .= '</div>';
										}
										
										break;
					
									case $aktions[3]:
									
										if(strlen($_POST['attr_value']) >= 8)
										{
											if(preg_match('/[A-Z]{1,}/',$_POST['attr_value']) != 0 && preg_match('/[0-9]{1,}/',$_POST['attr_value']) != 0)
											{
												$salt = randomstr(10);
												
												$password = passwdhash($salt,$_POST['attr_value']);
												
												$query = sprintf("
												UPDATE user
												SET user_password = '%s',
												user_salt = '%s'
												WHERE user_id = '%s';",
												$sql->real_escape_string($password),
												$sql->real_escape_string($salt),
												$sql->real_escape_string($_SESSION['user_id']));
												
												$sql->query($query);
												
												if($sql->affected_rows == 1)
												{
													$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
													$output .= '<p>Ihr Passwort wurde erfolgreich ge&auml;ndert.</p>';
													$output .= '</div>';
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
											$output .= '<p>Ihr Passwort muss mind. 8 Zeichen lang sein.</p>';
											$output .= '</div>';
										}
										
										break;
								}
                            }
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
                else
                {
                    $output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
                    $output .= '<p>Ung&uuml;ltige Aktion.</p>';
                    $output .= '</div>';
                }
            }
            else
            {
                $output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
                $output .= '<p>Ung&uuml;ltige Aktion.</p>';
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

    $output .= '<p><a class="w3-btn w3-block w3-padding-large blue" href="/user/">mein Account <i class="fas fa-user"></i></a></p>';
}
?>
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Account &auml;ndern</title>
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
						<h3>Account &auml;ndern</h3>
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