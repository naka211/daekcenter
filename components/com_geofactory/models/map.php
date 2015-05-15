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

class GeofactoryModelMap extends JModelItem{
	protected $mapContext 	= 'c';	// c=component, m=module, ...

	public function getItem($idMap = null){
		$app 	= JFactory::getApplication('site');
		$idMap 	= !empty($idMap) ? $idMap : (int) $app->input->getInt('id');
		$map 	= $this->_loadMap($idMap);

		// variables de travail
		$map->forceZoom	 = 0;

		$this->_loadMapTemplate($map);
		$this->_cleanCss($map) ;
		$this->_defineContext($map) ;

		$this->_item[$idMap] = $map;
		return $this->_item[$idMap];
	}

	public function setMapContext($c)	{$this->mapContext=$c;}
	public function createfile($idMap){
		$map = $this->_loadMap($idMap, true);
		$map = (array) $map ;

		return json_encode($map) ;
	}

	private function _loadMap($idMap, $full=false){
		if ($idMap<1)
			return JError::raiseError(404, JText::_('COM_GEOFACTORY_MAP_ERROR_ID'));

		try{
			$map = GeofactoryHelper::getMap($idMap) ;
			if (!$map)
				return JError::raiseError(404, JText::_('COM_GEOFACTORY_MAP_ERROR_UNKNOW'));

			if ($full)
				$this->_mergeAllDataToMap($map) ;
		}
		catch (Exception $e){
			if ($e->getCode() == 404){
				// Need to go thru the error handler to allow Redirect to work.
				JError::raiseError(404, $e->getMessage());
			}
			else{
				$this->setError($e);
				JError::raiseError(404, $e->getMessage());
			}

			return null;
		}

		// détermine le nom de la varaible de map et le nom du container 
		$map->mapInternalName = $this->mapContext.'_gf_'.$map->id ; 
				
		return $map ;
	}

	private function _defineContext(&$map){
		$app 	= JFactory::getApplication('site');
		$option = $app->input->getString('option', '');

		//reset...
		$map->gf_zoomMeId 	= 0;
		$map->gf_zoomMeType = '' ;
		$map->gf_curCat 	= -1 ;

		// dans ce cas on est pas dans un module, ni dans un profile
		if (strtolower($option)!="com_geofactory"){
			JPluginHelper::importPlugin('geocodefactory');
			$dsp 	= JDispatcher::getInstance();
			$dsp->trigger('defineContext', array($option, &$map));

			// compatibilé ascendante car avant on passait pas map, mais defineContext mettait les valeurs
			$session 	= JFactory::getSession();	
			$sszmid = $session->get('gf_zoomMeId');
			$sszmty = $session->get('gf_zoomMeType');

			if (($sszmid>0)			AND ($map->gf_zoomMeId==0))		$map->ss_zoomMeId	= $sszmid ;
			if ((strlen($sszmty)>0)	AND ($map->gf_zoomMeType==''))	$map->gf_zoomMeType	= $sszmid ;
		}
	}

