<div class="views">Calender Agenda View</div>    
<div class="buttons">
    <div class="enable_btn"><a href="<?php echo site_url('event/'); ?>">Agenda View</a></div>
    <div class="disable_btn"><a href="<?php echo site_url('event/month_view'); ?>">Month View</a></div>
</div>
<div class="seprator"></div>
<?php  if( !empty( $eventData ) ) {
			foreach( $eventData as $keys => $event ) { 	?>
<div class="postings">
    <div class="a_date">
        <div class="a_date_text">
			<div class="day"><?php echo date('l',strtotime($event->event_date)); ?>.</div>
            <div class="month"><?php echo date('F d,Y',strtotime($event->event_date)); ?> </div>
            <div class="time"><?php echo Time24hFormat_Into_AMPMTime($event->start_time); ?> - <?php echo Time24hFormat_Into_AMPMTime($event->end_time); ?></div>
        </div>
    </div>
        
    <article class="a_detail">
        <header><h2 class="posting_hdr"><?php echo $event->name; ?> </h2></header>
        <p>
            <?php echo $event->description; ?> 
            <div class="a_detail_text01">Sponsors</div>
            <div class="a_detail_text02"><?php echo $event->sponsors; ?></div>
            <div class="a_detail_text01">View Event on:</div>
            <div class="a_detail_btn">
                <a href="<?php echo CheckHTTP_InURL($event->eventbrite_event_url); ?>"><div class="orange_btn">Eventbrite</div></a>
                <a href="<?php echo CheckHTTP_InURL($event->facebook_event_url); ?>"><div class="facebook_btn">facebook</div></a>
            </div>
        </p>
    </article>
</div>
<?php } }?>