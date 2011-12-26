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
				$("<img/>").attr("src", item.media.m).addClass('flickr_img').appendTo("#images")
					.wrap("<a href='" + item.link + "'></a>");
					alert(item.title);
			});
		
			$("#description").html(data.description);
			$("#link").html("<a href='"+data.link+"' target=\"_blank\">Visit the album</a>");
				//Notice that the object here is "data" because that information sits outside of "items" in the JSON feed
		
/*			$('#images').cycle({
				fx:     'fade',
				speed:    'normal',
				timeout:  0,
				next:   '#next',
				prev:   '#prev'
			});*/
			
		});
	

});

</script>

<style>

#images {
	clear:both;
	width: 800px;
	margin: 0px auto;
	border: thin solid pink;
	min-height: 500px;
}

img.flickr_img {
	margin: 0 .4em;
	height: 220px;
}

</style>

<div id="container">


<h2>Photos</h2>


<div id="flickrNav"> 
    <a id="prev" href="#">&laquo;&nbsp;Prev</a>&nbsp;<a id="next" href="#">Next&nbsp;&raquo;</a>
</div>

<p id="link"></p>
<p id="description"></p> 
<div id="images"></div>

<? include('../include/footer.php') ?>