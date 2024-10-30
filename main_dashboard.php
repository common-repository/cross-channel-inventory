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
<table width="1048px;" border="0">
  <tr>
    <td style="vertical-align: top; width: 50%; padding: 0 2%;">
      <p><strong>Edit inventory in once place, and see the changes reflected everywhere.</strong><br>
      Whenever a product is sold, inventory is automatically updated across your other channels.</p>
      <p><strong>Easy Setup</strong><br>
        Begin syncing your product inventory data across your WordPress and Amazon channels in minutes.</p>
      <p><strong>Fast Product Synchronization</strong><br>
        Update stock in one place and see it reflected across all channels. Processed orders are reflected instantly across any channel you choose.</p>
      <p><strong>Affordable Pricing Options.</strong><br>
        Several pricing tiers that scale to your business!</p>
      <p><strong>Start Your 14-Day Free Trial</strong><br>
    Visit us at <a href="https://crosschannelinventory.com/" rel="nofollow ugc">Cross Channel Inventory</a> to begin your 14-day free trial!</p></td>
    <td style="vertical-align: top; padding: 0 2%;">
      <h1>Login with your CCI Account</h1>
      <p>You must have a Cross Channel Inventory Account to continue.</p>
      <p style="margin-bottom: 10%;">
        <a style="width: 100%" target="_blank" href="https://crosschannelinventory.com" class="button button-primary button-large">Register Now &gt;</a>        
      </p>
      <h1>Supported Channels:</h1>
      <p>
        <div style="text-align: center;">
          <img style="max-width: 280px;" src="https://crosschannelinventory.com/assets/media/image/Supported_Channels.png"/>
        </div>
      </p>
    </td>
  </tr>
</table>

 

<script>
  jQuery( document ).ready(function() {
    console.log("Ready - CCI Admin");
});

</script>
