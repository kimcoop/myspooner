<? 
	session_start();
	
/* Getting an ID:	
		$row = mysql_fetch_array($result);
		return $row[0];	
		
		Getting a row:
		while($row = mysql_fetch_array($result)){
				$events[] = $row;
			}
		return $events;	

		
	if (isset($_POST['action']) && !empty($_POST['action']) ){
		$action = $_POST['action'];
		$action = htmlspecialchars(trim($action));
		switch($action) {
			case 'getAllArticles':
				getAllArticles();
				break;
		}
	}
*/

function queryArticles() {
	$query = "SELECT * FROM article";
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


?>