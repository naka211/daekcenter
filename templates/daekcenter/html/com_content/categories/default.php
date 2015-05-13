<?php
defined('_JEXEC') or die;
$db = JFactory::getDBO();
$query = $db->getQuery(true);
$query->select('*');
$query->from($db->quoteName('#__content'));
$query->where($db->quoteName('state') . ' = 1 AND '.$db->quoteName('catid') . ' IN (9,10,11,12)');
//$query = "SELECT * FROM #__content WHERE state = 1 AND catid IN (9,10,11,12)";
$db->setQuery($query);
$stores = $db->loadObjectList();
?>

{module Breadcrumbs}
<div class="template">
	<section class="departments">
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs" id="myTab">
					<?php foreach($this->items[$this->parent->id] as $id => $item) { ?>
					<!--<li class="active"><a href="#jylland">JYLLAND</a></li>-->
					<li <?php if($item->alias == JRequest::getVar('region', 'jylland')) echo 'class="active"';?>><a href="#<?php echo $item->alias;?>"><?php echo $item->title;?></a></li>
					<?php }?>
				</ul>
				<div class="tab-content">
					<?php foreach($this->items[$this->parent->id] as $id => $item) { ?>
					<div id="<?php echo $item->alias;?>" class="tab-pane fade in <?php if($item->alias == JRequest::getVar('region', 'jylland')) echo 'active';?>">
						<?php foreach($stores as $store){
							if($store->catid == $item->id){
						?>
						<div class="col-md-6 item-departments">
							<h6>Dækcenter <span class="red"><?php echo $store->title;?></span></h6>
							<div class="row">
								<div class="col-md-6">
									<?php echo $store->introtext;?>
								</div>
								<div class="col-md-6">
									<h5>Åbningstider:</h5>
									<?php echo $store->fulltext;?>
									<a class="btn btn-seeprice" href="#">Se din dæk pris her <i class="fa fa-angle-double-right fa-lg"></i></a> </div>
							</div>
						</div>
						<?php }}?>
					</div>
					<?php }?>
				</div>
			</div>
		</div>
	</section>
</div>
