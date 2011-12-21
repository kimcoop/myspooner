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
			
			$('.postReply').live('click',function() {
				var el = $(this);
				var msgID = el.siblings('.msgID').val();
				var content = el.siblings('.replyContent').val();
				var toUser = el.siblings('.toUser').val();
				
				var dataString = 'action=postReply&rootMsgID='+msgID+
													'&content='+content+
													'&toUser='+toUser;alert(dataString);
				
					$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/latest_activity.php',
						 data: dataString,
						 success: function(data) {
								el.parent().replaceWith( $("<span></span").text(data.msg) );
							}
					});// end ajax
			});// end click	
			
				$('input.validation').click(function() {
				var el = $(this);
				var userID = el.val();
				
					$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/latest_activity.php',
						 data: {
						 			'action': 'validateUser',
						 			'user_id' : userID
						 },
						 success: function(data) {
								el.parent('span').replaceWith( $("<span class='validationMarked'></span").text(data.msg) );
							}
					});// end ajax
			});// end click
			
});

</script>

<div id="container">

<div id="validations">
	<h2>New Users</h2>Why am I seeing this? <span class='question'></span>	
	<span class='answer' style='display:none'>Anyone wishing to join MySpooner must be validated as family by an existing member.</span>
	<? echo formatRequests() ?>
</div>

<div id="notifications">
	<h2>Notifications: <? echo getNotifications('count'); ?></h2>
	<? echo formatNotifications() ?>
</div>


<div id="messages">
	<h2>Messages: <? echo getNewMessages('count'); ?></h2>
	<? echo formatMessages() ?>
</div>

<div id="updates">
		<h2>Latest Activity</h2>
	
	<? echo formatSpoonerDates(); //TODO - find a better way to organize these
		 echo formatJoiners();
		 echo formatLatestActivities() ?>
</div>

<? include('../include/footer.php') ?>