<?php

/**
 * Show all dealers.
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */

class FrontendDealerIndex extends FrontendBaseBlock
{
	/**
	 * Execute the extra.
	 */
	public function execute()
	{
		parent::execute();

		$this->getData();
		$this->loadTemplate();
		$this->loadForm();
		$this->validateForm();
		$this->parse();

	}

	/**
	 * Get the data.
	 */
	private function getData()
	{
		$this->brands = FrontendDealerModel::getAllBrands();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new FrontendForm('searchForm');
		$this->frm->setAction($this->frm->getAction());

		// init some vars
		$values = array();

		// get brand ids and put them in an array
		foreach($this->brands as $value)
		{
			$values[] = array('label' => $value['name'], 'value' => $value['id']);
		}

		// create elements
		$this->frm->addText('area');
		$this->frm->addDropdown('where', array('CloseToYou' => 'CloseToYou', 'InBelgium' => 'InBelgium', 'InNederlands' => 'InNederlands', 'InFrance' => 'InFrance'));
		$this->frm->addMultiCheckbox('type', $values);
		//$this->frm->addDropdown('map_type', array('ROADMAP' => FL::lbl('Roadmap', $this->getModule()), 'SATELLITE' => FL::lbl('Satellite', $this->getModule()), 'HYBRID' => FL::lbl('Hybrid', $this->getModule()), 'TERRAIN' => FL::lbl('Terrain', $this->getModule())), BackendModel::getModuleSetting($this->URL->getModule(), 'map_type', 'roadmap'));


	}

	/**
	 * Parse the data and compile the template.
	 */
	private function parse()
	{
		// parse the form
		$this->frm->parse($this->tpl);
		$this->tpl->assign('dealerSettings', FrontendModel::getModuleSettings('dealer'));

	}

	/**
	 * Validate form
	 */
	private function validateForm()
	{

		// is the form submitted
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate required fields
			$this->frm->getField('area')->isFilled(FL::err('AreaIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// reformat data
				$area = $this->frm->getField('area')->getValue();
				$area = $this->frm->getField('area')->getValue();

				// create array item with all brands in
				$brands = array();
				foreach($this->brands as $brand)
				{
					// if checkbox is checked save id in array values
					if(in_array($brand['id'], (array) $this->frm->getField('type')->getValue())) $brands[] = $brand['id'];
				}

				// assign dealers items and area
				$this->tpl->assign('dealerArea', $area);
				$this->tpl->assign('dealerItems', FrontendDealerModel::getAll($area,$brands));

			}
		}
	}
}