<?php
/**
 * @name		Geocode Factory
 * @package		geoFactory
 * @copyright	Copyright © 2013 - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cédric Pelloquin aka Rick <info@myJoom.com>
 * @website		www.myJoom.com
 */
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
/*
$params  = $this->item->params;
$images  = json_decode($this->item->images);
$urls    = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user    = JFactory::getUser();
$info    = $params->get('info_block_position', 0);*/
JHtml::_('behavior.caption');
$map 	= $this->item;
?>
<section class="sec_search_filter">
	<div class="row">
		<?php echo $map->formatedTemplate;?>
		<!--<div class="col-sm-6 map-detail">
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
					<select class="form-control" onChange='location.href="<?php echo JRoute::_('index.php?option=com_content&view=categories&id=8&Itemid=108');?>?region="+this.value'>
						<option>Vælg område/region</option>
						<option value="jylland">Dækcenter Jylland</option>
						<option value="fyn">Dækcenter Fyn</option>
						<option value="sjaelland">Dækcenter Sjælland</option>
						<option value="lolland-falster">Dækcenter Lolland-Falster</option>
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
				
				
			</div>
			
			
		</div>
		<div class="col-sm-6 main-map">
			<div class="wrap-map">
			<?php echo $map->formatedTemplate;?>
			</div>
		</div>-->
	</div>
	<!--row--> 
</section>
<?php return;?>
<div class="item-page<?php echo $this->params->get('pageclass_sfx') ;?>">
	<?php if ($this->params->get('show_page_heading') ) : ?>
		<div class="page-header">
			<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
		</div>
	<?php endif;?>
	<?php if ($this->params->get('show_title') || $this->params->get('show_author')) : ?>
		<div class="page-header">
			<h2>
				<?php if ($this->item->state == 0) : ?>
					<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
				<?php endif; ?>
				<?php if ($this->params->get('show_title')) : ?>
					<?php echo $this->escape($this->item->title); ?>
				<?php endif; ?>
			</h2>
		</div>
	<?php endif; ?>
	<?php
		echo $map->formatedTemplate ;
	?>
</div>

