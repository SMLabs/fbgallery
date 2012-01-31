<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" >
<body>
<div id="fb-root"></div>
<?php $addon_path='../addons/default/modules/'; ?>
<script>
var FB_PLUGIN_URL = '<?php echo $this->config->item('dch_fbgallery_module_name');   ?>';
var AJAX_IMAGE_PATH='<?php echo $addon_path.$this->config->item('dch_fbgallery_module_name').'img/ajax_wait.gif'  ?>';

</script>

<script type="text/javascript" src="<?php echo $addon_path.$this->config->item('dch_fbgallery_module_name').'/js/'."fb.js";  ?>" > </script>
<script type="text/javascript" src="<?php echo $addon_path.$this->config->item('dch_fbgallery_module_name').'/js/'."jquery-1.7.1.min.js";  ?>" > </script>
<?php
$facebook = new Facebookapi;
// update options if form is submitted
if (isset($_POST['submit'])) {
	fb_options_update_albums_page($_POST['fb_albums_page']);	
	update_option('fb_number_rows', $_POST['fb_number_rows']);
	update_option('fb_style', $_POST['fb_style']);
	if($_POST['fb_number_cols'] != 0) {
		update_option('fb_number_cols', $_POST['fb_number_cols']);
	}
	if(is_numeric($_POST['fb_embedded_width'])) {
		update_option('fb_embedded_width', $_POST['fb_embedded_width']);
	}
	update_option('fb_thumb_size', $_POST['fb_thumb_size']);
	update_option('fb_albums_per_page', $_POST['fb_albums_per_page']);
	update_option('fb_hide_pages', isset($_POST['fb_hide_pages']) ? 1 : 0);
	if(isset($_POST['fb_album_cmts'])) {
		fb_options_toggle_comments(true);
		update_option('fb_album_cmts', 1);
	} else {
		fb_options_toggle_comments(false);
		update_option('fb_album_cmts', 0);
	}
	foreach($styles as $style) {
		$stylesheet = FB_PLUGIN_PATH.'styles/'.$style.'/style.css';
		if(is_writable($stylesheet)) {
			file_put_contents($stylesheet, $_POST[$style.'_stylesheet']);
		}		
	}
	$sidebar_stylesheet = FB_PLUGIN_PATH.'styles/sidebar-style.css';
	if(is_writable($sidebar_stylesheet)) {
		file_put_contents($sidebar_stylesheet, $_POST['sidebar_stylesheet']);
	}
}

// add a photo album page if there is none
/*if(get_option('fb_albums_page') == 0) {
	$page = array(
		'post_author'		=> 1,
		'post_content'	 =>'',
		'post_title'		 =>'Photos',
		'post_name'			=>'photos',
		'comment_status' =>1,
		'post_parent'		=>0
	);
	// add a photo album page 
	if(get_bloginfo('version') >= 2.1) {	
		$page['post_status'] = 'publish';
		$page['post_type']	 = 'page';
	} else {
		$page['post_status'] = 'static';
	}
	$page_id = wp_insert_post($page);
	update_option('fb_albums_page', $page_id);
}*/

// get options to fill in input fields
#$fb_session         = get_option('fb_facebook_session');
$fb_albums_page     = get_option('fb_albums_page');
$fb_number_rows     = get_option('fb_number_rows');
$fb_number_cols     = get_option('fb_number_cols');
$fb_album_cmts      = get_option('fb_album_cmts');
$fb_thumb_size      = get_option('fb_thumb_size');
$fb_albums_per_page = get_option('fb_albums_per_page');
$fb_style           = get_option('fb_style');
$fb_embedded_width  = get_option('fb_embedded_width');
$fb_hide_pages      = get_option('fb_hide_pages');

?>

<?php /* if($facebook->msg): ?>
<div id="message" class="<?php echo $facebook->error ? 'error' : 'updated' ?> fade"><p><?php echo $facebook->msg ?></p></div>
<?php endif; */ ?>

