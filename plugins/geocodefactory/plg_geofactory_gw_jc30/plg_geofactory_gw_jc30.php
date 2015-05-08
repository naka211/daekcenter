<?php
/**
 * @name		Geocode Factory
 * @package		geoFactory
 * @copyright	Copyright © 2013 - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cédric Pelloquin <info@myJoom.com>
 * @website		www.myJoom.com
 *
 * Geocode Factory gateway plugin for Joomla contents (articles)
 * 
 * This plugin is designed to link Geocode Factory with a particular third party component
 * Any developer is free to develop his own plugin, sending a copy to myjoom will be recommended
 * In the comments when you read "third party component" this describe the component for witch the
 * plugin is designed 
 *
 * The first thing to do is define all members values with default values according your third 
 * party component
 *
 *	Notes : -	Comments starting with '-->' or into the functions are specifically write for the 
 *				current third party component.   
 *			-	Typing convention : TPC = third party component = the component for witch this this
 *   			plugin is write to work with Geocode Factory
 *			-	The function with the 'COMMON' tag dont need to be modified by you, they are common
 *				for all TPC
 *			- 	All comment in french are internal 
 *
 */

// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
jimport('joomla.plugin.plugin');
require_once JPATH_SITE.'/components/com_geofactory/helpers/geofactoryPlugin.php';

// article context
$com_path = JPATH_SITE.'/components/com_content/';
require_once $com_path.'router.php';
require_once $com_path.'helpers/route.php';
JModelLegacy::addIncludePath($com_path . '/models', 'ContentModel');

// uncomment this for debugging
//ini_set("display_errors","on");	error_reporting(E_ALL);

/**
 * Plugin main class
 *
 * plg_geofactory_gw_jc30 = Gecoode Factory Gateway for Joomla Content (articles)
 *
 * @package     geoFactory
 */
class plggeocodefactoryPlg_geofactory_gw_jc30 extends GeofactoryPluginHelper{
	// members - identify the third party component
	protected $gatewayName		= "Joomla Content 3.0" ;	// string - human readable name of the gateway purpose, add a version here when the backware compatiblity is no more applicable
	protected $gatewayCode		= "MS_JC" ; 				// string - this code always starting with MS_ (markerset), and with a 2 to 4 digits that identify the third party component 
	protected $gatewayOption	= "com_content";			// string - value of the joomla url variable "option" of the TPC (option=com_content...)

	// member - information about your TPC
	protected $isCategorised	= true ;				// bool - you component contains categories (at least id cat, id parent, name)
	protected $isProfileCom		= false ;				// bool - is your component a profile manager component (like CB or Jomsocial) ?
	protected $isSupportAvatar	= false ;				// bool - support images for entries or avatar for profiles ?
	protected $isSupportCatIcon	= false ;				// bool - support images for category ? 
	protected $isSingleGpsField	= false ;				// bool - did your component have a latitude and a longitude field (false), or a "Geo field" field that contains both (true) ?
	protected $defColorPline	= "red" ;				// string default color for the lines, the user can change this in plugin settings
	
	// members - define if  your product use/allows custom coordinates fields are used : false is they are fixed in code/database
	protected $custom_latitude	= false ; 				// bool
	protected $custom_longitude	= false ; 				// bool

	// members - define if your product allows custom address fields 
	protected $custom_street	= false ; 				// bool
	protected $custom_postal	= false ; 				// bool
	protected $custom_city		= false ; 				// bool
	protected $custom_county	= false ; 				// bool
	protected $custom_state		= false ; 				// bool
	protected $custom_country	= false ; 				// bool

	// members - default values for empty coordinates, should be null or 255 or ... 
	protected $defEmptyLat		= 255 ;					// custom
	protected $defEmptyLng		= 255 ;					// custom

	// member - internal variable
	protected $vGatewayInfo		= array() ;


