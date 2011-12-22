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
			case 'createArticle':
				createArticle();
				break;
			case 'search':
				search();
				break;
			case 'getArticlesByTag':
				$articles = getArticlesByTag($_POST['tag']);
				echo json_encode(array('articles'=>$articles));
				break;
			case 'comment':
				$aID = $_POST['articleID'];
				postComment($aID, $_POST['content'], $_SESSION['user_id']);
				$comments = formatCommentsForArticle($aID);
				echo json_encode(array('comments'=>$comments));
				break;
			case 'addNewTag':
				addNewTag($_POST['tag']);
				break;
			}
	}
	
 function postComment($artID, $content, $user_id) {
	$query = sprintf("INSERT INTO article_comment(post_date, content, article_id, user_id) VALUES(now(), '%s', '%s', '%s')", $content, $artID, $user_id);
	mysql_query($query);
	unset($_POST['action']);
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
 		$taggerID = $_SESSION['user_id'];
 		if (!empty($userTags)) {
 			foreach($userTags as $user) {
				$query = sprintf("INSERT INTO article_user_tag(article_id, user_id, tag_date, tagger_id) VALUES('%s', '%s', now(), '%s')", $articleID, $user, $taggerID);
				mysql_query($query);
 			}
 		} 
 }

 function addNewTag($tag) { //Don't insert if not unique
 
 	$query = "SELECT * from tag WHERE name='$tag'";
 	$result = mysql_query($query);
 	if (mysql_num_rows($result) < 1) {
		$query = "INSERT INTO tag(name) VALUES('$tag')";
		mysql_query($query);
		$tags = getTagsAsCheckbox();
		echo json_encode(array('tagsAsCheckboxes'=>$tags));
 	} else {
 		echo json_encode(array('error'=>'Tag already exists.'));
 	}
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
 
 function getAllUsers() {
 	$query = "SELECT id, fname, lname, email FROM user WHERE validated='1' ORDER BY fname ASC";
 	$result = mysql_query($query) or die(mysql_error());
	$tags = array();
	while($row = mysql_fetch_array($result)){
			$tags[] = $row;
		}
	return $tags;
 }
 
 function getUsersAsSelect($selectID =null, $active='') {
 	$users = getAllUsers();
 	$str = "";
 	if ($selectID != null) $str .= "<select id='$selectID'";
 	else $str .= "<select";
 	$str .= " class='editable' disabled>";
 	$str .= "<option value='0'>Not specified</option>";
 	foreach($users as $u) {
 		$id = $u['id'];
 		$name = getUsername($id, 'full');
 		$email = $u['email'];
 		if ($active == $id) $str .= "<option selected";
 		else $str .= "<option";
 		$str .= " id='$email' value='$id'>$name</option>";
 	}
 	$str .= "</select>";
 	return $str;
 }
 
 function getUserTagsAsCheckbox($phrase = 'Tag users in your post:') {
 	$tags = getAllUsers();
 	$str = "";
 	$str .= "<div class='tagSelect'><span>$phrase</span><div>";
 	foreach($tags as $tag) {
 		$id = $tag['id'];
 		$name = getUsername($id, 'full');
 		$str .= "<input type='checkbox' value='$id' class='userTag' name='userTags[]'>&nbsp;<label>$name</label><br>";
 	}
 	$str .= "</div></div>";
 	return $str; 
 }
 
 function getTagsAsCheckbox() {
 	$tags = getAllTags();
 	$str = "";
 	$str .= "<div class='tagSelect'><span>Tag your blog post:</span><span id='tagError' style='visibility:hidden;margin-left:3em'></span><div>";
 	foreach($tags as $tag) {
 		$name = $tag['name'];
 		$id = $tag['id'];
 		$str .= "<input type='checkbox' value='$id' class='tagForArticle' name='tags[]'>&nbsp;<label>$name</label><br>";
 	}
 	$str .= "</div></div>";
 	return $str;
 }
	/*
function search() {
	$val = $_POST['value'];
	$query = "SELECT * FROM article WHERE title LIKE '%$val%' OR content LIKE '%$val%'";
	$result = mysql_query($query) or die(mysql_error());
	$articles = array();
	while($row = mysql_fetch_array($result)){
			$articles[] = $row;
		}
	echo json_encode(array('results'=>$articles));	

}*/

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
	
	//$query .= " OR user.fname='$tag' AND user.id = article_user_tag.user_id AND article_user_tag.article_id = article.id";
	
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

function formatTagsForArticle($id) {
	$str = "";
	$tags = getTagsForArticle($id);
	foreach($tags as $tag) {
		$str .= "<span class='tag'>".$tag['name']."</span>";
	}
	return $str;
}

function formatUserTagsForArticle($id) {
	$str = "";
	$tags = getUserTagsForArticle($id);
	foreach($tags as $tag) {
		$id = $tag['id'];
		$str .= "<span class='tag'>".getUsername($id)."</span>";
	}
	return $str;
}

function formatArticles($articles) {
	$str = "";
	foreach($articles as $a) {
		$str .= "<div class='article'>";
		$str .= "<h3>".$a['title']."</h3>";
		$str .= "<span class='author'>by ".getUsername($a['user_id']);
		$str .= "</span><span class='memberTimestamp'>".toDateWithAgo($a['post_date'])."</span><br>";
		$str .= $a['content'];
		$str .= "<div class='tags'>";
		$str .= formatUserTagsForArticle($a['id']);		
		$str .= formatTagsForArticle($a['id']);
		$str .= "</div>"; // end div.tags
		$str .= formatCommentsForArticle($a['id']);
		
		$str .= "</div>";
	}//end foreach
	return $str;
}

function formatCommentsForArticle($id) {
	$str = "<div class='commentSection'>";

	$comments = getCommentsForArticle($id);
	$str .= "<span class='articleComments'>".totalCommentsForArticle($id)."</span>";
	$str .= "&nbsp;<div class='addComment'>Add a comment";
	$str .= "<div class='commentForm' style='display:none;'>";
	
	$str .= "<form method='post' action=''>";
	$str .= "<textarea placeholder='Your comment here' class='commentContent'></textarea>";
	$str .= "<input type='hidden' class='artID' value='".$id."'>";
	$str .= "&nbsp;<input type='submit' value='Comment' class='postComment'>";
	$str .="<div class='close'></div></form></div></div>"; // end div.addComment
	
	if (!empty($comments)) {
		$str .= "<div class='comments' style='display:none'>";
		foreach($comments as $comment) {
			$author = getUsername($comment['user_id']);
			$str .= "<div class='comment'>";
			$str .= "<span>".toDate($comment['post_date']).", ".$author." wrote";
			$str .= "</span><br>";
			$str .= $comment['content']."</div>";
		}
		$str .= "</div>"; // end div.comments
	}
	$str .= "</div>";
	return $str;
}

function getAllArticles() {
	$str = "";
	$articles = queryArticles();
	$str .= formatArticles($articles);
	return $str;
}
/*

function getTagsForTrip($id) {
	$query = "SELECT fname FROM spooner_trip_tag, user WHERE trip_id = '$id' AND user.id = spooner_trip_tag.user_id";
	$result = mysql_query($query);
	$tags = array();
	while($row = mysql_fetch_array($result)){
			$tags[] = $row;
		}
	return $tags;
}*/

function getUserTagsForArticle($id) {
	$query = "SELECT user.id, fname FROM article_user_tag, user WHERE article_id = '$id' AND user.id = article_user_tag.user_id";
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