	// ajoute les parametres utiles a la carte directement dans l'objet map
	private function _mergeAllDataToMap(&$map){
		JPluginHelper::importPlugin('geocodefactory');
		$dsp 	= JDispatcher::getInstance();
		$app 	= JFactory::getApplication('site');
		$config	= JComponentHelper::getParams('com_geofactory');
		$session= JFactory::getSession();

		// données de l'url
		$map->forceZoom			= $app->input->getInt('zf',0);
		$mapName				= $app->input->getString('mn');
		$map->mapInternalName	= strlen($mapName)>3?$mapName:$map->mapInternalName;

		$map->mapStyle = str_replace(array("\n","\t","  ","\r"),'',trim($map->mapStyle)) ;

		// prépare et teste les valeur du clustering
		$map->gridSize 		= intval($map->gridSize)>0?intval($map->gridSize):60 ;
		$map->minClustSize 	= intval($map->minimumClusterSize)>0?intval($map->minimumClusterSize):2 ;
		$map->endZoom 		= intval($map->clusterZoom)>0?intval($map->clusterZoom):10 ;
		$map->imagePath 	= (strlen(trim($map->imagePath))>0)?$map->imagePath:"";
		$map->imageSizes	= (strlen(trim($map->imageSizes))>0)?$map->imageSizes:"" ;

		// dyn radius maximum
		$map->dynRadDistMax	= intval($map->dynRadDistMax)>0?intval($map->dynRadDistMax):50 ;

		// chemin commun de toutes les images utilisées par JS
		$map->common_image_path = JURI::root().'media/com_geofactory/assets/' ;

		// passe le bon index.php
		$map->rootJoomla	= JURI::root(true)."/index.php" ;

		// charge les textes
		$map->idsNoStreetView 	= JText::_('COM_GEOFACTORY_NO_STREEETVIEW_HERE') ;
		$map->dynRadTxtCenter	= JText::_('COM_GEOFACTORY_DYN_RAD_MOVE_CENTER') ;
		$map->dynRadTxtDrag		= JText::_('COM_GEOFACTORY_DYN_RAD_MOVE_SIZER') ;

		// ajout de la note de bas de carte
		$map->hideNote = $config->get('hideNote')?1:0 ;

		// divers codes de plugins a verifier... ajoute ce qui y est
		$gateways = array() ;
		$dsp->trigger('gatewayPresent', array(&$gateways));
		// ajoute chaque gateway dispo comme nouvelle variable ... $map->pluginMachin = 1/0 ;
		if (count($gateways)){foreach($gateways as $k=>$v)$map->$k = $v ;}

		// ajout des données de section
		// si je suis en train d'afficher le profile d'un user depuis le plugin CB / JS
		$map->ss_zoomMeId	= $app->input->getInt('zmid');
		$map->ss_zoomMeTy	= $app->input->getString('tmty');

		// savoir si le zoom de la session de l'object courant est ou non un profile
		$map->ss_zoomMeProf = 0 ;
		$dsp->trigger('isProfile', array($map->ss_zoomMeTy, &$map->ss_zoomMeProf));
		$map->ss_zoomMeProf = $map->ss_zoomMeProf?1:0;

		// array contenant les coordonnées de l'article -> je suis dans un article, je centre la carte sur ce dernier
		// on peut pas gérer les articles avec zoomme, car l’id de l’article est parfois dans com_contents, com_frontpage, com_custom..... bref
		$articleArray = $session->get('gf_article_map_center', array());
		$session->clear('gf_article_map_center');
		$map->ss_artLat = count($articleArray)>1?$this->_checkCoord($articleArray[0]):255;
		$map->ss_artLng = count($articleArray)>1?$this->_checkCoord($articleArray[1]):255;

		// on en profite pour chercher les coordonées serverside... ca vaut ce que ca vaut... 
		$map->centerlatPhpGc = 0;
		$map->centerlngPhpGc = 0;

		if (function_exists('file_get_contents')){
			$ip = $_SERVER['REMOTE_ADDR'];
			if (strlen($ip)>6){
				$json = '' ;
			    $url = "http://ipinfo.io/{$ip}/json";
				 
				$opts = array('http'=>array('timeout'=>1));
				$context = stream_context_create($opts);
				$json = file_get_contents($url,false,$context);

				if (strlen($json)>2){
					$details = json_decode($json);
					if ((strlen($details->city)>3) AND (strlen($details->loc)>3)){
						$coor = explode(',', $details->loc);
						if (is_array($coor) AND (count($coor)==2)){
							$map->centerlatPhpGc = $this->_checkCoord($coor[0]);
							$map->centerlngPhpGc = $this->_checkCoord($coor[1]);
						}
					}
				}
			}
		}

		$map->centerlat = $this->_checkCoord($map->centerlat);
		$map->centerlng = $this->_checkCoord($map->centerlng);
	}

	private function _checkCoord($coord){
		if (!$coord)				return 0;
		if ($coord == "?")			return 0;
		if ($coord == 255)			return 0;
		if ($coord == "")			return 0;
		if (!is_numeric($coord))	return 0;

		return $coord;
	}