	// member only for this plugin variable
	protected $arBubbleFields 	= array();
	protected $plgTable			= '#__geofactory_contents' ;
	protected $iTopCategory		= 1 ; 					// fixed root cat
	
	/**
	 * Constructor
	 *
	 * @param   object	$subject The object to observe
	 * @param   array  $config  An array that holds the plugin configuration
	 * @since   1.0
	 */
	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		$this->vGatewayInfo[] = array($this->gatewayCode, $this->gatewayName) ;
		$this->arBubbleFields = array(	"introtext",	"introtextraw",		"fulltext",		"fulltextraw",
										"catid",		"category_title",	"created_by",	"modified_by",
										"metakey",		"metadesc",			"hits", 		"author", 
										'image_intro', 	'image_fulltext') ;
	}

	/**
	  * Define the list of Geocode Factory's features that are supported by your component
	  *
	  * Use to automatically build the backend markerset form
	  * if you are not sure, please ask Cédric or Rick info@myjoom.com
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 
	  * @return array
	  */
	public function getListFieldsMs($type){
		if (!$this->_isInCurrentType($type))
			return array($this->gatewayCode, array()) ;
	
		// comment the unsupported features (! the comas !)
		$listFields = array(
		//	"avatarImage", 			// avatar image field selectior 
		//	"section", 				// sections (typcal for sobipro)
		//	"field_title",			// select the field that is the title of entry/profile
		//	"salesRadField",		// support of sales are radius field
			"include_categories",	// category selector where to take the entries
			"childCats",			// include child categories
		//	"include_groups",		// user group selector where to take the profiles
		//	'linesFriends',				// friends are supported 
		//	'linesMyAddr',				// myaddresses are supported 
			'linesOwners',				// Owners are supported 
		//	'linesGuests',				// guests are supported 
			"catAuto",				// for category component : show only entries from the current cat when published as module
		//	"filter_opt",			// support advanced filters in queries ?
			"filter",				// support filters in queries ?
			"onlyPublished"			// state of the entries to load
		);
		return array($type, $listFields) ;
	}

	/**
	  * Get the list of custom fields from your application that can be used by Geocode Factory
	  *
	  * The list of fields is typicaly used in backend for selecting the filters, sales radius,
	  * city, zip, ... 
	  *
	  * @since 1.0
	  *
	  * @param string 	$typeList 
	  * @param object 	$ar 		will contains the database result (SELECT xxx as text, yyy as value ), value is typicaly the id of the field
	  * @param bool 	$all 		true = include all fields (at user risks), false only basic fields, like text inputs
	  */
	public function getCustomFields($typeList, &$ar, $all){
		if (!$this->_isInCurrentType($typeList))
			return ;

		$obj = new stdClass();
		$obj->text = "Default" ;
		$obj->value = 0 ;

		$ar=array($obj);
	}
	
	/**
	  * Query that list all published entries/profiles
	  * You need to return id, name, latitude, longitude
	  * And the function add 2 more internal query fields type_ms, c_status
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 			represent the latitude query field
	  * @param array 	$filters 		array of string from user filtering
	  * @param object 	$papa 			parent
	  * @return array 	
	  */
	public function getListQueryBackGeocode($type, $filters, $papa, $vAssign){
		// !!! on travaille avec le id de la table... pas celui de l'article !!!!
		if (!$this->_isInCurrentType($type))
			return array($this->gatewayCode, null) ;

		// Create a new query object.
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		$latSql	= 'a.latitude' ;
		$lngSql	= 'a.longitude' ;
		$test 	= $this->_getValidCoordTest($latSql, $lngSql);

		// Select the required fields from the table.
		$query->select(
			$papa->getState(
				'list.select',
				'a.id 		AS item_id,'.
				'j.title 	AS item_name,'.
				$latSql.' 	AS item_latitude,'.
				$lngSql.' 	AS item_longitude,'.
				$db->Quote($type) . ' AS type_ms,'.
				'IF('.$test.',1,0) AS c_status'
			)
		);

		$query->from($db->quoteName($this->plgTable).' AS a');
		$query->join('LEFT', '#__content AS j ON j.id=a.id_content');
		$query = $this->_finaliseGetListQueryBackGeocode($query, $filters);

		return array($type, $query) ;
	}

	/**
	  * needed because some TPC can have multiples directories, with each own settings
	  *
	  * build the vGatewayInfo member with all sub-directories. This array is visisible in backend and 
	  * need to be formated as an array of array($this->gatewayCode."-".$id, $this->gatewayName." - ".$name) ; 
	  * where id is the directory id, and name the directory name.
	  * For TPC that dont support multidirectories, leave the function blank : protected function _mergeInternalDirectories(){}
	  *
	  * @since 1.0
	  *
	  * @return array 	array of each sub directories formatted as described above
	  */
	protected function _mergeInternalDirectories(){
	}

	/**
	  * save the coordinate to an entry or profile
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 		internal use
	  * @param int 		$id 		id of the entry / profile that will receive coordinates, tested as int > 0
	  * @param array 	$vCoord 	array with the coordinates array(lat, long) tested as array and count > 1
	  * @param array 	$vAssign 	associative array for the existing using fields (field_city=>id_field_city, field_zip=>0, field_street=>id_field_street, ...)
	  * @return array 	Error or success message.
	  */
	public function setItemCoordinates($type, $id, $vCoord, $vAssign){
		// !!! on travaille avec le id de la table... pas celui de l'article !!!!
		if (!$this->_isInCurrentType($type))
			return array($this->gatewayCode) ; // retourne un seul élément, le résultat attend un vecteur de 2

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$fields = array("latitude=".$vCoord[0], "longitude=".$vCoord[1]);
		$query->update($db->quoteName($this->plgTable))->set($fields)->where("id=".$id);
		$db->setQuery($query);

		try {					$result = $db->execute();} 
		catch (Exception $e) {	return array($type, "Unknown error saving coordinates:".$e->getMessage()) ;}

		return array($type, "Coordinates properly saved (".implode(' ',$vCoord).").") ;
	}

	/**
	  * load the coordinates from a given list of entries or profiles
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 		internal use
	  * @param array 	$vid 		id of the entry / profile that will receive coordinates
	  * @param array 	$vCoord 	array that will get the coordinate
	  * @param array 	$params 	associative array for the existing using fields (field_city=>id_field_city, field_zip=>0, field_street=>id_field_street, ...)
	  */
	public function getItemCoordinates($type, $ids, &$vCoord, $params){
		if (!$this->_isInCurrentType($type))
			return  ;

		// !!! on travaille avec le id de l'article pas celui de la table
		$vCoord = array($this->defEmptyLat, $this->defEmptyLng);
		if (!is_array($ids) AND is_int($ids))
			$ids = array($ids);

		if (!is_array($ids) OR count($ids)<1)
			return ;

		// Select the required fields from the table.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select("id_content, latitude,longitude" );
		$query->from($db->quoteName($this->plgTable));
		$query->where('id_content IN('.implode(',',$ids).') AND type='.$db->Quote($this->gatewayOption));

		$db->setQuery($query);
		$res = $db->loadObjectList();
		if ($db->getErrorNum()) { trigger_error("getItemCoordinates : DB reports: ".$db->stderr(), E_USER_WARNING);}

    	if(!is_array($res) OR count($res)<1)
			return ;

		// cas du travail avec un seul, et avec plusieurs
		foreach($res as $coor){
				$vCoord[$coor->id_content] = array($coor->latitude,$coor->longitude);
		}
	}

	/**
	  * return the postal address of the given entry/profile id based on the given fields 
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 		internal use
	  * @param int 		$id 		id of the entry / profile to retrieve
	  * @param array 	$vAssign 	associative array for the existing using fields (field_city=>id_field_city, field_zip=>0, field_street=>id_field_street, ...)
	  * @return array 	addresse formatted as array("New York", "USA", "Avenue...", ...) 
	  */
	public function getItemPostalAddress($type, $id, $vAssign){
		$add = array() ;
		if (!$this->_isInCurrentType($type))
			return array($this->gatewayCode, $add) ;

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select("address");
		$query->from($db->quoteName($this->plgTable));
		$query->where("id=" . (int) $id);
		$db->setQuery($query, 0, 1);

		$res = $db->loadObjectList();
		if ($db->getErrorNum()) { trigger_error("getItemPostalAddress : DB reports: ".$db->stderr(), E_USER_WARNING);}

		if (!count($res))
			return array($type, $add) ;

		$res = $res[0];

		// choisi city arbitrairement 
		$add["field_city"] = $res->address ;
		return array($type, $add) ;
	}

	/**
	  * save the postal address of the given entry/profile id based on the given fields 
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 		internal use
	  * @param int 		$id 		id of the entry / profile to retrieve
	  * @param array 	$vAssign 	associative array for the existing using fields (field_city=>id_field_city, field_zip=>0, field_street=>id_field_street, ...)
	  * @return array 	$vAddress 	formatted as array(field_city=>"New York", "field_country"=>USA", "field=street"=>Avenue...", ...) 
	  */
	public function setItemAddress($type, $id, $vAssign, $vAddress){
		if (!$this->_isInCurrentType($type))
			return array($this->gatewayCode) ; // retourne un seul élément, le résultat attend un vecteur de 2

		if (!$id OR !count($vAddress))
			return false;

		$add = implode(";", $vAddress) ;
		$db = JFactory::getDBO();
		$value = trim($vAddress[$k]) ;
		$vals = array("address=".$db->quote($value)) ;

		if (!count($vals))
			return array($type, "Error saving address, nothing to save !") ;

		$query = $db->getQuery(true);
		$query->update($db->quoteName($this->plgTable));
		$query->set(implode(',', $vals));
		$query->where("id=".$id);
		$db->setQuery($query);

		try {					$result = $db->execute();} 
		catch (Exception $e) {	return array($type, "Unknown error saving address:".$e->getMessage()) ;}

		return array($type, "Address properly saved (".implode(' ',$vAddress).").") ;
	}

	/**
	  * Fill a list of pairs : catid=>"full path to cat icon"
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 			internal use
	  * @param array 	$listCatIcon 	associative array of each cat id, and the related icon path
	  */
	public function getRel_idCat_iconPath($type, &$listCatIcon){
		if (!$this->_isInCurrentType($type))
			return false ;
	}

	/**
	  * Fill a list of pairs : entryid=>catid
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 			internal use
	  * @param array 	$listCatIcon 	associative array of each entry id, and the first related cat id
	  */
	public function getRel_idEntry_idCat($type, &$listCatEntry){
		if (!$this->_isInCurrentType($type))
			return false ;
	}

	/**
	  * return the common path for the marker icon, usualy the path ends with a DS,
	  * exception : if your "getRel_idCat_iconPath" function return the icon with a starting /
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 				internal use
	  * @param int 		$markerIconType 	associative array of each entry id, and the first related cat id
	  * @param string 	$iconPathDs			resulting path
	  */
	public function getIconCommonPath($type, $markerIconType, &$iconPathDs){
		if (!$this->_isInCurrentType($type))
			return false ;

		// return the common path for the avatar icon (from the query)
		if ($markerIconType==3){
			return ;
		}

		// return the common path for the category icons
		if ($markerIconType==4){
			return ;
		}
	}

	/**
	  * query that returns a list of category id, name, parentid and the ordering
	  *
	  * !!! columns named catid, parentid, title, ordering !
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 				internal use
	  * @param object 	$vCats				resulting databse result
	  * @param int 		$idTopCat			top category id for this component or component section
	  */
	public function getAllSubCats($type, &$vCats, &$idTopCat){
		if (!$this->_isInCurrentType($type))
			return ;
		
		$idTopCat = $this->iTopCategory ;

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(	'id as catid, parent_id as parentid, title as title');
		$query->from(	$db->quoteName('#__categories'));
		$query->where(	'extension='.$db->quote('com_content'));
		$query->order(	'parent_id');

		$db->setQuery($query);
    	$vCats = $db->loadObjectList();

		if ($db->getErrorNum()) {	trigger_error("getCategories: DB reports: ".$db->stderr(), E_USER_WARNING);}
	}

	/**
	  * Build the main query depending the different option
	  *
	  * This is the master query to get the markers for the maps. 
	  * 'O.' is the alias  (main_table AS O) of the main FROM occurence
	  * Perhaps some parts of the query can be done in the _getMainQuery 
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 			internal use
	  * @param array 	$params			contains the different feature, if enabled or not
	  * @param array 	$sqlSelect		add each column you want to select, the comas are added later (execpt if you add 2 values in the same item)
	  * @param array 	$sqlJoin		add the needed joint you need
	  * @param array 	$sqlWhere		add the conditions based on the give join and select
	  */
	public function customiseQuery($type, $params, &$sqlSelect, &$sqlJoin, &$sqlWhere){
		if (!$this->_isInCurrentType($type))
			return ;

		// unique id of the entry
		$sqlSelect[]	= "O.id_content AS id, C.title, O.latitude, O.longitude" ;
		$sqlJoin[]		= "LEFT JOIN #__content AS C ON C.id=O.id_content" ;

		// if your TPC alows to join the owner of the entry (if not, comment this part of code) 
		if ($params['linesOwners']==1){
			$sqlSelect[]	= "C.created_by AS owner" ;
		}

		$params['linesFriends']		= (isset($oMs->lfr) AND $oMs->lfr>0)?1:0;
		$params['linesMyAddr']		= (isset($oMs->lma) AND $oMs->lma>0)?1:0;
		$params['']		= (isset($oMs->low) AND $oMs->low>0)?1:0;
		$params['linesGuests']		= (isset($oMs->lgu) AND $oMs->lgu>0)?1:0;


		// select only entries in categories 
		if (strlen($params['inCats'])>0)
			$sqlWhere[]	= "C.catid IN ({$params['inCats']})" ;

		// pulished state
		$sqlWhere[] = $this->_getPublishedState($params['onlyPublished']) ;

		$sqlWhere[] 	= $this->_getValidCoordTest("O.latitude", "O.longitude");
	}

	/**
	  * Method that build the main query. 
	  *
	  * The main query need to return : id, title, owner (if applicable), trace (if applicable), avatar, sales, latitude, longitude
	  *
	  * @since 1.0
	  *
	  * @param array 	$data			contains the different settings and formatted query pieces
	  * @param string 	$retQuery		returned query
	  */
	public function getMainQuery($data, &$retQuery){
		if (!$this->_isInCurrentType($data['type']))
			return ;

		$query = array() ;
		$query[] = $data['sqlSelect'];// formated select
		$query[] = "FROM {$this->plgTable} O" ;
		$query[] = $data['sqlJoin'];
		$query[] = $data['sqlWhere'];

		$retQuery = implode(" ", $query);
	}

	/**
	  * Method used to build the filters query part
	  *
	  * depending your database structure, you can add the filters to the main query, or do a query that build a list of id where the entries need to be
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 			internal use
	  * @param array 	$filters		contains the different filters (filter and filter_opt) that is define by the user 
	  * @param array 	$sqlSelect		add each column you want to select, the comas are added later (execpt if you add 2 values in the same item)
	  * @param array 	$sqlJoin		add the needed joint you need
	  * @param array 	$sqlWhere		add the conditions based on the give join and select
	  */
	public function setMainQueryFilters($type, $oMs, &$sqlSelect, &$sqlJoin, &$sqlWhere){
		if (!$this->_isInCurrentType($type))
			return ;

		if (isset($oMs->filter) && strlen($oMs->filter)>0){
			$sqlWhere[] = $oMs->filter;
		}
	}

	/**
	  * --> sobipro only
	  *
	  * clean the content of the image field
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 			internal use
	  * @param string 	$fieldImg		contain the input and output image to clean 
	  */
	public function getIconPathFromBrutDbValue($type, &$fieldImg){
	}

	/**
	  * --> CB only
	  * 	//http://www.myjoom3.csft.ro/index.php?option=com_comprofiler&task=userprofile&user=64&Itemid=5 or
	  *		//http://www.myjoom3.csft.ro/index.php?option=com_comprofiler&Itemid=5 (si par le menu)
	  *
	  * This function is send before display the map, and will clean result from sobipro search
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 			internal use
	  * @param string 	$vUid			result input and outup
	  */
	public function cleanResultsFromPlugins($type, &$vUid){
	}

	/**
	  * Get the color of the pline. Take the users value or a default value
	  *
	  * @since 1.0
	  *
	  * @param string 	$type 			internal use
	  * @param string 	$vUid			result input and outup
	  */
	public function getColorPline(&$vCol){
		$col = $this->params->get('linesOwners');
		$vCol[$this->gatewayCode.'linesOwners'] = strlen($col)>2?$col:$this->defColorPline ;
	}

	/**
	  * this function allows to know if we are into a categorie, or an entry view
	  * from your component. 
	  *
	  * @since 1.0
	  *
	  * @param string 	$option		joomla current url option value
	  */
	public function defineContext($option, &$map){
		// pas le bon ?
		if (strtolower($option) != strtolower($this->gatewayOption))
			return ;

		$app 		= JFactory::getApplication('site');
		$zoomMe		= 0 ;
		$task 		= strtolower(JRequest::getString("view"));

		if ($task == "article") {
			$zoomMe 	= JRequest::getInt("id", 0); 
		}
	
		$map->gf_zoomMeId 	= $zoomMe ;
		$map->gf_zoomMeType = $this->gatewayCode ;
	}

	/**
	  * retunr the custom fields allowed (selectable in assignation) for coordinates
	  *
	  * @since 1.0
	  *
	  * @param string 	$typeList	internal use
	  * @param string 	$option		list of val
	  */
	public function getCustomFieldsCoord($typeList, $options){
		if (!$this->_isInCurrentType($typeList))
			return ;
		// JC have own coordinates fields
		return ;
	}

	/**
	  * Fill the code of extension if dycat available
	  *
	  * @since 1.0
	  *
	  * @param array   	$resCode 	contains the list of available codes
	  */
	public function getCodeDynCat(&$resCode){
		if (!$this->isCategorised)
			return ;

		$resCode[] = $this->gatewayCode;
	}

	/**
	  * load the needed base data for the marker's bubble 
	  * and create an array of placeholders replacements
	  *
	  * @since 1.0
	  *
	  * @param object 	$objMarker	in : object containing the base data of the markers
	  * 							out : add some informations.
	  * @param string  	$titleField title field if applicatble
	  * @param string 	$typeList	internal use
	  */
	public function markerTemplateAndPlaceholder(&$objMarker, $params){
		if (!$this->_isInCurrentType($objMarker->type))
			return ;

		// working variables
		$menuId				= $this->_getMenuItemId(isset($params['menuId'])?$params['menuId']:0);
		$article =& JTable::getInstance('content');
		$article->load($objMarker->id);

		// prépare les images
		$article->image_intro		= JURI::root().'media/com_geofactory/assets/blank.png';
		$article->image_fulltext 	= JURI::root().'media/com_geofactory/assets/blank.png';
		if (strlen($article->images)>5){
			$images = json_decode($article->images);
			if (strlen($images->image_intro)>3)		$article->image_intro 		= JURI::root().$images->image_intro ;
			if (strlen($images->image_fulltext)>3)	$article->image_fulltext 	= JURI::root().$images->image_fulltext ;
		}

		$slug 				= $article->id.':'.$article->alias;
		$catslug 			= $article->catid;
		$objMarker->link	= JRoute::_(ContentHelperRoute::getArticleRoute($slug, $catslug)) ;
		$objMarker->rawTitle= $article->title ;
		
		// passe en revue tous les champs
		foreach ($this->arBubbleFields as $fName){
			$dispo = "{".$fName."}";

			// le champs est il utilisé ? 
			if (stripos($objMarker->template, $dispo)===false)
				continue ;

			if ($dispo == '{introtext}'){
				$objMarker->replace['{introtext}']		= JHtml::_('content.prepare', $article->introtext) ;
				continue ;
			}
			
			if ($dispo == '{introtextraw}'){
				$objMarker->replace['{introtextraw}']	= JHtml::_('string.truncate', $article->introtext, 0, true, false);
				continue ;
			}
			
			if ($dispo == '{fulltext}'){
				$objMarker->replace['{fulltext}']		= JHtml::_('content.prepare', $article->fulltext) ;
				continue ;
			}
			
			if ($dispo == '{fulltextraw}'){
				$objMarker->replace['{fulltextraw}'] 	= JHtml::_('string.truncate', $article->fulltext, 0, true, false);
				continue ;
			}
			
			$objMarker->replace[$dispo] = $article->$fName ;
		}

		// set the k=v in 2 arrays
		foreach($objMarker->replace as $k=>$v)
			$objMarker->search[] = $k ;
	}

	/**
	  * create an array of placeholders replacements for the bubble template builder
	  *
	  * @since 1.0
	  *
	  * @param string 	$typeList		internal use
	  * @param array 	$placeHolders 	array of array  array("Family"=>array(field=>"help", ), )
	  */
	public function getPlaceHoldersTemplate($typeList, &$placeHolders){
		if (!$this->_isInCurrentType($typeList))
			return ;

		$placeHolders = array() ;
		$onlyMe = "Joomla content special" ;
		if (! isset($placeHolders[$onlyMe]))
			$placeHolders[$onlyMe] = array() ;
		
		// passe en revue tous les champs
		foreach ($this->arBubbleFields as $fDispo){			
			$placeHolders[$onlyMe][$fDispo] = '{'.$fDispo.'}';
		}
	}

	/**
	  * this function will build the filter query (added in query in setMainQueryFilters)
	  *
	  * @since 1.0
	  *
	  * @param string 	$typeList	internal use
	  * @param string 	$jsPlugin 	contains the JS code
	  * @param string 	$txt 		help for this plugin, displayed when generate button is presse
	  */
	public function getFilterGenerator($typeList, &$jsPlugin, &$txt){
		if (!$this->_isInCurrentType($typeList))
			return ;

		// the generated Js code snipet will contains available variables are field, cond, value, result
		$jsPlugin.=	'result  = "( " + field + cond + " \'" + like + value + like + "\' )" ;';
		
		// information text
		$txt.= 		"&nbsp;&nbsp;SELECT values FROM articles_table WHERE internal_conditions AND <strong>(your_query)</strong>";
		$txt.= 		"</br></br>With Joomla Content you can use multiple conditions like :</br>";
		$txt.= 		"&nbsp;&nbsp;SELECT values FROM article_table WHERE internal_conditions AND <strong>((your_query_A) AND/OR (your_query_B))</strong>";
	}

	/**
	  * this function add the entry state (published or not) option to the main query (the main table is O.)
	  *
	  * @since 1.0
	  *
	  * @param int 	$state	0-published, 1-unpublished
	  * @return string
	  */
	public function _getPublishedState($state){
		// case of unpublished 
		if ($state==1)
			return 'C.state=0' ;

		// default published entries
		return 'C.state>0' ;
	}
}
