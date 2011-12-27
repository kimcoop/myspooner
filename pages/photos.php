<?

	include('../functions/functions.php');
	include('../include/header.php');
	
?>

<script type="text/javascript">

$(function() {

	$('header span').removeClass('active');
	$('#photos').addClass('active');
	
	//var flickr = new Galleria.Flickr();
/*
	$.getJSON("http://api.flickr.com/services/feeds/photos_public.gne?id=72827301@N02&lang=en-us&format=json&jsoncallback=?", function(data){
			$.each(data.items, function(i,item){
				$("<img/>").attr("src", item.media.m).addClass('flickr_img').appendTo("#gallery")
					//.wrap("<a href='" + item.link + "'></a>");
					
			});
		});
					
		
		flickr.user('72827301@N02', function(data) {
				$('#galleria').galleria({
 
					// add the data as dataSource
					dataSource: data,
			 
					// add a custom fullscreen button
					extend: function() {
							$('#fullscreen').click(this.proxy(function(e) {
									e.preventDefault();
			 
									// call the enterFullscreen() method from the galleria API
									this.enterFullscreen();
							}));
					}
			});
		});
		
		
		$('#slideshow').click(function() {

			var gallery = Galleria.get(0);
			gallery.play();

		});*/// create a flickr instance
        var flickr = new Galleria.Flickr();

        // cache the gallery
        var elem = $('#galleria');

        // create and append the loader growl
        var loader = $('<div>', {
            id: 'loader'
        }).appendTo('#gallery');

        // a local var used later
        var set;

        // set flickr to fetch description and increase the photo limit
        flickr.setOptions({
            max: 100,
            description: true
        });

        // attach event handler for the menu
        $('#menu a').click(function(e) {

            e.preventDefault();

            // toggle active class
            $(this).addClass('active').siblings('.active').removeClass('active');

            // extract the set id from the link href
            set = this.href.split('/');
            set = set[set.length-2];

            // add loader text and show
            loader.text('Loading '+$(this).text()).show();

            // load the set
            flickr.set(set, function(data) {

                // hide the loader
                loader.fadeOut('fast');

                // check if galleria has been initialized
                if (elem.data('galleria')) {

                    // load galleria with the new data
                    elem.data('galleria').load( data );

                // else initialize galleria (1st time)
                } else {
                    elem.galleria({

                        // add the data as dataSource
                        dataSource: data,

                        // add a custom fullscreen button
                        extend: function() {
                            $('#fullscreen').click(this.proxy(function(e) {
                                e.preventDefault();

                                // call the enterFullscreen() method from the galleria API
                                this.enterFullscreen();
                            }));
                        }
                    });
                }
            });
        });

        // trigger a click onload so that the first gallery will be displayed when entering
        $('#menu a:first-child').click();
		
  });// end $function
    

</script>

<style>
/*
 #galleria {
 	height:600px; 
	-moz-box-shadow: 0 4px 4px rgba(0, 0, 0, 0.4);
	-webkit-box-shadow: 0 4px 4px rgba(0, 0, 0, 0.4);
	box-shadow: 0 4px 4px rgba(0, 0, 0, 0.4);
 }*/
 
 html,
body { background:#111; font:13px/1.3 arial,sans-serif }
a { color:#823; text-decoration:none }
#containerr { width:960px; margin:20px auto; position:relative}
#gallery { width:780px; height:500px; float:right; position:relative; border:10px solid #141414 }
#galleria { width:100%; height:100%; background:#000 }
#loader { width:200px; height:100px; margin:-50px 0 0 -100px; position:absolute; left:50%; top:50%; color:#fff; text-align:center;
    z-index:4; display:none; background:#000 url('loader.gif') no-repeat 50% 25px; opacity:.8; line-height:150px; border-radius:6px }
#menu { width:140px; float:left; border-top:1px solid #2b2b2b }
#menu a { display:block; padding:10px 6px; border-bottom:1px solid #2b2b2b }
#menu a:hover { background:#151515; background:rgba(0,0,0,.1); }
#menu a.active { background:#111; background:rgba(0,0,0,.3); color:#fff }
#fullscreen { position:absolute; top:500px; left:0 }
#fullscreen:hover { color:#fff }
 
</style>

<div id="container">


<!--<h2>Photos</h2>

<div id="slideshow" class="clickable">Play slideshow</div><br>
<div id="fullscreen" class="clickable">Enter fullscreen mode</div><br>

<div id="galleria"></div>
-->

<div id="containerr">
    <div id="menu">
        <a href="http://flickr.com/photos/davidhellsing/sets/72057594078378762/">Asia</a>
        <a href="http://flickr.com/photos/davidhellsing/sets/72057594102602315/">Asia 2</a>
    </div>
    <a id="fullscreen" href="#">Enter fullscreen</a>
    <div id="gallery">
        <div id="galleria"></div>
    </div>
</div>

<? include('../include/footer.php') ?>