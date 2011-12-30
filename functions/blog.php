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
			case 'searchBlog':
				formatSearchResults($_POST['criteria']);
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
			case 'getMyPosts':
				getMyPosts($_POST['user']);
				unset($_POST['action']);
				break;
			case 'deleteArticle':
				deleteArticle($_POST['articleID']);
				unset($_POST['action']);
				break;
			case 'editArticle':
				editArticle($_POST['articleID'], $_POST['content']);
				unset($_POST['action']);
				break;
		}
	}
	
	function editArticle($aID, $content) {
	 $query = "UPDATE article SET content='$content' WHERE id='$aID'";
	 mysql_query($query);
	}
	
	function deleteArticle($aID) {
	 $query = "UPDATE article SET active='0' WHERE id='$aID'";
	 mysql_query($query);
	}
	
	function getMyPosts($user) {
		$query = "SELECT article.id, article.title, article.user_id, article.post_date, article.content, tag.name FROM article, tag, article_tag WHERE article.active='1' AND article.user_id='$user' AND article.id=article_tag.article_id AND article_tag.tag_id=tag.id GROUP BY article.id ORDER BY article.post_date";
		$result = mysql_query($query) or die(mysql_error());
		$results = array();
		while($row = mysql_fetch_array($result)){
				$results[] = $row;
			}
		if (empty($results)) $str = "You haven't written any blog posts :(";
		else $str = formatArticles($results, true);
		echo json_encode(array('articles'=>$str));
}
	
function formatSearchResults($val) {
	$results = searchBlog($val);
	$str = "";
	
	if (!empty($results)) {
	
		$str .= formatArticles($results);
	} else { // no matches
		$str .= "No results found.";
	}
	
	echo json_encode(array('results'=>$str));
}
	
function searchBlog($val) {
	$query = "SELECT article.id, article.title, article.user_id, article.post_date, article.content, tag.name FROM article, tag, article_tag WHERE article.active='1' AND title LIKE '%$val%' OR (tag.name LIKE '%$val%' AND tag.id = article_tag.tag_id AND article_tag.article_id = article.id)";
	$result = mysql_query($query) or die(mysql_error());
	$results = array();
	while($row = mysql_fetch_array($result)){
			$results[] = $row;
		}
	return $results;
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
 
 function getUsersAsSelect($selectID=null, $active='', $disable='true', $default='Not specified') {
 	$users = getAllUsers();
 	$str = "";
 	if ($selectID != null) $str .= "<select id='$selectID'";
 	else $str .= "<select";
 	
 	if ($disable) $str .= " class='editable' disabled>";
 	else $str .= " class='editable'>";
 	
 	$str .= "<option value='0'>".$default."</option>";
 	foreach($users as $u) {
 		$id = $u['id'];
 		$name = getUsername($id, 'full');
 		$email = $u['email'];
 		if ($id != $_SESSION['user_id']) {
			if ($active == $id) $str .= "<option selected";
			else $str .= "<option";
			$str .= " id='$email' value='$id'>$name</option>";
		}
 	}
 	$str .= "</select>";
 	return $str;
 }
 
 function getUserTagsAsCheckbox($phrase) {
 	$tags = getAllUsers();
 	$str = "";
 	$str .= "<div class='tagSelect'><div>";
 	foreach($tags as $tag) {
 		$id = $tag['id'];
 		if ($id != $_SESSION['user_id']) $name = getUsername($id, 'full');
 		$str .= "<input type='checkbox' value='$id' class='userTag' name='userTags[]'>&nbsp;<label>$name</label><br>";
 	}
 	$str .= "</div></div>";
 	return $str; 
 }
 
 function getTagsAsCheckbox($phrase) {
 	$tags = getAllTags();
 	$str = "";
 	$str .= "<div class='tagSelect'><div>";
 	foreach($tags as $tag) {
 		$name = $tag['name'];
 		$id = $tag['id'];
 		$str .= "<input type='checkbox' value='$id' class='tagForArticle' name='tags[]'>&nbsp;<label>$name</label><br>";
 	}
 	$str .= "</div></div>";
 	return $str;
 }

function queryArticles() {
	$query = "SELECT * FROM article WHERE article.active='1' ORDER BY post_date DESC";
	$result = mysql_query($query) or die(mysql_error());
	$articles = array();
	while($row = mysql_fetch_array($result)){
			$articles[] = $row;
		}
	return $articles;
}


function queryArticlesByTag($tag) {
	$tag = trim($tag);
	$query = "SELECT article.id, article.title, article.user_id, article.post_date, article.content, user.fname FROM article, article_user_tag, user WHERE article.active='1' AND user.fname='$tag' AND user.id = article_user_tag.user_id AND article_user_tag.article_id = article.id ORDER BY article.post_date DESC";

	$result = mysql_query($query) or die(mysql_error());
	$articles = array();
	while($row = mysql_fetch_array($result)){
			$articles[] = $row;
		}

	$query2 = "SELECT article.id, article.title, article.user_id, article.post_date, article.content, tag.name FROM article, tag, article_tag WHERE article.active='1' AND tag.name= '$tag' AND tag.id = article_tag.tag_id AND article_tag.article_id = article.id ORDER BY article.post_date DESC";

	$result2 = mysql_query($query2) or die(mysql_error());
	$articles2 = array();
	while($row = mysql_fetch_array($result2)){
			$articles2[] = $row;
		}
	return array_merge($articles, $articles2);
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

function formatArticles($articles, $editable = false) {
	$str = "";
	foreach($articles as $a) {
		$str .= "<div class='article'>";
		$str .= "<input type='hidden' class='article_id' value='".$a['id']."'>";
		
		if ($editable) {
			$str .= "<div class='button_container inline' style='float:left;margin-top:.7em'><span class='delete'></span></div>";
			$str .= "<div class='button_container inline' style='float:left;margin-top:.7em'><span class='edit'></span></div>";
			$str .= "&nbsp;";
		}
		
		$str .= "<h3>".$a['title']."</h3>";
		$str .= "<span class='author'>by ".getUsername($a['user_id'])."</span>";
		$str .= "<span class='memberTimestamp'>".toDateWithAgo($a['post_date'])."</span><br>";
		$str .= "<span class='article_content'>".$a['content']."</span>";
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
			$grav = "<img class='grav' src='".getGravatar($_SESSION['email'], 40)."'/>";
			$str .= "<span>".$grav.toDate($comment['post_date']).", ".$author." wrote";
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

function getUserTagsForArticle($id) {
	$query = "SELECT user.id, fname FROM article_user_tag, user WHERE article.active='1' AND article_id = '$id' AND user.id = article_user_tag.user_id";
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