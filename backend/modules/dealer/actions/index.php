<?php

/**
 * Display dealer locater overview
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */
class BackendDealerIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 */

	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the data grid
		$this->loadDataGrid();

		// parse the data grid
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Load the data grid.
	 */
	protected function loadDataGrid()
	{
		// create the data grid for the overview
		$this->datagrid = new BackendDataGridDB(BackendDealerModel::QRY_BROWSE, array(BackendLanguage::getWorkingLanguage()));


		// linkify the name column
		$this->datagrid->setColumnURL('name', BackendModel::createURLForAction('edit') . '&amp;id=[id]');

		// create the "edit" button for each row
		$this->datagrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BackendLanguage::getLabel('Edit'));
	}

	/**
	 * Parse the data grid.
	 */
	protected function parse()
	{
		$this->tpl->assign('datagrid', $this->datagrid->getNumResults() ? $this->datagrid->getContent() : false);
	}
}
