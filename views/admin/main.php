<section class="title"><h4>Facebook Galleries</h4></section>
<section class="item">

    <?php if(count($photos_albums)>0){?>
<table>
<?php foreach($photos_albums as $album_key=>$album_val){?>
<tr>
	<th><?php echo $album_val['album_name'] ?>&nbsp;&nbsp;<a href="<?php echo site_url('admin/'.$this->module.'/delete/album/'.$album_key) ?>">Delete Album</a></th>
</tr>
<tr>
	<td>
	<?php
		$count = 0;
		foreach($album_val as $photo_key=>$photo_val){
			if($count>2){
		?>
        <div style="float: left; width: 100px; height: 100px; overflow: hidden; padding: 12px 0pt; margin: 0pt 5px;" >
            <img src="<?php echo $photo_val['photo_picture'] ?>" height="100"  />
            <div><a href="<?php echo site_url('admin/'.$this->module.'/delete/photo/'.$photo_key) ?>">X</a></div>
        </div>
    <?php 
			}
		$count++;
		}?>
    </td>
</tr>
<?php }?>
</table>    
<?php }else{?>
	No record found
<?php }?>
</section>