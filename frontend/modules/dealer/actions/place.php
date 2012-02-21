<?php

/**
 * Show all dealers.
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */

class FrontendDealerPlace extends FrontendBaseBlock
{

	/**
	 * The area parameter
	 *
	 * @var	string
	 */
	private $area;

	/**
	 * The brands parameter
	 *
	 * @var	string
	 */
	private $brands;

	/**
	 * Execute the extra.
	 */
	public function execute()
	{
		parent::execute();
		$this->getDate();
		$this->loadTemplate();
		$this->parse();

	}

	/*
	 * Get data
	 */
	private function getDate()
	{
	    $this->area = $this->URL->getParameter(1);
	    $this->brands = $this->URL->getParameter(2);
	}
	/**
	 * Parse the data and compile the template.
	 */
	private function parse()
	{

		$this->tpl->assign('dealerItems', FrontendDealerModel::getAll($this->area,$this->brands));
		$this->tpl->assign('dealerArea', $this->area);
		// hide form
		$this->tpl->assign('dealerSettings', FrontendModel::getModuleSettings('dealer'));
	}
}
