<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_FbGallery extends Module {

	public $version = '1.1';

	public function info()
	{
		return array(
			'name' => array(
				'sl' => 'Facebook Gallery',
				'en' => 'Facebook Gallery',
				'de' => 'Facebook Gallery',
				'nl' => 'Facebook Gallery',
				'fr' => 'Facebook Gallery',
				'zh' => 'Facebook Gallery',
				'it' => 'Facebook Gallery',
				'ru' => 'Facebook Gallery',
				'ar' => 'Facebook Gallery',
				'pt' => 'Facebook Gallery',
				'cs' => 'Facebook Gallery',
				'es' => 'Facebook Gallery',
				'fi' => 'Facebook Gallery',
				'lt' => 'Facebook Gallery'
			),
			'description' => array(
				'sl' => 'Manage your gallary.',
				'en' => 'Manage your gallary.',
				'de' => 'Manage your gallary.',
				'nl' => 'Manage your gallary.',
				'fr' => 'Manage your gallary.',
				'zh' => 'Manage your gallary.',
				'it' => 'Manage your gallary.',
				'ru' => 'Manage your gallary.',
				'ar' => 'Manage your gallary.',
				'pt' => 'Manage your gallary.',
			    'cs' => 'Manage your gallary.',
				'es' => 'Manage your gallary.',
				'fi' => 'Manage your gallary.',
				'lt' => 'The Split Test is a module for Test the variation.'
			),
			'frontend' => TRUE,
			'backend' => TRUE,
			'menu' => 'content'
		);
	}

	public function install()
	{
		$this->dbforge->drop_table('fb_albums');
		$dch_album_fbgallery = "CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('fb_albums')."` (
							  `aid` varchar(40) DEFAULT NULL,
							  `page_id` bigint(20) unsigned DEFAULT NULL,
							  `cover_pid` varchar(40) DEFAULT NULL,
							  `owner` bigint(20) unsigned DEFAULT NULL,
							  `name` varchar(255) NOT NULL DEFAULT '',
							  `description` text,
							  `location` varchar(255) NOT NULL DEFAULT '',
							  `link` varchar(255) NOT NULL,
							  `size` int(11) unsigned NOT NULL DEFAULT '0',
							  `created` datetime DEFAULT NULL,
							  `modified` datetime DEFAULT NULL,
							  `hidden` tinyint(1) unsigned NOT NULL DEFAULT '0',
							  `ordinal` int(11) unsigned NOT NULL DEFAULT '0',
							  UNIQUE KEY `aid` (`aid`)
							) ENGINE=MyISAM DEFAULT CHARSET=utf8;
							";

		$this->dbforge->drop_table('fb_photos');
			$dch_photos_fbgallery = "CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('fb_photos')."` (
			  `pid` varchar(40) DEFAULT NULL,
			  `aid` varchar(40) DEFAULT NULL,
			  `owner` bigint(20) unsigned DEFAULT NULL,
			  `src` varchar(255) NOT NULL DEFAULT '',
			  `src_big` varchar(255) NOT NULL DEFAULT '',
			  `src_small` varchar(255) NOT NULL DEFAULT '',
			  `link` varchar(255) NOT NULL DEFAULT '',
			  `caption` text,
			  `created` datetime DEFAULT NULL,
			  `ordinal` int(11) unsigned NOT NULL DEFAULT '0',
			  KEY `pid` (`pid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

		$this->dbforge->drop_table('fb_options');
		
		#$this->dbforge->drop_table('fb_photos');
		$dch_options_fbgallery= "CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('fb_options')."` (
  			`option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  			`blog_id` int(11) NOT NULL DEFAULT '0',
			`option_name` varchar(64) NOT NULL DEFAULT '',
			`option_value` longtext NOT NULL,
			`autoload` varchar(20) NOT NULL DEFAULT 'yes',
			 PRIMARY KEY (`option_id`),
			UNIQUE KEY `option_name` (`option_name`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=909 ;";

		$dch_options_inserts='INSERT INTO `'.$this->db->dbprefix('fb_options')."` (`option_id`, `blog_id`, `option_name`, `option_value`, `autoload`) VALUES
		(5, 0, 'fb_version', '1.1', 'yes'),
		(6, 0, 'fb_albums_page', '662', 'yes'),
		(7, 0, 'fb_number_rows', '5', 'yes'),
		(8, 0, 'fb_number_cols', '3', 'yes'),
		(9, 0, 'fb_album_cmts', '1', 'yes'),
		(10, 0, 'fb_thumb_size', '130', 'yes'),
		(11, 0, 'fb_albums_per_page', '0', 'yes'),
		(12, 0, 'fb_style', 'colorbox', 'yes'),
		(13, 0, 'fb_embedded_width', '0', 'yes'),
		(14, 0, 'fb_hide_pages', '0', 'yes'),
		(15, 0, 'fb_secret', 'd8ff185c2dc7', 'yes'),
		(16, 0, 'fb_update_progress', '0', 'yes')
		";			

		if($this->db->query($dch_album_fbgallery) && $this->db->query($dch_photos_fbgallery) && $this->db->query($dch_options_fbgallery) && $this->db->query($dch_options_inserts) )
		{
			return TRUE;
		}
	}

	public function uninstall()
	{
	
		if($this->dbforge->drop_table($this->db->dbprefix('fb_options')))
		{
			return TRUE;
		}
		if($this->dbforge->drop_table($this->db->dbprefix('fb_photos')))
		{
			return TRUE;
		}
		if($this->dbforge->drop_table($this->db->dbprefix('fb_albums')))
		{
			return TRUE;
		}
	}


	public function upgrade($old_version)
	{
		// Your Upgrade Logic
		return TRUE;
	}

	public function help()
	{
		// Return a string containing help info
		// You could include a file and return it here.
		return "<h4>Overview</h4>
		<p>This is event management module only for DCH.</p>";
	}
}
/* End of file details.php */