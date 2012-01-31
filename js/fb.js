// JavaScript Document

window.fbAsyncInit = function() {
    FB.init({
      appId: '285217884873953',
      status     : true, // check login status
      cookie     : true, // enable cookies to allow the server to access the session
      oauth      : true, // enable OAuth 2.0
      xfbml      : true  // parse XFBML
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
					saveTokenInDb(accessToken);
					}
				//alert(accessToken);
			});
		} else {
			//jQuery('#facebook_login_fail_message').fadeOut(800).fadeIn(800).fadeOut(400).fadeIn(400);
			alert('Error');
		}
	}, {
		scope: 'user_photos,email,offline_access,publish_stream,user_birthday,user_location,user_work_history,user_about_me,user_hometown'
	});


}
function saveTokenInDb(accessToken){
	
	var ajUrl= 'fbgallery/saveFbToken_fbgallery';
	//var manageLinkHtml=jQuery('#mngAlbumUrl').html
	var ajxstatusImgPth= '../addons/default/modules/fbgallery/img/ajax_wait.gif';
	jQuery('#ajaxStatus').html('<img src="'+ ajxstatusImgPth + '"alt="ajax wait" />');
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

