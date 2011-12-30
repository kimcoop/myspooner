<?
	include('../functions/latest_activity.php');
	include('../include/header.php');
	include_once('../functions/blog.php');
	include_once('../functions/functions.php');
	include('../js/home_js.js');
	
?>

<script type="text/javascript">

$(function() {

	$('#viewSent').click(function() {
	
		user = $('#user').val();
		dataString = 'action=getSentMessages&user='+user;
		
		$.ajax({ 
					 type: 'post',
					 dataType: 'json',
					 url: '../functions/latest_activity.php',
					 data: dataString,
					 success: function(data) {
						if (data.error) {
							notice(data.error, 1);
						} else {
							$('#sentMessagesContainer').html(data.results).slideDown();
						} // end else
					 }
			});// end ajax
	});

	$('#newMessage').click(function() {
		$('#newMessageContainer').slideToggle();
	
	});
	
	$('#sendNewMessage').click(function() {
		recipient = $('#recipient').val();
		subject = $('#message_subject').val();
		content = $('#message_content').val();
		
		if (recipient < 1 || content=='') {
			notice('Recipient and content are required to send a message.', 1);
		} else { //ping db
			
			dataString = 'action=newMessage&recipient='+recipient
										+'&subject='+subject
										+'&content='+content;
			
			$.ajax({ 
					 type: 'post',
					 dataType: 'json',
					 url: '../functions/latest_activity.php',
					 data: dataString,
					 success: function(data) {
						if (data.error) {
							notice('error', 1);
						} else {
							$('#newMessageContainer').slideUp();
							$('#recipient').val(0);
							$('#message_subject').val('');
							$('#message_content').val('');
							notice(data.msg, 0);
						} // end else
					 }
			});// end ajax			
		}// end else
	
	});
});

</script>

<div id="container">

<input type='hidden' value='<? echo $_SESSION['user_id'] ?>' id='user'>

<div id="notifications">
	<h2>Notifications: <span class='georgia'><? echo getNotifications('count') + getTripNotifications('count') + getRequests('count'); ?></span> new</h2>
	<? echo formatRequests() ?>
	<? echo formatNotifications() ?>
</div>

<br>

<div id="messages">
	<h2 style='display:inline'>Messages: <? echo getNewMessages('count'); ?>&nbsp;&nbsp;</h2>
	<span id="notice" style="visibility:hidden">&nbsp;</span>
	
	<div id='viewSent' class='button_container' style='padding-top:1.2em'>View sent messages
	<span class='next'></span></div><br>
	<div id='newMessage' class='button_container' style=''>Write a message
	<span class='addNew'></span></div>
	
	<div id='newMessageContainer' class='full_message' style='display:none'>
		<? echo getUsersAsSelect('recipient', null, false, 'Select recipient'); ?>
		<input style='width:50%;margin-bottom:.5em;' type='text' placeholder='Subject here' id='message_subject'>
		<textarea style="height:6em;width:92%" id="message_content" maxlength="4000" placeholder="Message here"></textarea>
		<input type='button' value='Send' id='sendNewMessage'>
	</div>
	
	<div id='sentMessagesContainer' style='display:none'></div>

	<? echo formatMessages() ?>
</div>

<div id="updates">
		<h2>Latest Activity</h2>
	
	<? echo formatSpoonerDates(); //TODO - find a better way to organize these
		 echo formatJoiners();
		 echo formatLatestActivities() ?>
</div>

<? include('../include/footer.php') ?>