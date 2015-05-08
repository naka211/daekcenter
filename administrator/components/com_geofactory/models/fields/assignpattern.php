<?php
/**
 *
 * @name		Geocode Factory
 * @package		geoFactory
 * @copyright	Copyright © 2013 - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cédric Pelloquin aka Rick <info@myJoom.com>
 * @website		www.myJoom.com
 *
 */
defined('JPATH_BASE') or die;

class JFormFieldAssignPattern extends JFormFieldList {
	protected $type = 'assignPattern';

	protected function getOptions() {
		$typeList = null ;
		if ($this->fieldname!='current_view_center_pattern')
			$typeList = $this->form->getValue("typeList") ;

		$options = GeofactoryHelperAdm::getArrayObjAssign($typeList) ;
		return $options ;
	}
}
