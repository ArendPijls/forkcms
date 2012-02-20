<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the settings-action, it will display a form to set general dealer map settings
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */

class BackendDealerSettings extends BackendBaseActionEdit
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	/**
	 * Loads the settings form
	 */
	private function loadForm()
	{
		$this->frm = new BackendForm('settings');

		// add map info (overview map)
		$this->frm->addDropdown('zoom_level', array_combine(array_merge(array('auto'), range(3, 18)), array_merge(array(BL::lbl('Auto', $this->getModule())), range(3, 18))), BackendModel::getModuleSetting($this->URL->getModule(), 'zoom_level', 'auto'));
		$this->frm->addText('width', BackendModel::getModuleSetting($this->URL->getModule(), 'width'));
		$this->frm->addText('height', BackendModel::getModuleSetting($this->URL->getModule(), 'height'));
		$this->frm->addDropdown('map_type', array('ROADMAP' => BL::lbl('Roadmap', $this->getModule()), 'SATELLITE' => BL::lbl('Satellite', $this->getModule()), 'HYBRID' => BL::lbl('Hybrid', $this->getModule()), 'TERRAIN' => BL::lbl('Terrain', $this->getModule())), BackendModel::getModuleSetting($this->URL->getModule(), 'map_type', 'roadmap'));

	}

	/**
	 * Validates the settings form
	 */
	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			$this->frm->cleanupFields();

			if($this->frm->isCorrect())
			{
				// set our settings (overview map)
				BackendModel::setModuleSetting($this->URL->getModule(), 'zoom_level', (string) $this->frm->getField('zoom_level')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'width', (int) $this->frm->getField('width')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'height', (int) $this->frm->getField('height')->getValue());
				BackendModel::setModuleSetting($this->URL->getModule(), 'map_type', (string) $this->frm->getField('map_type')->getValue());

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_saved_settings');

				// redirect to the settings page
				$this->redirect(BackendModel::createURLForAction('settings') . '&report=saved');
			}
		}
	}
}
