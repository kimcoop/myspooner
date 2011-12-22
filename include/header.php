<? 
	session_start();
	include('functions/functions.php') 

?>

<!DOCTYPE html>
<html>
<head>
<title>MySpooner</title>

<link type="text/css" rel="stylesheet" href="../styles/main.css"/>
<link type="text/css" rel="stylesheet/less" href="../styles/myspooner.less"/>
<link type="text/css" rel="stylesheet/less" href="../styles/interior.less"/>
<link type="text/css" rel="stylesheet/less" href="../styles/interior-2.less"/>
<link rel='stylesheet' type='text/css' href='../styles/fullcalendar.css' />


<link href='http://fonts.googleapis.com/css?family=Lancelot' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="../js/less.min.js"></script>
<!--<script type="text/javascript" src="../js/jquery.min.js"></script>-->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<script type='text/javascript' src='../js/fullcalendar.js'></script>

<script type="text/javascript">

$(function() {
		
		$('#logout').click(function(e) {
			e.preventDefault();
			$.ajax({ 
					 type: 'post',
					 data: 'action=logout',
					 dataType: 'json',
					 url: '../functions/functions.php',
					 success: function(data) {
							window.location.href = '../index.php';
					 }
			});
			return false;
		});
		
		$('.nav').click(function() {
			id = this.id;
			window.location.href = id + '.php';		
		});
		
		$('.question').click(function() {
			$(this).next('.answer').css({
        'visibility' : 'visible',
        'opacity' : 0
    	}).fadeTo('slow', 1).delay(3000).fadeTo('slow', 0);
		});
		
		
			
			$('.tag').click(function() {
				var tag = $(this).text();
				$('#container').load('filterblog.php', function() {
					$('#filterOn').text('Displaying blog posts tagged as '+tag);
					$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/blog.php',
						 data: {
						 			'action': 'getArticlesByTag',
						 			'tag': tag
						 },
						 success: function(data) {
								$('#posts').html(data.articles);
							}
					});// end ajax
				});// end function		
			});// end click
			
			
			$('.user').click(function() {
				var user = $(this).attr('id');
				var dataString = 'action=getProfile&userID='+user;
				alert(dataString);
				
				$.ajax({ 
						 type: 'get',
						 dataType: 'json',
						 url: 'profile.php',
						 data: dataString,
						 success: function(data) {}
					});// end ajax		
				
				//window.location.href = 'profile.php';
			});// end click
		
	});
	
	
function isValidEmail(email) {
    var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(email);
};
		
</script>		

</head>

<? if (isset($_SESSION['username'])) { ?>

<header>

	<span id="username"><? echo $_SESSION['username'] ?></span>
	<span id="logout">Logout</span>

<span class="nav" id="calendar">Calendar</span>
<span class="nav" id="blog">Blog</span>
<span class="nav" id="settings">Settings</span>
<span class="nav" id="photos">Photos</span>
<span class="nav" id="wall">Wall</span>
<span class="nav" id="home">Home</span>

</header>

<? } ?>

<body>