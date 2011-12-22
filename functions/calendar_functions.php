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
		}
	}
	
	/*
	$query = "SELECT * FROM spooner_trip_tag, user 
	 WHERE trip_id='$trip_id' AND user_id=user.id ORDER BY fname ASC";
		$result = mysql_query($query) or die(mysql_error());
	*/

  function getAllTrips() {
		$query = "SELECT * FROM spooner_date as s, spooner_trip_tag as t";
		$query .= " WHERE s.id = t.trip_id";
		$query .= " ORDER BY arrival";
		$result = mysql_query($query) or die(mysql_error());
		$dates = array();
		while($row = mysql_fetch_array($result)){
				$user = getUsername($row['user_id'], 'short');
				$id = $row['s.id'];
				$title = $user.' in Spooner';
				$start = $row['arrival'];
				$end = $row['departure'];
				
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
		echo json_encode($trips);	
	}

?>