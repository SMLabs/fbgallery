<div><?php echo anchor('admin/'.$this->config->item('dch_event_module_name').'/create_event/', '+ Create New Event', 'title="Create New Event" '); ?></div><br />
<table>
    <tr style="background-color:#999;" >
        <th>Name of Event</th>
        <th>Description</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Sponsors</th>
        <th>Facebook event URL</th>
        <th>Eventbrite event URL</th>
        <th>Action</th>
    </tr>
<?php 
if( !empty( $eventData ) ) {
    foreach( $eventData as $keys => $event ) { 
		
		if( $event->status == "Active")
		{
			$status_link_label = "Deactive";
			$status_label = "Active";
		}
		else
		{
			$status_link_label = "Active";	
			$status_label = "Deactive";
		}
			
?>
    <tr>
        <td><?php echo WsStringFormat($event->name,0,20); ?></td>
        <td align="center"><?php echo WsStringFormat($event->description,0,20);?></td>
        <td align="center"><?php echo DateTime_24hFormat_Into_AMPM_Date_SlashFormat($event->start_date) ;?></td>
        <td align="center"><?php echo DateTime_24hFormat_Into_AMPM_Date_SlashFormat($event->end_date) ;?></td>
        <td align="center"><?php echo WsStringFormat($event->sponsors,0,20) ;?></td>
        <td align="center"><?php echo WsStringFormat($event->facebook_event_url,0,20) ;?></td>
        <td align="center"><?php echo WsStringFormat($event->eventbrite_event_url,0,20) ;?></td>
        <td align="center">
            <?php echo anchor(site_url('admin/'.$this->config->item('dch_event_module_name').'/edit_event/'. WsEncrypt( $event->id )),'Edit',array('class' => 'edit')) ?>&nbsp;|&nbsp;<a href="javascript: void(0);" <?php echo '" onclick="javascript:if(confirm(\'Are you sure you want to delete this event.\')){ document.location.href = \''.site_url('admin/'.$this->config->item('dch_event_module_name').'/delete_event/'. WsEncrypt( $event->id )).'\';}else{return false;}" ' ?> title="Delete Test">Delete</a>&nbsp;|&nbsp;<?php echo anchor(site_url('admin/'.$this->config->item('dch_event_module_name').'/update_event_status/'. WsEncrypt( $event->id )),$status_link_label) ?>
        </td>
    </tr>

<?php 
    } 
} else {
?>
No record found
<?php } ?>
</table>