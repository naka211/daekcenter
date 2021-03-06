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
JLoader::register('GeofactoryHelper', JPATH_COMPONENT.'/helpers/geofactory.php');

class GeofactoryViewGgmap extends JViewLegacy{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null){
		// Initialise variables.
		$this->form	= $this->get('Form');
		$this->item	= $this->get('Item');
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= GeofactoryHelperAdm::getActions();

		JToolbarHelper::title($isNew ? JText::_('COM_GEOFACTORY_MAPS_NEW_MAP') : JText::_('COM_GEOFACTORY_MAPS_EDIT_MAP'));

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||$canDo->get('core.create'))) {
			JToolbarHelper::apply('ggmap.apply');
			JToolbarHelper::save('ggmap.save');
		}
		if (!$checkedOut && $canDo->get('core.create')) {
			JToolbarHelper::save2new('ggmap.save2new');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolbarHelper::save2copy('ggmap.save2copy');
		}

		if (empty($this->item->id))  {
			JToolbarHelper::cancel('ggmap.cancel');
		} else {
			JToolbarHelper::cancel('ggmap.cancel', 'JTOOLBAR_CLOSE');
		}

//		JToolbarHelper::divider();
//		JToolbarHelper::help('COM_GEOFACTORY_HELP_XXX');
	}
}
