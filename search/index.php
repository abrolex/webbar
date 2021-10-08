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

	$showform = 1;

	if(!empty($_GET))
	{
		if(empty($_GET['search']) || $_GET['s'] == "" || empty($_GET['ps']))
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Es wurden keine Artikel gefunden.</p>';
			$output .= '</div>';
		}
		else
		{
			if(preg_match('/[^a-zA-ZöäüÖÄÜß0-9\s]/',$_GET['search']) == 0)
			{
				if(preg_match('/[^0-9]/',$_GET['s']) == 0)
				{
					if(preg_match('/[^0-9]/',$_GET['ps']) == 0)
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
							$search = '%'.$_GET['search'].'%';
							
							$query = sprintf("
							SELECT article_id
							FROM article
							WHERE article_keywords LIKE '%s';",
							$sql->real_escape_string($search));
							
							$result = $sql->query($query);
							
							$anzahl_gs = mysqli_num_rows($result);
							
							if($anzahl_gs > 0)
							{
								$output .= '<h4>'.$anzahl_gs.' Artikel gefunden</h4>';
								
								$query = sprintf("
								SELECT article_id,article_name
								FROM article
								WHERE article_keywords LIKE '%s'
								LIMIT %s,%s;",
								$sql->real_escape_string($search),
								$sql->real_escape_string($_GET['s']*$_GET['ps']),
								$sql->real_escape_string($_GET['ps']));
								
								$result = $sql->query($query);
								
								while($row = $result->fetch_array(MYSQLI_ASSOC))
								{
									$output .= '<div class="w3-row w3-section">';
									$output .= '<div class="w3-col s9 m9 l9">';
									$output .= '<button class="w3-btn w3-block grey">'.$row['article_name'].'</button>';
									$output .= '</div>';
									$output .= '<div class="w3-col s3 m3 l3">';
									$output .= '<a class="w3-btn w3-block blue" href="/view/?article_id='.$row['article_id'].'"><i class="fas fa-arrow-right"></i></a>';
									$output .= '</div>';
									$output .= '</div>';
								}
								
								if($anzahl_gs > $_GET['ps'])
								{
									$anzahl_s = ceil($anzahl_gs/$_GET['ps']);
									
									$output .= '<form action="/search/" method="get">';
									$output .= '<p><input type="hidden" name="search" value="'.$_GET['search'].'"/></p>';
									$output .= '<p><select onchange="document.forms[1].submit();" class="w3-select w3-border w3-white" name="s">';
									$output .= '<option value="">Seite ';
									$output .= $_GET['s']+1;
									$output .= ' von '.$anzahl_s.'</option>';
									
									for($i = 0; $i < $anzahl_s; $i++)
									{
										$output .= '<option value="'.$i.'">Seite ';
										$output .= $i+1;
										$output .= ' von '.$anzahl_s.'</option>';
									}
									
									$output .= '</select></p>';
									$output .= '<p><input type="hidden" name="ps" value="'.$_GET['ps'].'"/></p>';
									$output .= '</form>';
								}
								else
								{
									$output .= '<form></form>';
								}
								
								$output .= '<form action="/search/" method="get">';
								$output .= '<p><input type="hidden" name="search" value="'.$_GET['search'].'"/></p>';
								$output .= '<p><input type="hidden" name="s" value="0"/></p>';
								$output .= '<p><select onchange="document.forms[2].submit();" class="w3-select w3-border w3-white" name="ps">';
								$output .= '<option value="">'.$_GET['ps'].' pro Seite</option>';
								$output .= '<option value="5">5 pro Seite</option>';
								$output .= '<option value="10">10 pro Seite</option>';
								$output .= '<option value="15">15 pro Seite</option>';
								$output .= '</select></p>';
								$output .= '</form>';
							}
							else
							{
								$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
								$output .= '<p>Es wurden keine Artikel gefunden.</p>';
								$output .= '</div>';
							}
						}
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
					$output .= '<p>Es ist ein Fehler aufgetreten.</p>';
					$output .= '</div>';
				}
			}
			else
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Verwenden Sie nur folgende Zeichen f&uuml;r den Suchbegriff: a-z, A-Z, öäüÖÄÜß, 0-9</p>';
				$output .= '</div>';
			}
		}
	}
	else
	{
		$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
		$output .= '<p>Geben Sie einen Suchbegriff ein.</p>';
		$output .= '</div>';
	}
}
?>			
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Artikel suchen</title>
		<?php
		require($_SERVER['DOCUMENT_ROOT'].'/include/head.inc.php');
		?>
	</head>
	<body class="gradient-blue">
		<button class="w3-btn"><i class="fas fa-bars fa-2x"></i></button>
		<div class="w3-content" style="max-width:500px;margin-top:20vh;">
			<div class="w3-center">
				<h2>WebBar</h2>
				<div class="w3-bar">
					<a class="w3-bar-item w3-btn" href="/"><i class="fas fa-home fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="/user/"><i class="fas fa-user fa-2x"></i></a>
					<a class="w3-bar-item w3-btn" href="/cart/"><i class="fas fa-shopping-cart fa-2x"></i></a>
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
	</body>
</html>