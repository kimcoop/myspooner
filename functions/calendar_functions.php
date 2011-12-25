<?
	session_start();
	mysql_connect("localhost", "root", "") or die(mysql_error());
	mysql_select_db("myspooner") or die(mysql_error());
	include_once('functions.php');
	
/* Getting an ID:	
		$row = mysql_fetch_array($result);
		return $row[0];	
		
		Getting a row:
		while($row = mysql_fetch_array($result)){
				$events[] = $row;
			}
		return $events;	
*/
		
	if (isset($_POST['action']) && !empty($_POST['action']) ){
		$action = $_POST['action'];
		$action = htmlspecialchars(trim($action));
		switch($action) {
			case 'fetchCalendar':
				fetchCalendar();
				break;
			case 'createEvent':
				createEvent($_POST['name'], $_POST['content'], $_SESSION['user_id'], $_POST['start'], $_POST['end'], $_POST['location']);
				break;
			case 'getSpoonerTripDetails':
				getSpoonerTripDetails($_POST['id']);
				break;
			case 'getBirthdayDetails':
				getBirthdayDetails($_POST['id']);
				break;
			case 'getEventDetails':
				getEventDetails($_POST['id']);
				break;
		}
	}
		
	function getEventDetails($eventID) {
		$query = "SELECT * FROM event WHERE id='$eventID'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$details = $row['description'];
		$start = toDatetime($row['start_date']);
		$end = toDatetime($row['end_date']);
		$user = getUsername($row['user_id'], 'short');
		$posted = toDateOnly($row['post_date']);
		$loc = $row['location'];
		
		if (empty($loc)) $loc = "Not specified.";
		
		$details .= "<br><br>Begins ".$start."<br>Ends ".$end;
		$details .= "<br><br>Location: ".$loc;
		$details .= "<br><br>Posted on ".$posted;
		$details .= "<br>by ".$user;
		echo json_encode(array('details'=>$details));
	}		
	
	function getBirthdayDetails($id) {
		$query = "SELECT * FROM user WHERE id='$id'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$details = $row['fname']." was born<br>".toDateWithAgo($row['birthday'], 'fullyear');
		echo json_encode(array('details'=>$details));
	}		
	
	function getSpoonerTripDetails($tripID) {
		$query = "SELECT * FROM spooner_date WHERE id='$tripID'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		
		$start = toDateOnly($row['arrival']);
		$end = toDateOnly($row['departure']);
		
		$details = "Arriving ".$start."<br>";
		$details .= "Departing ".$end."<br><br>";
		
		$notes = $row['notes'];
		if (!empty($notes)) $details .= $notes."<br><br>";
		$user = getUsername($row['user_id'], 'short');
		$details .= "Trip posted on ".toDateOnly($row['post_date']);
		$details .= "<br>by ".$user;
		echo json_encode(array('details'=>$details));
	}	
	
	function createEvent($name, $content, $userID, $start, $end, $loc) {
		$startDate = date('Y-m-d h:i:s a', strtotime($start));
		$endDate = date('Y-m-d h:i:s a', strtotime($end));
	
		$query = "INSERT INTO event(name, start_date, end_date, description, post_date, user_id, location)";
		$query .= " VALUES('$name', '$startDate', '$endDate', '$content', now(), '$userID', '$loc')";
		$result = mysql_query($query);
		
		if (!result) echo json_encode(array('error'=>mysql_error()));
		else echo json_encode(array('msg'=>'Success'));
	}

  function getAllTrips() {
		$query = "SELECT * FROM spooner_date";
		$query .= " ORDER BY arrival";
		$result = mysql_query($query) or die(mysql_error());
		$dates = array();
		while($row = mysql_fetch_array($result)){
				//$user = getUsername($row['user_id'], 'short');
				$id = $row['id'];
				$formattedID = "spooner_".$id;
				$originator = getUsername($row['user_id']);
				$title = $originator;
				
				$query2 = "SELECT * FROM spooner_trip_tag";
				$query2 .= " WHERE trip_id='$id'";
				$result2 = mysql_query($query2);
				while($row2 = mysql_fetch_array($result2)) {
					$joiner = getUsername($row2['user_id']);
					$title .= ', '.$joiner;
				}
				
				$title .= ' in Spooner';
				$start = $row['arrival'];
				$end = $row['departure'];
				$notes = $row['notes'];
				
				$trip = array(
					'id' => $formattedID,
					'title' => $title,
					'start' => $start,
					'end' => $end				
				);
				
				$dates[] = $trip;
			}
		return $dates;
  }
  
  function getAllEvents() {
		$query = "SELECT * FROM event";
		$query .= " ORDER BY start_date";
		$result = mysql_query($query) or die(mysql_error());
		$dates = array();
		while($row = mysql_fetch_array($result)){
				$user = getUsername($row['user_id']);
				$id = "event_".$row['id'];
				$title = "Event: ".$row['name'];
				$start = $row['start_date'];
				$end = $row['end_date'];
				
				$event = array(
					'id' => $id,
					'title' => $title,
					'start' => $start,
					'end' => $end				
				);
				
				$dates[] = $event;
			}
		return $dates;
  }
  
  function getAllBirthdays() {
		$query = "SELECT id, fname, birthday FROM user";
		$result = mysql_query($query) or die(mysql_error());
		$dates = array();
		while($row = mysql_fetch_array($result)){
				$user = getUsername($row['id']);
				$id = "user_".$row['id'];
				$title = $user."'s birthday!";
				$bday = $row['birthday'];
				
				if (!empty($bday)) {
				
					$d = new DateTime($bday);
					$bday_m = $d->format('m');
					$bday_d = $d->format('d');
					$yr = date('Y');
					
					$thisBday = date("Y-m-d", mktime(0, 0, 0, $bday_m, $bday_d, $yr));
					$nextBday = date("Y-m-d", mktime(0, 0, 0, $bday_m, $bday_d, $yr+1)); //get next year's bday too
					$start = $bday;
					
					$bday = array(
						'id' => $id,
						'title' => $title,
						'start' => $thisBday
					);
					$dates[] = $bday;
					
					$bday = array(
						'id' => $id,
						'title' => $title,
						'start' => $nextBday
					);
					$dates[] = $bday;
				} //end if
				
			}
		return $dates;
  }

	function fetchCalendar() {
		$trips = getAllTrips();
		$events = getAllEvents();
		$birthdays = getAllBirthdays();
		
		
		
		
		echo json_encode(array_merge($trips, $events, $birthdays));	
	}

?>