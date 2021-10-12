<?php
$new_cart = 0;

if(!empty($_COOKIE['wb_cart_id']))
{
	if(preg_match('/[^a-z0-9]/',$_COOKIE['wb_cart_id']) == 0)
	{
		$query = sprintf("
		SELECT cart_content
		FROM cart
		WHERE cart_id = '%s';",
		$sql->real_escape_string($_COOKIE['wb_cart_id']));
			
		$result = $sql->query($query);
			
		if($row = $result->fetch_array(MYSQLI_ASSOC))
		{
			$cart = json_decode($row['cart_content'],true);
		
			$cart_count = count($cart);
		}
		else
		{
			$new_cart = 1;
		}
	}
	else
	{
		$new_cart = 1;
	}
}
else
{
	$new_cart = 1;
}
	
if($new_cart)
{
	$cart = array();
				
	$cart_count = 0;
				
	$wb_cart_id = hash('sha256',randomstr(10).strtotime('now'));
				
	$query = sprintf("
	INSERT INTO cart
	(cart_id,cart_content)
	VALUES
	('%s','%s');",
	$sql->real_escape_string($wb_cart_id),
	$sql->real_escape_string(json_encode($cart)));
				
	$sql->query($query);
				
	if($sql->affected_rows == 1)
	{
		setcookie('wb_cart_id',$wb_cart_id,time()+86400,'/');
	}
}
?>