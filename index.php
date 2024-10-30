<?php
   /*
   Plugin Name: Cross Channel Inventory
   Plugin URI: https://CrossChannelInventory.com
   description: Cross Channel Inventory keeps all your products in sync across multiple channels. Edit inventory in once place, and see the changes reflected everywhere.
   Version: 1.0.8
   Author: Cross Channel Inventory
   License: GPL-2.0+
   License URI: http://www.gnu.org/licenses/gpl-2.0.txt
   Requires at least: 4.9
   Tested up to: 5.8
   WC requires at least: 3.5
   WC tested up to: 5.1
 
   */



function shutDownCCIFunction() { 
    $error = error_get_last();
    // fatal error, E_ERROR === 1
    if ($error['type'] === E_ERROR) { 
        echo esc_html("<pre>Error: ".$error['message']."</pre>");
    } 
}
register_shutdown_function('shutDownCCIFunction');
add_action('admin_menu', 'func_cci_go_to_admin');

add_filter( 'init', function( $template ) {
    if ( isset( $_GET['cci_api'] ) ) {
        include plugin_dir_path( __FILE__ ) . 'api/index.php';
        die;
    }
} );

function func_cci_go_to_admin(){
    add_menu_page( 'CCI - Administration', 'Cross Channel Inventory', 'manage_options', 'cci-admin', 'func_cci_admin_init', '', 2 );
}

function func_cci_admin_init(){
        
global $wpdb;

$TASK = sanitize_text_field($_GET['task']);
$QUERY = sanitize_text_field($_GET['s']);
$Message = "";        


switch($TASK)
{
    case "test":

    break;
}
        
?>
    <div class="wrap">           
        <?php if(@$Message != ''){?>
        <p><strong><?php echo esc_html(@$Message);?></strong></p>
        <?php } ?>
        <h2 class="nav-tab-wrapper">
            <a href="/wp-admin/admin.php?page=cci-admin" class="nav-tab <?php if($TASK == ''){echo esc_html('nav-tab-active');}?>">Welcome</a>
            <a href="/wp-admin/admin.php?page=cci-admin&task=connect" class="nav-tab <?php if($TASK == 'connect'){echo esc_html('nav-tab-active');}?>">Connect</a>
        </h2>
        
        <!--Show Main Dashboard-->
        <?php if($TASK == ''){
            include_once "main_dashboard.php";
         }
         ?>        
        <!--End Main Dashboard-->
        
        <!--Show Connection Tab-->
        <?php if($TASK == 'connect'){
            include_once "connect.php";
         }
         ?>        
        <!--End Connection Tab-->

    <?php
}
?>