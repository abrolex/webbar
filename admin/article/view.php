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

	$output = '';

	if(!empty($_GET))
	{
		if(empty($_GET['article_id']))
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Es wurde keine ArtikelID gesendet.</p>';
			$output .= '</div>';
		}
		else
		{
			if(preg_match('/[^0-9]/',$_GET['article_id']) == 0)
			{
				$sql = mysqli_connect($app_sqlhost,$app_sqluser,$app_sqlpasswd,$app_sqldb);
				
				if(!$sql)
				{
					$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
					$output .= '<p>Es wurde keine ArtikelID gesendet.</p>';
					$output .= '</div>';
				}
				else
				{
					$query = sprintf("
					SELECT article_id,article_name,article_variant,article_price,article_keywords
					FROM article
					WHERE article_id = '%s';",
					$sql->real_escape_string($_GET['article_id']));
					
					$result = $sql->query($query);
					
					if($row = $result->fetch_array(MYSQLI_ASSOC))
					{
						$output .= '<p><a class="w3-btn w3-padding-large blue" href="del.php?article_id='.$row['article_id'].'&csrf_token='.$_SESSION['user_csrf_token'].'"><i class="fas fa-trash"></i></a></p>';
						
						$output .= '<form action="change.php" method="get">';
						$output .= '<div class="w3-section">';
						$output .= '<label>Artikelname</label>';
						$output .= '<div class="w3-row">';
						$output .= '<div class="w3-col s9 m9 l9">';
						$output .= '<input type="hidden" name="article_id" value="'.$row['article_id'].'"/>';
						$output .= '<input type="hidden" name="attr" value="article_name"/>';
						$output .= '<input class="w3-input w3-border" readonly="true" type="text" name="attr_value" placeholder="Artikelname" value="'.$row['article_name'].'"/>';
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
						$output .= '<label>Varianten</label>';
						$output .= '<div class="w3-row">';
						$output .= '<div class="w3-col s9 m9 l9">';
						$output .= '<input type="hidden" name="article_id" value="'.$row['article_id'].'"/>';
						$output .= '<input type="hidden" name="attr" value="article_variant"/>';
						$output .= '<input class="w3-input w3-border" readonly="true" type="text" name="attr_value" placeholder="Varianten" value="'.$row['article_variant'].'"/>';
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
						$output .= '<label>Preise in &euro;</label>';
						$output .= '<div class="w3-row">';
						$output .= '<div class="w3-col s9 m9 l9">';
						$output .= '<input type="hidden" name="article_id" value="'.$row['article_id'].'"/>';
						$output .= '<input type="hidden" name="attr" value="article_price"/>';
						$output .= '<input class="w3-input w3-border" readonly="true" type="text" name="attr_value" placeholder="Preise in &euro;" value="'.$row['article_price'].'"/>';
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
						$output .= '<label>Suchbegriffe</label>';
						$output .= '<div class="w3-row">';
						$output .= '<div class="w3-col s9 m9 l9">';
						$output .= '<input type="hidden" name="article_id" value="'.$row['article_id'].'"/>';
						$output .= '<input type="hidden" name="attr" value="article_keywords"/>';
						$output .= '<input class="w3-input w3-border" readonly="true" type="text" name="attr_value" placeholder="Suchbegriffe" value="'.$row['article_keywords'].'"/>';
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
				$output .= '<p>Die ArtikelID besteht nur aus Zahlen.</p>';
				$output .= '</div>';
			}
		}
	}
	else
	{
		$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
		$output .= '<p>Es wurde keine ArtikelID gesendet.</p>';
		$output .= '</div>';
	}
}
?>
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Admin | Artikel anzeigen</title>
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
					<a class="w3-bar-item w3-btn" href="../user/"><i class="fas fa-user fa-2x"></i></a>
					<a class="w3-bar-item w3-btn active" href="index.php"><i class="fas fa-cube fa-2x"></i></a>
				</div>
			</div>
			<div class="w3-container">
				<div class="w3-panel w3-white">
					<form action="search.php" method="get">
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
					<p><a class="w3-btn w3-block w3-padding-large blue" href="add.php">Artikel erstellen <i class="fas fa-plus"></i></a></p>
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