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
		
	if (isset($_POST['action']) && !empty($_POST['action']) ){
		$action = $_POST['action'];
		$action = htmlspecialchars(trim($action));
		switch($action) {
			case 'writeOnWall':
				writeOnWall($_POST['toUser'], $_POST['post']);
				unset($_POST['action']);
				break;
			case 'getProfile':
				getProfile($_POST['userID']);
				unset($_POST['action']);
				break;
		}
	}
	
	function getProfile($id) {
		$details = "";
		$details .= getLastName($id)."<br>".getEmail($id)."<br>".getPhone($id)."<br>".getAbout($id)."";
		$details .= getUsername(getMother($id))."<br>".getUsername(getFather($id))."<br>";
		$commentSpace = getCommentSpace('Your wall post here', 'Write Post', 'write_post');
		$addNew = getAddPost();
		echo json_encode(array('commentSpace'=>$commentSpace, 'profile'=>$details, 'addNew'=>$addNew, 'userID'=>$id));
	}
	
	function writeOnWall($to, $message) {
	
		$writer = $_SESSION['user_id'];
		$query = "INSERT into post(user_id, writer, post_date, content) ";
		$query .= "VALUES('$to', '$writer', now(), '$message')";
		$result = mysql_query($query);
		if ($result) echo json_encode(array('msg'=>'Successful wall post.'));
		else echo json_encode(array('msg'=>$query));
	
	}
	
	/*
	  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `writer` int(11) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
	*/
	
	function formatWall($id) {
		$str = "";
		$posts = getWall($id);
		
		if (!empty($posts)) {
			$str .= "<div class='posts'>";
			foreach($posts as $p) {
				$str .= "<div class='post'>";
				
				$writer = getUsername($p['writer']);
				$content = $p['content'];
				$date = toDateWithAgo($p['post_date']);
				
				$str .= "$content<span class='memberTimestamp'>by $writer $date</span>";
				
				$str .= "</div>"; //end div.post
			}
			$str .= "</div>"; // end div.posts
		} else $str .= "No wall posts yet.";
		return $str;
	}
		
	function getWall($id) {
		$query="SELECT * FROM post WHERE user_id='$id' ORDER BY post_date DESC";
		$result = mysql_query($query);
		$posts = array();
		while($row = mysql_fetch_array($result)){
				$posts[] = $row;
			}
		return $posts;	
	}
	
	?>