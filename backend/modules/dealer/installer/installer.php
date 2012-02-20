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
		$this->insertExtra('dealer', 'block', 'AllDealer', 'all_dealer', null, 'N');
		$this->insertExtra('dealer', 'widget', 'RandomDealer', 'random_dealer', null, 'N');
	}
}
