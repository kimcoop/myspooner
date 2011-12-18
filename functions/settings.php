<? 
	session_start();
	mysql_connect("localhost", "root", "") or die(mysql_error());
	mysql_select_db("myspooner") or die(mysql_error());
	include('functions.php');
	
/* Getting an ID:	
		$row = mysql_fetch_array($result);
		return $row[0];	
		
		Getting a row:
		while($row = mysql_fetch_array($result)){
				$events[] = $row;
			}
		return $events;	*/
		
	if (isset($_POST['action']) && !empty($_POST['action']) ){
		$action = $_POST['action'];
		$action = htmlspecialchars(trim($action));
		switch($action) {
			case 'updateUser':
				updateUser($_POST['fname'], $_POST['lname'], $_POST['email'], $_SESSION['user_id']);
				break;
		}
	}
	
	function updateUser($fname, $lname, $email, $id) {
		$query = "UPDATE user SET fname='$fname', lname='$lname', email='$email' WHERE id='$id'";
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
	
	?>