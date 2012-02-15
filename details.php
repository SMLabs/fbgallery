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
				'sl' => 'Manage your gallery.',
				'en' => 'Manage your gallery.',
				'de' => 'Manage your gallery.',
				'nl' => 'Manage your gallery.',
				'fr' => 'Manage your gallery.',
				'zh' => 'Manage your gallery.',
				'it' => 'Manage your gallery.',
				'ru' => 'Manage your gallery.',
				'ar' => 'Manage your gallery.',
				'pt' => 'Manage your gallery.',
			    'cs' => 'Manage your gallery.',
				'es' => 'Manage your gallery.',
				'fi' => 'Manage your gallery.',
				'lt' => 'Manage your gallery.'
			),
			
			'sections' => array(
			    'fbgallery' => array(
				    'name' => 'fbgallery_admin_section_title',
				    'uri' => 'admin/fbgallery',
				    'shortcuts' => array(
						array(
							'name' => 'fbgallery_admin_shortcut_import',
							'uri' => 'admin/fbgallery/import',
							'class' => ''
						)
					),
				)
		    ),
						
			'frontend' => TRUE,
			'backend' => TRUE,
			'menu' => 'content'
			
		);
	}

	public function install()
	{
		$this->dbforge->drop_table('fb_albums');
					
		$dch_album_fbgallery = "
			CREATE TABLE ".$this->db->dbprefix('fb_albums')." (
			  `id` bigint(20) DEFAULT NULL,
			  `cover_pid` bigint(20) DEFAULT NULL,
			  `owner` bigint(20) unsigned DEFAULT NULL,
			  `name` varchar(255) NOT NULL DEFAULT '',
			  `link` varchar(255) NOT NULL,
			  `created` datetime DEFAULT NULL,
			  `modified` datetime DEFAULT NULL,
			  `active` tinyint(1) unsigned NOT NULL DEFAULT '1'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		";					
							

		$this->dbforge->drop_table('fb_photos');
		
		$dch_photos_fbgallery = "
			CREATE TABLE ".$this->db->dbprefix('fb_photos')." (
			  `id` bigint(20) DEFAULT NULL,
			  `aid` bigint(20) DEFAULT NULL,
			  `picture` text NOT NULL,
			  `source` text NOT NULL,
			  `link` text NOT NULL,
			  `name` text,
			  `created` datetime DEFAULT NULL,
			  `modified` datetime NOT NULL,
			  `active` tinyint(4) unsigned NOT NULL DEFAULT '1'
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		";			
		
		if($this->db->query($dch_album_fbgallery) && $this->db->query($dch_photos_fbgallery)  )
		{
			return TRUE;
		}
	}

	public function uninstall()
	{
	
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
		<p>Facebook Photo Gallery.</p>";
	}
}
/* End of file details.php */