	private function _loadMapTemplate(&$map){
		// plugins...
		JPluginHelper::importPlugin('geocodefactory');
		$dsp 	= JDispatcher::getInstance();

		// charge les markersets
		$config			= JComponentHelper::getParams('com_geofactory');
		$app 			= JFactory::getApplication('site');
		$template 		= htmlspecialchars_decode($map->template) ;
		$mapVar			= $map->mapInternalName ;
		$map->fullCss 	= strlen($map->cssMap)>0?$map->cssMap:"" ;

		// --- la carte elle meme
		$width 	= strlen($map->mapwidth)>5?$map->mapwidth:"width:200px"; 
		$height	= strlen($map->mapheight)>5?$map->mapheight:"height:200px"; 
		$size = str_replace(' ', '', $width .";".$height.";");

		$carte = '<div id="gf_waitZone"></div>';
		$carte.= '<div id="'.$mapVar.'" class="gf_map" style="'.$size.'"></div>';

		// si pas de carte dans template ...
		if (strpos($template, "{map}") === false)
			$template = $template.'{map}' ;

		// dans ce cas j'enlève tout et créer un template auto
		if ($map->templateAuto==1){
			if (strpos($template, "{radius_form}") === false)
				$template = $template.'{radius_form}' ;

			$map->radFormMode=3;			
		}

		$template = str_replace('{map}', $carte, $template);

		// --- nombre de markers
		$number = '<span id="gf_NbrMarkersPre">0</span>';
		$template 	= str_replace('{number}', $number, $template);

		// --- dessin du side bar
		$template 	= str_replace('{sidebar}', '<div id="gf_sidebar"></div>', $template);

		// --- dessin du side list
		$template 	= str_replace('{sidelists}', '<div id="gf_sidelists"></div>', $template);

		// --- side list premium (mixe deux listes et mes les resulats )
		$template 	= str_replace('{sidelists_premium}', '<div id="gf_sidelistPremium"></div>', $template);

		// --- boutons
		$locate_me		= '<input id="gf_locate_me" 	type="button" 	value="'.JText::_('COM_GEOFACTORY_LOCATE_ME').'" 		onClick="'.$mapVar.'.LMBTN();" />';
		$save_me 		= '';//'<input id="gf_save_me" 		type="button" 	value="'.JText::_('COM_GEOFACTORY_SAVE_ME').'"			onClick="axSavePos(locateMarker.getPosition().lat(), locateMarker.getPosition().lng(), '.$mapVar.');" />';
		$reset_me		= '';//plus pour l'instant <input id="gf_reset_me" 		type="button" 	value="'.JText::_('COM_GEOFACTORY_RESET_ME').'"		onClick="axSavePos(255, 255, '.$mapVar.');" />';
		//$near_me		= '<input id="gf_near_me" 		type="button"	value="'.JText::_('COM_GEOFACTORY_NEAR_ME').'"			onClick="'.$mapVar.'.NMBTN();" />';
		$near_me		= '<button id="gf_near_me" onclick="'.$mapVar.'.NMBTN();" class="btn btnNearme" type="button">NÆR MIG</button>';
		$save_me_full 	= '';//plus pour l'instant '<input id="gf_save_me_full"	type="button" 	value="'.JText::_('COM_GEOFACTORY_SAVE_ME_FULL').'"	onClick="axSavePos(locateMarker.getPosition().lat(), locateMarker.getPosition().lng(), '.$mapVar.', true);" />';
		$reset_map 		= '<input type="button" onclick="'.$mapVar.'.SLRES();" id="gf_reset_rad_btn" value="'.JText::_('COM_GEOFACTORY_RESET_MAP').'"/>';

		// --- route si route est utilisé et que {route} n'est pas dans le template je l'ajoute automatiquement 
		$route = $this->_getRouteDiv();
		if ((strpos($template, '{route}')===false) AND ($map->useRoutePlaner))
			$template.= '{route}';

		// --- selecteur de listes
		$idMs = GeofactoryHelper::getArrayIdMs($map->id) ;
		$selector = "" ;
		$m_selector = "" ;
		$toggeler = "" ;
		$toggeler_map =  "";
		$selector_1 = "" ;
		$m_selector_1 = "" ;
		$toggeler_1 = "" ;
		$toggeler_img = "" ;
		$toggeler_img_1 = "" ;
		if (count($idMs)){
			$selector		.='<select id="gf_list_selector"  onChange="'.$mapVar.'.SLFP();" style="width:100%;"><option selected="selected" value="0">'.JText::_('COM_GEOFACTORY_ALL').'</option>';// la constante JALL pourrait etre utilisée mais cela résuit la souplesse de customisation
			$m_selector		.='<select id="gf_multi_selector" onChange="'.$mapVar.'.SLFP();" style="width:100%;" multiple="multiple" size="'.count($idMs).'" >';
			$toggeler		.='<div id="gf_toggeler">'; 
			$toggeler_img	.= $toggeler ; 
			$selector_1		.='<select id="gf_list_selector"  onChange="'.$mapVar.'.SLFP();" style="width:100%;">';
			$m_selector_1	.='<select id="gf_multi_selector" onChange="'.$mapVar.'.SLFP();" style="width:100%;" multiple="multiple" size="'.count($idMs).'" >';
			$toggeler_1		.= $toggeler ; 
			$toggeler_img_1	.= $toggeler ; 

			// slectionne le premier
			$first = true ;
			$sel_1 = ' selected="selected" ' ;
			$chk_1 = ' checked="checked" ' ;

	 		foreach($idMs as $ms){
	 			$list = GeofactoryHelper::getMs($ms) ;
	 			if (!$list)
	 				continue ;

	 			// je surdéfinit l'état des chks, avec ce setting, qui est testé que si isset (pour etre compatible avec ancien MS)
				$sel = ' selected="selected" ' ;
				$chk = ' checked="checked" ' ;
	 			if (isset($list->checked_loading) AND ((int)$list->checked_loading<1)){
	 				$sel = '';
	 				$chk = '';
	 			}

	 			// styles ...
				$map->fullCss 	.= strlen($list->cssMs)>0?$list->cssMs:"" ;
	 			
				// image pour les cas nécessaires
				$img 			= GeofactoryHelper::_getSelectorImage($list) ;

				$selector		.= '<option value="'.$list->typeList.'_'.$list->id.'">'.$list->name.'</option>';
				$m_selector		.= '<option value="'.$list->typeList.'_'.$list->id.'" selected="selected">'.$list->name.'</option>';
				$toggeler		.= '<div id="gf_toggeler_item_'.$list->id.'" style="margin-right:10px;float:left" class="gf_toggeler_item"><input type="checkbox" '.$chk.'			 name="'.$list->typeList.'_'.$list->id.'" onChange="'.$mapVar.'.SLFP();">'.$list->name.'</div>';

				$toggeler_map 	.= '<div   class="gfMapControls" id="gf_toggeler_item_'.$list->id.'" style="height:28px!important;margin-top:3px!important;padding:2px!important;float:left;width:100%;"><input type="checkbox"  '.$chk.' style="margin:0px!important;" name="'.$list->typeList.'_'.$list->id.'" onChange=" switchPanel(\'gf_toggeler\', 0); '.$mapVar.'.SLFP();"> '.$list->name.'</div>'; 

				$toggeler_img	.= '<div class="gf_toggeler_item" style="margin-right:10px;float:left"><input type="checkbox" '.$chk.'			name="'.$list->typeList.'_'.$list->id.'" onChange="'.$mapVar.'.SLFP();"><img src="'.$img.'"> '.$list->name.'</div>';
				$toggeler_img_1	.= '<div class="gf_toggeler_item" style="margin-right:10px;float:left"><input type="checkbox" '.$chk_1.'        name="'.$list->typeList.'_'.$list->id.'" onChange="'.$mapVar.'.SLFP();"><img src="'.$img.'"> '.$list->name.'</div>';

// ajouté par kostas mais ne fonctionne pas partout // http://www.myjoom.com/code/index.php/tasksComments?tasks_id=43&projects_id=7
//				$toggeler_img	.= '<div class="gf_toggeler_item span2"><input type="checkbox" checked="checked" id="'.$list->typeList.'_'.$list->id.'" name="'.$list->typeList.'_'.$list->id.'" onChange="'.$mapVar.'.SLFP();"><label for="'.$list->typeList.'_'.$list->id.'"><img src="'.$img.'"></label><div id="toggle_desc_title"> '.$list->name.'</div></div>';				
//				$toggeler_img_1	.= '<div class="gf_toggeler_item span2"><input type="checkbox" '.$chk_1.' id="'.$list->typeList.'_'.$list->id.'" name="'.$list->typeList.'_'.$list->id.'" onChange="'.$mapVar.'.SLFP();"><label for="'.$list->typeList.'_'.$list->id.'"><img src="'.$img.'"></label><div id="toggle_desc_title"> '.$list->name.'</div></div>';

				$toggeler_1		.= '<div class="gf_toggeler_item" style="margin-right:10px;float:left"><input type="checkbox" '.$chk_1.'        name="'.$list->typeList.'_'.$list->id.'" onChange="'.$mapVar.'.SLFP();">'.$list->name.'</div>';
				$selector_1		.= '<option '.$sel_1.' value="'.$list->typeList.'_'.$list->id.'">'.$list->name.'</option>';	
				$m_selector_1	.= $selector_1 ;

				if ($first){
					$first = false ;
					$sel_1 = '' ;
					$chk_1 = '' ;
				}
			}
			$selector		.= '</select>';
			$m_selector		.= '</select>';
			$toggeler_img	.= '</div>';
			$toggeler		.= '</div>';
			$toggeler_1		.= '</div>';
			$toggeler_img_1	.= '</div>';
			$selector_1		.= '</select>';
			$m_selector_1	.= '</select>';
		}

		$dsp->trigger('beforeParseMapTemplate', array(&$template,$idMs, $map, $mapVar));

		// --- Radius form - si il n'y a pas le radius, je l'ajoute artificiellement
		$radius_form 		= $this->_getRadForm($map, $mapVar, $toggeler_map, count($idMs)) ;
		$radius_form_hide 	= '<div style="display:none;" >' .$radius_form.'</div>' ;
		if ((strpos($template, '{radius_form}')===false) AND (strpos($template, '{radius_form_hide}')===false))
			$template.= ($map->radFormMode==2)?'{radius_form}':'{radius_form_hide}' ;

		$template 	= str_replace('{locate_me}', 				$locate_me, 		$template);
		$template 	= str_replace('{near_me}', 					$near_me, 			$template);
		$template 	= str_replace('{save_me_full}', 			$save_me_full, 		$template);
		$template 	= str_replace('{save_me}', 					$save_me, 			$template);
		$template 	= str_replace('{reset_me}', 				$reset_me, 			$template);
		$template 	= str_replace('{reset_map}', 				$reset_map, 		$template);
		$template 	= str_replace('{radius_form}', 				$radius_form, 		$template);
		$template 	= str_replace('{radius_form_hide}', 		$radius_form_hide,	$template);
		$template 	= str_replace('{route}', 					$route, 			$template);
		$template 	= str_replace('{toggle_selector_icon_1}',	$toggeler_img_1,	$template);
		$template 	= str_replace('{toggle_selector_icon}',		$toggeler_img,		$template);
		$template 	= str_replace('{toggle_selector}',	 		$toggeler, 			$template);
		$template 	= str_replace('{multi_selector}', 			$m_selector, 		$template);
		$template 	= str_replace('{selector}', 				$selector, 			$template);
		$template 	= str_replace('{toggle_selector_1}', 		$toggeler_1, 		$template);
		$template 	= str_replace('{multi_selector_1}', 		$m_selector_1, 		$template);
		$template 	= str_replace('{selector_1}', 				$selector_1, 		$template);

		// --- debug
		$debug = "" ;
		if (GeofactoryHelper::isDebugMode()){
			$debug = '<ul id="gf_debugmode">';
			$debug.= '	<li>Debug mode ON</li>';
			$debug.= '	<li>Map variable name : "'.$mapVar.'"</li>';
			$debug.= '	<li>Data file : <a id="gf_debugmode_xml"></a></li>';
			$debug.= '	<li>Last bubble : <a id="gf_debugmode_bubble"></a></li>' ;
			$debug.= '</ul>';
		}

		// --- container general
		$template = '<div id="gf_template_container">'.$debug.$template.'</div>';

		// --- dynacat
		$template = $this->_replaceDynCat($template) ;

		$map->formatedTemplate = $template ;
	}

