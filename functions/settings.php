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
		}
	}

	function formatSpoonerDates($id) {
		$str = "";
		$dates = getSpoonerDates($id);
		
		if (!empty($dates)) {
			if (count($dates)==1) $str .= "Your trip:<br>";
			else $str .= "Your trips:<br>";
		
			foreach($dates as $date) {
				$arrival = $date['arrival'];
				$departure = $date['departure'];
				$str .= toDateOnly($arrival)." until ";
				$str .= toDateOnly($departure)."<br>";
			}
		}
		return $str;
	}

	function getSpoonerDates($id) {
		$query = "SELECT * FROM spooner_date WHERE user_id='$id' ORDER BY post_date DESC";
		$result = mysql_query($query);
		$dates = array();
		while($row = mysql_fetch_array($result)){
				$dates[] = $row;
			}
		return $dates;	
	}
	
	function setSpoonerDates() {
		$user_id = $_POST['user_id'];
		$arrival = date('Y-m-d', strtotime($_POST['arrival']));
		$departure = date('Y-m-d', strtotime($_POST['departure']));
		$notes = $_POST['notes'];
		$query = "INSERT INTO spooner_date(user_id, post_date, arrival, departure, notes) ";
		$query .= " VALUES('$user_id', now(), '$arrival', '$departure', '$notes')";
		mysql_query($query);
		
		echo json_encode(array('msg'=>'Spooner dates saved!'));	
	}
	
	
	function updateUser() {
		$fname = trim($_POST['fname']); $lname = trim($_POST['lname']); $email = trim($_POST['email']); $id = $_SESSION['user_id'];
		$phone = trim($_POST['phone']); $mother = trim($_POST['mother']); $father = trim($_POST['father']); $about = trim($_POST['about']);		
		$query = "UPDATE user SET fname='$fname', lname='$lname', email='$email', phone='$phone', mother='$mother', father='$father', about='$about' WHERE id='$id'";
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
	
	?>