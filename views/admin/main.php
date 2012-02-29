<section class="title"><h4>Facebook Galleries</h4></section>
<section class="item">

<?php if(count($albums) > 0):?>
	<table>
	<?php foreach($albums as $album): ?>
	<tr class="fbgal-album">
		<th class="fbgal-heading">
			<h3>
				<?=$album->name ?>
				<a class="fbgal-remove btn red" href="<?=site_url('admin/'.$this->module.'/delete/album/'.$album->id)?>">Remove</a>
			</h3>
		</th>
	</tr>
	<tr>
		<td class="fbgal-photos">
		<ul class="fbgal-photos-pad sortphotos">
		<?php foreach($album->photos as $photo):?>
	        <li id="photo_<?=$photo->id?>">
	        	<div class="fbgal-photo <?=$photo->active==1 ? 'active' : 'inactive' ?>">
	        		<div class="fbgal-photo-pad">
						<div class="fbgal-photo-img"><img src="<?=site_url($this->module.'/thumb/?src='.$photo->source.'&width=100&height=100')?>" /></div>
						<div class="fbgal-photo-operations">
				<?php if($photo->active==1):?>
		        			<a class="fbgal-op-activation" href="<?=site_url('admin/'.$this->module.'/deactivate/photo/'.$photo->id)?>"><span>Deactivate</span></a>
		        <?php else:?>
		        			<a class="fbgal-op-activation" href="<?=site_url('admin/'.$this->module.'/activate/photo/'.$photo->id)?>"><span>Activate</span></a>
		        <?php endif;?>
		        			<a class="fbgal-op-zoom" href="<?=$photo->source?>" rel="prettyPhoto[<?=$album->id?>]"><span>View</span></a>
		        		</div>
		        		
		        	</div>
	        	</div>
	        </li>
		<?php endforeach;?>
		</ul>
	    </td>
	</tr>
	<?php endforeach;?>
	</table>    
<?php else:?>
	<table class="no-records">
		<tr>
			<td>
				<div>No Albums...</div>
			</td>
		</tr>
	</table>
<?php endif;?>
</section>