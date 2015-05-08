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

class GeofactoryModelAssigns extends JModelList{

	public function __construct($config = array()){
		if (empty($config['filter_fields'])){
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'typeList', 'a.typeList',
				'ordering', 'a.ordering',
				'state', 'a.state',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time'
			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null){
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_geofactory');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.name', 'asc');
	}

	protected function getListQuery(){
		// vérifier que pour chaque produit, il y ai au moins un defautl
		$this->_createDefaultRow() ;

		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id AS id,'.
				'a.name AS name,'.
				'a.extrainfo AS extrainfo,'.
				'a.checked_out AS checked_out,'.
				'a.checked_out_time AS checked_out_time,' .
				'a.typeList,'.
				'a.state AS state'
				)
		);

		$query->from($db->quoteName('#__geofactory_assignation').' AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)){
			$query->where('a.state = '.(int) $published);
		} elseif ($published === ''){
			$query->where('(a.state IN (0, 1))');
		}

		$query->group('a.id, a.name, a.checked_out, a.checked_out_time, a.state, editor');

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('a.name LIKE '.$search);
			}
		}
		
		$query->order($db->escape('a.name').' '.$db->escape($this->getState('list.direction', 'ASC')));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}
	
	private function _createDefaultRow(){
		// pour chaque plugin, vérifie qu'il y aie une config default
		JPluginHelper::importPlugin( 'geocodefactory');
		$dispatcher = JDispatcher::getInstance();
		$vvPluginsInfos = $dispatcher->trigger( 'getPlgInfo' );
		
		$table = JTable::getInstance('Assign', 'GeofactoryTable');
		foreach($vvPluginsInfos as $vPluginsInfos){
			foreach($vPluginsInfos as $plgInfo){
				if ($table->_existDefault($plgInfo[0], $plgInfo[1])>0)
					continue ;
					
				$table->_createDefault($plgInfo[0], $plgInfo[1]);
			}
		}
	}
}
