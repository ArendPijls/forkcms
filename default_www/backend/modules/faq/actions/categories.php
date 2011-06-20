<?php

/**
 * This is the categories-action, it will display the overview of faq categories
 *
 * @package		backend
 * @subpackage	faq
 *
 * @author		Lester Lievens <lester@netlash.com>
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.1
 */
class BackendFaqCategories extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load datagrids
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Loads the datagrid
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendFaqModel::QRY_DATAGRID_BROWSE_CATEGORIES, BL::getWorkingLanguage());

		// set headers
		$this->datagrid->setHeaderLabels(array('num_items' => ucfirst(BL::lbl('Amount'))));

		// enable drag and drop
		$this->datagrid->enableSequenceByDragAndDrop();

		// set column URLs
		$this->datagrid->setColumnURL('title', BackendModel::createURLForAction('edit_category') . '&amp;id=[id]');

		// add column
		$this->datagrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit_category') . '&amp;id=[id]', BL::lbl('Edit'));

		// our JS needs to know an id, so we can send the new order
		$this->datagrid->setRowAttributes(array('id' => '[id]'));

		// disable paging
		$this->datagrid->setPaging(false);
	}


	/**
	 * Parse & display the page
	 *
	 * @return	void
	 */
	private function parse()
	{
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}


	/**
	 * Convert the count in a human readable one.
	 *
	 * @return	string
	 * @param	int $count		The count.
	 * @param	string $link	The link for the count.
	 */
	public static function setClickableCount($count, $link)
	{
		// redefine
		$count = (int) $count;
		$link = (string) $link;

		// return link in case of more than one item, one item, other
		if($count > 1) return '<a href="' . $link . '">' . $count . ' ' . BL::getLabel('Questions') . '</a>';
		if($count == 1) return '<a href="' . $link . '">' . $count . ' ' . BL::getLabel('Question') . '</a>';
		return '';
	}
}

?>