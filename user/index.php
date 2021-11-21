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
		SELECT user_username,user_email,user_credit,user_cart,user_location_id,location_name
		FROM user
		INNER JOIN location ON user_location_id = location_id
		WHERE user_id = '%s';",
		$sql->real_escape_string($_SESSION['user_id']));
		
		$result = $sql->query($query);
		
		if($row = $result->fetch_array(MYSQLI_ASSOC))
		{	
			$cart = json_decode($row['user_cart'],true);
			
			$cart_count = count($cart);
			
			$output .= '<p>Sie sind mit folgenden Daten eingeloggt.</p>';
			$output .= '<p class="w3-center"><a class="w3-btn w3-block w3-padding-large blue" href="/logout/?csrf_token='.$_SESSION['user_csrf_token'].'">Logout <i class="fas fa-sign-out-alt"></i></a></p>';
			
			$output .= '<form action="change.php" method="get">';
			$output .= '<div class="w3-section">';
			$output .= 'Username';
			$output .= '<div class="w3-row">';
			$output .= '<div class="w3-col s9 m9 l9">';
			$output .= '<input type="hidden" name="attr" value="username"/>';
			$output .= '<input class="w3-border w3-input" name="attr_value" type="text" readonly="true" value="'.$row['user_username'].'" placeholder="Username"/>';
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
			$output .= '<input class="w3-border w3-input" type="email" disabled="true" readonly="true" value="'.$row['user_email'].'" placeholder="E-Mail-Adresse"/>';
			$output .= '</div>';
			
			$output .= '<form action="change.php" method="get">';
			$output .= '<div class="w3-section">';
			$output .= 'Guthaben';
			$output .= '<div class="w3-row">';
			$output .= '<div class="w3-col s9 m9 l9">';
			$output .= '<input type="hidden" name="attr" value="credit"/>';
			$output .= '<input class="w3-input w3-border" type="text" disabled="true" value="'.$row['user_credit'].'"/>';
			$output .= '<input type="hidden" name="attr_value" value="info"/>';
			$output .= '<input type="hidden" name="csrf_token" value="'.$_SESSION['user_csrf_token'].'"/>';
			$output .= '</div>';
			$output .= '<div class="w3-col s3 m3 l3">';
			$output .= '<button class="w3-btn w3-block w3-border border-blue blue" type="submit"><i class="fas fa-edit"></i></button>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</form>';
			
			$output .= '<form action="change.php" method="get">';
			$output .= '<div class="w3-section">';
			$output .= 'Lokation';
			$output .= '<div class="w3-row">';
			$output .= '<div class="w3-col s9 m9 l9">';
			$output .= '<input type="hidden" name="attr" value="location"/>';
			$output .= '<select class="w3-select w3-border w3-white" style="height:40.5px;" name="attr_value" disabled="true">';
			$output .= '<option value="'.$row['user_location_id'].'">'.$row['location_name'].'</option>';
			
			$query = "
			SELECT location_id,location_name
			FROM location
			WHERE location_id != 1";
			
			$result = $sql->query($query);
			
			while($row = $result->fetch_array(MYSQLI_ASSOC))
			{
				$output .= '<option value="'.$row['location_id'].'">'.$row['location_name'].'</option>';
			}
			
			$output .= '</select>';
			$output .= '<input type="hidden" name="csrf_token" value="'.$_SESSION['user_csrf_token'].'"/>';
			$output .= '</div>';
			$output .= '<div class="w3-col s3 m3 l3">';
			$output .= '<button onclick="startEdit(3);" class="w3-btn w3-block w3-border border-blue blue" name="edit_btn" type="button"><i class="fas fa-edit"></i></button>';
			$output .= '<button onclick="cancelEdit(3);" class="w3-btn w3-block w3-border w3-border-red w3-red" style="display:none;" name="cancel_btn" type="button"><i class="fas fa-times"></i></button>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '<p><button onclick="document.forms[3].submit();" class="w3-btn w3-padding-large blue" style="display:none;" name="save_btn" type="button">speichern <i class="fas fa-save"></i></button></p>';
			$output .= '</form>';
			
			
			
			$output .= '<p><a class="w3-btn w3-padding-large w3-block blue" href="/user/change_passwd.php">Passwort &auml;ndern <i class="fas fa-key"></i></a></p>';
			
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
		<script src="https://www.w3schools.com/lib/w3.js"></script>
		<script type="text/javascript" src="/js/view.js"></script>
	</body>
</html>