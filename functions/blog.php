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
			case 'getArticlesByTag':
				getArticlesByTag($_POST['tag']);
				break;
			case 'comment':
				postComment($_POST['articleID'], $_POST['content'], $_SESSION['user_id']);
				break;
		}
	}
	
 function postComment($artID, $content, $user_id) {
	$query = sprintf("INSERT INTO article_comment(post_date, content, article_id, user_id) VALUES(now(), '%s', '%s', '%s')", $content, $artID, $user_id);
	mysql_query($query);
	unset($_POST);
 }
	
 function tagArticle($tags, $articleID) {
 		if (!empty($tags)) {
 			foreach($tags as $tag) {
				$query = sprintf("INSERT INTO article_tag(article_id, tag_id) VALUES('%s', '%s')", $articleID, $tag);
				mysql_query($query);
 			}
 		} 
 }
 
 function userTagArticle($userTags, $articleID) {
 		if (!empty($userTags)) {
 			foreach($userTags as $user) {
				$query = sprintf("INSERT INTO article_user_tag(article_id, user_id) VALUES('%s', '%s')", $articleID, $user);
				mysql_query($query);
 			}
 		} 
 }

 function addNewTag() { //TODO don't insert if not unique
 	$query = "INSERT INTO tag(name) VALUES(".$_POST['tag'].")";
 	mysql_query($query);
 }
	
 function getAllTags() {
 	$query = "SELECT * FROM tag ORDER BY name ASC";
 	$result = mysql_query($query) or die(mysql_error());
	$tags = array();
	while($row = mysql_fetch_array($result)){
			$tags[] = $row;
		}
	return $tags;
 }
 
 function getAllUserTags() {
 	$query = "SELECT id, fname FROM user ORDER BY fname ASC";
 	$result = mysql_query($query) or die(mysql_error());
	$tags = array();
	while($row = mysql_fetch_array($result)){
			$tags[] = $row;
		}
	return $tags;
 }
 
 function getUserTagsAsCheckbox() {
 	$tags = getAllUserTags();
 	$str = "";
 	$str .= "<div class='tagSelect'><span>Tag users in your post:</span><div>";
 	foreach($tags as $tag) {
 		$fname = $tag['fname'];
 		$id = $tag['id'];
 		$str .= "<input type='checkbox' value='$id' class='userTag' name='userTags[]'>&nbsp;<label>$fname</label><br>";
 	}
 	$str .= "</div></div>";
 	return $str; 
 }
 
 function getTagsAsCheckbox() {
 	$tags = getAllTags();
 	$str = "";
 	$str .= "<div class='tagSelect'><span>Tag your blog post:</span><div>";
 	foreach($tags as $tag) {
 		$name = $tag['name'];
 		$id = $tag['id'];
 		$str .= "<input type='checkbox' value='$id' class='tag' name='tags[]'>&nbsp;<label>$name</label><br>";
 	}
 	$str .= "</div></div>";
 	return $str;
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

function queryArticlesByTag($tag) {
	$query = "SELECT article.id, article.title, article.user_id, article.post_date, article.content FROM article, article_tag, tag WHERE tag.name= '$tag'";
	$query .= " AND tag.id = article_tag.tag_id AND article_tag.article_id = article.id";
	$query .= " ORDER BY post_date DESC";
	$result = mysql_query($query) or die(mysql_error());
	$articles = array();
	while($row = mysql_fetch_array($result)){
			$articles[] = $row;
		}
	return $articles;
}

function getArticlesByTag($tag) {
	$articlesByTag = queryArticlesByTag($tag);
	$str = formatArticles($articlesByTag);
	return $str;
}

function formatArticles($articles) {
	$str = "";
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
		
		$str .= formatCommentsForArticle($a['id']);
		
		$str .= "</div>";
	}//end foreach
	return $str;
}

function formatCommentsForArticle($id) {
	$str = "";

	$comments =  getCommentsForArticle($id);
	$str .= totalCommentsForArticle($id);
	$str .= "&nbsp;<div class='addComment'>Add a comment";
	$str .= "<div class='commentForm' style='display:none;'>";
	
	$str .= "<form method='post' action=''>";
	$str .= "<textarea placeholder='Your comment here' class='commentContent'></textarea>";
	$str .= "<input type='hidden' class='artID' value='".$id."'>";
	$str .= "&nbsp;<input type='submit' value='Comment' class='postComment'>";
	$str .="</form></div></div>"; // end div.addComment
	
	if (!empty($comments)) {
		$str .= "<div class='comments'>";
		foreach($comments as $comment) {
			$author = getUsername($comment['user_id']);
			$str .= "<div class='comment'>";
			$str .= "<span>On ".toDate($comment['post_date']).", ".$author." wrote";
			$str .= "</span><br>";
			$str .= $comment['content']."</div>";
		}
		$str .= "</div>"; // end div.comments
	}
	return $str;
}

function getAllArticles() {
	$str = "";
	$articles = queryArticles();
	$str .= formatArticles($articles);
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
	$query = "SELECT * FROM article_comment WHERE article_id = '$id' ORDER BY post_date DESC";
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
	$tags = $_POST['tags'];
	$userTags = $_POST['userTags'];
	
	if ($title!='' && $content!='') {
	
		$user_id = $_SESSION['user_id'];
		$query = "INSERT INTO article(title, user_id, content, post_date) VALUES('$title','$user_id','$content',now())";
		$result = mysql_query($query);
		
		$articleID = mysql_insert_id();
		tagArticle($tags, $articleID);
		userTagArticle($userTags, $articleID);
		
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