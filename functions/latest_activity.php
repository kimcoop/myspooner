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
			case 'createArticle':
				createArticle();
				break;
		}
	}*/
	
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
			$str .= "<div class='latest'>".$username." wrote a blog post.<span class='timestamp'>$date</span>";
			$str .= "<p>$title</p></div>";
			
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