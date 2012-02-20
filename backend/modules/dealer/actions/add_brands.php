<?php

/**
 * Display a form to create a new brand.
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */

class BackendDealerAddBrands extends BackendBaseActionAdd
{
	public function execute()
	{
		parent::execute();

		// load form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse the datagrid
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('add');

		// create elements
		$this->frm->addText('name', null, 255, 'inputText title', 'inputTextError, title');
		$this->frm->addImage('avatar');
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
			$this->frm->getField('name')->isFilled(BL::err('NameIsRequired'));

			// validate avatar
			if($this->frm->getField('avatar')->isFilled())
			{
				// correct extension
				if($this->frm->getField('avatar')->isAllowedExtension(array('jpg', 'jpeg', 'gif', 'png'), BL::err('JPGGIFAndPNGOnly')))
				{
					// correct mimetype?
					$this->frm->getField('avatar')->isAllowedMimeType(array('image/gif', 'image/jpg', 'image/jpeg', 'image/png'), BL::err('JPGGIFAndPNGOnly'));
				}

			}

			// no errors?
			if($this->frm->isCorrect())
			{
				// build item
				$item['name'] = $this->frm->getField('name')->getValue();

				// has the user submitted an avatar?
				if($this->frm->getField('avatar')->isFilled())
				{
					// delete old avatar if it isn't the default-image
					if($this->frm->getField('avatar') != 'no-avatar.jpg')
					{
						SpoonFile::delete(FRONTEND_FILES_PATH . '/frontend_dealer/brands/source/' . $this->record['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH . '/frontend_dealer/brands/128x128/' . $this->record['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH . '/frontend_dealer/brands/64x64/' . $this->record['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH . '/frontend_dealer/brands/32x32/' . $this->record['avatar']);
					}

					// create new filename
					$filename = rand(0,3) . '_' . $item['id'] . '.' . $this->frm->getField('avatar')->getExtension();

					// add into settings to update
					$item['avatar'] = $filename;

					// resize (128x128)
					$this->frm->getField('avatar')->createThumbnail(FRONTEND_FILES_PATH . '/frontend_dealer/brands/128x128/' . $filename, 128, 128, true, false, 100);

					// resize (64x64)
					$this->frm->getField('avatar')->createThumbnail(FRONTEND_FILES_PATH . '/frontend_dealer/brands/64x64/' . $filename, 64, 64, true, false, 100);

					// resize (32x32)
					$this->frm->getField('avatar')->createThumbnail(FRONTEND_FILES_PATH . '/frontend_dealer/brands/32x32/' . $filename, 32, 32, true, false, 100);
				}

				// insert the item
				$item['id'] = BackendDealerModel::insertBrand($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('brands') . '&report=added&var=' . urlencode($item['name']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
