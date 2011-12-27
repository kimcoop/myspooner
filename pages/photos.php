<?

	include('../functions/functions.php');
	include('../include/header.php');
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#photos').addClass('active');
/*
	$.getJSON("http://api.flickr.com/services/feeds/photos_public.gne?id=72827301@N02&lang=en-us&format=json&jsoncallback=?", function(data){
			$.each(data.items, function(i,item){
				$("<img/>").attr("src", item.media.m).addClass('flickr_img').appendTo("#gallery")
					//.wrap("<a href='" + item.link + "'></a>");
					
			});
		});*/
					
		var flickr = new Galleria.Flickr();
		flickr.user('72827301@N02', function(data) {
				$('#galleria').galleria({
						dataSource: data,
				});
		});
		
  });
    

</script>
<style>
 #galleria{height:600px;}
</style>

<div id="container">


<h2>Photos</h2>

<div id="galleria"></div>


<? include('../include/footer.php') ?>