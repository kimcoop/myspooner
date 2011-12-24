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
		return $events;	*/
		
	if (isset($_POST['action']) && !empty($_POST['action'])) {
		$action = $_POST['action'];
		$action = htmlspecialchars(trim($action));
		switch($action) {
			case 'updateUser':
				updateUser();
				unset($_POST['action']);
				break;
			case 'setSpoonerDates':
				setSpoonerDates();
				unset($_POST['action']);
				break;
			case 'updateTrip':
				updateTrip($_POST['tripID'], $_POST['startDate'], $_POST['endDate'], $_POST['notes']);
				unset($_POST['action']);
				break;
			case 'deleteTrip':
				deleteTrip($_POST['tripID']);
				unset($_POST['action']);
				break;
		}
	}
	
	function deleteTrip($tripID) {
		$query = "DELETE FROM spooner_date WHERE id='$tripID'";
		mysql_query($query);
		$query = "DELETE FROM spooner_trip_tag WHERE trip_id='$tripID'";
		mysql_query($query);
	}	
	
	function updateTrip($tripID, $newStart, $newEnd, $notes) {
		$arrival = date('Y-m-d', strtotime($newStart));
		$departure = date('Y-m-d', strtotime($newEnd));
		$query = "UPDATE spooner_date SET arrival='$arrival', departure='$departure', notes='$notes' WHERE id='$tripID'";
		mysql_query($query);	
	}

	function formatSpoonerDates($id) {
		$str = "";
		$dates = getSpoonerDates($id);
		
		if (!empty($dates)) {
		
				foreach($dates as $date) {
					$trip_id = $date['id'];
					$str .= "<div class='spoonerTrip' id='$trip_id'><span>";
					$arrival = $date['arrival'];
					$departure = $date['departure'];
					$notes = $date['notes'];
					
					if ($_SESSION['user_id'] == $date['tagger_id']) $posted_by = "you";
					else $posted_by = getUsername($date['tagger_id']);
					
					$post_date = toDateWithAgo($date['post_date']);
					$str .= toDateOnly($arrival, 1)." until "; //the '1' means put spans around the dates
					$str .= toDateOnly($departure, 1);
					$str .= "<div class='button_container inline'><span class='delete'></span></div>";
					$str .= "<div class='button_container inline'><span class='edit'></span></div>";
					$str .= "</span><span class='memberTimestamp' style='float:right;'>Posted by $posted_by $post_date";					
					$str .= "</span>";
					if (!empty($notes)) $str .= "<br>Trip notes: <span class='trip_date'>".$notes."</span>";
					
				  $query = "SELECT * FROM spooner_trip_tag, user WHERE trip_id='$trip_id' AND spooner_trip_tag.user_id=user.id ORDER BY fname";
					$result = mysql_query($query) or die(mysql_error());
					if (mysql_num_rows($result) > 0) $str .= "<br>";
					while($row = mysql_fetch_array($result)){
							$str .= "<span class='tag user' style='font-weight:normal'>".$row['fname']."</span>";
						}
					
					$str .= "<br><br></div>";
				}
				
		$str .= "</div>";
		}
		return $str;
	}

	function getSpoonerDates($id) {
		$query = "SELECT * FROM spooner_date as s, spooner_trip_tag as t WHERE active=1 AND s.id = t.trip_id AND t.user_id='$id' OR t.user_id='$id' GROUP BY trip_id ORDER BY post_date DESC";
		$result = mysql_query($query);
		$dates = array();
		while($row = mysql_fetch_array($result)){
				$dates[] = $row;
			}
		return $dates;	
	}
	
	function setSpoonerDates() {
		$user_id = $_SESSION['user_id'];
		$arrival = date('Y-m-d', strtotime($_POST['arrival']));
		$departure = date('Y-m-d', strtotime($_POST['departure']));
		$notes = $_POST['notes'];
		$query = "INSERT INTO spooner_date(user_id, post_date, arrival, departure, notes) ";
		$query .= " VALUES('$user_id', now(), '$arrival', '$departure', '$notes')";
		mysql_query($query);
		
		$tripID = mysql_insert_id();
		tagSpoonerTrip($_POST['userTags'], $tripID, $user_id);
		$formattedDates = formatSpoonerDates($user_id);
		echo json_encode(array('msg'=>'Spooner dates saved!', 'trips'=>$formattedDates));	
	}

	function tagSpoonerTrip($userTags, $tripID, $taggerID) {
 		if (!empty($userTags)) {
 			foreach($userTags as $user) {
				$query = sprintf("INSERT INTO spooner_trip_tag(trip_id, user_id, tag_date, tagger_id) VALUES('%s', '%s', now(), '%s')", $tripID, $user, $taggerID);
				mysql_query($query);
 			}
 		} 
 }
	function updateUser() {
		$fname = trim($_POST['fname']); $lname = trim($_POST['lname']); $email = trim($_POST['email']); $id = $_SESSION['user_id'];
		$phone = trim($_POST['phone']); $mother = trim($_POST['mother']); $father = trim($_POST['father']); $about = trim($_POST['about']);		
		$birthday = date('Y-m-d', strtotime(trim($_POST['birthday'])));
		
		$query = "UPDATE user SET fname='$fname', lname='$lname', email='$email', phone='$phone', mother='$mother', father='$father', about='$about', birthday='$birthday' WHERE id='$id'";
		mysql_query($query);
	}
	
	function getUserInfo($id) {
		$query = "SELECT * FROM user WHERE id='$id'";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		return $row;
	}
	
	function getLastName($id) {
		$row = getUserInfo($id);
		return $row['lname'];
	}
	
	function getEmail($id) {
		$row = getUserInfo($id);
		return $row['email'];
	}
	
	function getJoinDate($id) {
		$row = getUserInfo($id);
		return $row['join_date'];
	}
	
	function getPhone($id) {
		$row = getUserInfo($id);
		return $row['phone'];
	}
	
	function getMother($id) {
		$row = getUserInfo($id);
		return $row['mother'];
	}
	
	function getFather($id) {
		$row = getUserInfo($id);
		return $row['father'];
	}
	
	function getAbout($id) {
		$row = getUserInfo($id);
		return $row['about'];
	}
	
	function getLastLogin($id) {
		$row = getUserInfo($id);
		return $row['last_login'];
	}
	
	function getBirthday($id) {
		$row = getUserInfo($id);
		return $row['birthday'];
	}
	
	function getUserByName($name) {
		$query = "SELECT id FROM user WHERE fname='$name'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		return $row['id'];
	}
	
	?>