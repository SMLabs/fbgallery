<?php
class Facebookapi {
	var $facebook	 = null;
	var $sessions = array();
	var $token		= null;
	var $error		= false;
	var $msg			= null;
	var $secret	 = null;
	var $progress = 0;
	var $increment = null;
	
	
	

	function Facebookapi($authToken='') {
		if(!class_exists('Facebook'))
			require_once('facebook-platform/facebook.php');
		
			$this->facebook = new Facebook(array(
  				'appId'  => '285217884873953',
  				'secret' => '32138cda04f976011269ad7bcf29a60d',
			));
		$sessions = get_option('fb_facebook_session');
		
		if( $authToken  != ''){
		$this->facebook->setAccessToken($authToken);
		
		$info = $this->facebook->api('/me');
		#$uid =$info['id'];
		
			$save=true;
		if(is_array($sessions)){
			foreach($sessions as $key=>$value){
				
				if($value['authToken']==$authToken){
					$save=false;
					}
				}
		}else{
			$sessions=array();
			}
			$sessions[]= array(
			
					'authToken'=>$authToken,
					'uid' => $info['id'],
					'name' => $info['name'],
					'expires' => '0'		
			);
			
		if($save){
			update_option('fb_facebook_session',$sessions);
		}
		
		}
		#$facebook = new FB_Facebook(FB_API_KEY, FB_API_SECRET, null, true);
		#$this->facebook = $facebook->api_client;
		
		global $fb_message;
		$this->msg = &$fb_message;
		
		// check if the facebook session is the structure from older
		// versions of Facebook Gallery, if so remove it to start over
		$sessions = get_option('fb_facebook_session');
		if(isset($sessions['session_key'])) {
			update_option('fb_facebook_session', '');
		}
		
		
		// set sessions to the object
$this->set_sessions();

		// get token every time for additional users
		#$this->token = $this->get_auth_token();

		// determine how much to increment the progress bar after each request
		$this->progress  = get_option('fb_update_progress');
		$this->increment = count($this->sessions) > 0 ? 100 / (count($this->sessions) * 3) : 0;
	}
	

	/**
	 * Activates the provided UID to perform actions on that account.
	 * @param int $uid
	 * @return bool Whether or not the UID was found
	 */
	function select_session($uid) {
		foreach ($this->sessions as $session) {
			if ($session['uid'] == $uid) {
				$this->facebook->set_user($uid);
				$this->facebook->use_session_secret($session['secret']);
				$this->facebook->session_key = $session['session_key'];
				return true;
			}
		}
		return false;
	}

	function link_active() {
		return count($this->sessions) > 0;
	}

	function get_auth_token() {
		$this->facebook->session_key = '';
		$this->facebook->secret = FB_API_SECRET;
		$this->token = $this->facebook->auth_createToken();
		if(!$this->token) {
			$this->error = true;
			$this->msg = 'Facebook Gallery is unable to connect to Facebook.';
		}
		return $this->token;
	}

	function set_sessions() {
		$sessions = get_option('fb_facebook_session');

		if(!$sessions)
			return false;

		// make sure all accounts are still active
		foreach($sessions as $key => $session) {
			$this->facebook->setAccessToken($session['authToken']);
			$user = $this->facebook->api('me/');
			if($this->facebook->error_code == 102) {
				// if it can't get the user than remove it from the Facebook session array because
				// the link isn't active anymore
				$this->msg = 'The link to '.$sessions[$key]['name'].'\'s account was lost.	 Please authorize the account again.';
				unset($sessions[$key]);
				update_option('fb_facebook_session', $sessions);
			}
		}

		$this->sessions = $sessions;
		return count($sessions) > 0;
	}

	function get_auth_session($token) {
		$sessions = $this->sessions;

		try {
			$new_session = $this->facebook->auth_getSession($token);
		}
		catch( Exception $e ) {
			$this->error = true;
			$this->msg = 'Unable to activate account: ' . $e->getMessage();
			return false;
		}

		// check to see if this account is already linked
		$active = array();
		if(is_array($sessions)) {
			foreach($sessions as $value) { $active[] = $value['uid']; }
		}
		if(in_array($new_session['uid'], $active)) {
			$this->msg = 'That user is already linked to Facebook Gallery.';
			return false;
		}

		// get user's name
		$this->select_session($new_session['uid']);
		$user = $this->facebook->users_getInfo($new_session['uid'], array('name'));
		$new_session['name'] = $user[0]['name'];
		//if(!$new_session['name'])
			//return false;
		if(!is_array($sessions)) $sessions = array();
		$sessions[] = $new_session;
		update_option('fb_facebook_session', $sessions);
		$this->msg = 'Facebook Gallery is now linked to '.$new_session['name'].'\'s Facebook account.	Now you need to <a href="'.FB_MANAGE_URL.'">import</a> your albums.';

		$this->set_sessions();
		return count($sessions) > 0;
	}

