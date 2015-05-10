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
        });
    </script>
    </head>
    <body>
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
							<ul class="nav navbar-nav">
								<li><a href="index.php">DANMARKSKORT</a></li>
								<li><a href="departments.php">AFDELINGER</a></li>
								<li><a class="dropdown-toggle" data-toggle="dropdown" href="tyre-riminfo.php">DÆK- OG FÆLG INFO <b class="caret"></b></a>
									<ul class="dropdown-menu">
										<li><a href="tyre-riminfo.php">Teknisk info – Dæk</a></li>
										<li><a href="tyre-riminfo.php">Teknisk info – Fælge</a></li>
										<li><a href="tyre-riminfo.php">Teknisk info – Hjul</a></li>
										<li><a href="tyre-riminfo.php">EU-mærkning af dæk</a></li>
										<li><a href="tyre-riminfo.php">Vedligeholdelsesguide dæk</a></li>
										<li><a href="tyre-riminfo.php">Udskiftning af dæk</a></li>
										<li><a href="tyre-riminfo.php">Vinterdæk regler</a></li>
										<li><a href="tyre-riminfo.php">Bliv forhandler</a></li>
									</ul>
								</li>
								<li><a href="credit.php">FÅ KREDIT - RENTEFRIT</a></li>
							</ul>
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
				<section class="sec_search_filter">
					<div class="row">
						<div class="col-sm-6 map-detail">
							<h1>FIND NÆRMESTE FORHANDLER</h1>
							<div class="row rowForm-searh">
								<div class="col-sm-6">
									<label>Find nærmeste forhandler ved at søge efter adresse postnummer eller by</label>
								</div>
								<div class="col-sm-6">
									<form class="form-inline">
										<div class="form-group">
											<input type="text" class="form-control">
										</div>
										<button type="submit" class="btn"><i class="fa fa-search"></i> SØG NU</button>
									</form>
								</div>
							</div>
							<div class="row rowSelect_land">
								<div class="col-sm-6">
									<label>Eller vælg område/region</label>
								</div>
								<div class="col-sm-6">
									<select class="form-control">
										<option>Vælg område/region</option>
										<option>Dækcenter Jylland</option>
										<option>Dækcenter Fyn</option>
										<option>Dækcenter Sjælland</option>
										<option>Dækcenter Lolland-Falster</option>
									</select>
								</div>
							</div>
							<div class="departments-list">
								<h1 class="head-tt highlite">LOLLAND - FALSTER</h1>
								<div class="department-item">
									<h3>Dækcenter <span class="highlite">Nykøbing</span></h3>
									<div class="row">
										<div class="col-xs-6 desc">
											<div class="info">
												<p>Randersvej 8,<br>
													4800 Nykøbing F<br>
													Tlf. 88 81 09 33<br>
													<a class="highlite" href="http://www.car-center.dk">www.car-center.dk</a> </p>
											</div>
										</div>
										<div class="col-xs-6 opening-hours">
											<div class="inner">
												<h4>Åbningstider:</h4>
												<p>Mandag - torsdag 7.30 - 16.00<br>
													Fredag 7.30 - 14.15 </p>
											</div>
											<a href="#" class="btn see-price-btn">Se din dæk pris her <i class="fa fa-angle-double-right"></i></a> </div>
									</div>
								</div>
								<!-- department-item -->
								<div class="department-item">
									<h3>Dækcenter <span class="highlite">Nykøbing</span></h3>
									<div class="row">
										<div class="col-xs-6 desc">
											<div class="info">
												<p>Randersvej 8,<br>
													4800 Nykøbing F<br>
													Tlf. 88 81 09 33<br>
													<a class="highlite" href="http://www.car-center.dk">www.car-center.dk</a> </p>
											</div>
										</div>
										<div class="col-xs-6 opening-hours">
											<div class="inner">
												<h4>Åbningstider:</h4>
												<p>Mandag - torsdag 7.30 - 16.00<br>
													Fredag 7.30 - 14.15 </p>
											</div>
											<a href="#" class="btn see-price-btn">Se din dæk pris her <i class="fa fa-angle-double-right"></i></a> </div>
									</div>
								</div>
								<!-- department-item -->
								<div class="department-item">
									<h3>Dækcenter <span class="highlite">Nykøbing</span></h3>
									<div class="row">
										<div class="col-xs-6 desc">
											<div class="info">
												<p>Randersvej 8,<br>
													4800 Nykøbing F<br>
													Tlf. 88 81 09 33<br>
													<a class="highlite" href="http://www.car-center.dk">www.car-center.dk</a> </p>
											</div>
										</div>
										<div class="col-xs-6 opening-hours">
											<div class="inner">
												<h4>Åbningstider:</h4>
												<p>Mandag - torsdag 7.30 - 16.00<br>
													Fredag 7.30 - 14.15 </p>
											</div>
											<a href="#" class="btn see-price-btn">Se din dæk pris her <i class="fa fa-angle-double-right"></i></a> </div>
									</div>
								</div>
								<!-- department-item --> 
							</div>
							<!-- departments-list --> 
							
						</div>
						<div class="col-sm-6 main-map">
							<div class="wrap-map"> <img class="img-responsive" src="<?php echo $tmpl;?>images/map-demo.jpg"> </div>
						</div>
					</div>
					<!--row--> 
				</section>
				<!-- sec_search_filter -->
				
				<section class="sec_content_bot">
					<div class="row">
						<div class="col-sm-7 article-left">
							<h2><a href="#"> HELE DANMARKS DÆKCENTER - <span class="highlite">WWW.DÆKCENTER.NU</span> SOMMERDÆK - VINTERDÆK - FÆLGE</a></h2>
							<p>Lorem ipsum dolor sit amet consectetuer adipiscing elit donec odio quisque volutpat mattis eros nullam malesuada erat ut turpis suspendisse urna nibh viverra non semper suscipit posuere a pede donec nec justo eget felis facilisis fermentum aliquam porttitor mauris sit amet orci aenean dignissim pellentesque felis morbi in sem quis dui placerat ornare pellentesque odio nisi, euismod in pharetra a ultricies in diam sed arcu cras consequat lorem ipsum dolor sit amet.</p>
							<p>Sed egestas, ante et vulputate volutpat, eros pede semper est, vitae luctus metus libero eu augue. Morbi purus libero, faucibus adipiscing commodo quis gravida id, est sed lectus. </p>
						</div>
						<div class="col-sm-5 facebook-box">
							<div class="wrap-fb"> <img src="<?php echo $tmpl;?>images/facebook-box.jpg"> </div>
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