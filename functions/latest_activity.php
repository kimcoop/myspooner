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
				unset($_POST['action']);
				break;
			case 'postReply':
				postReply($_POST['rootMsgID'], $_POST['toUser'], $_POST['content']);
				unset($_POST['action']);
				break;
		}
	}
	
	function formatThreadedMessages($rootID) {
		$str = "";
		$messages = getThreadedMessages($rootID);
		
		if (!empty($messages)) {
			$str .= "<div class='threadedMessages'>";
			foreach($messages as $m) {
				$from = getUsername($m['written_by']);
				$date = toDate($m['post_date']);
				$content = $m['content'];
				$str .= "<div class='thread'>";
				$str .= "<div class='greenText'>$from wrote:<span class='timestamp greenText' style='float:right'>$date</span></div>";
				$str .= "$content";
				$str .= "</div>";		
			}
			$str .= "</div>"; //end div.threadedMessages
		}
		return $str;	
	}
	
	function getThreadedMessages($rootID) {
		$query = "SELECT * FROM message WHERE root_msg_id = '$rootID' ORDER BY post_date DESC";
		$result = mysql_query($query);
		$messages = array();
		while($row = mysql_fetch_array($result)){
				$messages[] = $row;
			}
		return $messages;	
	}
	
	function getRootMessage($id) {
		$query = "SELECT root_msg_id FROM message WHERE id='$id'";	
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		return $row[0];	
	}
	
	function formatMessages() {
		$str = "";
		$messages = getNewMessages();
		if (!empty($messages) ) {
			foreach($messages as $message) {
				
				$id = $message['id'];
				
				if ($message['is_reply'])	$id = getRootMessage($id);
				
				$subject = $message['subject'];
				if ($subject==null) $subject = 'No subject';
				$fromID = $message['written_by'];
				$from = getUsername($fromID);
				$date = toDate($message['post_date']);
				$content = $message['content'];
				$str .= "<div class='message'><span class='preview'>$from wrote you a message.&nbsp;<span class='timestamp'>$date</span></span><span class='checkboxes'><input type='checkbox' class='message' value='$id' name='received'>&nbsp;<label>Mark as read</label></span></div>";
				$str .= "<div class='full_message' style='display:none'><div class='subject'>Subject: $subject</div><div class='content'>$content.</div>";
				
				$str .= formatThreadedMessages($id);
				
				$str .= formatReply($id, $fromID); //$fromID becomes $to
				$str .= "</div>";

			}
		}
		return $str;
	}
	
	function formatReply($id, $to) {
		$str = "<div class='reply'>";
		$str .= "<form method='post' action=''>";
		$str .= "<textarea placeholder='Your reply here' class='replyContent'></textarea>";
		$str .= "<input type='hidden' class='msgID' value='".$id."'>";
		$str .= "<input type='hidden' class='toUser' value='".$to."'>";
		$str .= "&nbsp;<input type='submit' value='Send' class='postReply'>";
		$str .= "</form></div>"; // end div.replyContent
		return $str;
	}
	
	function postReply($rootMsg, $to, $content) {
		$writer = $_SESSION['user_id'];
		
		$query = "UPDATE message SET received='1' WHERE id=$rootMsg"; //mark the root message as read
		mysql_query($query);
	
		$query = "INSERT INTO message(written_by, recipient, post_date, content, is_reply, root_msg_id) ";
		$query .= " VALUES('$writer', '$to', now(), '$content', '1', '$rootMsg')";
		mysql_query($query);
		
		echo json_encode(array('msg'=>'Reply sent.'));		
	}
	
	function getNewMessages($action=null) {
		$id = $_SESSION['user_id'];
		$query = "SELECT * FROM message WHERE recipient='$id' AND received='0' ORDER BY post_date DESC";
		$result = mysql_query($query);
			
			if ($action != null) {
				$messages = mysql_num_rows($result);
				return "<span class='georgia'>$messages</span> new";
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
		
		if ($type=='message') {
			$query = "UPDATE $type SET received=1 WHERE root_msg_id='$id'";
			mysql_query($query);		
		}
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
			$str .= "<div class='notification'>$tagger tagged you in a post called $post. <span class='timestamp'>$date</span>";
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
			return "<span class='georgia'>$tags</span> new";
		} else {
			$notifications = array();
			while($row = mysql_fetch_array($result)){
					$notifications[] = $row;
				}
			return $notifications;
		}
	}
	
	function formatSpoonerDates() {
		$str = "";
		$dates = getSpoonerDates();
		foreach($dates as $d) {
			$username = getUsername($d['user_id']);
			$date = toDate($d['post_date']);
			$arrival = toDateOnly($d['arrival']);
			$departure = toDateOnly($d['departure']);			
			$str .= "<div class='latest'><div class='greenText'>".$username." posted a Spooner trip!</div>";
			$str .= "<span class='datetime'>$date</span>";
			$str .= "$arrival until $departure.";
			$str .= "</div>";
		}
		return $str;	
	}
	
	function getSpoonerDates() {
		$date = $_SESSION['last_login'];
		$query = "SELECT * FROM spooner_date WHERE post_date>'$date' ORDER BY post_date DESC";
		$result = mysql_query($query) or die(mysql_error());
		$dates = array();
		while($row = mysql_fetch_array($result)){
				$dates[] = $row;
			}
		return $dates;	
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
			$str .= "<div class='latest'>".$username." <span class='greenText'>joined</span> MySpooner!<span class='datetime'>$date</span></div>";
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
				$str .= "<div class='latest'>".$username." <span class='pinkText'>wrote</span> a blog post titled $title<span class='datetime'>$date</span>";
				$str .= "<br>".formatUserTagsForArticle($a['id']).formatTagsForArticle($a['id'])."</div>";			
			}
		}
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