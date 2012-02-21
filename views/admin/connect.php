<?php include("facebook_common.php");?>
<div class="fb-connect">
	Please connect with facebook first:	<a href="#" onclick="loginUser('<?=site_url('admin/'.$this->module.'/import') ?>'); return false;"><img src="<?=image_url('fbconnect.jpg',$this->module) ?>" alt="Facebook Login"  width="100" /> </a>
</div>