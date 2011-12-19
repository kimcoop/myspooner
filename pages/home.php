<?

	include('../functions/latest_activity.php');
	include('../include/header.php');
	include_once('../functions/blog.php');
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#home').addClass('active');
	
	$('input.message').click(function() {
				var el = $(this);
				var messageID = el.val();
					$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/latest_activity.php',
						 data: {
						 			'action': 'markAsReceived',
						 			'type'	: 'message',
						 			'received': messageID
						 },
						 success: function(data) {
								el.parent('span').replaceWith( $("<span class='messageMarked'></span").text(data.msg) );
							}
					});// end ajax
			});// end click
	
			$('input.notification').click(function() {
				var el = $(this);
				var tagID = el.val();
					$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/latest_activity.php',
						 data: {
						 			'action': 'markAsReceived',
						 			'type' : 'article_user_tag',
						 			'received': tagID
						 },
						 success: function(data) {
								el.parent('span').replaceWith( $("<span class='notificationMarked'></span").text(data.msg) );
							}
					});// end ajax
			});// end click
			
			$('span.preview').click(function() {
				el = $(this);
				el.parent().next('.full_message').slideToggle();			
			});
});

</script>

<div id="container">

<h2>Notifications: <? echo getNotifications('count'); ?></h2>

<div id="notifications">
<? echo formatNotifications() ?>
</div>

<h2>Messages: <? echo getNewMessages('count'); ?></h2>

<div id="messages">
<? echo formatMessages() ?>
</div>

<h2>Latest Activity</h2>
<div id="updates">
<? echo formatJoiners();
	 echo formatLatestActivities() ?>
</div>

<? include('../include/footer.php') ?>