<script>
jQuery(document).ready(function() {
	
	GetEventDaysList( jQuery('#id').val() );
	
	jQuery('#start_date').datetimepicker({
		//showSecond: true,
		//timeFormat: 'hh:mm:ss',
		ampm: true,
		onClose: function(dateText, inst) {
			var endDateTextBox = jQuery('#end_date');
			if (endDateTextBox.val() != '') {
				var testStartDate = new Date(dateText);
				var testEndDate = new Date(endDateTextBox.val());
				if (testStartDate > testEndDate)
					endDateTextBox.val(dateText);
			}
			else {
				endDateTextBox.val(dateText);
			}
			
			SetEventDaysList(jQuery('#start_date').val(), jQuery('#end_date').val(),jQuery('#id').val());
		},
		onSelect: function (selectedDateTime){
			var start = jQuery(this).datetimepicker('getDate');
			///jQuery('#end_date').datetimepicker('option', 'minDate', new Date(start.getTime()));
			jQuery('#end_date').datetimepicker('getDate');
		}
	});
	
	jQuery('#end_date').datetimepicker({
		//showSecond: true,
		//timeFormat: 'hh:mm:ss',
		ampm: true,
		onClose: function(dateText, inst) {
			var startDateTextBox = jQuery('#start_date');
			if (startDateTextBox.val() != '') {
				var testStartDate = new Date(startDateTextBox.val());
				var testEndDate = new Date(dateText);
				if (testStartDate > testEndDate)
					startDateTextBox.val(dateText);
			}
			else {
				startDateTextBox.val(dateText);
			}
			
			SetEventDaysList(jQuery('#start_date').val(), jQuery('#end_date').val(), jQuery('#id').val());
			
		},
		onSelect: function (selectedDateTime){
			var end = jQuery(this).datetimepicker('getDate');
			//jQuery('#start_date').datetimepicker('option', 'maxDate', new Date(end.getTime()) );
			jQuery('#start_date').datetimepicker('getDate');
		}
	});
	
	 jQuery("#frm_create_event").validate();
});

function SetEventDaysList(start_date, end_date, event_id )
{
	
	jQuery.ajax({
		type : "POST",
		url  : "<?php echo site_url('admin/event/set_event_scheduler/'); ?>",
		dataType: "json",
		data : "start_date="+start_date+"&end_date="+end_date+"&act=edit&event_id="+event_id,
		success : 
		function(response)
		{
			jQuery('#event_date_list').html(response.content);
			
			jQuery('.dp').timepicker({
				hourGrid: 4,
				minuteGrid: 10,
				//showSecond: true,
				//timeFormat: 'hh:mm:ss',
				ampm: true
			});
		}
	 });  	
}

function GetEventDaysList( event_id )
{
	jQuery.ajax({
		type : "POST",
		url  : "<?php echo site_url('admin/event/get_event_scheduler/'); ?>",
		dataType: "json",
		data : "event_id="+event_id,
		success : 
		function(response)
		{
			jQuery('#event_date_list').html(response.content);
			
			jQuery('.dp').timepicker({
				hourGrid: 4,
				minuteGrid: 10,
				//showSecond: true,
				//timeFormat: 'hh:mm:ss',
				ampm: true
			});
		}
	 });  		
}

</script>

<style type="text/css">
* { font-family: Verdana; font-size: 96%; }
label { 
	width: 14em; 
	cursor:default;
	font-size:12px;
	font-weight:normal;
	float:left;
}

input.error{ border:1px solid #F30; }
label.error { float:right ; color: red; padding-left: .5em; vertical-align: top; background:none; font-size:0px;}
p { clear: both; }
.submit { margin-left: 12em; }
em { font-weight: bold; padding-right: 1em; vertical-align: top; }

select {
	min-width:90px;
	visibility:visible;
	}
input {
	width:200px;
	position:relative;
	top:-12px;	

}

pre {
	padding:1px;	
	margin:1px;
	float:none;
	font-size:10px;
	width:330px;
	white-space:normal;
}	
</style>

<form action="<?php echo site_url('admin/'.$this->config->item('dch_event_module_name').'/update_event/'); ?>" method="post" name="frm_update_event" id="frm_update_event" >
	<input type="hidden" name="id" id="id" value="<?php echo $event->id;?>"  />
	<div> <span style="font-size:16px; font-weight:bold;">Edit Event</span><br />
    <div style="padding:20px;">
        <div>
            <label>Name:</label>&nbsp;
            <input type="text" name="name" id="name" value="<?php echo $event->name;?>" class="required"   />
        </div><br />
        <div>
			<label for="start_date">Start Date & Time</label>&nbsp;
			<input type="text" id="start_date" name="start_date" value="<?php echo DateTime_24hFormat_Into_AMPM_SlashFormat($event->start_date);?>" class="required" />
       </div><br />
       <div>         
			<label for="end_date">End Date & Time</label>&nbsp;
			<input type="text" id="end_date" name="end_date" value="<?php echo DateTime_24hFormat_Into_AMPM_SlashFormat($event->end_date);?>" class="required" />        
        </div>        
        
        <div id="event_date_list" ></div><br />
    	
        <div><label>Sponsors:</label>&nbsp;
            <input type="text" name="sponsors" id="sponsors" value="<?php echo $event->sponsors;?>" class="required"  />
    	</div><br />
		<div>
            <label>URL for Facebook event:</label>&nbsp;
            <input type="text" name="facebook_event_url" id="facebook_event_url" value="<?php echo $event->facebook_event_url;?>" class="required"  />
    	</div><br />
		<div>
            <label>URL for Eventbrite event:</label>&nbsp;
            <input type="text" name="eventbrite_event_url" id="eventbrite_event_url" value="<?php echo $event->eventbrite_event_url;?>" class="required"  />
    	</div><br />
		<div>
            <label>Description:</label><br /><br />
            <textarea id="description" name="description" cols="50" rows="2" class="required"><?php echo $event->description;?></textarea>
    	</div><br />        

    </div>
    
	<div class="buttons align-left">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save') )); ?>
	</div>
   </div> 
</form>