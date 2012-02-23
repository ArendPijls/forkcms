<?php

/**
 * Installer for the dealer module
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */

class DealerInstaller extends ModuleInstaller
{
	/**
	 * Install the module.
	 */
	public function install()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'dealer' as a module
		$this->addModule('dealer', 'The dealer module.');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'dealer');

		// general settings
		$this->setSetting('location', 'zoom_level', 'auto');
		$this->setSetting('location', 'width', 400);
		$this->setSetting('location', 'height', 300);
		$this->setSetting('location', 'map_type', 'ROADMAP');
		$this->setSetting('location', 'distance', 25);
		$this->setSetting('location', 'limit', 50);
		$this->setSetting('location', 'units', 'KM');

		// action rights
		$this->setActionRights(1, 'dealer', 'add');
		$this->setActionRights(1, 'dealer', 'delete');
		$this->setActionRights(1, 'dealer', 'edit');
		$this->setActionRights(1, 'dealer', 'index');
		$this->setActionRights(1, 'dealer', 'sequence');
		$this->setActionRights(1, 'dealer', 'settings');

		// set navigation
		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$this->setNavigation($navigationModulesId, 'Dealer', 'dealer/index', array('dealer/add', 'dealer/edit'));
		$this->setNavigation($navigationModulesId, 'Brands', 'dealer/brands', array('search/add_brands', 'search/edit_brands'));

		// settings navigation
		$navigationSettingsId = $this->setNavigation(null, 'Settings');
		$navigationModulesId = $this->setNavigation($navigationSettingsId, 'Modules');
		$this->setNavigation($navigationModulesId, 'Dealer', 'dealer/settings');

		// add extra's
		$this->insertExtra('dealer', 'block', 'Dealer', 'all_dealer', null, 'N');
		$this->insertExtra('dealer', 'widget', 'RandomDealer', 'random_dealer', null, 'N');
	}
}
