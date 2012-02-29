<div class="fbgal-album-paging">
	<div class="fbgal-album-paging-pad">
	<?php if($next):?>
		<a href="<?=site_url($this->module.'/photos/'.$next->id)?>" class="fbgal-next-album"><span><?=$next->name?></span></a>
	<?php endif;?>
	
	<?php if($prev):?>
		<a href="<?=site_url($this->module.'/photos/'.$prev->id)?>" class="fbgal-prev-album"><span><?=$prev->name?></span></a>
	<?php endif;?>
	</div>
</div>