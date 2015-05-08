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

class GeofactoryViewMap extends JViewLegacy{
	protected $item;
	protected $params;
	protected $state;
	protected $user;

	// fonction pouvant etre appelée par le module ...
	public function initView($map)	{
		$app			= JFactory::getApplication();
		$user			= JFactory::getUser();
		$userId			= $user->get('id');
		$dispatcher		= JEventDispatcher::getInstance();

		$this->item 	= $map; 
		$this->state 	= $this->get('State');
		$this->user  	= $user;
		$this->params 	= $app->getParams();

		// utile pour le module ...
		if (!isset($this->document) OR !$this->document)
			$this->document = JFactory::getDocument(); ;

	}

	public function display($tpl = null){
		$this->initView($this->get('Item'));

		// Check for errors.
		if (count($errors = $this->get('Errors'))){
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		// Create a shortcut for $item.
		$item = $this->item;
		$item->tagLayout      = new JLayoutFile('joomla.content.tags');

		// Add router helpers.
		$item->slug			= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
	//	$item->catslug		= $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
	//	$item->parent_slug = $item->parent_alias ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;

		// No link for ROOT category
	//	if ($item->parent_alias == 'root'){
	//		$item->parent_slug = null;
	//	}
	
		$item->tags = new JHelperTags;
		$item->tags->getItemTags('com_geofactory.map', $this->item->id);

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		$this->_prepareDocument();

		parent::display($tpl);
	}

	public function _prepareDocument(){
		$session= JFactory::getSession();
		$config	= JComponentHelper::getParams('com_geofactory');
		$root 	= JURI::root() ;

		// parametres de l'url à charger
		$urlParams = array() ;
		$urlParams[] = 'idmap='.$this->item->id ;
		$urlParams[] = 'mn='.$this->item->mapInternalName ;
		$urlParams[] = 'zf='.$this->item->forceZoom ;
		$urlParams[] = 'gfcc='.$this->item->gf_curCat;
		$urlParams[] = 'zmid='.$this->item->gf_zoomMeId;
		$urlParams[] = 'tmty='.$this->item->gf_zoomMeType;
		$urlParams[] = 'code='.rand(1,100000) ;

		$dataMap = implode('&',$urlParams) ;

		// chargement des divers biblios
		$jqMode 		= $config->get('jqMode') ;
		$jqVersion 		= strlen($config->get('jqVersion'))>0?$config->get('jqVersion'):'2.0';
		$jqUiversion	= strlen($config->get('jqUiversion'))>0?$config->get('jqUiversion'):'1.10';
		$jqUiTheme 		= strlen($config->get('jqUiTheme'))>0?$config->get('jqUiTheme'):'none';

		// site ssl ?
		$http = (isset($_SERVER['HTTPS']) AND ($_SERVER['HTTPS']))?"https://":"http://" ;
		
		// avec les tabs
		$jqui = $this->item->useTabs?true:false ; // c'est un peu con comme test ca....
		if ($this->item->useTabs AND ($jqMode==0 OR $jqMode==2))// pas ui... 
			$jqMode = 1 ;

		// en fonction du mode
		switch($jqMode){
			case 0 :	//COM_GEOFACTORY_JQ_MODE_NONE
				break;
			case 2 : 	//COM_GEOFACTORY_JQ_MODE_JOOMLA
				JHtml::_('jquery.framework');
				if ($jqui)
					JHtml::_('jquery.ui');
				break;
			case 3 : 	//COM_GEOFACTORY_JQ_MODE_CDN_GOOGLE
				$this->document->addScript(			$http.'ajax.googleapis.com/ajax/libs/jquery/'.$jqVersion.'/jquery.min.js') ;
				if ($jqui){
					$this->document->addScript(		$http.'ajax.googleapis.com/ajax/libs/jqueryui/'.$jqUiversion.'/jquery-ui.min.js' ); 
					$this->document->addStyleSheet(	$http.'ajax.googleapis.com/ajax/libs/jqueryui/'.$jqUiversion.'/themes/'.$jqUiTheme.'/jquery-ui.css' );
				}
				break;
			case 4 : 	//COM_GEOFACTORY_JQ_MODE_CDN_JQUERY
				$this->document->addScript(			$http.'code.jquery.com/jquery-'.$jqVersion.'.min.js') ;
				if ($jqui){
					$this->document->addScript(		$http.'code.jquery.com/ui/'.$jqUiversion.'/jquery-ui.min.js' ); 
					$this->document->addStyleSheet(	$http.'code.jquery.com/ui/'.$jqUiversion.'/themes/'.$jqUiTheme.'/jquery-ui.css' );
				}
				break;
			default :
			case 1 :	//COM_GEOFACTORY_JQ_MODE_LOCAL
				$this->document->addScript(			$root.'components/com_geofactory/assets/js/jquery/'.$jqVersion.'/jquery.min.js') ;
				if ($jqui){
					$this->document->addScript(		$root.'components/com_geofactory/assets/js/jqueryui/'.$jqUiversion.'/jquery-ui.min.js'); 
					$this->document->addStyleSheet(	$root.'components/com_geofactory/assets/js/jqueryui/'.$jqUiversion.'/themes/_name_/jquery-ui.css');
				}
				break;
		}

		// charge le fichier JS principal 
		// en créer une instance
		$jsVarName = $this->item->mapInternalName ;
		$js = array() ;
		$js[] = "var {$jsVarName}=new clsGfMap();" ;
		$js[] = " var gf_sr = '{$root}';" ;
		$js[] = "function init_{$jsVarName}(){sleepMulti(repos);" ;
		$js[] = 'jQuery.getJSON('.$jsVarName.'.getMapUrl("'.$dataMap.'"),function(data){' ;
			$js[] = "if (!{$jsVarName}.checkMapData(data)){document.getElementById('{$jsVarName}').innerHTML = 'Map error.'; console.log('Bad map format given in init_{$jsVarName}().'); return ;} " ;
			$js[] = " {$jsVarName}.setMapInfo(data, '{$jsVarName}');" ;

			// le code source qui apparait sur la page source
			$this->_loadDynCatsFromTmpl($jsVarName, $js) ;
			$this->_setKml($jsVarName, $js);
			$this->_loadDynCatsFromTmpl($jsVarName, $js);
			$this->_setLayers($jsVarName, $js);
			$this->_getSourceUrl($jsVarName, $js, $root);
			$this->_loadTiles($jsVarName, $js);

			// charge les marqueurs, sur toute la carte ou uniquement sur la partie concernée (recherche rayon depuis externe ... module...)
			$gf_ss_search_phrase = $session->get('gf_ss_search_phrase', null);
			if($gf_ss_search_phrase AND (strlen($gf_ss_search_phrase) > 0)){
				$js[] = "{$jsVarName}.searchLocationsFromInput();" ;
				$session->clear('gf_ss_search_phrase');
			}else if ($this->item->useBrowserRadLoad==1){
				$js[] = "{$jsVarName}.getBrowserPos(false, true);" ;
			}else{
				$js[] = "{$jsVarName}.searchLocationsFromPoint(null);" ;
			}

			$js[] = " });" ; // getJSON

		$js[] = "}" ; //initGfCarte

		// charge la carte une fois le DOM chargé
		$js[] = "google.maps.event.addDomListener(window, 'load', init_{$jsVarName});";		

		$sep = " " ;
		if (GeofactoryHelper::isDebugMode())
			$sep = "\n" ;

		// met la bonne variable
		$js = implode($sep, $js) ;

		// apikey ?
		$ggApikey =  strlen( $config->get('ggApikey') ) > 3 ? "&key=".$config->get('ggApikey') : "" ;

		// layers utilisés ?
		$arLayers = array() ;
		if (is_array($this->item->layers)){
			foreach($this->item->layers as $tmp){
				if (intval($tmp) > 0) 
					$arLayers[] = $tmp ;
			}
		}
		$lib = ((count($arLayers) > 0) AND (in_array(4, $arLayers) OR in_array(5, $arLayers) OR in_array(6, $arLayers))) ? ",weather" : "" ;
		
		// si utilisé pour boutons layers ou par radius on map 
		if (count($arLayers) > 0 || $this->item->radFormMode>1)
			$this->document->addStyleSheet('components/com_geofactory/assets/css/geofactory-maps_btn.css' );

		// ajoute les divers styles (après réflexion, peut-etre pas judicieux de les cahrger en AX comme le script, car doit etre déclaré avec dessin de la page ...)
		$this->document->addStyleDeclaration($this->item->fullCss);

		// map language
		$mapLang = (strlen($config->get('mapLang'))>1)?'&language='.$config->get('mapLang'):'' ;

		// map api
		$this->document->addScript($http.'maps.googleapis.com/maps/api/js?sensor=true'.$ggApikey.$mapLang.'&libraries=places'.$lib);

		//http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclustererplus/src/
		if ($this->item->useCluster)
			$this->document->addScript($root.'components/com_geofactory/assets/js/markerclusterer-5150212.js');
	
		// si il y a un custom js file il est installé sous forme de custom.js.php avec des commentaires.
		if (file_exists(JPATH_BASE.'/components/com_geofactory/assets/js/custom.js'))
			$this->document->addScript($root.'components/com_geofactory/assets/js/custom.js');

		$this->document->addScript($root.'components/com_geofactory/assets/js/map_api-5150215.js');
		$this->document->addScriptDeclaration($js);

		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$pathway = $app->getPathway();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu){
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
	}

	// recherche les divers placeholders dans le template de carte
	// dans le template un dyncat est mis par MS avec l'id du parent [dyncat_MT#88]
	protected function _loadDynCatsFromTmpl($jsVarName, &$js) {
		$regex = '/{dyncat\s+(.*?)}/i';
		$text = $this->item->template ;
		if ( JString::strpos($text, "{dyncat ") === false ) 
			return ;

		// find all instances of plugin and put in $matches
		preg_match_all( $regex , $text, $matches );
	 	$count = count( $matches[0] );

		if ( $count < 1) 
			return ;

		for ($i=0; $i < $count; $i++ ){
			$code = str_replace("{dyncat ", '', $matches[0][$i] ); // $matches[0][$i] => [dyncat_xxxxxxx] => [xxxxxxx]
			$code = str_replace("}", '', $code );
			$code = trim( $code );	// => MT#88
 
			// on doit avoir "sps:lat:lng:rad:opt_text"
			$vCode = explode('#', $code) ;
			if ((count($vCode) < 1) OR (strlen($vCode[1])<1))
				continue ;

			$ext = $vCode[0];
			$idP = $vCode[1] ;

			$js[] = "{$jsVarName}.loadDynCat('{$ext}', {$idP}, 'gf_dyncat_{$ext}_{$idP}', '{$jsVarName}'); " ;
		}
	}

	protected function _setKml($jsVarName, &$js){
		$vKml = explode(';',$this->item->kml_file);
		if (count($vKml)<1)
			return;

		foreach($vKml as $kml){
			$kml = trim($kml);
			if (strlen($kml)<3)
				continue ;

			$js[] = $jsVarName.".addKmlLayer('{$kml}');";
		}
	}

	// http://tile.openstreetmap.org/#Z#/#X#/#X#.png|Osm|18|Free map|true|256 ; 
	protected function _loadTiles($jsVarName, &$js){
		$map = $this->item ;
	
		$vTypes = array();
			
		// types de base, controle qu'il les inclue dans la liste ou non
		$vAvailableTypes = isset($map->mapTypeAvailable)?$map->mapTypeAvailable:null ;
		$ref = array("SATELLITE","HYBRID","TERRAIN","ROADMAP");

		// aucun type ? alors on prends ceux de base
		if (!is_array($vAvailableTypes) && (count($vAvailableTypes)==0))
			$vAvailableTypes = $ref;

		foreach($vAvailableTypes as $baseType){
			// c'est un type de base (les autres sont traités plus bas)
			if (in_array($baseType,$ref))
				$vTypes[] = "google.maps.MapTypeId.{$baseType}" ;
		}

		$listTileTmp = explode(";", $map->tiles) ;
		$listTile = array() ;

		if (is_array($listTileTmp) && count($listTileTmp)>0){
			foreach($listTileTmp as $ltmp){
				if (strlen(trim($ltmp))<3)
					continue ;
				$listTile[] = $ltmp ;
			}
		}

		// recherche si il en a des customs dans les parametres
		if (is_array($listTile) && (count($listTile)>0)){
			$idx = 0 ;
			foreach($listTile as $tile){
				$tile 	= explode('|', $tile) ;			
				$url 	= (count($tile)>0)?trim($tile[0]):"" ;
				$name 	= (count($tile)>1)?trim($tile[1]):"Name ?" ;
				$maxZ	= (count($tile)>2)?trim($tile[2]):"18" ;
				$alt 	= (count($tile)>3)?trim($tile[3]):"" ;
				$isPng	= (count($tile)>4)?trim($tile[4]):"true" ;
				$size	= (count($tile)>5)?trim($tile[5]):"256" ;
				
				// si pas dans la liste, suivant !
				if (! in_array($name, $vAvailableTypes))
					continue ;

				if (strlen($url)<1)
					continue ;

				$idx++ ;
				$varName 	= "tile_{$idx}" ;
				$vTypes[] 	= "'{$name}'" ;

				// si a une url bing, alors on me la bonne
				$bing = false ;
				$jarry = false ;
				if ($url=="http://bing.com/aerial") 	{$url = "http://ecn.t3.tiles.virtualearth.net/tiles/a";$bing=true;}
				if ($url=="http://bing.com/label") 		{$url = "http://ecn.t3.tiles.virtualearth.net/tiles/h";$bing=true;}
				if ($url=="http://bing.com/road") 		{$url = "http://ecn.t3.tiles.virtualearth.net/tiles/r";$bing=true;}
				if ($url=="http://jarrypro.com") 		{$jarry = true;}

				// contruit le layer
				// -> 'http://tile.openstreetmap.org/#Z#/#X#/#Y#.png'
				// -> 'http://tile.openstreetmap.org/' + z + '/' + X + '/' + ll.y + '.png' 
				$url		= str_replace("#X#", "' + X + '", 	$url);
				$url		= str_replace("#Y#", "' + ll.y + '",	$url);
				$url		= str_replace("#Z#", "' + z + '", 	$url);

				$js[] 					= "var otTile 	= new clsTile('{$name}', {$size}, {$isPng}, {$maxZ}, '{$alt}' );" ;
				if ($bing)		$js[] 	= "otTile.fct = function(ll, z){ return otTile.getBingUrl('{$url}', ll,z);} ; " ;
				elseif ($jarry)	$js[]	= "otTile.fct = function(ll, z){ var ymax = 1 << z;var y = ymax - ll.y -1; return 'http://jarrypro.com/images/gmap_tiles/'+z+'/'+ll.x+'/'+y+'.jpg';}; " ;
				else			$js[]	= "otTile.fct = function(ll, z){ var X = ll.x % (1 << z); return '{$url}' ;} ;" ;
				$js[] 					= "otTile.createTile({$jsVarName}.map) ; " ;
			}
		}

		if (count($vTypes)>0){
			$js[] = "var optionsUpdate={mapTypeControlOptions: {mapTypeIds: [".implode(',',$vTypes)."],style: google.maps.MapTypeControlStyle.{$map->mapTypeBar}}};{$jsVarName}.map.setOptions(optionsUpdate);";
			if (($map->mapTypeOnStart == "ROADMAP") OR ($map->mapTypeOnStart == "SATELLITE") OR ($map->mapTypeOnStart == "HYBRID") OR ($map->mapTypeOnStart == "TERRAIN"))
				$js[] = $jsVarName.".map.setMapTypeId(google.maps.MapTypeId.{$map->mapTypeOnStart});	 ";
			else
				$js[] = $jsVarName.".map.setMapTypeId('{$map->mapTypeOnStart}'); ";
		}
		
		return ;
	}

	protected function _setLayers($oMap, &$js){
		// ajout du bouton pour les couches
		$arLayersTmp = $this->item->layers;

		if (!is_array($arLayersTmp) OR !count($arLayersTmp))
			return ;

		$arLayers = array() ;
		foreach($arLayersTmp as $tmp){
			if (intval($tmp) > 0) 
				$arLayers[] = $tmp ;
		}

		// construction du bouton layers
		if (is_array($arLayers) && count($arLayers) > 0){
			$txt 	= array(JText::_('COM_GEOFACTORY_TRAFFIC'),
							JText::_('COM_GEOFACTORY_TRANSIT'),
							JText::_('COM_GEOFACTORY_BICYCLE'),
							JText::_('COM_GEOFACTORY_WEATHER'),
							JText::_('COM_GEOFACTORY_CLOUDS' ),
							JText::_('COM_GEOFACTORY_HIDE_ALL'), 
							JText::_('COM_GEOFACTORY_MORE_BTN'), 
							JText::_('COM_GEOFACTORY_MORE_BTN_HLP'));
			$js[] 	= '	var layb = [] ;	var sep = new separator();' ;

			if (in_array(1, $arLayers))	$js[] = ' layb.push( new checkBox({gmap: '.$oMap.'.map, title: "'.$txt[0].'" , id: "traffic", 	label: "'.$txt[0].'" }) ); ';
			if (in_array(2, $arLayers))	$js[] = ' layb.push( new checkBox({gmap: '.$oMap.'.map, title: "'.$txt[1].'" , id: "transit", 	label: "'.$txt[1].'" }) ); ';
			if (in_array(3, $arLayers))	$js[] = ' layb.push( new checkBox({gmap: '.$oMap.'.map, title: "'.$txt[2].'" , id: "biking", 	label: "'.$txt[2].'" }) ); ';
			if (in_array(4, $arLayers))	$js[] = ' layb.push( new checkBox({gmap: '.$oMap.'.map, title: "'.$txt[3].'" , id: "weatherF",	label: "'.$txt[3].'" }) ); ';
			if (in_array(5, $arLayers))	$js[] = ' layb.push( new checkBox({gmap: '.$oMap.'.map, title: "'.$txt[3].'" , id: "weatherC",	label: "'.$txt[3].'" }) ); ';
			if (in_array(6, $arLayers))	$js[] = ' layb.push( new checkBox({gmap: '.$oMap.'.map, title: "'.$txt[4].'" , id: "cloud", 	label: "'.$txt[4].'" }) ); ';

			$js[] = ' layb.push( sep );' ;
			$js[] = 'layb.push( new optionDiv({ gmap: '.$oMap.'.map, name: "'.$txt[5].'",title: "'.$txt[5].'", id: "mapOpt"}) );' ;
			$js[] = 'var ddDivOptions = {items: layb ,id: "myddOptsDiv"};' ;
			$js[] = 'var dropDownDiv = new dropDownOptionsDiv(ddDivOptions);' ;
			$js[] = 'var dropDownOptions = {gmap: '.$oMap.'.map, name: "'.$txt[6].'", id: "ddControl", title: "'.$txt[7].'", position: google.maps.ControlPosition.TOP_RIGHT, dropDown: dropDownDiv};' ;
			$js[] = 'var dropDown1 = new dropDownControl(dropDownOptions);' ;
		}
	}

 	// Génère l'url du fichier source
	protected function _getSourceUrl($oMap, &$js, $root){
		$config		= JComponentHelper::getParams('com_geofactory');

		// carte en cours
		$idmap 		= $this->item->id ;
		
		// 3.0.65 sinon il inclu pas bien le fichier dans certains cas a cause du SEF -> JURI::root()."index{$indexFile}.php?opt
		//	 -> comme ici ca ne marchait pas : https://www.aamet.org/search/location-map.html
		//   -> mais chez lui ca ne fonctionne pas car si il appelle le site en http://www.site.com JURI::root() retourne http://site.com, ce qui génère ceci http://stackoverflow.com/questions/10143093/origin-is-not-allowed-by-access-control-allow-origin (https://mail.google.com/mail/ca/u/0/?shva=1#inbox/13c7d62b691e9d3f)
		//     -> en passant JURI::root(true)."/index{$indexFile}.php?op
		//   -> donc le JURI::root(true) fonctione partout a priori ...
		$app		= JFactory::getApplication();
		$itemid 	= $app->input->get('Itemid', 0, 'int');
		$lang 		= $app->input->getString('lang');
		if (strlen($lang)>1)	$lang="&lang={$lang}"; else $lang = '' ;
		$paramsUrl	= "&gfcc={$this->item->gf_curCat}&zmid={$this->item->gf_zoomMeId}&tmty={$this->item->gf_zoomMeType}&code=".rand(1,100000).$lang;
	
		// cache: si cache et si cache expiré, crée le nouveau fichier
		$useCache = 0 ;
		if ($this->item->cacheTime > 0){
			$cache_file_serverpath = GeofactoryHelper::getCacheFileName($idmap, $itemid, 1) ;
			$cache_file = GeofactoryHelper::getCacheFileName($idmap, $itemid, 0) ;
			$filemtime	= @filemtime($cache_file_serverpath);  // returns FALSE if file does not exist
			if (!$filemtime or (time()-$filemtime >= $this->item->cacheTime))	$useCache = 0 ;
			else 																$useCache = 1 ;
		}

		// si debug on passe le conteneur du msg debug
		$debugCont = (GeofactoryHelper::isDebugMode())?"gf_debugmode_xml":"null" ;
		$js[] = "{$oMap}.nameDebugCont='{$debugCont}';";
		$js[] = "{$oMap}.setXmlFile('{$paramsUrl}',{$useCache},{$idmap},{$itemid});";
	}
}
