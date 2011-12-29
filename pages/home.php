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
		alert('todo view sent messages');
	});

	$('#newMessage').click(function() {
		$('#newMessageContainer').slideDown();
	
	});
	
	$('#sendNewMessage').click(function() {
		recipient = $('#recipient').val();
		subject = $('#message_subject').val();
		content = $('#message_content').val();
		
		if (recipient < 1 || content=='') {
			alert('error');
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
							notice('error');
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

<div id="notifications">
	<h2>Notifications: <span class='georgia'><? echo getNotifications('count') + getTripNotifications('count') + getRequests('count'); ?></span> new</h2>
	<? echo formatRequests() ?>
	<? echo formatNotifications() ?>
</div>


<div id="messages">
	<h2>Messages: <? echo getNewMessages('count'); ?></h2>
	<div id="notice" style="visibility:hidden">&nbsp;</div>
	
	<div id='viewSent' class='button_container' style=''>View sent messages
	<span class='next'></span></div><br>
	
	<div id='newMessage' class='button_container' style=''>Write a message
	<span class='addNew'></span></div>
	
	<div id='newMessageContainer' class='full_message' style='display:none'>
		<? echo getUsersAsSelect('recipient', null, false, 'Select recipient'); ?>
		<input style='width:50%;margin-bottom:.5em;' type='text' placeholder='Subject here' id='message_subject'>
		<textarea style="height:6em;width:92%" id="message_content" maxlength="4000" placeholder="Message here"></textarea>
		<input type='button' value='Send' id='sendNewMessage'>
	</div>
	
	<? echo formatMessages() ?>
</div>

<div id="updates">
		<h2>Latest Activity</h2>
	
	<? echo formatSpoonerDates(); //TODO - find a better way to organize these
		 echo formatJoiners();
		 echo formatLatestActivities() ?>
</div>

<? include('../include/footer.php') ?>