<section class="title"><h4>Facebook Galleries</h4></section>
<section class="item">

<?php if(count($photos_albums) > 0){?>
	<table>
<?php   foreach($photos_albums as $album_key=>$album_val){?>
	<tr class="fbgal-album">
		<th class="fbgal-heading">
			<h3><?=$album_val['album_name'] ?><a class="fbgal-remove btn red" href="<?=site_url('admin/'.$this->module.'/delete/album/'.$album_key) ?>">Remove</a></h3>
		</th>
	</tr>
	<tr>
		<td class="fbgal-photos">
		<div class="fbgal-photos-pad">
<?php
			$count = 0;
			foreach($album_val as $photo_key=>$photo_val){
				if($count>2){
?>
	        <div class="fbgal-photo">
	        	<div class="fbgal-photo-pad">
	        		<div class="fbgal-photo-img" style="background-image:url('<?=$photo_val['photo_picture'] ?>')">&nbsp;&nbsp;</div>
	        		
	        		<div class="fbgal-photo-operations">
	        			<a href="<?=site_url('admin/'.$this->module.'/delete/photo/'.$photo_key) ?>">Delete</a> | <a href="<?=$photo_val['photo_source']?>" rel="prettyPhoto[gallery]">View</a>
	        		</div>
	        		
	        	</div>
	        </div>
<?php 
				}
				$count++;
			}
?>
		</div>
	    </td>
	</tr>
<?php   }?>
	</table>    
<?php }else{?>
	<table class="no-records">
		<tr>
			<td>
				<div>No Albums...</div>
			</td>
		</tr>
	</table>
<?php }?>
</section>