<div>
	<ul>
<?php 
		if( !empty( $eventData ) ) {
			foreach( $eventData as $keys => $event ) { 	
?>
    	<li>
			<?php echo $event->name; ?>
		</li>
<?php 
		}
			}
?>        
    </ul>

</div>