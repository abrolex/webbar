<?php
require($_SERVER['DOCUMENT_ROOT'].'/include/config.inc.php');

$output = '';

$showform = 1;

if(!empty($_GET))
{
	if(empty($_GET['article_name']) || empty($_GET['article_variant']) || empty($_GET['article_price']) || empty($_GET['article_keywords']))
	{
		$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
		$output .= '<p>Es wurden nicht alle Felder ausgef&uuml;llt.</p>';
		$output .= '</div>';
	}
	else
	{
		if(preg_match('/[^a-zA-ZöäüÖÄÜß0-9\s]/',$_GET['article_name']) == 0)
		{
			$article_name = $_GET['article_name'];
			
			if(preg_match('/[^a-zA-ZöäüÖÄÜß0-9\s\/\.]/',$_GET['article_variant']) == 0)
			{
				$article_variant = $_GET['article_variant'];
				
				if(preg_match('/[^0-9\.\/]/',$_GET['article_price']) == 0)
				{
					$article_price = $_GET['article_price'];
					
					if(preg_match('/[^a-zA-ZöäüÖÄÜß0-9\s]/',$_GET['article_keywords']) == 0)
					{
						$article_keywords = $_GET['article_keywords'];
						
						$sql = mysqli_connect($app_sqlhost,$app_sqluser,$app_sqlpasswd,$app_sqldb);
						
						if(!$sql)
						{
							$output .= '<div class="w3-panel w3-text-red">';
							$output .= '<p>Es wurden nicht alle Felder ausgef&uuml;llt.</p>';
							$output .= '</div>';
						}
						else
						{
							$query = sprintf("
							INSERT INTO article
							(article_name,article_variant,article_price,article_keywords)
							VALUES
							('%s','%s','%s','%s');",
							$sql->real_escape_string($article_name),
							$sql->real_escape_string(json_encode(explode('/',$article_variant))),
							$sql->real_escape_string(json_encode(explode('/',$article_price))),
							$sql->real_escape_string($article_keywords));
							
							$sql->query($query);
							
							if($sql->affected_rows == 1)
							{
								$showform = 0;
								
								$output .= '<div class="w3-panel w3-border w3-border-green w3-text-green">';
								$output .= '<p>Der Artikel wurde erfolgreich angelegt.</p>';
								$output .= '</div>';
								$output .= '<p><a class="w3-btn w3-block w3-padding-large blue" href="add.php">Artikel erstellen <i class="fas fa-plus"></i></a></p>';
							}
							else
							{
								$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
								$output .= '<p>Der Artikel konnte nicht angelegt werden.</p>';
								$output .= '</div>';
							}
						}
					}
					else
					{
						$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
						$output .= '<p>Verwenden Sie nur folgende Zeichen für die Suchbegriffe: a-z, A-Z, öäüÖÄÜß, 0-9</p>';
						$output .= '</div>';
					}
				}
				else
				{
					$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
					$output .= '<p>Verwenden Sie nur folgende Zeichen für die Preise: 0-9, ./</p>';
					$output .= '</div>';
				}
			}
			else
			{
				$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
				$output .= '<p>Verwenden Sie nur folgende Zeichen für die Varianten: a-z, A-Z, öäüÖÄÜß, 0-9, ./</p>';
				$output .= '</div>';
			}
		}
		else
		{
			$output .= '<div class="w3-panel w3-border w3-border-red w3-text-red">';
			$output .= '<p>Verwenden Sie nur folgende Zeichen für den Artikelname: a-z, A-Z, öäüÖÄÜß, 0-9</p>';
			$output .= '</div>';
		}
	}				
}

if($showform)
{
	$output .= '<form action="add.php" method="get">';
	$output .= '<p><label for="article_name">Artikelname</label><input class="w3-input w3-border" type="text" name="article_name" placeholder="Cola"';
	
	if(!empty($article_name))
	{
		$output .= ' value="'.$article_name.'"';
	}
	
	$output .= '/></p>';
	$output .= '<p><label for="article_variant">Varianten</label><input class="w3-input w3-border" type="text" name="article_variant" placeholder="groß/klein"';
	
	if(!empty($article_variant))
	{
		$output .= ' value="'.$article_variant.'"';
	}
	
	$output .= '/></p>';
	$output .= '<p><label for="article_price">Preise in &euro;</label><input class="w3-input w3-border" type="text" name="article_price" placeholder="5.00/3.00"';
	
	if(!empty($article_price))
	{
		$output .= ' value="'.$article_price.'"';
	}
	
	$output .= '/></p>';
	$output .= '<p><label for="article_keywords">Suchbegriffe</label><input class="w3-input w3-border" type="text" name="article_keywords"';
	
	if(!empty($article_keywords))
	{
		$output .= ' value="'.$article_keywords.'"';
	}
	
	$output .= '/></p>';
	$output .= '<p><button class="w3-btn w3-block w3-padding-large blue" type="submit">Artikel erstellen <i class="fas fa-plus"></i></button></p>';
	$output .= '</form>';
}
?>
<!DOCTYPE HTML>
<html lang="de">
	<head>
		<title>WebBar | Admin | Artikel erstellen</title>
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