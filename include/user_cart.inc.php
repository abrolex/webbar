<?php
$query = sprintf("
SELECT user_cart
FROM user
WHERE user_id = '%s';",
$sql->real_escape_string($_SESSION['user_id']));
	
$result = $sql->query($query);
	
if($row = $result->fetch_array(MYSQLI_ASSOC))
{
	$cart = json_decode($row['user_cart'],true);
		
	$cart_count = count($cart);
}
?>