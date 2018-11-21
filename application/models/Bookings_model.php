<?php

class Bookings_model extends CI_Model
{


	var $table_headings = '';
	var $table_rows = array();


	public function __construct()
	{
		parent::__construct();
	}




	function GetByDate($date = NULL)
	{
		if ($date == NULL) {
			$date = date("Y-m-d");
		}

		$day_num = date('w', strtotime($date));
		$query_str = "SELECT * FROM bookings WHERE (`date`='$date' OR day_num=$day_num)";
		$query = $this->db->query($query_str);
		$result = $query->result_array();
		return $result;
	}




	function TableAddColumn($td)
	{
		$this->table_headings .= $td;
	}




	function TableAddRow($data)
	{
		$this->table_rows[] = $data;
	}




	function Table()
	{
		$table = '<tr>' . $this->table_headings . '</tr>';
		/* foreach($this->table_rows as $row){
			$table .= '<tr>' . $row . '</tr>';
		} */
		return $table;
	}




	function BookingCell($data, $key, $rooms, $users, $room_id, $url, $booking_date_ymd = '', $holidays = array())
	{

		// Check if there is a booking
		if(isset($data[$key])){

			// There's a booking for this ID, set var
			$booking = $data[$key];

			if($booking->date == NULL){
			// If no date set, then it's a static/timetable/recurring booking
				$cell['class'] = 'static';
				$cell['body']= '';
			} else {
			// Date is set, it's a once off staff booking
				$cell['class'] = 'staff';
				$cell['body'] = '';
			}

		// Username info
			if(isset($users[$booking->user_id])){
				$username = $users[$booking->user_id]->username;
				$displayname = trim($users[$booking->user_id]->displayname);
				if(strlen($displayname) < 2){ $displayname = $username; }
				$cell['body'] .= '<strong>'.$displayname.'</strong>';
				$user = 1;
			}

			// Any notes?
			if($booking->notes){
				if(isset($user)){ $cell['body'] .= '<br />'; }
				$cell['body'] .= '<span title="'.$booking->notes.'">'.character_limiter($booking->notes, 15).'</span>';
			}

			// Edit if admin?
			/* if($this->userauth->CheckAuthLevel(ADMINISTRATOR, $this->authlevel)){
				$edit_url = site_url('bookings/edit/'.$booking->booking_id);
				$cell['body'] .= '<br /><a href="'.$edit_url.'" title="Edit this booking"><img src="webroot/images/ui/edit.png" width="16" height="16" alt="Edit" title="Edit this booking" hspace="8" /></a>';
				$edit = 1;
			} */

			// Cancel if user is an Admin, Room owner, or Booking owner
			$user_id = $this->session->userdata('user_id');
			if(
				($this->userauth->CheckAuthLevel(ADMINISTRATOR, $this->authlevel)) OR
				($user_id == $booking->user_id) OR
				( ($user_id == $rooms[$room_id]->user_id) && ($booking->date != NULL) )
			){
				$cancel_msg = 'Are you sure you want to cancel this booking?';
				if($user_id != $booking->user_id){
					$cancel_msg = 'Are you sure you want to cancel this booking?\n\n(**) Please take caution, it is not your own booking!!';
				}
				$cancel_url = site_url('bookings/cancel/'.$booking->booking_id);
				if(!isset($edit)){ $cell['body'] .= '<br />'; }
				$cell['body'] .= '<a onclick="if(!confirm(\''.$cancel_msg.'\')){return false;}" href="'.$cancel_url.'" title="Cancel this booking"><img src="' . base_url('assets/images/ui/delete.gif') . '" width="16" height="16" alt="Cancel" title="Cancel this booking" hspace="8" /></a>';
			}

		}
		elseif (isset($holidays[$booking_date_ymd]))
		{
			$cell['class'] = 'holiday';
			$cell['body'] = $holidays[$booking_date_ymd][0]->name;
		}
		else
		{

			// No bookings
			$book_url = site_url('bookings/book/'.$url);
			$cell['class'] = 'free';
			$cell['body'] = '<a href="'.$book_url.'"><img src="' . base_url('assets/images/ui/accept.gif') . '" width="16" height="16" alt="Book" title="Book" hspace="4" align="absmiddle" />Book</a>';
			if($this->userauth->CheckAuthLevel(ADMINISTRATOR, $this->authlevel)){
				$cell['body'] .= '<input type="checkbox" name="recurring[]" value="'.$url.'" />';
			}

		}
	#$cell['width'] =
		#return sprintf('<td class="%s" valign="middle" align="center">%s</td>', $cell['class'], $cell['body']);
		return $this->load->view('bookings/table/bookingcell', $cell, True);
	}





