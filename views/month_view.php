<script type='text/javascript'>

	jQuery(document).ready(function() {
	
		jQuery('#calendar').fullCalendar({
		
			editable: false,
			
			theme: true,
			
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			
			events: "<?php echo site_url('event/event_calendar_data/'); ?>",
			
			/*eventDrop: function(event, delta) {
				alert(event.title + ' was moved ' + delta + ' days\n' +
					'(should probably update your database)');
			},*/
			
			loading: function(bool) {
				//if (bool) jQuery('#loading').show();
				//else jQuery('#loading').hide();
			}
			
		});
		
	});

</script>
<style type='text/css'>

	/*body {
		margin-top: 40px;
		text-align: center;
		font-size: 14px;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		}*/
		
	#loading {
		position: absolute;
		top: 5px;
		right: 5px;
		}

	#calendar {
		width: 900px;
		margin: 0 auto;
		}
		
		html .fc a, .fc table { font-size:1em;
		}
		
		html .fc a, .fc table tr td a { font-size:1em;
		}

</style>

<div class="views">Calender Month View</div>    
<div class="buttons">
    <div class="disable_btn"><a href="<?php echo site_url('event/'); ?>">Agenda View</a></div>
    <div class="enable_btn"><a href="<?php echo site_url('event/month_view'); ?>">Month View</a></div>
</div>
<div class="seprator"></div>
<div id='calendar'></div>
