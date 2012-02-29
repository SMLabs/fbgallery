(function($){
	$(document).ready(function(){
		// Setup default javascript
		$("a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'fast',slideshow:10000, hideflash: true,autoplay_slideshow:false});
		
		// sort photos
		$( "ul.sortphotos" ).sortable({
			cursor:'crosshair',
			stop:function(event,ui){
			
				var post = {'order':[]};
				
				$('ul.sortphotos li').each(function(index,el){
					post.order.push(el.id.replace('photo_',''));
				});
												
				$.ajax({
					'url':'admin/fbgallery/order_photos',
					'type':'POST',
					'data':post,
					'success':function(){
						console.log('updated order!');
					}
				});
			}
		});
		
		$( "ul.sortable" ).disableSelection();
	});
})(jQuery)