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
			
			indicator = $('<span></span>').text('Click to read');
			indicator_close = $('<span></span').text('Click to close');
			$('span.preview').mouseover(function() {
				el = $(this);
				message = el.parent().next('.full_message');
				if (message.hasClass('reading')) {
					indicator_close.appendTo(el);
				} else {
					indicator.appendTo(el);
				}
			}).mouseout(function() {
				indicator.detach();
				indicator_close.detach();
			});
			
			$('span.preview').click(function() {
				indicator.detach(); indicator_close.detach();
				el = $(this);
				message = el.parent().next('.full_message');
				message.slideToggle('slow', function() {
				
					if (message.css('display')=='block') {
						message.addClass('reading');
					} else { 
						message.removeClass('reading');
					}		
				
				});
				
			});
						
			$('.postReply').live('click',function() {
				var el = $(this);
				var msgID = el.siblings('.msgID').val();
				var content = el.siblings('.replyContent').val();
				var toUser = el.siblings('.toUser').val();
				
				var dataString = 'action=postReply&rootMsgID='+msgID+
													'&content='+content+
													'&toUser='+toUser;
					$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/latest_activity.php',
						 data: dataString,
						 success: function(data) {
								notice('Reply sent.', 0); //todo y this not work
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
			
			$('input.trip_notification').click(function() {
				var el = $(this);
				var tripID = el.val();
				
					$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/latest_activity.php',
						 data: {
						 			'action': 'markTripAsReceived',
						 			'tripID' : tripID
						 },
						 success: function(data) {
								el.parent('span').replaceWith( $("<span class='tripMarked'></span").text(data.msg) );
							}
					});// end ajax
			});// end click
			
});

</script>