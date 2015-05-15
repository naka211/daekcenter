<?php 
$tmpl = JURI::base().'templates/daekcenter/';
?>
<!doctype html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <jdoc:include type="head" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1; user-scalable=1;" />
    <link href='http://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo $tmpl;?>css/bootstrap.min.css">
    <!-- <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"> -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $tmpl;?>fonts/css/stylesheet.css">
    <link rel="stylesheet" href="<?php echo $tmpl;?>fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo $tmpl;?>css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $tmpl;?>css/style-mobile.css">

    <!-- JS <script src="http://code.jquery.com/jquery.min.js"></script> -->
    <script src="<?php echo $tmpl;?>js/jquery-1.10.2.min.js"></script>
    <script src="<?php echo $tmpl;?>js/bootstrap.min.js"></script>
    <!-- Add fancyBox -->
    <script type="text/javascript" src="<?php echo $tmpl;?>fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script type="text/javascript" src="<?php echo $tmpl;?>fancybox/source/helpers/jquery.fancybox-media.js"></script>
    <script type="text/javascript">
        $(document).ready(function() { 
            $(".fancybox").fancybox();
            $(".fancybox_login").fancybox({  
                 beforeShow: function(){
                  $(".fancybox-skin").addClass('wrap_login'); 
                 // $(".fancybox-overlay").addClass('aaa'); 
                 }
            }); 
            $(".fancybox_send_gift").fancybox({  
                 beforeShow: function(){
                  $(".fancybox-skin").addClass('wrap_send_gift');  
                 }
            }); 
			
			$(".parent>a").addClass("dropdown-toggle");
			$(".parent>a").attr("data-toggle", "dropdown");
			$(".nav-child").addClass("dropdown-menu");
			$(".parent>a").append('<b class="caret"></b>');
        });
    </script>
    </head>
    <body>
	<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3&appId=755854121175731";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
	<div class="wrapper">
		<header id="header"> 
			<!--Navigation -->
			<nav class="navbar navbar-default navbar-fixed-top">
				<div class="container">
					<div class="inner-content"> <a class="brand" href="index.php"><img src="<?php echo $tmpl;?>images/logo.png" alt=""></a> 
						<!-- Brand and toggle get grouped for better mobile display -->
						<div class="navbar-header page-scroll relative">
							<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
							<a class="navbar-brand" href="index.php"></a> </div>
						
						<!-- Collect the nav links, forms, and other content for toggling -->
						<div class="collapse navbar-collapse pull-right" id="navigation">
							{module Main Menu}
						</div>
						<!-- /.navbar-collapse --> 
					</div>
					<!-- inner-content --> 
				</div>
				<!-- container --> 
			</nav>
		</header>
		<!-- /#header -->
		<div class="main-content">
			<div class="container">
				<jdoc:include type="component" />
				<!-- sec_search_filter -->
				
				<section class="sec_content_bot">
					<div class="row">
						<div class="col-sm-7 article-left">
							{article 1}{text}{/article}
						</div>
						<div class="col-sm-5 facebook-box">
							<div class="wrap-fb">
								<div class="fb-page" data-href="https://www.facebook.com/Daekcenter" data-width="405" data-height="224" data-hide-cover="false" data-show-facepile="true" data-show-posts="false"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/Daekcenter"><a href="https://www.facebook.com/Daekcenter">Dækcenter</a></blockquote></div></div>
							</div>
						</div>
					</div>
				</section>
				<!-- sec_content_bot --> 
			</div>
			<!--/.container--> 
			
		</div>
		<!--main-content-->
		<footer>
			<div class="container">
				<div class="row">
					<div class="col-sm-8">
						<p>© 2015. daekcenter.nu - All rights reserved.</p>
					</div>
					<div class="col-sm-4">
						<p>Design af <a href="http://mywebcreations.dk/" target="_blank">My Web Creations</a></p>
					</div>
				</div>
			</div>
		</footer>
		<script type="text/javascript">
    $(document).ready(function(){ 
        $("#myTab a").click(function(e){
            e.preventDefault();
            $(this).tab('show');
        });
    });
</script> 
	</div>
	<!--/.wrap-->
</body>
</html>