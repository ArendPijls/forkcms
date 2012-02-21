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

		$this->loadTemplate();

		$this->parse();

	}

	/**
	 * Parse the data and compile the template.
	 */
	private function parse()
	{

		//$this->tpl->assign('authors', FrontendDealerModel::getAll2());

		$this->tpl->assign('dealerItems', FrontendDealerModel::getAll());

		// hide form
		$this->tpl->assign('dealerSettings', FrontendModel::getModuleSettings('dealer'));
	}
}