	protected function _cleanCss(&$map){
		// fixe le problème des templates responsive
		$css = ' #'.$map->mapInternalName.' img{max-width:none!important;}' ;

		// ajoute les éventuels styles perso
		$css = $css.$map->fullCss ;

		// clean...
		$css = str_replace(array("\t", "\r\n"), "", $css);
		$css = str_replace(array("    ", "   ", "  "), " ", $css);
		$css = str_replace("#", " #", $css);
//		$css = str_replace(".", " .", $css);
		$css = str_replace("{ ", "{", $css);
		$css = str_replace("} ", "}", $css);
		$css = str_replace(" {", "{", $css);
		$css = str_replace(" }", "}", $css);
		$map->fullCss = strlen(trim($css))>0?trim($css):"";
	}

	protected function _getRadiusDistances($map, $gf_mod_search){
		$ses 	= JFactory::getSession();
		$app 	= JFactory::getApplication('site');

		// récupère la valeur du champs de recherche du module qui n'est pas sur cette page et le met dans la session
		$gf_mod_radius = $app->input->getFloat('gf_mod_radius', $ses->get('mj_rs_ref_dist',0));

		if($gf_mod_search AND (strlen($gf_mod_search) > 0)){
			$ses->set('gf_ss_search_phrase',$gf_mod_search);
			$ses->set('gf_ss_search_radius',$gf_mod_radius);
		}

		// ajout de la liste des valeurs
		$listVal = explode(',', $map->frontDistSelect) ;
		$find = false ;
		$radForm = '<select id="radiusSelect" style="width:75px!important;" class="gfMapControls" onChange="'.$map->mapInternalName.'.SLFI();">';
		foreach($listVal as $val){
			$sel = ($val==$gf_mod_radius)?' selected="selected" ':'';
			if ($sel!='')
				$find = true ;
			$radForm.= '<option value="'.$val.'" '.$sel.'>'.$val.'</option>';
		}
		
		// si il entre une valeur custom par le module (ou l'url), on l'ajoute
		if (!$find AND is_numeric($gf_mod_radius) AND ($gf_mod_radius>0))
			$radForm.= '<option value="'.$gf_mod_radius.'" selected="selected">'.$gf_mod_radius.'</option>';

		$radForm.='</select>';

		return $radForm ;
	}

