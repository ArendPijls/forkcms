<?php

/**
 * Search form with brands and on submit showing dealer locaters
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
		$this->frm->addDropdown('country', array('AROUND' => FL::lbl('TheClosestTo', $this->getModule()), 'BE' => FL::lbl('InBelgium', $this->getModule()), 'NL' => FL::lbl('InNetherlands', $this->getModule()), 'FR' => FL::lbl('InFrance', $this->getModule())));
		$this->frm->addMultiCheckbox('type', $values);
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

			// get input values
			$area = $this->frm->getField('area')->getValue();
			$country = $this->frm->getField('country')->getValue();

			// ignore manipulated dropdownbox by hackers
			if($country != "AROUND" and $country != "BE" and $country != "NL" and $country != "FR") $this->frm->addError('Eat some peanuts');

			// no errors?
			if($this->frm->isCorrect())
			{
				// create array item with all brands in
				$brands = array();

				foreach($this->brands as $brand)
				{
					// if checkbox is checked save id in array values
					if(in_array($brand['id'], (array) $this->frm->getField('type')->getValue())) $brands[] = $brand['id'];
				}

				$getDealers = FrontendDealerModel::getAll($area,$brands,$country);

				// check of there are dealers
				if(count($getDealers) > 0)
				{
					// assign dealers items and area
					$this->tpl->assign('dealerArea', $area);
					$this->tpl->assign('dealerItems', $getDealers);
					$this->tpl->assign('dealerHeadingText', 'Found %s dealers');
					$this->tpl->assign('numDealers', count($getDealers));
				}
				else
				{
					$this->tpl->assign('dealerErrorNoDealers', 1);
				}
			}
		}
	}
}