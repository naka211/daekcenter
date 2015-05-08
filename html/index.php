<!doctype html>
<html lang="en">
	<?php require_once('head.php'); ?>
<body>
	<div class="wrapper"> 
		<?php require_once('header.php'); ?> 
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
											        <a class="highlite" href="http://www.car-center.dk">www.car-center.dk</a>
											        </p>
										      	</div>
										   </div>
										   <div class="col-xs-6 opening-hours"> 
										      	<div class="inner">
											         <h4>Åbningstider:</h4>
											         <p>Mandag - torsdag 7.30 - 16.00<br>
											            Fredag 7.30 - 14.15
											         </p>
											    </div>      
										        <a href="#" class="btn see-price-btn">Se din dæk pris her <i class="fa fa-angle-double-right"></i></a>
										   </div>
									   </div>
									</div><!-- department-item --> 
									<div class="department-item">
									   <h3>Dækcenter <span class="highlite">Nykøbing</span></h3>
									   <div class="row">
									   		<div class="col-xs-6 desc"> 
										      	<div class="info"> 
											         <p>Randersvej 8,<br>
											        4800 Nykøbing F<br>
											        Tlf. 88 81 09 33<br>
											        <a class="highlite" href="http://www.car-center.dk">www.car-center.dk</a>
											        </p>
										      	</div>
										   </div>
										   <div class="col-xs-6 opening-hours"> 
										      	<div class="inner">
											         <h4>Åbningstider:</h4>
											         <p>Mandag - torsdag 7.30 - 16.00<br>
											            Fredag 7.30 - 14.15
											         </p>
											    </div>      
										        <a href="#" class="btn see-price-btn">Se din dæk pris her <i class="fa fa-angle-double-right"></i></a> 
										   </div>
									   </div>
									</div><!-- department-item --> 
									<div class="department-item">
									   <h3>Dækcenter <span class="highlite">Nykøbing</span></h3>
									   <div class="row">
									   		<div class="col-xs-6 desc"> 
										      	<div class="info"> 
											        <p>Randersvej 8,<br>
											        4800 Nykøbing F<br>
											        Tlf. 88 81 09 33<br>
											        <a class="highlite" href="http://www.car-center.dk">www.car-center.dk</a>
											        </p>
										      	</div>
										   </div>
										   <div class="col-xs-6 opening-hours"> 
										      	<div class="inner">
											         <h4>Åbningstider:</h4>
											         <p>Mandag - torsdag 7.30 - 16.00<br>
											            Fredag 7.30 - 14.15
											         </p>
											    </div>      
										        <a href="#" class="btn see-price-btn">Se din dæk pris her <i class="fa fa-angle-double-right"></i></a> 
										   </div>
									   </div>
									</div><!-- department-item -->  
								</div><!-- departments-list --> 

							</div>  
							<div class="col-sm-6 main-map">
								<div class="wrap-map">
									<img class="img-responsive" src="images/map-demo.jpg">
								</div>
							</div>
						</div><!--row-->
					</section><!-- sec_search_filter -->

					<?php require_once('sec_content_bot.php'); ?>
				</div><!--/.container-->   

		</div><!--main-content-->  
		<?php require_once('footer.php'); ?> 
	</div><!--/.wrap--> 
</body>
</html>