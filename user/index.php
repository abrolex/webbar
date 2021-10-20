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

	$output = '';

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
		SELECT user_username,user_email,user_credit,user_cart,location_name
		FROM user
		INNER JOIN location ON user_location_id = location_id
		WHERE user_id = '%s';",
		$sql->real_escape_string($_SESSION['user_id']));
		
		$result = $sql->query($query);
		
		if($row = $result->fetch_array(MYSQLI_ASSOC))
		{
			$user_username = $row['user_username'];
			
			$user_email = $row['user_email'];
			
			$user_credit = $row['user_credit'];
			
			$cart = json_decode($row['user_cart'],true);
			
			$cart_count = count($cart);
			
			$location_name = $row['location_name'];
			
			$location_options = '';
			
			$query = "
			SELECT location_id,location_name
			FROM location
			WHERE location_id != 1";
			
			$result = $sql->query($query);
			
			while($row = $result->fetch_array(MYSQLI_ASSOC))
			{
				$location_options .= '<option value="'.$row['location_id'].'">'.$row['location_name'].'</option>';
			}
			
			$output .= '<p>Sie sind mit folgenden Daten eingeloggt.</p>';
			$output .= '<p><a class="w3-btn w3-padding-large blue" href="/logout/?csrf_token='.$_SESSION['user_csrf_token'].'">Logout <i class="fas fa-sign-out-alt"></i></a></p>';
			
			$output .= '<form action="change.php" method="post">';
			$output .= '<div class="w3-section">';
			$output .= 'Username';
			$output .= '<div class="w3-row">';
			$output .= '<div class="w3-col s9 m9 l9">';
			$output .= '<input type="hidden" name="attr" value="username"/>';
			$output .= '<input class="w3-border w3-input" name="attr_value" type="text" readonly="true" value="'.$user_username.'" placeholder="Username"/>';
			$output .= '<input type="hidden" name="csrf_token" value="'.$_SESSION['user_csrf_token'].'"/>';
			$output .= '</div>';
			$output .= '<div class="w3-col s3 m3 l3">';
			$output .= '<button onclick="startEdit(1);" class="w3-btn w3-block w3-border border-blue blue" name="edit_btn" type="button"><i class="fas fa-edit"></i></button>';
			$output .= '<button onclick="cancelEdit(1);" class="w3-btn w3-block w3-border w3-border-red w3-red" style="display:none;" name="cancel_btn" type="button"><i class="fas fa-times"></i></button>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '<p><button onclick="document.forms[1].submit();" class="w3-btn w3-padding-large blue" style="display:none;" name="save_btn" type="button">speichern <i class="fas fa-save"></i></button></p>';
			$output .= '</form>';
			
			$output .= '<div class="w3-section">';
			$output .= 'E-Mail-Adresse';
			$output .= '<input class="w3-border w3-input" type="email" disabled="true" readonly="true" value="'.$user_email.'" placeholder="E-Mail-Adresse"/>';
			$output .= '</div>';
			
			
			$output .= '<form action="change.php" method="post">';
			$output .= '<div class="w3-section">';
			$output .= 'Lokation';
			$output .= '<div class="w3-row">';
			$output .= '<div class="w3-col s9 m9 l9">';
			$output .= '<input type="hidden" name="attr" value="location"/>';
			$output .= '<select class="w3-border w3-select" style="height:40.5px;" name="attr_value" disabled="true">';
			$output .= '<option value="">'.$location_name.'</option>';
			$output .= $location_options;
			$output .= '</select>';
			$output .= '<input type="hidden" name="csrf_token" value="'.$_SESSION['user_csrf_token'].'"/>';
			$output .= '</div>';
			$output .= '<div class="w3-col s3 m3 l3">';
			$output .= '<button onclick="startEdit(2);" class="w3-btn w3-block w3-border border-blue blue" name="edit_btn" type="button"><i class="fas fa-edit"></i></button>';
			$output .= '<button onclick="cancelEdit(2);" class="w3-btn w3-block w3-border w3-border-red w3-red" style="display:none;" name="cancel_btn" type="button"><i class="fas fa-times"></i></button>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '<p><button onclick="document.forms[2].submit();" class="w3-btn w3-padding-large blue" style="display:none;" name="save_btn" type="button">speichern <i class="fas fa-save"></i></button></p>';
			$output .= '</form>';
			
			$output .= '<form action="change.php" method="post">';
			$output .= '<div class="w3-section">';
			$output .= 'Guthaben';
			$output .= '<div class="w3-row">';
			$output .= '<div class="w3-col s9 m9 l9">';
			$output .= '<input type="hidden" name="attr" value="guthaben"/>';
			$output .= '<input class="w3-input w3-border" type="text" disabled="true" value="'.$user_credit.'"/>';
			$output .= '<input type="hidden" name="attr_value" value="info" placeholder="neues Passwort"/>';
			$output .= '<input type="hidden" name="csrf_token" value="'.$_SESSION['user_csrf_token'].'"/>';
			$output .= '</div>';
			$output .= '<div class="w3-col s3 m3 l3">';
			$output .= '<button class="w3-btn w3-block w3-border border-blue blue" type="submit"><i class="fas fa-edit"></i></button>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</form>';
			
			$output .= '<form action="change.php" method="post">';
			$output .= '<div class="w3-section">';
			$output .= 'Passwort';
			$output .= '<div class="w3-row">';
			$output .= '<div class="w3-col s9 m9 l9">';
			$output .= '<input type="hidden" name="attr" value="password"/>';
			$output .= '<input class="w3-border w3-input" name="attr_value" type="password" value="" placeholder="neues Passwort"/>';
			$output .= '<input type="hidden" name="csrf_token" value="'.$_SESSION['user_csrf_token'].'"/>';
			$output .= '</div>';
			$output .= '<div class="w3-col s3 m3 l3">';
			$output .= '<button class="w3-btn w3-block w3-border border-blue blue" type="submit"><i class="fas fa-save"></i></button>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</form>';
			
			$output .= '<p><a class="w3-btn w3-padding-large w3-block blue" href="/order/?s=0&ps=5">Meine Bestellungen <i class="fas fa-list"></i></a></p>';
		}
	}
}
?>		
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Account anzeigen</title>
		<?php
		require($_SERVER['DOCUMENT_ROOT'].'/include/head.inc.php');
		?>
	</head>
	<body class="gradient-blue">
		<button class="w3-btn"><i class="fas fa-bars fa-2x"></i></button>
		<div class="w3-content" style="max-width:500px;margin-top:20vh;">
			<div class="w3-center">
				<a href="/"><h2>WebBar</h2></a>
				<div class="w3-bar">
					<a class="w3-bar-item w3-btn" href="/"><i class="fas fa-home fa-2x"></i></a>
					<a class="w3-bar-item w3-btn active" href="#"><i class="fas fa-user fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="/cart/"><i class="fas fa-shopping-cart fa-2x"></i> 
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
					<form action="/search/" method="get">
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
		<script type="text/javascript" src="/js/view.js"></script>
	</body>
</html>