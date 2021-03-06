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

	$output = '';

	if(!empty($_GET))
	{
		if(empty($_GET['user_id']))
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Es wurde keine UserID gesendet.</p>';
			$output .= '</div>';
		}
		else
		{
			if(preg_match('/[^0-9]/',$_GET['user_id']) == 0)
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
					SELECT user_id,user_email,user_username,user_location_id,location_name,user_credit,user_active,user_admin
					FROM user
					INNER JOIN location ON location_id = user_location_id
					WHERE user_id = '%s';",
					$sql->real_escape_string($_GET['user_id']));
					
					$result = $sql->query($query);
					
					if($row = $result->fetch_array(MYSQLI_ASSOC))
					{
						$output .= '<p><a class="w3-btn w3-padding-large blue" href="del.php?user_id='.$row['user_id'].'&csrf_token='.$_SESSION['user_csrf_token'].'"><i class="fas fa-trash"></i></a> ';
						
						if($row['user_active'])
						{
							$output .= '<a class="w3-btn w3-padding-large blue" href="change.php?user_id='.$row['user_id'].'&attr=active&attr_value=0&csrf_token='.$_SESSION['user_csrf_token'].'"><i class="fas fa-times"></i></a> ';
						}
						else
						{
							$output .= '<a class="w3-btn w3-padding-large blue" href="change.php?user_id='.$row['user_id'].'&attr=active&attr_value=1&csrf_token='.$_SESSION['user_csrf_token'].'"><i class="fas fa-check"></i></a> ';
						}
						
						if($row['user_admin'])
						{
							$output .= '<a class="w3-btn w3-padding-large blue" href="change.php?user_id='.$row['user_id'].'&attr=admin&attr_value=0&csrf_token='.$_SESSION['user_csrf_token'].'"><i class="fas fa-arrow-down"></i></a> ';
						}
						else
						{
							$output .= '<a class="w3-btn w3-padding-large blue" href="change.php?user_id='.$row['user_id'].'&attr=admin&attr_value=1&csrf_token='.$_SESSION['user_csrf_token'].'"><i class="fas fa-arrow-up"></i></a> ';
						}
						
						$output .= '<a class="w3-btn w3-padding-large blue" href="change.php?user_id='.$row['user_id'].'&attr=password&attr_value=reset&csrf_token='.$_SESSION['user_csrf_token'].'"><i class="fas fa-key"></i></a>';
						$output .= '</p>';
						
						$output .= '<form action="change.php" method="get">';
						$output .= '<div class="w3-section">';
						$output .= '<label>E-Mail-Adresse</label>';
						$output .= '<div class="w3-row">';
						$output .= '<div class="w3-col s9 m9 l9">';
						$output .= '<input type="hidden" name="user_id" value="'.$row['user_id'].'"/>';
						$output .= '<input type="hidden" name="attr" value="email"/>';
						$output .= '<input class="w3-input w3-border" readonly="true" type="email" name="attr_value" placeholder="E-Mail-Adresse" value="'.$row['user_email'].'"/>';
						$output .= '</div>';
						$output .= '<div class="w3-col s3 m3 l3">';
						$output .= '<button onclick="startEdit(1);" class="w3-btn w3-block w3-border border-blue blue" name="edit_btn" type="button"><i class="fas fa-edit"></i></button>';
						$output .= '<button onclick="cancelEdit(1);" class="w3-btn w3-block w3-border w3-border-red w3-red" style="display:none;" name="cancel_btn" type="button"><i class="fas fa-times"></i></button>';
						$output .= '</div>';
						$output .= '</div>';
						$output .= '</div>';
						$output .= '<p><input type="hidden" name="csrf_token" value="'.$_SESSION['user_csrf_token'].'"/></p>';
						$output .= '<p><button onclick="document.forms[1].submit();" class="w3-btn w3-padding-large blue" style="display:none;" name="save_btn" type="button">speichern <i class="fas fa-save"></i></button></p>';
						$output .= '</form>';
						
						$output .= '<form action="change.php" method="get">';
						$output .= '<div class="w3-section">';
						$output .= '<label>Username</label>';
						$output .= '<div class="w3-row">';
						$output .= '<div class="w3-col s9 m9 l9">';
						$output .= '<input type="hidden" name="user_id" value="'.$row['user_id'].'"/>';
						$output .= '<input type="hidden" name="attr" value="username"/>';
						$output .= '<input class="w3-input w3-border" readonly="true" type="text" name="attr_value" placeholder="Username" value="'.$row['user_username'].'"/>';
						$output .= '</div>';
						$output .= '<div class="w3-col s3 m3 l3">';
						$output .= '<button onclick="startEdit(2);" class="w3-btn w3-block w3-border border-blue blue" name="edit_btn" type="button"><i class="fas fa-edit"></i></button>';
						$output .= '<button onclick="cancelEdit(2);" class="w3-btn w3-block w3-border w3-border-red w3-red" style="display:none;" name="cancel_btn" type="button"><i class="fas fa-times"></i></button>';
						$output .= '</div>';
						$output .= '</div>';
						$output .= '</div>';
						$output .= '<p><input type="hidden" name="csrf_token" value="'.$_SESSION['user_csrf_token'].'"/></p>';
						$output .= '<p><button onclick="document.forms[2].submit();" class="w3-btn w3-padding-large blue" style="display:none;" name="save_btn" type="button">speichern <i class="fas fa-save"></i></button></p>';
						$output .= '</form>';
						
						$output .= '<form action="change.php" method="get">';
						$output .= '<div class="w3-section">';
						$output .= '<label>Guthaben in &euro;</label>';
						$output .= '<div class="w3-row">';
						$output .= '<div class="w3-col s9 m9 l9">';
						$output .= '<input type="hidden" name="user_id" value="'.$row['user_id'].'"/>';
						$output .= '<input type="hidden" name="attr" value="credit"/>';
						$output .= '<input class="w3-input w3-border" readonly="true" type="text" name="attr_value" placeholder="Guthaben in &euro;" value="'.$row['user_credit'].'"/>';
						$output .= '</div>';
						$output .= '<div class="w3-col s3 m3 l3">';
						$output .= '<button onclick="startEdit(3);" class="w3-btn w3-block w3-border border-blue blue" name="edit_btn" type="button"><i class="fas fa-edit"></i></button>';
						$output .= '<button onclick="cancelEdit(3);" class="w3-btn w3-block w3-border w3-border-red w3-red" style="display:none;" name="cancel_btn" type="button"><i class="fas fa-times"></i></button>';
						$output .= '</div>';
						$output .= '</div>';
						$output .= '</div>';
						$output .= '<p><input type="hidden" name="csrf_token" value="'.$_SESSION['user_csrf_token'].'"/></p>';
						$output .= '<p><button onclick="document.forms[3].submit();" class="w3-btn w3-padding-large blue" style="display:none;" name="save_btn" type="button">speichern <i class="fas fa-save"></i></button></p>';
						$output .= '</form>';

						$output .= '<form action="change.php" method="get">';
						$output .= '<div class="w3-section">';
						$output .= '<label>Lokation</label>';
						$output .= '<div class="w3-row">';
						$output .= '<div class="w3-col s9 m9 l9">';
						$output .= '<input type="hidden" name="user_id" value="'.$row['user_id'].'"/>';
						$output .= '<input type="hidden" name="attr" value="location"/>';
						$output .= '<select class="w3-select w3-border w3-white" name="attr_value" disabled="true">';
						$output .= '<option value="'.$row['user_location_id'].'">'.$row['location_name'].'</option>';

						$query = "
						SELECT location_id,location_name
						FROM location";

						$result = $sql->query($query);

						while($row = $result->fetch_array(MYSQLI_ASSOC))
						{
							$output .= '<option value="'.$row['location_id'].'">'.$row['location_name'].'</option>';
						}

						$output .= '</select>';
						$output .= '</div>';
						$output .= '<div class="w3-col s3 m3 l3">';
						$output .= '<button onclick="startEdit(4);" class="w3-btn w3-block w3-border border-blue blue" name="edit_btn" type="button"><i class="fas fa-edit"></i></button>';
						$output .= '<button onclick="cancelEdit(4);" class="w3-btn w3-block w3-border w3-border-red w3-red" style="display:none;" name="cancel_btn" type="button"><i class="fas fa-times"></i></button>';
						$output .= '</div>';
						$output .= '</div>';
						$output .= '</div>';
						$output .= '<p><input type="hidden" name="csrf_token" value="'.$_SESSION['user_csrf_token'].'"/></p>';
						$output .= '<p><button onclick="document.forms[4].submit();" class="w3-btn w3-padding-large blue" style="display:none;" name="save_btn" type="button">speichern <i class="fas fa-save"></i></button></p>';
						$output .= '</form>';	
					}
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
		$output .= '<p>Es wurde keine UserId gesendet.</p>';
		$output .= '</div>';
	}
}
?>
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Admin | User anzeigen</title>
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
					<p><a class="w3-btn w3-block w3-padding-large blue" href="add.php">User erstellen <i class="fas fa-user-plus"></i></a></p>
				</div>
				<div class="w3-panel w3-white">
					<h4>User anzeigen</h4>
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