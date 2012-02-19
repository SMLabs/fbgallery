<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '<?php echo $this->config->item('APP_ID') ?>', // App ID
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      xfbml      : true,  // parse XFBML
	  oauth      : true // enable OAuth 2.0
    });

    // Additional initialization code here
  };

  // Load the SDK Asynchronously
  (function(d){
     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     d.getElementsByTagName('head')[0].appendChild(js);
   }(document));

function loginUser() {

	//jQuery('#ajax_loader').show();
	FB.login(function (response) {
		if (response.authResponse) {
			//FB.getAuthResponse
			FB.api('/me', function(info) {
				var accessToken  =   response.authResponse.accessToken
				if(accessToken){
					//saveTokenInDb(accessToken);
					window.top.location = "<?php echo site_url('admin/'.$this->module.'/import_fanpage') ?>";
				}
				//alert(accessToken);
			});
		} else {
			//jQuery('#facebook_login_fail_message').fadeOut(800).fadeIn(800).fadeOut(400).fadeIn(400);
			alert('Error');
		}
	}, {
		scope: 'user_photos,email,offline_access,publish_stream,user_birthday,user_location,user_work_history,user_about_me,user_hometown,manage_pages'
	});


}

function saveTokenInDb(accessToken){
	alert(accessToken);
	var ajUrl= '<?php echo ADDONPATH."modules/".$this->module ?>';
	//var manageLinkHtml=jQuery('#mngAlbumUrl').html
	//var ajxstatusImgPth= '../addons/default/modules/fbgallery/img/ajax_wait.gif';
	//jQuery('#ajaxStatus').html('<img src="'+ ajxstatusImgPth + '"alt="ajax wait" />');
	jQuery.ajax({
	type: "POST",
	data: "&accessToken="+accessToken,
	url: ajUrl,
	success: function(results) {
		//jQuery('#ajaxStatus').html(jQuery('#mngAlbumUrl').html());
		jQuery('#ajaxStatus').html('Imported Successfully...');
		//location.reload();

	}
	});
		
}



</script>

<section class="title"><h4>Import Fan Page Galleries:&nbsp;&nbsp;</h4><h4><?php if($facebook->getUser()){?><img src="https://graph.facebook.com/<?php echo $facebook->getUser() ?>/picture" width="30" align="absmiddle"  /> <?php echo $fb_user_profile['name'] ?><?php }?></h4></section>
<section class="item">
<?php if($facebook->getUser()){?>
	<?php if(isset($albums)){ ?>

	<table>
	    <tr>
	        <th>Album Name</th>
	        <th>Import</th>
	    </tr>
        <?php if(count($albums['data'])>0){?>
		<?php foreach($albums['data'] as $v){ ?>
	    <tr>
	        <td><?php echo $v['name'] ?></td>
	        <td><form id="frm_<?php echo $v['id'] ?>" action="<?php echo site_url('admin/'.$this->module.'/import_fanpage') ?>" method="post" ><a href="javascript: void(0);" onclick="$('#frm_<?php echo $v['id'] ?>').submit();">Import</a><input type="hidden" value="<?php echo $v['id'] ?>" name="aid" /></form></td>
	    </tr>        
        <?php }?>        
        <?php }else{ ?>
			<td colspan="2">No record found </td>
		<?php }?>                
     </table>   
	<?php }elseif(isset($accounts)){?> 
		Please select a page from where you want to import Albums 
	<table>
	    <tr>
	        <th>Fan Page Name</th>
	    </tr>
		<?php foreach($accounts['data'] as $v){ ?>
	    <tr>
	        <td>
            <?php echo anchor(site_url('admin/'.$this->module.'/import_fanpage/'. $v['id']),$v['name']) ?>
	    </tr>        
        <?php }?>        
     </table>                   
    <?php }?>        
    
<?php }else{?>
<div>
	Please connect with facebook first:	<a href="#" onclick="loginUser(); return false;"><img src="<?php echo ADDONPATH."modules/".$this->module ?>/img/fbconnect.jpg" alt="Facebook Login"  width="100" /> </a>
</div>   
<?php }?>
</section>