	protected function _replaceDynCat($text) {
		$dyncat = array() ;
		$regex = '/{dyncat\s+(.*?)}/i';
		if ( JString::strpos($text, "{dyncat ") === false ) 
			return $text ;

		preg_match_all( $regex , $text, $matches );
	 	$count = count( $matches[0] );

		if ( $count < 1) 
			return $text ;
		
		for ($i=0; $i < $count; $i++ ){
			$code 	= str_replace("{dyncat ", '', $matches[0][$i] ); // $matches[0][$i] => [dyncat_xxxxxxx] => [xxxxxxx]
			$code 	= str_replace("}", '', $code );
			$code 	= trim( $code );	// => MS_MT#88
			$vCode 	= explode('#', $code) ;
		
			if ((count($vCode) < 1) OR (strlen($vCode[1])<1))
				continue ;

			// remplace mon code
			$loading 	= JHTML::_('select.genericlist', array(JHTML::_('select.option', '', "...loading...")), "", 'class="gf_dyncat_sel" size="1"', 'value', 'text');
			$div 		= '<span id="gf_dyncat_'.$vCode[0].'_'.$vCode[1].'">'.$loading.'</span>' ;
			$text 		= preg_replace('{'.$matches[0][$i].'}', $div, $text );
		}

		return $text ;
	}

