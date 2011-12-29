<?
	session_start();
	include_once('../functions/blog.php');
	include_once('../functions/settings.php');
	include_once('../include/header.php');	
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#trips_events').addClass('active');
	
	var editingTrip = false;
	var tripID;
	
	var editingIndicator = $('<span class="greenText"></span>').html('<strong>Editing trip. Doubleclick blue text to change it. </strong>');
	var saveButton = $('<input type="button" style="margin-right:.4em" value="Save" id="saveTripButton">');
	var cancelButton = $('<input type="button" value="Cancel" id="cancelTripButton">');
	var lineBreak = $('<div></div>').html('<br>');
	
	$('.edit').click(function() {
	
		if (!editingTrip) { //ensure we're not already editing a trip
				var noticeSpace = $('#dates_notice');
				noticeSpace.stop();
				editingTrip = true;
				parent = $(this).parent().parent().parent();
				tripID = parent.attr('id');
				
				parent.prepend(lineBreak);
				parent.prepend(cancelButton);
				parent.prepend(saveButton);
				parent.prepend(editingIndicator);
				
				var el = parent.find('.trip_date').addClass('editingThisTrip').attr('contenteditable',true);
				
			} else {
					$('#dates_notice').stop();
					$('#dates_notice').text('Please first save or cancel your changes.').addClass('errorText').css({
						'visibility':'visible',
						'opacity':0
					}).fadeTo('slow',1).delay(2000).fadeTo('slow',0);
			}
		});
	
	$('#cancelTripButton').live('click',function() {
		var el = $('.editingThisTrip');
		el.removeAttr('contenteditable').removeClass('editingThisTrip');
		removeButtons();
	}); //end cancelTripButton	
	
	$('#saveTripButton').live('click',function() {
	
			var startDateObj = $('.editingThisTrip:first');
			var startDate = startDateObj.text().trim();
			var endDate = startDateObj.next('.editingThisTrip').text().trim();
			
			if ( $('.editingThisTrip').length == 3)
				var notes = $('.editingThisTrip').last().text().trim();
			else notes = '';
		
			var dataString = 'action=updateTrip&tripID='+tripID
												+'&startDate='+startDate
												+'&endDate='+endDate
												+'&notes='+notes;
	
			$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/settings.php',
						 data: dataString,
						 success: function() {
						 		removeButtons();
						 		$('.editingThisTrip').removeAttr('contenteditable').removeClass('editingThisTrip');
								$('#dates_notice').text('Trip saved.').addClass('errorText').css({
										'visibility':'visible',
										'opacity':0
									}).fadeTo('slow',1).delay(2000).fadeTo('slow',0);									
						}
				});
				return false;
	}); //end saveTripButton	
	
	$('.delete').click(function() {
		var conf = confirm('Are you sure you want to delete this trip?');
		
		if (conf) {	
			var parent = $(this).parent().parent().parent();
			var tripID = parent.attr('id');
			var dataString = 'action=deleteTrip&tripID='+tripID;
			
				$.ajax({ 
							 type: 'post',
							 dataType: 'json',
							 url: '../functions/settings.php',
							 data: dataString,
							 success: function() {
									$('#dates_notice').text('Trip deleted.').addClass('errorText').css({
											'visibility':'visible',
											'opacity':0
										}).fadeTo('slow',1, function() {
										parent.fadeOut();
									}).delay(2000).fadeTo('slow',0);
							}
					});
					return false;
			}	
				
	}); //end delete


	$('.newTrip').live('click', function() {
		$('#newTrip').slideToggle();	
	});
	
	$('#cancelNewTrip').live('click', function() {
		$('#newTrip').slideUp();
	});

	$('#arrival').datepicker();
	$('#departure').datepicker();
	
	$('#saveSpoonerDates').live('click', function() {
		var user_id = $('#user_id').val();
		var arrival = $('#arrival').val();
		var departure = $('#departure').val();
		var notes = $('#notes').val();
		
		if (arrival=='' || departure=='') {
			$('#dates_notice').text('Arrival and departure dates must be filled in.').addClass('errorText').css({
										'visibility':'visible',
										'opacity':0
									}).fadeTo('slow',1).delay(2000).fadeTo('slow',0);
		} else {
	
		var dataString = 'action=setSpoonerDates'+
											'&user_id='+user_id+
											'&arrival='+arrival+
											'&departure='+departure+
											'&notes='+notes;
			
		var userTags = $('input.userTag:checked');
											
		$.each(userTags, function() {
			dataString += '&userTags[]=' + $(this).val();
		});
	
		$.ajax({
					 type: 'post',
					 dataType: 'json',
					 url: '../functions/settings.php',
					 data: dataString,
					 success: function(data) {
							$('#dates_notice').addClass('greenText').text(data.msg).fadeIn().delay(2000).fadeOut();
							$('#newTrip').fadeOut('slow');
							$('#userTrips').delay(1000).replaceWith( data.trips );
					}
			});
			return false;
		}
	}); //end click
	
	$('#viewEventCal').click(function() {
		window.location.href = 'calendar.php';
	});
	
	$('#createAnEvent').click(function() {
		window.location.href = 'new_event.php';
	});

	function removeButtons() {
		editingIndicator.detach();
		saveButton.detach();
		lineBreak.detach();
		cancelButton.detach();
		editingTrip = false;
	};

});

</script>

<div id="container">

<? $id = $_SESSION['user_id'];
	 echo "<input type='hidden' value='$id' id='user_id'>";
?>

	<h2>Trips & Events</h2>

	<div id='createAnEvent' class='button_container' style='float:none;margin-top:0em'>Create an event
		<span class='addNew'></span>
	</div>
	<br>
	<div id='viewEventCal' class='button_container' style='float:none;margin-top:0em'>View calendar
		<span class='next'></span>
	</div>
	<br>
	<div class='spoonerDates'>
		
		<div>
			<div class='newTrip button_container' style='float:none;margin-top:0em'>Post a Spooner trip
			<span class='addNew'></span></div>
		</div>
		
		<div id="dates_notice" style="visibility:hidden;">&nbsp;</div>
		<div id='newTrip' style='display:none'>
			<br>
			Arriving:&nbsp;<input type='text' placeholder='When are you coming?' id='arrival' value=''><br><br>
			Leaving:&nbsp;<input type='text' placeholder='When are you leaving?' id='departure' value=''><br><br>
			Trip notes:&nbsp;<input type='text' placeholder='Any other details' id='notes' style='z-index:4000' value=''><br><br>
					
		<? echo getUserTagsAsCheckbox('Tag others on this trip:'); ?>
						
			<br><br><br><br><br><br><input id='saveSpoonerDates' style='z-index:400000' type='button' value='Announce!'>
			<input id='cancelNewTrip' style='z-index:400000' type='button' value='Cancel'>
		</div><!-- end div#newTrip-->
		
		<br>
			<div id="userTrips">
				<? echo formatSpoonerDates($id); ?>
			</div>
	</div><!-- end div.spoonerDates -->


<? include('../include/footer.php') ?>