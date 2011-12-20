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
		
	/*if (isset($_POST['action']) && !empty($_POST['action']) ){
		$action = $_POST['action'];
		$action = htmlspecialchars(trim($action));
		switch($action) {
			case 'markAsReceived':
				markAsReceived($_POST['type'], $_POST['received']);
				unset($_POST['action']);
				break;
			case 'postReply':
				postReply($_POST['rootMsgID'], $_POST['toUser'], $_POST['content']);
				unset($_POST['action']);
				break;
		}
	}
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
				$date = toDate($p['post_date']);
				
				$str .= "$content<span class='timestamp' style='float:right'>by $writer on $date</span>";
				
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