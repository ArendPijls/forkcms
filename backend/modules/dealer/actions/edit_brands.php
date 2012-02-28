<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Edit a brand.
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */
class BackendDealerEditBrands extends BackendBaseActionEdit
{
	/**
	 * Execute the action.
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the dealer locater exist
		if($this->id !== null && BackendDealerModel::existsBrand($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get data
			$this->getData();

			// load form
			$this->loadForm();

			// validate form
			$this->validateForm();

			// parse
			$this->parse();

			// display
			$this->display();
		}

		// no brands found
		else $this->redirect(BackendModel::createURLForAction('brands') . '&error=non-existing');
	}

	/**
	 * Get the data.
	 */
	private function getData()
	{
		$this->record = BackendDealerModel::getBrand($this->id);
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('edit');

		// set hidden values
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden'), 'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');

		// create elements
		$this->frm->addText('name', $this->record['name'], 255, 'inputText title', 'inputTextError, title');
		$this->frm->addImage('image');
	}

	/**
	 * Parse the form.
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// assign fields
		$this->tpl->assign('item', $this->record);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validate fields
			$this->frm->getField('name')->isFilled(BL::err('FieldIsRequired'));

			// validate image
			if($this->frm->getField('image')->isFilled())
			{
				// correct extension
				if($this->frm->getField('image')->isAllowedExtension(array('jpg', 'jpeg', 'gif', 'png'), BL::err('JPGGIFAndPNGOnly')))
				{
					// correct mimetype?
					$this->frm->getField('image')->isAllowedMimeType(array('image/gif', 'image/jpg', 'image/jpeg', 'image/png'), BL::err('JPGGIFAndPNGOnly'));
				}
			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['id'] = $this->id;
				$item['name'] = $this->frm->getField('name')->getValue();

				// has the user submitted an image?
				if($this->frm->getField('image')->isFilled())
				{
					// delete old image if it isn't the default-image
					if($this->frm->getField('image') != 'no-image.jpg')
					{
						SpoonFile::delete(FRONTEND_FILES_PATH . '/frontend_dealer/brands/source/' . $this->record['image']);
						SpoonFile::delete(FRONTEND_FILES_PATH . '/frontend_dealer/brands/128x128/' . $this->record['image']);
						SpoonFile::delete(FRONTEND_FILES_PATH . '/frontend_dealer/brands/64x64/' . $this->record['image']);
						SpoonFile::delete(FRONTEND_FILES_PATH . '/frontend_dealer/brands/32x32/' . $this->record['image']);
					}

					// create new filename
					$filename = rand(0,1000) . '_' . SpoonFilter::urlise($item['name']) . '.' . $this->frm->getField('image')->getExtension();

					// add into settings to update
					$item['image'] = $filename;

					// resize (128x128)
					$this->frm->getField('image')->createThumbnail(FRONTEND_FILES_PATH . '/frontend_dealer/brands/128x128/' . $filename, 128, 128, true, false, 100);

					// resize (64x64)
					$this->frm->getField('image')->createThumbnail(FRONTEND_FILES_PATH . '/frontend_dealer/brands/64x64/' . $filename, 64, 64, true, false, 100);

					// resize (32x32)
					$this->frm->getField('image')->createThumbnail(FRONTEND_FILES_PATH . '/frontend_dealer/brands/32x32/' . $filename, 32, 32, true, false, 100);
				}

				// update the dealer
				BackendDealerModel::updateBrand($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $item));

				// everything has been saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('brands') . '&report=edited&var=' . urlencode($item['name']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}

