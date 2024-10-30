<?php

define( 'CCI_PLUGIN_DIR', plugin_dir_path(  __FILE__ ) );
include_once CCI_PLUGIN_DIR . 'lib/class_cci.php';

$CCI = new CCI();

$ACTION = sanitize_text_field(@$_GET['action']);

switch($ACTION)
{
  case "something":
    
  break;
}

?>

<style>
  p{
    font-size: 16px;
  }
</style>


<p><a href="https://crosschannelinventory.com"><img style="max-width: 1024px;" src="https://crosschannelinventory.com/assets/media/image/banner-1544x500.png"/></a></p>
<table width="1078px;" border="0">
  <tr>
    <td style="vertical-align: top; padding: 0 2%;">
      <div style="border-bottom: solid 1px #CCC; padding-bottom: 4%;">
        <?php
        if(!$CCI->testConnection())
        {
          $SITE_URL = urlencode(get_site_url());
          $tempHash = sanitize_text_field($CCI->generateTempHash());
        ?>		
            <h1>Begin Connection</h1>
            <p>Get Started: Connect Your Site to Cross Channel Inventory.</p>
            <a target="_blank" href="https://crosschannelinventory.com/connect/?site=<?php echo urlencode($SITE_URL);?>&wtoken=<?php echo sanitize_text_field($tempHash);?>&scope=wordpress" class="button button-primary button-large">Begin: Connect WordPress to Cross Channel Inventory</a>        
        <?php
        }
        else
        {
          ?>
        <h1>Welcome to Cross Channel Inventory!</h1>
        <p>Connection Successful.</p> 
          <?php
        }
        ?>
  </div>
    <div style="border-bottom: solid 1px #CCC; padding-bottom: 2%; padding-top: 2%;">
      <h1>Need Help?</h1>
      <p>Email us at <a href="mailto:Support@CrossChannelInventory.com">Support@CrossChannelInventory.com</a> for assistance.</p>
    </div>
	</td>
  </tr>
</table>

<script>
  jQuery( document ).ready(function() {
    console.log("Ready - CCI Admin");
});

</script>