	protected function _getRadForm($map, $mapVar, $toggeler_map, $nbrMs) {
		$app 	= JFactory::getApplication('site');

		// récupère la valeur du champs de recherche du module qui n'est pas sur cette page et le met dans la session
		$gf_mod_search	= $app->input->getString('gf_mod_search', null);
		$form_start		= '<form id="gf_radius_form" onsubmit="'.$mapVar.'.SLFI();return false;" class="form-inline">' ;
		$input 			= '<div class="form-group"><input type="text" id="addressInput" value="'.$gf_mod_search.'" class="gfMapControls form-control" placeholder=""/></div>' ;
		$dists 			= $this->_getRadiusDistances($map, $gf_mod_search) ; 
		//$search_btn		= '<input type="button" onclick="'.$mapVar.'.SLFI();" id="gf_search_rad_btn" value="'.JText::_('COM_GEOFACTORY_SEARCH').'"/>';
		$search_btn		= '<button type="button" class="btn" onclick="'.$mapVar.'.SLFI();" id="gf_search_rad_btn"><i class="fa fa-search"></i>SØG NU</button>';
		$divsOnMapGo	= '<div id="gf_map_panel" style="padding-top:5px;display:none;" ><div id="gf_radius_form" style="margin:0;padding:0;">' ;
		$radius_form 	= '' ;

		if ($map->radFormMode==0){// default
			$radius_form.= 	$form_start ;
			//$radius_form.= 		'<label for="addressInput">'.JText::_('COM_GEOFACTORY_ADDRESS') .'</label>';
			$radius_form.= 		$input ;
			//$radius_form.= 		'<label for="radiusSelect">'.JText::_('COM_GEOFACTORY_RADIUS').'</label>';
			$radius_form.=		$search_btn;
			$radius_form.= 		$dists ;
			$radius_form.=	'</form>';
		}
		if ($map->radFormMode==1){//snipet
			//					default="Search from [input_center][distance_sel][search_btn]"
			$radius_form.= 	$form_start ;
			$radius_form.= 		$map->radFormSnipet ;
			$radius_form = 		str_replace('[input_center]', 	$input, 		$radius_form);
			$radius_form = 		str_replace('[distance_sel]', 	$dists, 		$radius_form);
			$radius_form = 		str_replace('[search_btn]', 	$search_btn, 	$radius_form);
			$radius_form.=	'</form>';
		}

		// pas de liste mais mode template auto ?
		if ($map->radFormMode==3 && $nbrMs && $nbrMs>0)
			$map->radFormMode=2 ;

		if ($map->radFormMode==3){
			$radius_form.= $divsOnMapGo ;
			$radius_form.= 		$input;
			$radius_form.= 		$dists; 
			$radius_form.= 		'<input type="button" class="gfMapControls" onclick="'.$mapVar.'.SLRES();" id="gf_reset_rad_btn" value="'.JText::_('COM_GEOFACTORY_RESET_MAP').'"/>';
			$radius_form.= 		' <input type="button" class="gfMapControls" value="Filters" onclick="switchPanel(\'gf_toggeler\', 0);" style="" />';
			$radius_form.= 	'</div>';
			$radius_form.=	'<div id="gf_toggeler" style=" display:none;">'; 
			$radius_form.= 		$toggeler_map ;
			$radius_form.= 	'</div>';
			$radius_form.= '</div>';
		}	

		if ($map->radFormMode==2){	// on map
			$radius_form.= $divsOnMapGo ;
			$radius_form.= 		$input;
			$radius_form.= 		$dists; 


			$radius_form.=		'<input type="button" onclick="'.$mapVar.'.SLFI();" 	id="gf_search_rad_btn" 	value="" style="display:none" />';
			$radius_form.= 		'<input type="button" onclick="'.$mapVar.'.SLRES();" 	id="gf_reset_rad_btn" 	value="" style="display:none" />';


			$radius_form.= 	'</div>';
			$radius_form.= '</div>';
		}	

		return $radius_form ;
	}	

	protected function _getRouteDiv(){
		$route = '<div  id="gf_routecontainer">' ;
		$route.= 	'<select id="gf_transport">' ;
		$route.= 		'<option value="DRIVING">'.JText::_('COM_GEOFACTORY_DRIVING').'</option>' ;
		$route.= 		'<option value="WALKING">'.JText::_('COM_GEOFACTORY_WALKING').'</option>' ;
		$route.= 		'<option value="BICYCLING">'.JText::_('COM_GEOFACTORY_BICYCLING').'</option>' ; // work only on usa http://groups.google.com/group/google-maps-js-api-v3/browse_thread/thread/2f5e553c32eca3ad
		$route.= 	'</select>' ;
		//$route.=' <input type="button" onclick="'.$mapVar.'.RESRTE();" 		value="'.JText::_('COM_GEOFACTORY_RESET_ME').'"/>';
		$route.= 	'<div id="gf_routepanel">' ;
		$route.= 	'</div>' ;
		$route.= '</div>' ;
		
		return $route ;
	}
}