	function remove_user($key) {
		// remove all of this user's albums and photos
		global $wpdb;

		$albums = fb_get_album(0, $this->sessions[$key]['uid']);
		if(is_array($albums)) {
			foreach($albums as $album) {
				fb_delete_page($album['page_id']);
			}
		}

		$wpdb->query('DELETE FROM `'.FB_ALBUM_TABLE."` WHERE `owner` = '".$this->sessions[$key]['uid'] . "'");
		$wpdb->query('DELETE FROM `'.FB_PHOTO_TABLE."` WHERE `owner` = '".$this->sessions[$key]['uid'] . "'");

		$this->msg = 'The link to '.$this->sessions[$key]['name'].'\'s Facebook account has been removed.';

		unset($this->sessions[$key]);
		update_option('fb_facebook_session', $this->sessions);
	}

	function update_progress($reset = false) {
		if($reset == true) {
			$this->progress = 0;
		}
		else {
			$this->progress = $this->progress + $this->increment;
		}
		if($this->progress > 100) {
			$this->progress = 100;
		}
		update_option('fb_update_progress', $this->progress);
		return $this->progress;
	}

	function increase_time_limit() {
		// allow the script plenty of time to make requests
		if(!ini_get('safe_mode') && !strstr(ini_get('disabled_functions'), 'set_time_limit'))
			set_time_limit(500);
	}

