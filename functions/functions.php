<?

	session_start();
	mysql_connect("localhost", "root", "") or die(mysql_error());
	mysql_select_db("myspooner") or die(mysql_error());
	 
	
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
			case 'register':
				register();
				break;
			case 'login': 
				login();	
				break;
			case 'logout':
				logout();
				break;
		}
	}
	
	function ago($time) {
   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
   $lengths = array("60","60","24","7","4.35","12","10");

   $now = time();

       $difference     = $now - $time;
       $tense         = "ago";

   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
       $difference /= $lengths[$j];
   }

   $difference = round($difference);

   if($difference != 1) {
       $periods[$j].= "s";
   }

   return "$difference $periods[$j] ago";
 }
	
	function getUsername($id) {
		$query = "SELECT fname FROM user WHERE id = '$id'";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		return $row[0];
	}
	
	function toDate($date){
		$r = strtotime($date);
		
		return ago($r);
		//$return = $r->format('l, m-d-Y @ g:i a');
		//return $return;
	}	
	
	function toDateOnly($date){
		$r = new DateTime($date);
		$return = $r->format('l, m-d-Y');
		return $return;
	}
	
	function register() {
		$email= grab('email');
		$password= md5(grab('password'));
		$fname= grab('fname');
		$lname= grab('lname');
		
		if ($email != '' && $password != '' && $fname != '' && $lname != '') {
		
			$result = toQuery("INSERT INTO user(email,password,fname,lname,join_date,last_login) VALUES('$email','$password','$fname','$lname',now(), now())");
			if ($result) {
				$_SESSION['username'] = $fname;
				$_SESSION['last_login'] = 0;
				$_SESSION['user_id'] = mysql_insert_id();
				echo json_encode(array('msg'=>'Successful registration!', 'error'=>''));	
			} else {
				echo json_encode(array('error'=>'Problems processing your registration.'));	
			}
		} else {
			echo json_encode(array('error'=>'Please fill in all fields.'));	
		}
	}

	function grab($var) {
		$p = $_POST[$var];
		return htmlspecialchars(trim($p));	
	}
	
	function toQuery($query) {
		return mysql_query($query);
	}
	
	function login() {
		$email= grab('email');
		$password= md5(grab('password'));
		
			if ($email != '' && $password != '') {
			$query = "SELECT * from user WHERE email='$email' AND password='$password'";
			$result = mysql_query($query);
			
			if ( mysql_num_rows($result) > 0) {
				$row = mysql_fetch_array($result);
				$fname = $row['fname'];
				$id = $row['id'];
				$_SESSION['user_id'] = $id;
				$msg = "Welcome back, $fname.";
				$query = "SELECT last_login FROM user WHERE id='$id'";
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				$_SESSION['last_login'] = $row['last_login'];
				$_SESSION['username'] = $fname;
				echo json_encode(array('msg'=>$msg, 'error'=>''));	
			} else {
				echo json_encode(array('error'=>"Unrecognized login."));	
			}
		} else {
			echo json_encode(array('error'=>'Please fill in your email and password.'));	
		}
	}
	
	function logout() {
		$id = $_SESSION['user_id'];
		$query = "UPDATE user SET last_login=now() WHERE id='$id'";
		mysql_query($query);
		session_destroy();
	}
?>