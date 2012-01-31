<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fbgallery extends Public_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		
		$this->config->set_item('dch_event_module_name', "event" );
	}
	
	/**
	 * Index method
	 *
	 * @access public
	 * @return void
	 */
	public function index(){
		$data["eventData"] = $this->event_model->FrontendDisplayEvents();
		//print_r($data["eventData"]); exit;
		$this->template->build('event', $data);
	}
	
	public function month_view(){
		//$data["eventData"] = $this->event_model->FrontendDisplayEvents();
			
		$this->template
			->append_metadata( css('event.css', $this->config->item('dch_event_module_name')) )
			->append_metadata( css('fullcalendar/cupertino/theme.css', $this->config->item('dch_event_module_name')) )
			->append_metadata( css('fullcalendar/fullcalendar.css', $this->config->item('dch_event_module_name')) )			
			//->append_metadata( css('fullcalendar/fullcalendar.print.css', $this->config->item('dch_event_module_name')) )						
			
			->append_metadata( js('jquery-ui-1.8.17.custom/jquery-1.7.1.min.js', $this->config->item('dch_event_module_name')) )
			->append_metadata( js('jquery-ui-1.8.17.custom/jquery-ui-1.8.17.custom.min.js', $this->config->item('dch_event_module_name')) )
			->append_metadata( js('fullcalendar/fullcalendar.min.js', $this->config->item('dch_event_module_name')) )			
			
			->build('month_view');
	}
	
	public function event_calendar_data()
	{
		$data["eventData"] = $this->event_model->EventCalendarData();
		///print_r($data["eventData"]); exit;
		foreach( $data["eventData"] as $keys => $event )
		{
			$data['calendar'][$keys]['id'] = $event->id;	
			$data['calendar'][$keys]['title'] = "VIEW EVENT DETAILS";
			$data['calendar'][$keys]['start'] = date("Y-m-d", strtotime($event->event_date));	
			$data['calendar'][$keys]['url'] = "";	
		}
		
		echo json_encode($data["calendar"]);
		
		
	///	echo json_encode($data["eventData"]);
		
		/*$year = date('Y');
		$month = date('m');

		echo json_encode(array(
	
			array(
				'id' => 111,
				'title' => "Event1",
				'start' => "$year-$month-10",
				'url' => "http://yahoo.com/"
			),
			
			array(
				'id' => 222,
				'title' => "Event2",
				'start' => "$year-$month-20",
				'end' => "$year-$month-22",
				'url' => "http://yahoo.com/"
			)
	
		));	*/	
	}

}