	function html($params = array())
	{
		$defaults = array(
			'school_id' => NULL,
			'display' => NULL,
			'cols' => NULL,
			'date' => NULL,
			'room_id' => NULL,
			'school' => array(),
			'uri' => NULL,
		);

		$data = array_merge($defaults, $params);
		extract($data);

		// Format the date to Ymd
		if ($date == NULL) {
			$date = Now();
			$date_ymd = date("Y-m-d", $date);
		} else {
			$date_ymd = date("Y-m-d", $date);
		}

		// Today's weekday number
		$day_num = date('w', $date);
		$day_num = ($day_num == 0 ? 7 : $day_num);

		// Get info on the current week
		$this_week = $this->WeekObj($date, $school_id);

		// Init HTML + Jscript variable
		$html = '';
		$jscript = '';

		// Put users into array with their ID as the key
		foreach ($school['users'] as $user) {
			$users[$user->user_id] = $user;
		}

		// Get rooms
		$rooms = $this->Rooms();
		if ($rooms == FALSE) {
			$html .= msgbox('error', 'There are no rooms available. Please contact your administrator.');
			return $html;
		}


		// Find out which columns to display and which view type we use
		$style = $this->BookingStyle();
		if(!$style OR ($style['cols'] == NULL OR $style['display'] == NULL) ){
			$html = msgbox('error', 'No booking style has been configured. Please contact your administrator.');
			return $html;
		}
		$cols = $style['cols'];
		$display = $style['display'];


		// Select a default room if none given (first room)
		if($room_id == NULL){
			$room_c = current($rooms);
			$room_id = $room_c->room_id;
			unset($room_c);
		}


		// Load the appropriate select box depending on view style
		switch($display){
			case 'room':
			$html .= $this->load->view('bookings/select_room', array('rooms' => $rooms, 'room_id' => $room_id, 'chosen_date' => $date_ymd ), True);
			break;
			case 'day':
			$html .= $this->load->view('bookings/select_date', array('chosen_date' => $date), True);
			break;
			default:
			$html .= msgbox('error', 'Application error: No display type set.');
			return $html;
			break;
		}
	// Date/room selecter bar
	#$selects['date'] = $this->load->view('bookings/select_date', array('chosen_date' => $date), True);
	#$selects['room'] = $this->load->view('bookings/select_room', array('rooms' => $rooms), True);
		#$html .= $this->load->view('bookings/selects', $selects, True);


	// Return error if nothing available
	/*if(!$this_week){
		$html = $this->load->view('msgbox/error', 'Could not get details of current week - probably no week configured for this date.', True);
		#return $html;
	}*/




		// Do we have any info on this week name?
	if($this_week){

			// Get dates for each weekday
		if($display == 'room'){
			$this_date = strtotime("-1 day", strtotime($this_week->date));
			foreach($school['days_list'] as $d_day_num => $d_day_name){
				$weekdates[$d_day_num] = date("Y-m-d", strtotime("+1 day", $this_date));
				$this_date = strtotime("+1 day", $this_date);
			}
		}

		$week_bar['style'] = sprintf('padding:3px;font-weight:bold;background:#%s;color:#%s', $this_week->bgcol, $this_week->fgcol);

			// Change the week bar depending on view type
		switch($display){
			case 'room':
			$week_bar['back_date'] = date("Y-m-d", strtotime("last Week", $date));
			$week_bar['back_text'] = '&lt;&lt; Previous week';
			$week_bar['back_link'] = sprintf('bookings/index/date/%s/room/%s/direction/back', $week_bar['back_date'], $room_id);
			$week_bar['next_date'] = date("Y-m-d", strtotime("next Week", $date));
			$week_bar['next_text'] = 'Next week &gt;&gt;';
			$week_bar['next_link'] = sprintf('bookings/index/date/%s/room/%s/direction/next', $week_bar['next_date'], $room_id);
			$week_bar['longdate'] = 'Week commencing '.date("l jS F Y", strtotime($this_week->date));
			break;
			case 'day':
			$week_bar['longdate'] = date("l jS F Y", $date);
			$week_bar['back_date'] = date("Y-m-d", strtotime("yesterday", $date));
			$week_bar['back_link'] = sprintf('bookings/index/date/%s/direction/back', $week_bar['back_date']);
			$week_bar['next_date'] = date("Y-m-d", strtotime("tomorrow", $date));
			$week_bar['next_link'] = sprintf('bookings/index/date/%s/direction/next', $week_bar['next_date']);
			if(date("Y-m-d") == date("Y-m-d", $date)){
				$week_bar['back_text'] = '&lt;&lt; Yesterday';
				$week_bar['next_text'] = 'Tomorrow &gt;&gt; ';
			} else {
				$week_bar['back_text'] = '&lt;&lt; Back';
				$week_bar['next_text'] = 'Next &gt;&gt; ';
			}
			break;
		}
		$week_bar['week_name'] = $this_week->name;
		$html .= $this->load->view('bookings/week_bar', $week_bar, True);
	} else {
		$html .= msgbox('error', 'A configuration error prevented the timetable from loading: <strong>no week configured</strong>.<br /><br />Please contact your administrator.');
			#return $html;
		$err = true;
	}

		// See if our selected date is in a holiday
	if ($display === 'day')
	{
			// If we are day at a time, it is easy!
			// = get me any holidays where this day is anywhere in it
		$sql = "SELECT *
		FROM holidays
		WHERE date_start <= '{$date_ymd}'
		AND date_end >= '{$date_ymd}' ";
	}
	else
	{
			// If we are room/week at a time, little bit more complex
		$week_start = date('Y-m-d', strtotime($this_week->date));
		$week_end = date('Y-m-d', strtotime('+' . count($school['days_list']) . ' days', strtotime($this_week->date)));

		$sql = "SELECT *
		FROM holidays
		WHERE
		/* Starts before this week, ends this week */
		(date_start <= '$week_start' AND date_end <= '$week_end')
		/* Starts this week, ends this week */
		OR (date_start >= '$week_start' AND date_end <= '$week_end')
		/* Starts this week, ends after this week */
		OR (date_start >= '$week_start' AND date_end >= '$week_end')
		";
	}


	$query = $this->db->query($sql);
	$holidays = $query->result();

		// Organise our holidays by date
	$holiday_dates = array();
	$holiday_interval = new DateInterval('P1D');

	foreach ($holidays as $holiday)
	{
			// Get all dates between date_start & date_end
		$start_dt = new DateTime($holiday->date_start);
		$end_dt = new DateTime($holiday->date_end);
		$end_dt->modify('+1 day');
		$range = new DatePeriod($start_dt, $holiday_interval, $end_dt);
		foreach ($range as $date)
		{
			$holiday_ymd = $date->format('Y-m-d');
			$holiday_dates[ $holiday_ymd ][] = $holiday;
		}
	}

	if ($display === 'day' && isset($holiday_dates[$date_ymd])) {
			// The date selected IS in a holiday - give them a nice message saying so.
		$holiday = $holiday_dates[ $date_ymd ][0];
		$msg = sprintf(
			'The date you selected is during a holiday priod (%s, %s - %s).',
			$holiday->name,
			date("d/m/Y", strtotime($holiday->date_start)),
			date("d/m/Y", strtotime($holiday->date_end))
		);
		$html .= msgbox('exclamation', $msg);

			// Let them choose the date afterwards/before
			// If navigating a day at a time, then just go one day.
			// If navigating one room at a time, move by one week
		if ($display === 'day') {
			$next_date = date("Y-m-d", strtotime("+1 day", strtotime($holiday->date_end)));
			$prev_date = date("Y-m-d", strtotime("-1 day", strtotime($holiday->date_start)));
		} elseif ($display === 'room') {
			$next_date = date("Y-m-d", strtotime("+1 week", strtotime($holiday->date_end)));
			$prev_date = date("Y-m-d", strtotime("-1 week", strtotime($holiday->date_start)));
		}

		if ( ! isset($uri['direction'])) {
			$uri['direction'] = 'forward';
		}

		switch($uri['direction']){
			case 'forward':
			default:
			$uri['date'] = $next_date;
			$html .= '<p><strong><a href="'.site_url('bookings/index/date/'.$next_date.'/direction/forward').'">Click here to view immediately after the holiday.</a></strong></p>';
			break;
			case 'back':
			$html .= '<p><strong><a href="'.site_url('bookings/index/date/'.$prev_date.'/direction/back').'">Click here to view immediately before the holiday.</a></strong></p>';
			break;
		}
			#return $html;
		$err = true;
	}


		// Get periods
	if ($style['display'] == 'day') {
		$periods = $this->periods_model->GetBookable($day_num);
	} else {
		$periods = $this->periods_model->GetBookable();
	}

	if (empty($periods)) {
		$html .= msgbox('error', 'There are no periods configured or available for this day.');
		$err = TRUE;
	}

	if (isset($err) && $err == TRUE) {
		return $html;
	}


	$count['periods'] = count($periods);
	$count['rooms'] = count($rooms);
	$count['days'] = count($school['days_list']);
		#$col_width = sprintf('%d%%', (round($period_count/10) * 100) / $period_count );
	$col_width = sprintf('%s%%', round(100/($count[$cols]+1)));


		// Open form
	$html .= '<form name="bookings" method="POST" action="' . site_url('bookings/recurring') . '">';
	$html .= form_hidden('room_id', $room_id);


		// Here goes, start table
	$html .= '<table border="0" bordercolor="#ffffff" cellpadding="2" cellspacing="2" class="bookings" width="100%">';


		// COLUMNS !!
	$html .= '<tr><td>&nbsp;</td>';


	switch($cols){
		case 'periods':
		foreach($periods as $period){
			$period->width = $col_width;
			$html .= $this->load->view('bookings/table/cols_periods', $period, True);
		}
		break;
		case 'days':
		foreach($school['days_list'] as $dayofweek){
			$day['width'] = $col_width;
			$day['name'] = $dayofweek;
			$html .= $this->load->view('bookings/table/headings/days', $day, True);
		}
		break;
		case 'rooms':
		foreach($rooms as $room){
					// Room name etc
			if($room->photo != NULL){
				$roomtitle['photo_lg'] = 'webroot/images/roomphotos/640/'.$room->photo;
				$roomtitle['photo_sm'] = 'webroot/images/roomphotos/160/'.$room->photo;
				$roomtitle['event'] = 'onmouseover="doTooltip(event,'.$room->room_id.')" onmouseout="hideTip()"';
				$roomtitle['width'] = 760;
				$jscript .= "messages[$room->room_id] = new Array('{$roomtitle['photo_sm']}','{$room->location}');\n";
			} else {
				$roomtitle['width'] = 400;
				$roomtitle['event'] = '';
			}
			$room->roomtitle = $roomtitle;
			$room->width = $col_width;
			$room->school_id = $school_id;
					#$jscript .= "messages[$room->room_id] = new Array('{$roomtitle['photo_sm']}','{$room->location}');\n";
			$html .= $this->load->view('bookings/table/cols_rooms', $room, True);
		}
		break;
	}	// End switch for cols

		// End COLUMNS row
		#$html .= '</tr>';


		// Get bookings
		#$query_str = "SELECT * FROM bookings WHERE school_id='$school_id' AND ((date >='$date_ymd') OR date Is Null)";
		#$query = $this->db->query($query_str);
		#$results = $query->result_array();

	$bookings = array();

		// Here we go!
	switch($display){

		case 'room':

				// ONE ROOM AT A TIME - COLS ARE PERIODS OR DAY NAMES...

		switch($cols){

			case 'periods':

						/*
							   [P1] [P2] [P3] ...
							[M]
							[T]
						*/

						// Columns are periods, so each row is a day name

							foreach($school['days_list'] as $day_num => $day_name){


							// Get booking
							// TODO: Need to get date("Y-m-d") of THIS weekday (Mon, Tue, Wed) for this week
								$bookings = array();
								$query_str = "SELECT * FROM bookings "
								."WHERE school_id='$school_id' "
								."AND room_id='$room_id' "
								."AND ((day_num=$day_num AND week_id=$this_week->week_id) OR date='$weekdates[$day_num]') ";
								$query = $this->db->query($query_str);
								$results = $query->result();
								if($query->num_rows() > 0){
									foreach($results as $row){
									#echo $row->booking_id;
										$bookings[$row->period_id] = $row;
									}
								}
								$query->free_result();

							// Start row
								$html .= '<tr>';

							// First cell
								$day['width'] = $col_width;
								$day['name'] = $day_name;
								$html .= $this->load->view('bookings/table/rowinfo/days', $day, True);

							//$booking_date_ymd = strtotime('+' . ($day_num - 1) . ' days', strtotime($date_ymd));
							//$booking_date_ymd = date('Y-m-d', $booking_date_ymd);
								$booking_date_ymd = $weekdates[$day_num];

							// Now all the other ones to fill in periods
								foreach($periods as $period){


								// URL
									$url = 'period/%s/room/%s/day/%s/week/%s/date/%s';
									$url = sprintf($url, $period->period_id, $room_id, $day_num, $this_week->week_id, $booking_date_ymd);

								// Check bitmask to see if this period is bookable on this day
									if ($period->days_array[ $day_num ]) {

										// Bookable
										$html .= $this->BookingCell($bookings, $period->period_id, $rooms, $users, $room_id, $url, $booking_date_ymd, $holiday_dates);
									} else {
									// Period not bookable on this day, do not show or allow any bookings
										$html .= '<td align="center">&nbsp;</td>';
									}

							}		// Done looping periods (cols)

							// This day row is finished
							$html .= '</tr>';

						}


					break;		// End $display 'room' $cols 'periods'

					case 'days':

						/*
								[M] [T] [W] ...
							[P1]
							[P2]
						*/

						// Columns are days, so each row is a period

							foreach($periods as $period){

							// Get booking
							// TODO: Need to get date("Y-m-d") of THIS weekday (Mon, Tue, Wed) for this week
								$bookings = array();
								$query_str = "SELECT * FROM bookings "
								."WHERE school_id='$school_id' "
								."AND room_id='$room_id' "
								."AND period_id='$period->period_id' "
								."AND ( (week_id=$this_week->week_id) OR (date >= '$weekdates[1]' AND date <= '$weekdates[7]' ) )";
													#."AND ((day_num=$day_num AND week_id=$this_week->week_id) OR date='$date_ymd') ";
								$query = $this->db->query($query_str);
								$results = $query->result();
								if($query->num_rows() > 0){
									foreach($results as $row){
									#echo $row->booking_id;
										if($row->date != NULL){
											$this_daynum = date('w', strtotime($row->date) );
											$bookings[$this_daynum] = $row;
										} else {
											$bookings[$row->day_num] = $row;
										}
									}
								}
								$query->free_result();


							// Start row
								$html .= '<tr>';

							// First cell, info
								$period->width = $col_width;
								$html .= $this->load->view('bookings/table/rowinfo/periods', $period, True);

							//$booking_date_ymd = strtotime('+' . ($day_num - 1) . ' days', strtotime($date_ymd));
							//$booking_date_ymd = date('Y-m-d', $booking_date_ymd);


								foreach($school['days_list'] as $day_num => $day_name){

									$booking_date_ymd = $weekdates[$day_num];

								#$html .= '<td align="center" valign="middle">BOOK</td>';

									$url = 'period/%s/room/%s/day/%s/week/%s/date/%s';
									$url = sprintf($url, $period->period_id, $room_id, $day_num, $this_week->week_id, $booking_date_ymd);


								// Check bitmask to see if this period is bookable on this day
									$school['days_bitmask']->reverse_mask($period->days);
									if($school['days_bitmask']->bit_isset($day_num)){
									// Bookable
										$html .= $this->BookingCell($bookings, $day_num, $rooms, $users, $room_id, $url, $booking_date_ymd, $holiday_dates);
									} else {
									// Period not bookable on this day, do not show or allow any bookings
										$html .= '<td align="center">&nbsp;</td>';
									}

								}

							// This period row is finished
								$html .= '</tr>';

							}

					break;		// End $display 'room' $cols 'days'

				}

				break;
				case 'day':

				// ONE DAY AT A TIME - COLS ARE DAY NAMES OR ROOMS

				switch($cols){

					case 'periods':

						/*
								[P1] [P2] [P3] ...
							[R1]
							[R2]
						*/

						// Columns are periods, so each row is a room

							foreach($rooms as $room){

								$bookings = array();
							// See if there are any bookings for any period this room.
							// A booking will either have a date (teacher booking), or a day_num and week_id (static/timetabled)
								$query_str = "SELECT * FROM bookings "
								."WHERE school_id='$school_id' "
								."AND room_id='$room->room_id' "
								."AND ((day_num=$day_num AND week_id=$this_week->week_id) OR date='$date_ymd') ";
								$query = $this->db->query($query_str);
								$results = $query->result();
								if($query->num_rows() > 0){
									foreach($results as $row){
									#echo $row->booking_id;
										$bookings[$row->period_id] = $row;
									}
								}
								$query->free_result();

							// Start row
								$html .= '<tr>';

								$roomtitle = array();
								if($room->photo != NULL){
									$roomtitle['photo_lg'] = 'webroot/images/roomphotos/640/'.$room->photo;
									$roomtitle['photo_sm'] = 'webroot/images/roomphotos/160/'.$room->photo;
									$roomtitle['event'] = 'onmouseover="doTooltip(event,'.$room->room_id.')" onmouseout="hideTip()"';
									$roomtitle['width'] = 760;
									$jscript .= "messages[".$room->room_id."] = new Array('".$roomtitle['photo_sm']."','".$room->location."');\n";
								} else {
									$roomtitle['width'] = 400;
									$roomtitle['event'] = '';
								}
								$room->roomtitle = $roomtitle;
								$room->width = $col_width;
								$room->school_id = $school_id;
								$html .= $this->load->view('bookings/table/rowinfo/rooms', $room, True);

								foreach($periods as $period){
									$url = 'period/%s/room/%s/day/%s/week/%s/date/%s';
									$url = sprintf($url, $period->period_id, $room->room_id, $day_num, $this_week->week_id, $date_ymd);

								// Check bitmask to see if this period is bookable on this day
									if ($period->days_array[ $day_num ]) {
										// Bookable
										$html .= $this->BookingCell($bookings, $period->period_id, $rooms, $users, $room->room_id, $url, $date_ymd, $holiday_dates);
									} else {
									// Period not bookable on this day, do not show or allow any bookings
										$html .= '<td align="center">&nbsp;</td>';
									}
								}

							// End row
								$html .= '</tr>';

							}

					break;		// End $display 'day' $cols 'periods'

					case 'rooms':

							/*
								[R1] [R2] [R3] ...
							[P1]
							[P2]
						*/

						// Columns are rooms, so each row is a period

							foreach($periods as $period){

								$bookings = array();
							// See if there are any bookings for any period this room.
							// A booking will either have a date (teacher booking), or a day_num and week_id (static/timetabled)
								$query_str = "SELECT * FROM bookings "
								."WHERE school_id='$school_id' "
								."AND period_id='$period->period_id' "
								."AND ((day_num=$day_num AND week_id=$this_week->week_id) OR date='$date_ymd') ";
								$query = $this->db->query($query_str);
								$results = $query->result();
								if($query->num_rows() > 0){
									foreach($results as $row){
									#echo $row->booking_id;
										$bookings[$row->room_id] = $row;
									}
								}
								$query->free_result();

							// Start period row
								$html .= '<tr>';

							// First cell, info
								$period->width = $col_width;
								$html .= $this->load->view('bookings/table/rowinfo/periods', $period, True);

								foreach($rooms as $room){
									$url = 'period/%s/room/%s/day/%s/week/%s/date/%s';
									$url = sprintf($url, $period->period_id, $room->room_id, $day_num, $this_week->week_id, $date_ymd);

								// Check bitmask to see if this period is bookable on this day
									$school['days_bitmask']->reverse_mask($period->days);
									if($school['days_bitmask']->bit_isset($day_num)){
									// Bookable
										$html .= $this->BookingCell($bookings, $room->room_id, $rooms, $users, $room->room_id, $url, $date_ymd, $holiday_dates);
									} else {
									// Period not bookable on this day, do not show or allow any bookings
										$html .= '<td align="center">&nbsp;</td>';
									}
								}

							// End period row
								$html .= '</tr>';

							}

					break;		// End $display 'day' $cols 'rooms'

				}

			break;

		}


		$html .= $this->Table();


		// Finish table
		$html .= '</table>';


		// Visual key
		$html .= $this->load->view('bookings/key', NULL, True);


		// Do javascript for hover DIVs for room information
		if($jscript != ''){ $html .= '<script type="text/javascript">'.$jscript.'</script>'; }


		// Show link to making a booking for admins
		if($this->userauth->CheckAuthLevel(ADMINISTRATOR, $this->authlevel)){
			$html .= $this->load->view('bookings/make_recurring', array('users' => $school['users']), True);
		}


		// Finaly return the HTML variable so the controller can then pass it to the view.
		return $html;
	}