	function update_albums() {
		global $wpdb;

		$this->increase_time_limit();

		// reset album import progress
		$this->update_progress(true);

		// if this is the first import then reset the order at the end to make the newest on top
		$reset_order = count($this->fbgallery_model->getAlbums()) > 0 ? false : true;

		// get albums for each user from Facebook
		$fb_albums = array(); $fb_photos = array();
		foreach($this->sessions as $key=>$session) {
			// setup general info
			
			$this->facebook->setAccessToken($session['authToken']);
			$info = $this->facebook->api('/me');
			$uid =$info['id'];
			
			#$this->select_session($uid);

			try {
				// get all albums
				#$result = $this->facebook->api('/me/albums');
				
				$fql = "SELECT aid,object_id,owner,cover_pid,cover_object_id,name,created,modified,description,location,size,link,visible,modified_major,edit_link,type,can_upload,photo_count,video_count FROM album WHERE owner=$uid";
					$params = array(
						'method' => 'fql.query',
						'query' => $fql ,
						'callback' => '',
					);
				
				$result = $this->facebook->api($params);
				
				if(!is_array($result)) // the current user has no photos so move on
					continue;
				$fb_albums = array_merge($fb_albums, $result);
				$this->update_progress();

				// get all photos - queries are limited to 5,000 items per query so we need to split them up
				// technically this could still error out if the user 100+ photos per album, in that case
				// the following number would need to change to 25 or lower
				$albums_per_query = 50; $i = 0; $album_offset = 0;
				while ($album_offset < count($result)) {
					$fql="SELECT pid, aid, owner, src, src_big, src_small, link, caption, created FROM photo WHERE aid IN (SELECT aid FROM album WHERE owner = '$uid' LIMIT $albums_per_query OFFSET $album_offset)";
					$param = array(
						'method' => 'fql.query',
						'query' => $fql,
						'callback' => ''
					);

					$photos= $this->facebook->api($param);

					#$photos = $this->facebook->fql_query("SELECT pid, aid, owner, src, src_big, src_small, link, caption, created FROM photo WHERE aid IN (SELECT aid FROM album WHERE owner = '$uid' LIMIT $albums_per_query OFFSET $album_offset)");
					$fb_photos = array_merge($fb_photos, (array) $photos);
					$album_offset = ($albums_per_query * ++$i);
				}
				$this->update_progress();

				// get photos of user
				$fb_user_photos = $this->facebook->api('me/photos');
				if($fb_user_photos) {
					foreach($fb_user_photos as $k=>$v) $fb_user_photos[$k]['aid'] = $uid;
					$fb_photos = array_merge($fb_photos, (array)$fb_user_photos);
					$fb_albums[] = array(
						'aid'=>$uid,
						'cover_pid'=>$fb_user_photos[0]['pid'],
						'owner'=>$uid,
						'name'=>'Photos of '.(count($this->sessions) > 1 ? $session['name'] : 'Me'),
						'created'=>time(),
						'modified'=>time(),
						'description'=>'',
						'location'=>'',
						'link'=>"http://www.facebook.com/photo_search.php?id=$uid",
						'size'=>count($fb_user_photos)
					);
				}
			} 
			catch (Exception $e) {
				if ($e->getCode() == 102) {
					unset($this->sessions[$key]);
					update_option('fb_facebook_session', $this->sessions);
					$this->msg = "The account for {$session['name']} is no longer active.  Please add the account again from the settings panel.";
				}
				else {
					$this->msg = "There was an error while retrieving your photos: {$e->getMessage()} [Error #{$e->getCode()}]";
				}
				return false;
			}
		}

		// put all the albums in an array with the aid as the key
		$albums = $this->fbgallery_model->fb_get_album();
		if($albums) {
			foreach($albums as $album) {
				$wp_albums[$album['aid']] = $album;
			}
		}

		// go through all the facebook albums see which ones need to be added
		foreach($fb_albums as $fb_album) {
			$wp_album = isset($wp_albums[$fb_album['aid']]) ? $wp_albums[$fb_album['aid']] : false;
			$album_data = array(
				'cover_pid' => $fb_album['cover_pid'],
				'owner' => $fb_album['owner'],
				'name' => $fb_album['name'],
				'created' => !empty($fb_album['created']) ? date('Y-m-d H:i:s', $fb_album['created']) : '',
				'modified' => !empty($fb_album['modified']) ? date('Y-m-d H:i:s', $fb_album['modified']) : '',
				'description' => $fb_album['description'],
				'location' => $fb_album['location'],
				'link' => $fb_album['link'],
				'size' => $fb_album['size']
			);
			
			
			
			// if it already exists, just update it with any updated info
			if ($this->fbgallery_model->getAlbums($fb_album['aid'])) {
				
				// check to make sure the page exists and update the name of the page if needed
				$album_data['page_id'] = 0;
			
				$this->db->update($this->db->dbprefix('fb_album'), $album_data, array('aid' => $fb_album['aid']));
			}
			// it doesn't exist so create it
			else {
				$album_data['aid'] = $fb_album['aid'];
				$album_data['page_id'] = fb_add_page($fb_album['name']);
				$album_data['hidden'] = 0;
				$album_data['ordinal'] = fb_get_next_ordinal();
				$this->db->insert($this->db->dbprefix('fb_album'), $album_data);
			}
		}

		// update the photos
		$this->db->query('DELETE FROM '.$this->db->dbprefix('fb_album'));
		$ordinal = 1;
		foreach($fb_photos as $photo) {
			if($last_aid !== $photo['aid']) { // reset ordinal if we're on a new album now
				$ordinal = 1;
			}
			$album_data = array(
				'pid' => $photo['pid'],
				'aid' => $photo['aid'],
				'owner' => $photo['owner'],
				'src' => $photo['src'],
				'src_big' => $photo['src_big'],
				'src_small' => $photo['src_small'],
				'link' => $photo['link'],
				'caption' => $photo['caption'],
				'created' => date('Y-m-d H:i:s', $photo['created']),
				'ordinal' => $ordinal
			);
			$this->db->insert($this->db->dbprefix('fb_photos'), $album_data);

			// handle ordinal
			$last_aid = $photo['aid'];
			$ordinal++;
		}

		// put IDs of all albums in an array
		foreach($fb_albums as $fb_album) {
			$album_ids[] = $fb_album['aid'];
		}

		$wp_albums = fb_get_album();
	

		// now reset the order if needed
		if($reset_order) {
			fb_reset_album_order();
		}

		if(!$this->msg) {
			$this->msg = 'Albums imported successfully.';
		}
		$this->update_progress(true);
	}
}



?>
