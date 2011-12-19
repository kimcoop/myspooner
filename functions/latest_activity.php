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
				markAsReceived($_POST['tagID']);
				break;
		}
	}
	
	function markAsReceived($tagID) {
		$query = "UPDATE article_user_tag SET received=1 WHERE id='$tagID'";
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
			$str .= "<div class='notification'>On $date, $tagger tagged you in a post called $post.&nbsp;";
			$str .= "<input type='checkbox' class='notification' value='$id' name='received'></div>";
		}
		
		return $str;
	}
	
	function getNotifications($action=null) {
		$id = $_SESSION['user_id'];
		$query = "SELECT * FROM article_user_tag WHERE received=0 AND user_id='$id'";
		$result = mysql_query($query);
		
		if ($action != null) {
			$plural = "";
			$tags = mysql_num_rows($result);
			if ($tags == 1) return "You've been tagged 1 time.";
			else if ($tags == 0) return "No new tags at this time.";
			else return "You've been tagged $tags times.";
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
		$query = "SELECT last_login FROM user WHERE id='$id'";
		$result = mysql_query($query);
		$row = mysql_fetch_array($result);
		$date = $row['last_login'];
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
		foreach($activities as $a) {
			$username = getUsername($a['user_id']);
			$content = $a['content'];
			$date = toDate($a['post_date']);
			$title = getArticleTitle($a['id']);
			$str .= "<div class='latest'>".$username." <span class='pinkText'>wrote</span> a blog post.&nbsp;&nbsp;<span class='timestamp'>$date</span>";
			$str .= "<br>$title".formatUserTagsForArticle($a['id']).formatTagsForArticle($a['id'])."</div>";			
		}
		return $str;
	}
	
	function getLatest() {
		$all = array();
		$query = "SELECT * FROM article ORDER BY post_date DESC";
		//$query = "SELECT * FROM article UNION SELECT * FROM article_comment ORDER BY post_date";
		$result = mysql_query($query) or die(mysql_error());
		while($row = mysql_fetch_array($result)){
				$all[] = $row;
			}
		return $all;	
	}
	
	
	?>