<!-- Start Fbgallery Output -->

<section class="fbgal-section main">
	<div class="fbgal-main-links">
		<h3>Chose an Album</h3>
		<ul>
			<li><a href="<?php echo site_url($this->module) ?>">View All Galleries</a></li>
<?php foreach($albums as $album):?>
			<li><a href="<?php echo site_url($this->module.'/photos/'.$album->id) ?>"><?php echo $album->name ?></a></li>
<?php endforeach;?>
		</ul>
	</div>

	<div class="fbgal-main-albums">
		<ul>
<?php foreach($albums as $album):?>
			<li class="fbgal-thumb"> 
				<a href="<?php echo site_url($this->module.'/photos/'.$album->id) ?>">
					<img src="<?=site_url($this->module.'/thumb/?src='.$album->source.'&width=210&height=160')?>" class="fbgal-thumb-photo" />
					<span class="fbgal-thumb-overlay"> <img src="<?=image_url('zoom.png',$this->module)?>" class="fbgal-zoom-img" /> </span>
					<?php echo $album->name ?>
				</a>
			</li>
<?php endforeach;?>
		</ul>
	</div>
</section>

<!-- End Fbgallery output -->