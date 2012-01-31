<?php
class Admin extends Admin_Controller {

	private $user_id = "";
	
	/**
	 * Constructor method
	 *
	 * @return void
	 */
	function __construct()
	{
		// Call the parent's constructor method
		parent::__construct();
		
		$this->config->set_item('dch_fbgallery_module_name', "fbgallery" );
		$this->load->library('facebookapi');
		
		// Get the user ID, if it exists
		$user = $this->ion_auth->get_user();
		
		
		
		if( !empty( $user ) )
		{
			$this->user_id = $user->id;	
			$this->config->set_item('user_id', $this->user_id );
		}
	}
	
	
	function index()
	{
		if( $this->user_id != ""  ) {
			
			$data['options']= $this->fbgallery_model->getOption();
			#print_r($data['options']);exit;
			$this->template
			->append_metadata( js('fb.js', $this->config->item('dch_fbgallery_module_name')) )
			->append_metadata( css('styles/admin-styles.css', $this->config->item('dch_fbgallery_module_name')) )
			->append_metadata( js('admin.js', $this->config->item('dch_fbgallery_module_name')) )
			->append_metadata( js('styles/colorbox/js/colorbox.js', $this->config->item('dch_fbgallery_module_name')) )
			->append_metadata( css('styles/colorbox/colorbox.css', $this->config->item('dch_fbgallery_module_name')) )
			->build('admin/options_fbgallery',$data);
			#$data["fbgalleryData"] = $this->fbgallery_model->ManagefbgalleryOptions();
			
			/*$this->template
			->append_metadata( css('fabgallery.css', $this->config->item('dch_fbgallery_module_name')) )
			->build('admin/manage_fbgallery', $data);*/
		}else {
			$this->template->build('admin/access_failed');
		}
	}
	
	function saveFbToken_fbgallery()
	{
		if( $this->user_id != "" ) {
			$this->load->model('fbgallery_model');
			$fbInstance = new Facebookapi($this->input->post('accessToken'));
			
				$fbInstance->update_albums();exit;
		
		}else{ 
			
			$this->template->build('admin/access_failed'); 
		}
	}
	
	function save_event()
	{
		if( $this->user_id != "" ) {		
			$test_id = $this->event_model->SaveEvent( $_REQUEST ); 
		
			redirect(site_url('admin/' . $this->config->item('dch_event_module_name')));	
		}else {
			$this->template->build('admin/access_failed');
		}
	}
	
	
	function edit_event( $encrypted_event_id )
	{
		if( $this->user_id != "" ) {		
		
			$event_id = WsDecrypt( $encrypted_event_id );
					
			$data["event"] = $this->event_model->GetEvent_ById( $event_id ); 
			$data["event"] = $data["event"][0];
			
			$this->template
			->append_metadata( css('event.css', $this->config->item('dch_event_module_name')) )
			->append_metadata( css('jquery-ui-1.8.17.custom/ui-darkness/jquery-ui-1.8.17.custom.css', $this->config->item('dch_event_module_name')) )
			->append_metadata( css('jquery-ui-1.8.17.custom/jquery-ui-timepicker-addon.css', $this->config->item('dch_event_module_name')) )
			->append_metadata( js('jquery-ui-1.8.17.custom/jquery-ui-1.8.17.custom.min.js', $this->config->item('dch_event_module_name')) )
			->append_metadata( js('jquery-ui-1.8.17.custom/jquery-ui-timepicker-addon.js', $this->config->item('dch_event_module_name')) )
			->append_metadata( js('jquery.validate.js', $this->config->item('dch_event_module_name')) )
			->build('admin/edit_event', $data);
		}else {
			$this->template->build('admin/access_failed');
		}
	}
	
	function update_event()
	{
		if( $this->user_id != "" ) {		
			$action = $this->event_model->UpdateEvent( $_REQUEST ); 
		
			redirect(site_url('admin/' . $this->config->item('dch_event_module_name')));	
		}else {
			$this->template->build('admin/access_failed');
		}
	}
	
	function delete_event( $encrypted_event_id )
	{
		if( $this->user_id != "" ) {		
			
			$event_id = WsDecrypt( $encrypted_event_id );			
			
			$this->event_model->DeleteEvent( $event_id ); 
			
			redirect(site_url('admin/' . $this->config->item('dch_event_module_name')));	
			
			
		}else {
			$this->template->build('admin/access_failed');
		}
	}
	
	function update_event_status( $encrypted_event_id )
	{
		if($this->user_id!="")
		{
				$event_id = WsDecrypt( $encrypted_event_id );
				
				$this->event_model->UpdateEventStatus( $event_id ); 
				
				redirect(site_url('admin/' . $this->config->item('dch_event_module_name')));	
					
			}
	}
	
