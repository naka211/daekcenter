<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.beez3
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
{module Breadcrumbs}
<section class="credit borb mb20 pb30">
	<div class="row">
		<div class="content-left col-md-3">
			{module Left Menu}
			<!--<ul class="nav-left">
				<li class="active"><a href="tyre-riminfo.php">Teknisk info – Dæk</a></li> 
				<li><a href="tyre-riminfo.php">Teknisk info – Fælge</a></li>
				<li><a href="tyre-riminfo.php">Teknisk info – Hjul</a></li>
				<li><a href="tyre-riminfo.php">EU-mærkning af dæk</a></li>
				<li><a href="tyre-riminfo.php">Vedligeholdelsesguide dæk</a></li>
				<li><a href="tyre-riminfo.php">Udskiftning af dæk</a></li>
				<li><a href="tyre-riminfo.php">Vinterdæk regler</a></li>
				<li><a href="tyre-riminfo.php">Bliv forhandler</a></li>
			</ul>-->
		</div>
		<div class="content-right col-md-9">
			<h1><?php echo $this->escape($this->item->title); ?></h1>
			<?php echo $this->item->text; ?>
		</div>
	</div>
</section>