<div class="wrap">
	<div id="fb-panel">
		
		<h2 style="clear: none"><span id="mngAlbumUrl"><a href="#">Manage Albums &raquo;</a></span></h2>
		<p>This plugin links to your Facebook account and imports all of your albums into a page on your blog. To get 
			started you need to give permission to the plugin to access your Facebook account and then import 
			your albums on the management page.</p>
			<h3>Facebook</h3>
			<p>To use this plugin, you must link it to your Facebook account.</p>
			<table class="accounts">
				<tr>
					<td valign="top" width="170">
						<h3>Add an Account</h3>
						<?php if($facebook->token): ?>
						<form method="post" id="apply-permissions" action="">
                        
							<input type="hidden" name="activate-facebook" value="<?php echo $facebook->token ?>" />
                            
							<p><a id="grant-permissions" href="http://www.facebook.com/login.php?api_key=<?php echo FB_API_KEY ?>&amp;v=1.0&amp;auth_token=<?php echo $facebook->token ?>&amp;popup=0&amp;skipcookie=1&amp;ext_perm=user_photos,offline_access,user_photo_video_tags" class="button-secondary" target="_blank">Step 1: Authenticate &gt;</a></p>
							<p><a id="request-permissions" href="http://www.facebook.com/connect/prompt_permission.php?api_key=<?php echo FB_API_KEY ?>&next=<?php echo urlencode('http://www.facebook.com/desktopapp.php?api_key='.FB_API_KEY.'&popup=1') ?>&cancel=http://www.facebook.com/connect/login_failure.html&display=popup&ext_perm=offline_access,user_photos,user_photo_video_tags" class="button-secondary" target="_blank">Step 2: Get Permissions &gt;</a></p>
							<p><input type="submit" class="button-secondary" value="Step 3: Apply Permissions &gt;" /></p>
						</form>
						<?php else: ?>
						<a href="#" onclick="loginUser(); return false;"><img src="<?php echo $addon_path.$this->config->item('dch_fbgallery_module_name') ?>/img/fbconnect.jpg" alt="Facebook Login"  width="100" /> </a>
                        <div id="ajaxStatus" style="height:35px;"></div>
                        
						<?php endif ?>
					</td>
					<td valign="top">
						<h3>Current Accounts</h3>
						<?php 
						if($facebook->link_active()): 
						foreach($facebook->sessions as $key=>$value): 
						?>
						<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
							<img src="http://www.facebook.com/favicon.ico" align="absmiddle"> <a href="http://www.facebook.com/profile.php?id=<?php echo $facebook->sessions[$key]['uid'] ?>" target="_blank"><?php echo $facebook->sessions[$key]['name']; ?></a>
							<input type="hidden" name="deactivate-facebook" value="<?php echo $key ?>">
							<input type="hidden" name="page" value="<?php echo $_GET['page'] ?>">
							<input type="submit" class="button-secondary" value="Remove" onclick="return confirm('Removing an account also removes all of the photos associated with the account.  Would you like to continue?')">
						</form>
						<?php endforeach; ?>
						<?php else: ?>
						<p>There are currently no active Facebook accounts.</p>
						<?php endif; ?>
						<?php if($facebook->link_active()): ?>
						<p><small>This plugin has been given access to data from your Facebook account.	You can revoke this access at any time by clicking remove above or by changing your <a href="http://www.facebook.com/privacy.php?view=platform&tab=ext" target="_blank">privacy</a> settings.</small></p>
						<?php endif; ?>
					</td>
				</tr>
			</table>
	
		<form method="post" action="<?php echo $this_page ?>&amp;updated=true">		
			
			<table class="form-table">
				<tr>
					<th scope="row"></th>
					<td>
						<select name="fb_albums_page">
							<?php if(!fb_albums_page_is_set()): ?>
							<option value="0" selected>Please select...</option>
							<?php endif; ?>
							<?php fb_parent_dropdown($fb_albums_page); ?>
						</select><br />
						<small>Select the page you want to use to display the photo albums.</small>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Albums Per Page') ?></th>
					<td>
						<input name="fb_albums_per_page" type="text" value="<?php echo $fb_albums_per_page; ?>" size="3" />
						<small><?php _e('Number of albums to display on each page of the main gallery. Set to \'0\' to show all.') ?></small>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Number of Rows') ?></th>
					<td>
						<input name="fb_number_rows" type="text" value="<?php echo $fb_number_rows; ?>" size="3" />
						<small><?php _e('Set to \'0\' to display all.') ?></small>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Number of Columns') ?></th>
					<td>
						<input name="fb_number_cols" type="text" value="<?php echo $fb_number_cols; ?>" size="3" />
						<small><?php _e('The number of columns of pictures.') ?></small>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Display Style') ?></th>
					<td>
						<select name="fb_style">
							<?php foreach($styles as $style): 
							$selected = $style == $fb_style ? ' selected' : null; ?>
							<option value="<?php echo $style ?>"<?php echo $selected; ?>><?php echo $style ?></option>
							<?php endforeach; ?>
						</select>
						<small><?php _e('Select the style you want to use to display the albums.') ?></small>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Embedded Width') ?></th>
					<td>
						<input name="fb_embedded_width" type="text" value="<?php echo $fb_embedded_width; ?>" size="3" />px
						<small><?php _e('Restrain the width of the embedded photo if it is too wide for your theme.	Set to \'0\' to display the full size.') ?></small>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Max Thumbnail Size') ?></th>
					<td>
						<input name="fb_thumb_size" type="text" value="<?php echo $fb_thumb_size; ?>" size="3" />px
						<small><?php _e('The maximum size of the thumbnail. The default is 130px.') ?></small>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Album Commenting') ?></th>
					<td>
						<label><input name="fb_album_cmts" type="checkbox" value="1" <?php if($fb_album_cmts) echo 'checked'; ?> />
						<small><?php _e('Allow commenting on individual albums.	This must be supported by your theme.') ?></small></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Hide Album Pages') ?></th>
					<td>
						<label><input name="fb_hide_pages" type="checkbox" value="1" <?php if($fb_hide_pages) echo 'checked'; ?> />
						<small><?php _e('Exclude album pages from being displayed in places where pages are listed.') ?></small></label>
					</td>
				</tr>

			</table>


			<p class="submit">
				<input type="submit" name="submit" value="<?php _e('Update Options') ?> &raquo;" />
			</p>
		</form>
	</div>
</div> 
</body>