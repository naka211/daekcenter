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
require_once JPATH_COMPONENT.'/helpers/geofactory.php';

class GeofactoryControllerMap extends JControllerLegacy{
	protected $text_prefix = 'COM_GEOFACTORY';

	public function getJson(){
		$app 		= JFactory::getApplication();
		$idMap 		= $app->input->getInt('idmap', -1);
		$model 		= $this->getModel('Map');
		$json 		= $model->createfile($idMap) ;

		// vide le output
		ob_clean();flush();
		echo $json ;
		$app->close();
	}

	public function geocodearticle(){
		$app 		= JFactory::getApplication();
		$id 		= $app->input->getInt('idArt', -1) ;
		$lat 		= $app->input->getFloat('lat') ;
		$lng 		= $app->input->getFloat('lng') ;
		$adr 		= $app->input->getString('adr') ;

		$db 		= JFactory::getDBO();
		$cond 		= 'type='.$db->Quote('com_content').' AND id_content='.(int) $id; 
		$query 		= $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__geofactory_contents');
		$query->where($cond);
		$db->setQuery($query);
		$update 	= $db->loadResult();

		// update or insert 
		$query->clear(); 
		if ((int) $update > 0){
 			$fields = array('latitude='.(float)$lat,'longitude='.(float)$lng,'address='.$db->quote($adr));
			$query->update($db->quoteName('#__geofactory_contents'))->set($fields)->where($cond);
		} else {
			$values = array($db->quote(''),$db->quote('com_content'), (int)$id, $db->quote($adr), (float)$lat, (float)$lng);
			$query->insert($db->quoteName('#__geofactory_contents'))->values(implode(',', $values));
		}

		$db->setQuery($query);
	    $result = $db->execute();
		$app->close();
	}
}
