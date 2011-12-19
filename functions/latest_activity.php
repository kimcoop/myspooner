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
			case 'markAsReceived':
				markAsReceived($_POST['type'], $_POST['received']);
				break;
		}
	}
	
	function formatMessages() {
		$str = "";
		$messages = getNewMessages();
		if (!empty($messages) ) {
			foreach($messages as $message) {
				$subject = $message['subject'];
				$from = getUsername($message['written_by']);
				$date = toDate($message['post_date']);
				$content = $message['content'];
				$id = $message['id'];
				$str .= "<div class='message'><span class='preview'>On $date, $from wrote you a message.</span><span class='checkboxes'><input type='checkbox' class='message' value='$id' name='received'>&nbsp;<label>Mark as read</label></span></div>";
				$str .= "<div class='full_message' style='display:none'><div class='subject'>Subject: $subject</div><div class='content'>$content.</div></div>";
			}
		}
		return $str;
	}
	
	function getNewMessages($action=null) {
		$id = $_SESSION['user_id'];
		$query = "SELECT * FROM message WHERE recipient='$id' AND received='0' ORDER BY post_date DESC";
		$result = mysql_query($query);
			
			if ($action != null) {
				$messages = mysql_num_rows($result);
				return "$messages new";
			} else {
				$messages = array();
				while($row = mysql_fetch_array($result)){
						$messages[] = $row;
					}
				return $messages;
			}
	}
	
	function markAsReceived($type, $id) {
		$query = "UPDATE $type SET received=1 WHERE id='$id'";
		mysql_query($query);
		echo json_encode(array('msg'=>'Marked as read.'));
	}
	
	function formatNotifications() {
		$str = "";
		$tags = getNotifications();
		foreach($tags as $tag) {
			$post = getArticleTitle($tag['article_id']);
			$tagger = getUsername($tag['tagger_id']);
			$date = toDate($tag['tag_date']);
			$id = $tag['id'];
			$str .= "<div class='notification'>On $date, $tagger tagged you in a post called $post.";
			$str .= "<span class='checkboxes'><input type='checkbox' class='notification' value='$id' name='received'>&nbsp;<label>Mark as read</label></span></div>";
		}
		
		return $str;
	}
	
	function getNotifications($action=null) {
		$id = $_SESSION['user_id'];
		$query = "SELECT * FROM article_user_tag WHERE received=0 AND user_id='$id'";
		$result = mysql_query($query);
		
		if ($action != null) {
			$tags = mysql_num_rows($result);
			return "$tags new";
		} else {
			$notifications = array();
			while($row = mysql_fetch_array($result)){
					$notifications[] = $row;
				}
			return $notifications;
		}
	}
	
	function getJoiners(){
		$id = $_SESSION['user_id'];
		$date = $_SESSION['last_login'];
		$query = "SELECT * FROM user WHERE join_date>'$date' ORDER BY join_date DESC";
		$result = mysql_query($query) or die(mysql_error());
		$joiners = array();
		while($row = mysql_fetch_array($result)){
				$joiners[] = $row;
			}
		return $joiners;
	}
	
	function formatJoiners() {
		$str = "";
		$joins = getJoiners();
		foreach($joins as $j) {
			$username = getUsername($j['id']);
			$date = toDate($j['join_date']);
			$str .= "<div class='latest'>".$username." <span class='greenText'>joined</span> MySpooner!<span class='timestamp'>$date</span></div>";
		}
		return $str;	
	}
	
	function getArticleTitle($id) {
		$query = "SELECT title FROM article WHERE id='$id'";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		return $row['title'];	
	}
	
	function formatLatestActivities() {
		$str = "";
		$activities = getLatest();
		if (!empty($activities)) {
			foreach($activities as $a) {
				$username = getUsername($a['user_id']);
				$content = $a['content'];
				$date = toDate($a['post_date']);
				$title = getArticleTitle($a['id']);
				$str .= "<div class='latest'>".$username." <span class='pinkText'>wrote</span> a blog post.&nbsp;&nbsp;<span class='timestamp'>$date</span>";
				$str .= "<br>$title".formatUserTagsForArticle($a['id']).formatTagsForArticle($a['id'])."</div>";			
			}
		} else $str .= "No updates since your last login on ".toDate($_SESSION['last_login']).".";
		return $str;
	}
	
	function getLatest() {
		$all = array();
		$date = $_SESSION['last_login'];
		$query = "SELECT * FROM article WHERE post_date>'$date' ORDER BY post_date DESC";
		//$query = "SELECT * FROM article UNION SELECT * FROM article_comment ORDER BY post_date";
		$result = mysql_query($query) or die(mysql_error());
		while($row = mysql_fetch_array($result)){
				$all[] = $row;
			}
		return $all;	
	}
	
	
	?>