<?

	include('../functions/functions.php');
	include('../include/header.php');
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#calendar').addClass('active');

	$('.fc-header').live('click',function() {
		$('#details').fadeOut('fast');
	});
	
	$('#close_details').live('click',function() {
		$('#details').fadeOut();
	});
		
	$('#big_calendar').fullCalendar({
		events: {
        url: '../functions/calendar_functions.php',
        type: 'POST',
        data: {
            action: 'fetchCalendar'
        },       
        error: function() {
            alert('Error fetching events.');
        },
        color: '#74E8D5',   // a non-ajax option
        textColor: '#333' // a non-ajax option
    }, //end events
    
     eventClick: function(calEvent, jsEvent, view) {
     
     		var eventID = calEvent.id;
     		arr = eventID.split('_');
     		id = arr[1];
     		table = arr[0];
     		
     		if (table=='spooner') {
     			action = 'getSpoonerTripDetails';
     		} else if (table=='event') {
     			action = 'getEventDetails';
     		} else {
     			action = 'getBirthdayDetails';
     		}
     		
     		var dataString = 'action='+action
     											+'&id='+id;     		
				$.ajax({ 
						 type: 'post',
						 dataType: 'json',
						 url: '../functions/calendar_functions.php',
						 data: dataString,
						 success: function(data) {
								details = data.details;
								
								var x = jsEvent.pageX; var y = jsEvent.pageY;
								$('#details').fadeIn().css({
									'top':y+6,
									'left':x-3
								}).html(
									"<h2>"+calEvent.title+"</h2><p>"+details+"</p><span class='close' id='close_details'></span>"
								);
						 }
				});
     
     		
     		
    }// end eventClick
    
	}); //end fullCalendar

});

</script>

<div id="details" style="display:none"></div>

<div id="container">
<a href="javascript:window.print()">Click to print</a>

<div id="big_calendar"></div>


<? include('../include/footer.php') ?>