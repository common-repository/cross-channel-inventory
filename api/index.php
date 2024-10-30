<?php

define( 'CCI_PLUGIN_DIR', plugin_dir_path( dirname( __FILE__ ) ) );
include_once CCI_PLUGIN_DIR . 'lib/class_cci.php';

$CCI = new CCI();

$json = file_get_contents('php://input');
$data = json_decode($json);

$TASK = $CCI->SanitizeText($_GET['task']);
$TOKEN = $CCI->SanitizeText($_GET['cci_token']);
$TARGET_DATE = $CCI->SanitizeText(urldecode($_GET['date']));

$STATUS = "";
$DATA = "";
$ResultData = array();

switch($TASK)
{
	case "connect":
		$TEMP_HASH = get_option("cci_temp_hash");
		
		if($TEMP_HASH === $CCI->SanitizeText($_GET['wtoken']))
		{
			//Save CCI Token
			update_option( "cci_token", $TOKEN );
			$STATUS = "OK";
		}
		else
		{
			$STATUS = "ERROR";
		}
		
	break;

	case "product_check":
		//Authenticate Token
		$CCI_TOKEN = $CCI->getCCIToken();
		if($CCI_TOKEN === $TOKEN)
		{
			$ResultData = $CCI->doesSKUExist($CCI->SanitizeText($_GET['sku']));
			$STATUS = "OK";
		}
		else
		{
			$STATUS = "NOT AUTHENTICATED";
		}		
		
	break;

	case "get_products":
		//Authenticate Token
		$CCI_TOKEN = $CCI->getCCIToken();
		if($CCI_TOKEN === $TOKEN)
		{
			$ResultData = $CCI->listProducts();
			$STATUS = "OK";
		}
		else
		{
			$STATUS = "NOT AUTHENTICATED";
		}
		
	break;

	case "add_items":
		//Authenticate Token
		$CCI_TOKEN = $CCI->getCCIToken();
		if($CCI_TOKEN === $TOKEN)
		{
			foreach($data as $ProductItem){
				//Check if item exists
				if($CCI->doesSKUExist($ProductItem->sku) == false)
				{
					$ProductID = $CCI->addProduct($ProductItem->name, $ProductItem->description, $ProductItem->stock, $ProductItem->sku);
					$CCI->updateItemPrice($ProductItem->sku, $ProductItem->price);
				}
			}
			
			$STATUS = "OK";
		}
		else
		{
			$STATUS = "NOT AUTHENTICATED";
		}		
	break;

	case "get_orders":
		//Authenticate Token
		$CCI_TOKEN = $CCI->getCCIToken();
		if($CCI_TOKEN === $TOKEN)
		{
			if(isset($TARGET_DATE))
			{
				$ResultData = $CCI->listOrders($TARGET_DATE);
			}
			else
			{
				$ResultData = $CCI->listOrders();
			}
			
			$STATUS = "OK";
		}
		else
		{
			$STATUS = "NOT AUTHENTICATED";
		}
		
	break;

	case "get_order_details":
		//Authenticate Token
		$CCI_TOKEN = $CCI->getCCIToken();
		if($CCI_TOKEN === $TOKEN)
		{
			$ResultData = $CCI->getOrderDetails($data->order_id);
			$STATUS = "OK";
		}
		else
		{
			$STATUS = "NOT AUTHENTICATED";
		}
		
	break;

	case "update_details":
		//Authenticate Token
		$CCI_TOKEN = $CCI->getCCIToken();
		if($CCI_TOKEN === $TOKEN)
		{			
			foreach($data as $ProductItem){
				$CCI->updateStock($ProductItem->sku, $ProductItem->stock);
			}
			
			$ResultData = $CCI->listProducts();
			
			$STATUS = "OK";
		}
		else
		{
			$STATUS = "NOT AUTHENTICATED";
		}		
	break;

	default:
		$STATUS = "Unknown";
	break;
}


echo json_encode(array('status'=>@$STATUS, 'resultdata'=>@$ResultData, 'sent'=>@$data));	

?>