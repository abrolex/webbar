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
		if(empty($_GET['article_id']) || empty($_GET['attr']) || empty($_GET['attr_value']) || empty($_GET['csrf_token']))
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Es konnte kein Artikel ge&auml;ndert werden.</p>';
			$output .= '</div>';
		}
		else
		{
			if(preg_match('/[^0-9]/',$_GET['article_id']) == 0)
			{
				$aktions = array('article_name','article_variant','article_price','article_keywords');
				
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
								
										if(preg_match('/[^a-zA-ZöäüÖÄÜß0-9\s]/',$_GET['attr_value']) == 0)
										{
											$query = sprintf("
											UPDATE article
											SET article_name = '%s'
											WHERE article_id = '%s';",
											$sql->real_escape_string($_GET['attr_value']),
											$sql->real_escape_string($_GET['article_id']));
											
											break;
										}
										else
										{
											$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
											$output .= '<p>Verwenden Sie nur folgende Zeichen f&uuml;r den Artikelname: a-z, A-Z, öäüÖÄÜß, 0-9</p>';
											$output .= '</div>';
											
											break;
										}
										
									case $aktions[1]:
									
										if(preg_match('/[^a-zA-ZöäüÖÄÜß0-9\s\/\.]/',$_GET['attr_value']) == 0)
										{
											$query = sprintf("
											UPDATE article
											SET article_variant = '%s'
											WHERE article_id = '%s';",
											$sql->real_escape_string($_GET['attr_value']),
											$sql->real_escape_string($_GET['article_id']));
											
											break;
										}
										else
										{
											$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
											$output .= '<p>Verwenden Sie nur folgende Zeichen f&uuml;r die Varianten: a-z, A-Z, öäüÖÄÜß, 0-9, ./</p>';
											$output .= '</div>';
											
											break;
										}
										
									case $aktions[2]:
									
										if(preg_match('/[^0-9\/\.]/',$_GET['attr_value']) == 0)
										{
											$query = sprintf("
											UPDATE article
											SET article_price = '%s'
											WHERE article_id = '%s';",
											$sql->real_escape_string($_GET['attr_value']),
											$sql->real_escape_string($_GET['article_id']));
											
											break;
										}
										else
										{
											$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
											$output .= '<p>Verwenden Sie nur folgende Zeichen f&uuml;r die Preise: 0-9, ./</p>';
											$output .= '</div>';
											
											break;
										}
										
									case $aktions[3]:
									
										if(preg_match('/[^a-zA-ZöäüÖÄÜß0-9\s]/',$_GET['attr_value']) == 0)
										{
											$query = sprintf("
											UPDATE article
											SET article_keywords = '%s'
											WHERE article_id = '%s';",
											$sql->real_escape_string($_GET['attr_value']),
											$sql->real_escape_string($_GET['article_id']));
											
											break;
										}
										else
										{
											$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
											$output .= '<p>Verwenden Sie nur folgende Zeichen f&uuml;r die Suchbegriffe: a-z, A-Z, öäüÖÄÜß, 0-9</p>';
											$output .= '</div>';
											
											break;
										}
								}
								
								if(!empty($query))
								{
									$sql->query($query);
									
									if($sql->affected_rows == 1)
									{
										$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
										$output .= '<p>Der Artikel wurde erfolgreich gespeichert.</p>';
										$output .= '</div>';
									}
									else
									{
										$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
										$output .= '<p>Der Artikel konnte nicht gespeichert werden.</p>';
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
					$output .= '<p>Es konnte keine Aktion durchgef&uuml;hrt werden.</p>';
					$output .= '</div>';
				}
				
				$output .= '<p><a class="w3-btn w3-block w3-padding-large blue" href="view.php?article_id='.$_GET['article_id'].'"><i class="fas fa-arrow-left"></i> zur&uuml;ck</a></p>';
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
		$output .= '<p>Es konnte keine Aktion durchgef&uuml;hrt werden.</p>';
		$output .= '</div>';
	}
}
?>		
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Admin | Artikel &auml;ndern</title>
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
					<a class="w3-bar-item w3-btn" href="/admin/user/?s=0&ps=5"><i class="fas fa-user fa-2x"></i></a>
					<a class="w3-bar-item w3-btn active" href="/admin/article/?s=0&ps=5"><i class="fas fa-cube fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="/admin/order/?s=0&ps=5&state=0"><i class="fas fa-list fa-2x"></i></a>
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
					<h4>Artikel &auml;ndern</h4>
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