	function set_event_scheduler( )
	{
		$fromDate = $_REQUEST['start_date']; 
		$toDate = $_REQUEST['end_date'];
		
		$fromDateTS = strtotime($fromDate);
		$toDateTS = strtotime($toDate);
		
		$content = '';
		$content.='<div class="timeblock" >
        			<span class="time_scheduler_label" >Set Time Scheduler</span>';
		$content.='<pre>
					<div>
						<div class="timeblocklabels" style=" width:93px;">Dates</div>&nbsp;
						<div class="timeblocklabels" style="width:103px;">Start Time</div>&nbsp;
						<div class="timeblocklabels" style="width:96px;">End Time</div><div><br>';
		
		for ($currentDateTS = $fromDateTS; $currentDateTS <= $toDateTS; $currentDateTS += (60 * 60 * 24)) {
			// use date() and $currentDateTS to format the dates in between
			
			
			$currentDateStr = date("Y-m-d",$currentDateTS );
			
			$labeldate = date("m/d/Y", $currentDateTS);

			$content.='<div style="position:relative; height:30px;"><label class="timedateslabel" >'.$labeldate.'</label>&nbsp;';
			
			if( $_REQUEST['act'] == 'add') { 
             	$content.='<input type="text" id="event_endtime" name="event_endtime[]"  value="'.GetTime_From_DateTime_AMPM_SlashFormat($toDate).'" class="required dp" style="width:100px; padding:4px; float:right; top:0px; margin:2px;" />
						  <input type="text" id="event_starttime" name="event_starttime[]"  value="'.GetTime_From_DateTime_AMPM_SlashFormat($fromDate).'" class="required dp" style="width:100px; padding:4px; float:right; top:0px; margin:2px;" />';
			}
			elseif( $_REQUEST['act'] == 'edit') { 
			
			$event_time = $this->event_model->GetEventDaysTime( $_REQUEST["event_id"], $currentDateStr ); 
			
			$start_time = "";
			$end_time = "";
			$time_id = "";
			
			
			if( !empty($event_time) )
			{
				$event_time = $event_time[0];
				$start_time = Time24hFormat_Into_AMPMTime($event_time->start_time);
				$end_time = Time24hFormat_Into_AMPMTime($event_time->end_time);
				$time_id = $event_time->id;
			}
			
             $content.='<input type="text" id="event_endtime" name="event_endtime[]" value="'.$start_time.'" class="required dp" style="width:100px; padding:4px; float:right; top:0px; margin:2px;" />
						<input type="text" id="event_starttime" name="event_starttime[]" value="'.$end_time.'" class="required dp" style="width:100px; padding:4px; float:right; top:0px; margin:2px;" />
						<input type="hidden" id="event_timeid" name="event_timeid[]" value="'.$time_id.'"  />';
			}
			
			$content.='<input type="hidden" id="event_date" name="event_date[]" value="'.$currentDateStr.'"  />
						</div>';			
		}
		
		$content.='</pre></div>';
		
		
		echo json_encode(array('content' => $content ) );
	}
	
	function get_event_scheduler( )
	{
		$event_id = $_REQUEST['event_id']; 
		
		$event_scheduler = $this->event_model->GetEventScheduler( $event_id ); 
		
		$content = '';
		$content.='<div class="timeblock" >
					<span class="time_scheduler_label" >Set Time Scheduler</span>';
		$content.='<pre>
					<div>
						<div class="timeblocklabels" style=" width:93px;">Dates</div>&nbsp;
						<div class="timeblocklabels" style="width:103px;">Start Time</div>&nbsp;
						<div class="timeblocklabels" style="width:96px;">End Time</div><div><br>';
		
		foreach( $event_scheduler as $keys => $event )
		{	
			
			$event_date = date("Y-m-d", strtotime( $event->event_date ));
			
			$labeldate = date("m/d/Y", strtotime($event->event_date));

			$content.='<div style="position:relative; height:30px;"><label class="timedateslabel" >'.$labeldate.'</label>&nbsp;
                		<input type="text" id="event_endtime" name="event_endtime[]"  value="'.Time24hFormat_Into_AMPMTime($event->end_time).'" class="required dp" style="width:100px; padding:4px; float:right; top:0px; margin:2px;" />
						<input type="text" id="event_starttime" name="event_starttime[]"  value="'.Time24hFormat_Into_AMPMTime($event->start_time).'" class="required dp" style="width:100px; padding:4px; float:right; top:0px; margin:2px;" />
                		<input type="hidden" id="event_date" name="event_date[]" value="'.$event_date.'"  />
						</div>';			
		}
		
		$content.='</pre></div>';
		
		echo json_encode(array('content' => $content ));
	}
}