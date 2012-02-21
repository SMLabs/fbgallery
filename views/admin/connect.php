<?php include("facebook_common.php");?>
<section class="title"><h4>Connect to Facebook</h4></section>
<section class="item">
	<table class="fbconnect">
		<tr>
			<td>
				<a href="#" onclick="loginUser('<?=site_url('admin/'.$this->module.'/import') ?>'); return false;"><img src="<?=image_url('fbconnect.png',$this->module) ?>" alt="Facebook Login" /></a>
			</td>
		</tr>
	</table>
</section>