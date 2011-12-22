<?

	include('../functions/functions.php');
	include('../include/header.php');
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#calendar').addClass('active');
	
	$('#big_calendar').fullCalendar({
		events: {
        url: '../functions/calendar_functions.php',
        type: 'POST',
        data: {
            action: 'fetchCalendar'
        },
        success: function() {
        	alert('Successfully loaded events.');
        },        
        error: function() {
            alert('Error fetching events.');
        },
        color: 'yellow',   // a non-ajax option
        textColor: 'black' // a non-ajax option
    } //end events	
	}); //end fullCalendar

});

</script>

<div id="container">


<h2>Calendar</h2>


<div id="big_calendar"></div>


<? include('../include/footer.php') ?>