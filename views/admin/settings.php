<?php include("facebook_common.php");?>
<div class="one_whole">
	<section class="title"><h4>Facebook Gallery Settings:</h4></section>
	<section class="item">
		<div class="form_inputs ui-tabs-panel ui-widget-content ui-corner-bottom">
			<form  action="<?php echo site_url('admin/'.$this->module.'/settings') ?>" method="post" >
				<fieldset>
					<ul class="ui-sortable">
						<li id="site_name" class="">
							<label for="app_id">
								Application ID <small>The ID of the application you'll use to connect to facebook.</small>
							</label>
							<div class="input type-text">
								<input type="text" name="app_id" value="<?php echo $app_id ?>" id="app_id" class="text width-20">
							</div>
						</li>
						
						<li id="site_slogan" class="even">
							<label for="app_secret">
								Application Secret<small>The secret key of the application you'll use to connect to facebook.</small>
							</label>
							<div class="input type-text">
								<input type="text" name="app_secret" value="<?php echo $app_secret ?>" id="app_secret" class="text width-20">
							</div>
						</li>
					</ul>
				</fieldset>
				<div class="buttons padding-top align-right">
					<button type="submit" name="btnAction" value="save" class="btn blue"><span>Save</span></button>
				</div>
			</form>
		</div>
	</section>
</div>

<div class="one_whole">
	<section class="title"><?php if(!$facebook->getUser()):?><h4>You need to connect to Facebook</h4><?php else:?><h4>You are connected to Facebook</h4><?php endif;?></section>
	<section class="item">
		<table class="fbconnect">
			<tr>
				<td>
					<?php if(!$facebook->getUser()):?>
					<a href="#" onclick="loginUser('<?=site_url('admin/'.$this->module.'/import') ?>'); return false;"><?php echo Asset::img('module::fbconnect.png','Facebook Connect'); ?> </a>
					<?php else:?>
					<span><img src="https://graph.facebook.com/<?php echo $facebook->getUser()?>/picture/200x200" /> <?=$fb_user_profile['name'] ?></span>
					<?php endif;?>
				</td>
			</tr>
		</table>
	</section>
</div>