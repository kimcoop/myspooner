<?

	include('../functions/functions.php');
	include('../include/header.php');
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#photos').addClass('active');

	$.getJSON("http://api.flickr.com/services/feeds/photos_public.gne?id=72827301@N02&lang=en-us&format=json&jsoncallback=?", function(data){
			$.each(data.items, function(i,item){
				$("<img/>").attr("src", item.media.m).appendTo("#images")
					.wrap("<a href='" + item.link + "'></a>");
			});
		
			$("#title").html(data.title);
			$("#description").html(data.description);
			$("#link").html("<a href='"+data.link+"' target=\"_blank\">Visit the Viget Inspiration Pool!</a>");
				//Notice that the object here is "data" because that information sits outside of "items" in the JSON feed
		
			$('#images').cycle({
				fx:     'fade',
				speed:    'normal',
				timeout:  0,
				next:   '#next',
				prev:   '#prev'
			});
		});
	

});

</script>

<div id="container">


<h2>Photos</h2>


<div id="flickrNav"> 
    <a id="prev" href="#">Prev</a><a id="next" href="#">Next</a>
</div>

<h1 id="title"></h1> 
<p id="description"></p> 
<p id="link"></p> 
<div id="images"></div>

<? include('../include/footer.php') ?>