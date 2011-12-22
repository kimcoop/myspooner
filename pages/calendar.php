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
        error: function() {
            alert('Error fetching events.');
        },
        color: '#74E8D5',   // a non-ajax option
        textColor: '#333' // a non-ajax option
    } //end events	
	}); //end fullCalendar

});

</script>

<div id="container">


<div id="big_calendar"></div>


<? include('../include/footer.php') ?>