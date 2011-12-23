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
				createEvent($_POST['name'], $_POST['content'], $_SESSION['user_id'], $_POST['start'], $_POST['end']);
				break;
		}
	}
		
	
	function createEvent($name, $content, $userID, $start, $end) {
		$startDate = date('Y-m-d', strtotime($start));
		$endDate = date('Y-m-d', strtotime($end));
	
		$query = "INSERT INTO event(name, start_date, end_date, description, post_date, user_id)";
		$query .= " VALUES('$name', '$startDate', '$endDate', '$content', now(), '$userID')";
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
				if (!empty($notes)) $title .= ". ".$notes;
				
				$trip = array(
					'id' => $id,
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
				$id = $row['id'];
				$title = "Event: ".$row['name']." ".$row['description'];
				$start = $row['start_date'];
				$end = $row['end_date'];
				
				$trip = array(
					'id' => $id,
					'title' => $title,
					'start' => $start,
					'end' => $end				
				);
				
				$dates[] = $trip;
			}
		return $dates;
  
  }


	function fetchCalendar() {
		$trips = getAllTrips();
		$events = getAllEvents();
		echo json_encode(array_merge($trips, $events));	
	}

?>