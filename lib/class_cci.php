<?php

class CCI{	
	
	public const TABLE_NAME = "";
    public const LOG_TABLE_NAME = "";
	
	function hasCCIToken()
	{
		global $wpdb;
		
		$CCI_TOKEN = get_option("cci_token");
		
		if($CCI_TOKEN != false && $CCI_TOKEN != "")
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function getCCIToken()
	{
		global $wpdb;
		
		$CCI_TOKEN = get_option("cci_token");
		
		return $CCI_TOKEN;
	}
	
	function testConnection()
	{
		if($this->hasCCIToken())
		{
			$CCI_TOKEN = $this->getCCIToken();
			
			$Host = "https://crosschannelinventory.com/oauth_test/?token=".$CCI_TOKEN;
			
			$response = wp_remote_get( $Host );
			$body     = wp_remote_retrieve_body( $response );
			
			$arrJsonResponse = json_decode($body, true);
			
			if($arrJsonResponse['status'] == "true")
			{
				return true;
			}
			else
			{
				return false;
			}			
		}
		else{
			return false;
		}
	}
	
	function generateTempHash()
	{
		global $wpdb;
		
		$NewHash = $this->generateAlphaNumeric();
		update_option( "cci_temp_hash", $NewHash );
		
		return $NewHash;
	}
	
	function generateAlphaNumeric()
	{
		return bin2hex(random_bytes(16));
	}
	
	function listProducts()
	{
		global $wpdb;
		
		$arrResults = $wpdb->get_results("SELECT `ID`,
										 `post_title` FROM `wp_posts` where post_type = 'product' and post_status = 'publish'");

		
		/*$params = array(
			'posts_per_page' => 5,
			'post_type' => 'product'
		);

		$WPQuery = new WP_Query($params);
		$AllProducts = $WPQuery->posts();*/
		
		$arrInventory = array();
		
		
		foreach($arrResults as $Product)
		{
			$arrProduct = wc_get_product( $Product->ID );
			$ProductSKU = $arrProduct->get_sku();

			if($ProductSKU != "")
			{
				$arrInventory[] = array("id"=>$Product->ID,
										"name"=>$arrProduct->get_name(),
										"description"=>$arrProduct->get_description(),
										"price"=>$arrProduct->get_price(),
										"stock"=>$arrProduct->get_stock_quantity(),
										"date"=>$arrProduct->get_date_created(),
										"image"=>get_the_post_thumbnail_url($Product->ID),
										"sku"=>$arrProduct->get_sku()
										);
			}

		}
		
		return $arrInventory;
	}
	
	function listOrders($LastOrderDate = null)
	{
		global $wpdb;

		if($LastOrderDate != null)
		{
			//Place $LastOrderDate into site's local timezone
			$SiteTimeZone = get_option('gmt_offset');
			
			//echo "<br>Timezone: ".$SiteTimeZone."<br>";

			$ConvertedOrderDate = new DateTime($LastOrderDate, new DateTimeZone("America/New_York"));
			$ConvertedOrderDate->setTimeZone(new DateTimeZone($SiteTimeZone));
			$ConvertedOrderDate->modify('-1 hour');
			$ConvertedOrderDatetoString = $ConvertedOrderDate->format('Y-m-d H:i:s');
			
			/*echo "<br>Received Order Date: ".$LastOrderDate;
			echo "<br>Converted Date (-1 hour to be safe): ".$ConvertedOrderDatetoString. " - Converted To Site Timezone: ".$SiteTimeZone;
			echo "<br>";*/
	
			$arrResults = $wpdb->get_results($wpdb->prepare("SELECT `ID`, `post_title` FROM `wp_posts` WHERE `post_type` = 'shop_order'
																					AND `post_date` >= %s
																					AND `post_status` = 'wc-completed'", $ConvertedOrderDatetoString ));
		}
		else
		{
			$arrResults = $wpdb->get_results("SELECT `ID`, `post_title` FROM `wp_posts` WHERE `post_type` = 'shop_order'
																					AND `post_status` = 'wc-completed'
																					ORDER BY `post_date` ASC LIMIT 100;");
		}
		
		
		$arrOrders = array();

		foreach($arrResults as $Order)
		{
			//Get All Products sold in order
			$order = wc_get_order( $Order->ID );
			$SoldItems = $order->get_items();
			
			/*echo "<pre>";
			print_r($SoldItems);
			echo "</pre>";*/
			
			foreach($SoldItems as $SoldItem)
			{
				$product_id = $SoldItem->get_product_id();
				$arrProduct = wc_get_product( $product_id );
				
				//echo "<br> Checking: ".$product_id;
				/*echo "<pre>";
				print_r($arrProduct);
				echo "</pre>";*/			
				if($product_id > 0)
				{
					$arrOrders[] = array("order_id"=>$Order->ID,
										"stock_change"=>$SoldItem->get_quantity(),
										"date"=>$order->get_date_completed(),
										"sku"=>$arrProduct->get_sku()
										);				
				}
			}
		}
		
		/*echo "<pre>";
		print_r($arrOrders);
		echo "</pre>";*/
			
		return $arrOrders;
	}
	
	function getOrderDetails($OrderID)
	{
		$arrDetails = array();
		
		//Get All Products sold in order
		$order = wc_get_order( $OrderID );
		$SoldItems = $order->get_items();
		
		/*echo "<pre>";
		print_r($SoldItems);
		echo "</pre>";*/
		
		foreach($SoldItems as $SoldItem)
		{
			$product_id = $SoldItem->get_product_id();
			$arrProduct = wc_get_product( $product_id );
			
			//echo "<br> Checking: ".$product_id;
			/*echo "<pre>";
			print_r($arrProduct);
			echo "</pre>";*/
			
			if($product_id > 0)
			{
				$arrDetails[] = array("order_id"=>$OrderID,
									"stock_change"=>$SoldItem->get_quantity(),
									"date"=>$order->get_date_completed(),
									"sku"=>$arrProduct->get_sku()
									);				
			}
		}
		
		return $arrDetails;
	}
	
	function doesSKUExist($SKU)
	{
		global $wpdb;
		
		$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $SKU ) );
		
		if($product_id)
		{
			//if ( $product_id ) return new WC_Product( $product_id );
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function addProduct($ProductTitle, $ProductDescription, $Stock, $SKU)
	{
		global $wpdb;
		
		$post_id = wp_insert_post( array(
			'post_title' => $ProductTitle,
			'post_content' => $ProductDescription,
			'post_status' => 'publish',
			'post_type' => "product",
		));
		
		wp_set_object_terms( $post_id, 'simple', 'product_type' );
		
		//update_post_meta( $post_id, '_price', $ProductPrice );
		update_post_meta( $post_id, '_manage_stock', 'yes');
		update_post_meta( $post_id, '_sku', $SKU );
		
		if($Stock > 0)
		{
			update_post_meta($post_id, '_stock', $Stock);
			update_post_meta($post_id, '_stock_status', 'instock');
		}
		else
		{
			update_post_meta($post_id, '_stock_status', 'outofstock');
		}		
		
		return $post_id;
	}
	
	function updateStock($SKU, $NewQTY)
	{
		global $wpdb;
		
		$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $SKU ) );
		
		//echo "Found: ".$product_id;
		
		if($product_id > 0)
		{
			if($NewQTY > 0)
			{
				update_post_meta($product_id, '_stock', $NewQTY);
				update_post_meta($product_id, '_manage_stock', 'yes');
				update_post_meta($product_id, '_stock_status', 'instock');
			}
			else
			{
				update_post_meta($product_id, '_stock', $NewQTY);
				update_post_meta($product_id, '_stock_status', 'outofstock');
			}
			
			// And finally (optionally if needed)
			wc_delete_product_transients( $product_id ); // Clear/refresh the variation cache
		}
	}
	
	function updateItemPrice($SKU, $NewPrice = 0.00)
	{
		global $wpdb;
		
		$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $SKU ) );
		
		if($product_id > 0)
		{
			if($NewPrice != "")
			{
				update_post_meta($product_id, '_regular_price', $NewPrice);
				update_post_meta($product_id, '_price', $NewPrice);
			}
		}
	}
	
	function SanitizeText($input)
	{
		$output = sanitize_text_field($input);
		
		return $output;
	}
}

?>