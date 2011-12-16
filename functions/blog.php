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
		return $events;	*/

		
	if (isset($_POST['action']) && !empty($_POST['action']) ){
		$action = $_POST['action'];
		$action = htmlspecialchars(trim($action));
		switch($action) {
			case 'createArticle':
				createArticle();
				break;
			case 'search':
				search();
				break;
		}
	}
	
	
	
function search() {
	$val = $_POST['value'];
	$query = "SELECT * FROM article WHERE title LIKE '%$val%' OR content LIKE '%$val%'";
	$result = mysql_query($query) or die(mysql_error());
	$articles = array();
	while($row = mysql_fetch_array($result)){
			$articles[] = $row;
		}
	echo json_encode(array('results'=>$articles));	

}

function queryArticles() {
	$query = "SELECT * FROM article ORDER BY post_date DESC";
	$result = mysql_query($query) or die(mysql_error());
	$articles = array();
	while($row = mysql_fetch_array($result)){
			$articles[] = $row;
		}
	return $articles;
}

function getAllArticles() {
	$str = "";
	$articles = queryArticles();
	foreach($articles as $a) {
		$str .= "<div class='article'>";
		$str .= "<h3>".$a['title']."</h3>";
		$str .= "<span class='author'>by ".getUsername($a['user_id']);
		$str .= " on " .toDate($a['post_date'])."</span><br>";
		$str .= $a['content'];
		
		$tags = getUserTagsForArticle($a['id']);
		$str .= "<div class='tags'>";
		foreach($tags as $tag) {
			$str .= "<span class='tag'>".$tag['fname']."</span>";
		}
		
		$tags = getTagsForArticle($a['id']);
		foreach($tags as $tag) {
			$str .= "<span class='tag'>".$tag['name']."</span>";
		}
		$str .= "</div>"; // end div.tags
		
		$comments =  getCommentsForArticle($a['id']);
		$str .= totalCommentsForArticle($a['id']);
		$str .= "<div class='comments'>";
		foreach($comments as $comment) {
			$author = getUsername($comment['user_id']);
			$str .= "<div class='comment'>";
			$str .= "<span>On ".toDate($comment['comment_date']).", ".$author." wrote";
			$str .= "</span><br>";
			$str .= $comment['text']."</div>";
		}		
		$str .= "</div>"; // end div.comments
		
		$str .= "</div>";
	}//end foreach
	return $str;
}

function getUserTagsForArticle($id) {
	$query = "SELECT fname FROM article_user_tag, user WHERE article_id = '$id' AND user.id = article_user_tag.user_id";
	$result = mysql_query($query);
	$tags = array();
	while($row = mysql_fetch_array($result)){
			$tags[] = $row;
		}
	return $tags;
}

function getTagsForArticle($id) {
	$query = "SELECT name FROM article_tag, tag WHERE article_id = '$id' AND article_tag.tag_id = tag.id";
	$result = mysql_query($query);
	$tags = array();
	while($row = mysql_fetch_array($result)){
			$tags[] = $row;
		}
	return $tags;
}

function getCommentsForArticle($id) {
	$query = "SELECT * FROM article_comment WHERE article_id = '$id' ORDER BY comment_date DESC";
	$result = mysql_query($query);
	$comments = array();
	while($row = mysql_fetch_array($result)){
			$comments[] = $row;
		}
	return $comments;
}

function totalCommentsForArticle($id) {
	$query = "SELECT count(*) FROM article_comment WHERE article_id = '$id'";
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	$numComments = $row[0];
	if ($numComments == 1) return "1 comment";
	else return "$numComments comments";
}

function createArticle() {

	$title= $_POST['title'];
	$content= $_POST['content'];
	//tags, user tags
	
	if ($title!='' && $content!='') {
	
		$user_id = $_SESSION['user_id'];
		$query = "INSERT INTO article(title, user_id, content, post_date) VALUES('$title','$user_id','$content',now())";
		$result = mysql_query($query);
		if ($result) {
			echo json_encode(array('msg'=>'Your post was added!'));	
		} else {
			echo json_encode(array('error'=>'error'));	
		}
	} else {
		echo json_encode(array('error'=>'Please enter all fields.'));
	}
}


?>