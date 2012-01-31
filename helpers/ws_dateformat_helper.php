<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');


function DateTimeAMPM_SlashFormat_Into24hFormat( $datetime )
{
	$DateArr = explode('/',$datetime);
	$Month = $DateArr[0];
	$Day = $DateArr[1];
	$DateYearArr = explode(' ',$DateArr[2]);
	$Year = $DateYearArr[0];
	$Time = $DateYearArr[1] . " " . $DateYearArr[2];
	
	$time_in_24_hour_format  = date("H:i:s", strtotime($Time));
	
	$newdate = $Year . "-" . $Month . "-" .	$Day . " " . $time_in_24_hour_format;
	
	return $newdate;
}

function Date_SlashFormat_Into24hFormat( $date )
{
	$DateArr = explode('/',$date);
	$Month = $DateArr[0];
	$Day = $DateArr[1];
	$DateYearArr = explode(' ',$DateArr[2]);
	$Year = $DateYearArr[0];
	
	$newdate = $Year . "-" . $Month . "-" .	$Day; 
	
	return $newdate;
}


function DateTime_24hFormat_Into_AMPM_SlashFormat( $datetime )
{
	$DateArr = explode('-',$datetime);
	$Year = $DateArr[0];
	$Month = $DateArr[1];
	$DateDayArr = explode(' ',$DateArr[2]);
	$Day = $DateDayArr[0];
	$Time = $DateDayArr[1];
	
	$time_in_12_hour_format  = date("g:i a", strtotime($Time));
	
	$newdate = $Month . "/" .	$Day . "/" . $Year . " " . $time_in_12_hour_format;
	return $newdate;
}


function DateTime_24hFormat_Into_AMPM_Date_SlashFormat( $datetime )
{
	$DateArr = explode('-',$datetime);
	$Year = $DateArr[0];
	$Month = $DateArr[1];
	$DateDayArr = explode(' ',$DateArr[2]);
	$Day = $DateDayArr[0];
	
	$newdate = $Month . "/" .	$Day . "/" . $Year;
	return $newdate;
}



function GetTime_From_DateTime_AMPM_SlashFormat( $datetime )
{
	$DateArr = explode('/',$datetime);
	$Month = $DateArr[0];
	$Day = $DateArr[1];
	$DateYearArr = explode(' ',$DateArr[2]);
	
	$Year = $DateYearArr[0];
	$Time = $DateYearArr[1] . " " . $DateYearArr[2];
	
	return $Time;
}


function TimeAMPM_Into_24hFormatTime( $time )
{
	return date("H:i:s", strtotime($time));
}


function Time24hFormat_Into_AMPMTime( $time )
{
	return date("g:i a", strtotime($time));
}
