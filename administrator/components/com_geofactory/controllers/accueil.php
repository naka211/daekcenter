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

jimport('joomla.updater.update');
require_once JPATH_ADMINISTRATOR.'/components/com_installer/models/update.php';

class GeofactoryControllerAccueil extends JControllerLegacy{
	function update(){
		JToolbarHelper::title("MyJoom updater");

		// recherche l email enregistrée par le user
		$config 	= JComponentHelper::getParams('com_geofactory');
		$subsEnd 	= $config->get('subsEnd');
		$subsEnd 	= urlencode(base64_encode($subsEnd));
		$app 		= JFactory::getApplication();
		$file		= $app->input->getVar('file') ;
		$free		= $app->input->getInt('free', 0) ;
		$url 		= "http://www.myjoom.com/index.php?option=com_geofactory&amp;task=gus&amp;exp={$subsEnd}&amp;ext=/{$file}" ;

		if ($free==1){
			$url 	= "http://www.myjoom.com/myjoom-updater/XTYGdkckuQwMBOks/includes{$file}" ;

			// on passe au package, car le package lui meme ne peut pas être utilisé facilement en version check, donc pour moi c'est plus simple de tout mettre a jour si il fait le core (en plus il install les nouveaux plugins/modules)
			if ($file=='geofactory.zip'){
				$file='pkg_geofactory.zip';
				$url = "http://www.myjoom.com/myjoom-updater/XTYGdkckuQwMBOks/{$file}" ;
			}
		}

		$p_file 	= JInstallerHelper::downloadPackage($url);

		if (!$p_file){
			$this->_raiseError(JText::_('COM_INSTALLER_PACKAGE_DOWNLOAD_FAILED'));
			exit ;
		}

		$config		= JFactory::getConfig();
		$tmp_dest	= $config->get('tmp_path');
		$package	= JInstallerHelper::unpack($tmp_dest . '/' . $p_file);
		if (!$package){
			$this->_raiseError("Error update: Unable to unpack '{$tmp_dest}/{$p_file}'.<br /><br />Please go on www.myjoom.com > Memberships > My Subscriptions and download you file and install it manualy.");
			exit ; 
		}

		$installer	= JInstaller::getInstance();
		if (!$installer->update($package['dir'])){
			$msg = "Error update: unable to install the unpacked file.<br /><br />Please go on www.myjoom.com > Memberships > My Subscriptions and download you file and install it manualy." ; 
			$result = false;
		}
		else{
			$msg = "Successfully updated, please click the 'Check for updates' button to refresh.";
			$result = true;
		}

		$app->enqueueMessage($msg);

		if (!is_file($package['packagefile'])){
			$config = JFactory::getConfig();
			$package['packagefile'] = $config->get('tmp_path') . '/' . $package['packagefile'];
		}

		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
	}

	public function updates(){
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_installer/models', 'InstallerModel');
		$model = JModelLegacy::getInstance('update', 'InstallerModel');
		$model->purge();
		$model->findUpdates();

		$this->setRedirect(JRoute::_('index.php?option=com_geofactory&view=accueil&task=accueil', false));
	}

	protected function _raiseError($msg){
		?>
		<div id="system-message-container">
			<div class="alert alert-error">
				<h4 class="alert-heading">Error</h4>
					<p><?php echo $msg; ?></p>
			</div>
		</div>
		<?php
		exit ; 
	}
}