	public function Cancel($booking_id)
	{
		$sql = "DELETE FROM bookings
				WHERE booking_id=$booking_id
				LIMIT 1";

		$query = $this->db->query($sql);
		return ($query && $this->db->affected_rows() == 1);
	}




	function BookingStyle()
	{
		$query_str = "SELECT d_columns,displaytype FROM school LIMIT 1";
		$query = $this->db->query($query_str);
		if ($query->num_rows() == 1) {
			$row = $query->row();
			$style['cols'] = $row->d_columns;
			$style['display'] = $row->displaytype;
			return $style;
		} else {
			$style = FALSE;
		}
	}




	/**
	 * Get rooms and their users
	 *
	 */
	function Rooms()
	{
		$sql = "SELECT rooms.*, users.user_id, users.username, users.displayname
				FROM rooms
				LEFT JOIN users ON users.user_id=rooms.user_id
				WHERE rooms.bookable=1
				ORDER BY name asc";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			$result = $query->result();
			// Put all room data into an array where the key is the room_id
			foreach ($result as $room) {
				$rooms[$room->room_id] = $room;
			}
			return $rooms;
		}

		return FALSE;
	}




	/**
	 * Returns an object containing the week information for a given date
	 *
	 */
	public function WeekObj($date, $school_id = NULL)
	{
		// First find the monday date of the week that $date is in
		if (date("w", $date) == 1) {
			$nextdate = date("Y-m-d", $date);
		} else {
			$nextdate = date("Y-m-d", strtotime("last Monday", $date));
		}

		// Get week info that this date falls into
		$sql = "SELECT * FROM weeks, weekdates
				WHERE weeks.week_id = weekdates.week_id
				AND weekdates.date = '$nextdate'
				LIMIT 1";

		$query = $this->db->query($sql);

		if ($query->num_rows() == 1) {
			$row = $query->row();
		} else {
			$row = false;
		}

		return $row;
	}




	/**
	 * Add a booking
	 *
	 */
	function Add($data = array())
	{
		// Run query to insert blank row
		$this->db->insert('bookings', array('booking_id' => NULL));
		// Get id of inserted record
		$booking_id = $this->db->insert_id();
		// Now call the edit function to update the actual data for this new row now we have the ID
		return $this->Edit($booking_id, $data);
	}




	function Edit($booking_id, $data)
	{
		$this->db->where('booking_id', $booking_id);
		$result = $this->db->update('bookings', $data);
		// Return bool on success
		if ($result) {
			return $booking_id;
		} else {
			return false;
		}
	}




	function ByRoomOwner($user_id = 0)
	{
		$maxdate = date("Y-m-d", strtotime("+14 days", Now()));
		$today = date("Y-m-d");
		$sql = "SELECT rooms.*, bookings.*, users.username, users.displayname, users.user_id, periods.name as periodname
				FROM bookings
				JOIN rooms ON rooms.room_id=bookings.room_id
				JOIN users ON users.user_id=bookings.user_id
				JOIN periods ON periods.period_id=bookings.period_id
				WHERE rooms.user_id='$user_id' AND bookings.cancelled=0
				AND bookings.date IS NOT NULL
				AND bookings.date <= '$maxdate'
				AND bookings.date >= '$today'
				ORDER BY bookings.date, rooms.name ";

		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			// We have some bookings
			return $query->result();
		}

		return FALSE;
	}




	function ByUser($user_id)
	{
		$maxdate = date("Y-m-d", strtotime("+14 days", Now()));
		$today = date("Y-m-d");
		// All current bookings for this user between today and 2 weeks' time
		$sql = "SELECT rooms.*, bookings.*, periods.name as periodname, periods.time_start, periods.time_end
				FROM bookings
				JOIN rooms ON rooms.room_id=bookings.room_id
				JOIN periods ON periods.period_id=bookings.period_id
				WHERE bookings.user_id='$user_id' AND bookings.cancelled=0
				AND bookings.date IS NOT NULL
				AND bookings.date <= '$maxdate'
				AND bookings.date >= '$today'
				ORDER BY bookings.date asc, periods.time_start asc";

		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result();
		} else {
			return false;
		}
	}




	function TotalNum($user_id = 0)
	{
		$today = date("Y-m-d");

		// All bookings by user, EVER!
		$sql = "SELECT * FROM bookings WHERE user_id='$user_id'";
		$query = $this->db->query($sql);
		$total['all'] = $query->num_rows();

		// All bookings by user, for this academic year, up to and including today
		$sql = "SELECT * FROM bookings
				JOIN academicyears ON bookings.date >= academicyears.date_start
				WHERE user_id='$user_id' ";
		$query = $this->db->query($sql);
		$total['yeartodate'] = $query->num_rows();

		// All bookings up to and including today
		$sql = "SELECT * FROM bookings WHERE user_id='$user_id' AND date <= '$today'";
		$query = $this->db->query($sql);
		$total['todate'] = $query->num_rows();

		// All "active" bookings (today onwards)
		$sql = "SELECT * FROM bookings WHERE user_id='$user_id' AND date >= '$today'";
		$query = $this->db->query($sql);
		$total['active'] = $query->num_rows();

		return $total;
	}




}
