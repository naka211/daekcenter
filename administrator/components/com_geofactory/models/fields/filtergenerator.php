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

class JFormFieldFilterGenerator extends JFormField {
	protected $type = 'filterGenerator';

	protected function getInput() {
		$fieldsOpt 	= $this->_getFilterFieldsOptions() ;
		$operators 	=  $this->_getFilterOperatorsOptions(array('=','<>','<','>', 'LIKE')) ;
		$attr 		= 'class="input-medium"';
		$typeList 	= $this->form->getValue("typeList") ;

		// récupère le generateur de filtre JS pour le plugin
		$title 		= 'Filter generator help';
		$txt 		= "The Geocode Factory query will be build with your optionnal filter, like this sample (your filter in bold) : <br />" ; //JText::_('COM_GEOFACTORY_QUERY_FILTER_HELP')
		$jsPlugin	= '';
		$config 	= JComponentHelper::getParams('com_geofactory');
		JPluginHelper::importPlugin( 'geocodefactory');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger( 'getFilterGenerator', array($typeList, &$jsPlugin, &$txt) );
		$this->addJs($jsPlugin) ;

		$html = array() ;
		$html[] = '<div class="input-prepend input-append">';
		$html[] = 	JHtml::_('select.genericlist', $fieldsOpt, "gf_filter_generator_fields", $attr, 'value', 'text');
		$html[] = 	JHtml::_('select.genericlist', $operators, "gf_filter_generator_operator", $attr, 'value', 'text');
		$html[] = 	'<input type="text" value="value_to_test" class="btn input"  id="gf_filter_generator_value"> ' ;
		$html[] = 	'<input type="button" value="insert" class="btn input" name="" onClick="insertFilter();"> ' ;
		$html[] = '</div><br />';
		$html[] = '<div id="gf_filter_generator_help" class="alert alert-info" style="display:none;width:500px;"><h4>'.$title.'</h4><p>'.$txt.'</p></div>' ;
		$html[] = '<textarea name="' . $this->name . '" id="' . $this->id . '" style="float:left!important;width:500px;height:75px;">';
		$html[] = $this->value ;
		$html[] = '</textarea>';
		return implode($html) ;
	}

	protected function _getFilterFieldsOptions(){
		$config 	= JComponentHelper::getParams('com_geofactory');
		$all 		= true ;//(bool) $config->get('useAllFields');
		$options 	= array() ;
		$typeList 	= $this->form->getValue("typeList") ;

		JPluginHelper::importPlugin( 'geocodefactory');
		$dispatcher = JDispatcher::getInstance();

		// try if the specific function exists ...
		$dispatcher->trigger( 'getCustomFieldsForFilter', array($typeList, &$options, $all) );

		// ... no? then send the regular function ! 
		if (count($options)<1)
			$dispatcher->trigger( 'getCustomFields', array($typeList, &$options, $all) );
 
		array_unshift($options, JHtml::_('select.option', '0', JText::_('JSELECT'))) ;

		return $options ;
	}

	protected function _getFilterOperatorsOptions($ar){
		$res = array() ;
		foreach ($ar as $op){
			$temp = new stdClass() ;
			$temp->value = $op ;
			$temp->text = $op ;

			$res[] = $temp ;
		}

		return $res ;
	}

	protected function addJs($jsPlugin){
		$js=array() ;
		$js[] = "function insertFilter(){ ";
		$js[] = "	jQuery('#gf_filter_generator_help').show();";
		$js[] = "	var field 		= jQuery('#gf_filter_generator_fields'	).val();";
		$js[] = "	var cond 		= jQuery('#gf_filter_generator_operator').val();";
		$js[] = "	var value 		= jQuery('#gf_filter_generator_value'	).val();";
		$js[] = "	var result 		= '?';";
		$js[] = "	var like 		= '' ;";
		$js[] = "	if(cond=='LIKE'){ cond=' LIKE '; like='%'; }";
		$js[] = "	if(field=='0') {alert('Select a value.'); return ;}";

		$js[] = 	$jsPlugin ;

		$js[] = "	jQuery('#".$this->id."').val(jQuery('#".$this->id."').val() + ' ' + result);";
		$js[] = "}";

		GeofactoryHelperAdm::loadJsCode($js); 
	}
}