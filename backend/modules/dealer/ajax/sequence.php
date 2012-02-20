<?php

/**
 * Reorder dealer
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */
class BackendDealerAjaxSequence extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$newIdSequence = trim(SpoonFilter::getPostValue('new_id_sequence', null, '', 'string'));

		// list id
		$ids = (array) explode(',', rtrim($newIdSequence, ','));

		// loop id's and set new sequence
		foreach($ids as $i => $id)
		{
			// build item
			$item['id'] = (int) $id;
			$item['sequence'] = $i + 1;

			// exists
			if(BackendDealerModel::exists($item['id'])) BackendDealerModel::update($item);
		}

		// success output
		$this->output(self::OK, null, 'sequence updated');
	}
}
