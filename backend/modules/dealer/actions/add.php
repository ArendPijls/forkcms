<?php

/**
 * Display a form to create a new dealer.
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */

class BackendDealerAdd extends BackendBaseActionAdd
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

		// set hidden values
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden', $this->URL->getModule()), 'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');

		// create elements
		$this->frm->addText('name', null, 255, 'inputText title', 'inputTextError, title');
		$this->frm->addEditor('dealer');
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
		$this->frm->addText('street');
		$this->frm->addText('number');
		$this->frm->addText('zip');
		$this->frm->addText('city');
		$this->frm->addDropdown('country', SpoonLocale::getCountries(BL::getInterfaceLanguage()), 'BE');
		$this->frm->addText('tel');
		$this->frm->addText('fax');
		$this->frm->addText('email');
		$this->frm->addText('site');
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
			$this->frm->getField('dealer')->isFilled(BL::err('DealerIsRequired'));
			$this->frm->getField('street')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('number')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('zip')->isFilled(BL::err('FieldIsRequired'));
			$this->frm->getField('city')->isFilled(BL::err('FieldIsRequired'));

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
				$item['description'] = $this->frm->getField('dealer')->getValue();
				$item['street'] = $this->frm->getField('street')->getValue();
				$item['number'] = $this->frm->getField('number')->getValue();
				$item['zip'] = $this->frm->getField('zip')->getValue();
				$item['city'] = $this->frm->getField('city')->getValue();
				$item['country'] = $this->frm->getField('country')->getValue();
				$item['tel'] = $this->frm->getField('tel')->getValue();
				$item['fax'] = $this->frm->getField('fax')->getValue();
				$item['email'] = $this->frm->getField('email')->getValue();
				$item['site'] = $this->frm->getField('site')->getValue();
				$item['hidden'] = $this->frm->getField('hidden')->getValue();
				$item['user_id'] = BackendAuthentication::getUser()->getUserId();
				$item['language'] = BackendLanguage::getWorkingLanguage();
				$item['sequence'] = BackendDealerModel::getMaximumSequence() + 1;
				$item['created_on'] = BackendModel::getUTCDate();
				$item['edited_on'] = BackendModel::getUTCDate();

				// has the user submitted an avatar?
				if($this->frm->getField('avatar')->isFilled())
				{
					// delete old avatar if it isn't the default-image
					if($this->frm->getField('avatar') != 'no-avatar.jpg')
					{
						SpoonFile::delete(FRONTEND_FILES_PATH . '/frontend_dealer/avatars/source/' . $this->record['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH . '/frontend_dealer/avatars/128x128/' . $this->record['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH . '/frontend_dealer/avatars/64x64/' . $this->record['avatar']);
						SpoonFile::delete(FRONTEND_FILES_PATH . '/frontend_dealer/avatars/32x32/' . $this->record['avatar']);
					}

					// create new filename
					$filename = rand(0,3) . '_' . $item['id'] . '.' . $this->frm->getField('avatar')->getExtension();

					// add into settings to update
					$item['avatar'] = $filename;

					// resize (128x128)
					$this->frm->getField('avatar')->createThumbnail(FRONTEND_FILES_PATH . '/frontend_dealer/avatars/128x128/' . $filename, 128, 128, true, false, 100);

					// resize (64x64)
					$this->frm->getField('avatar')->createThumbnail(FRONTEND_FILES_PATH . '/frontend_dealer/avatars/64x64/' . $filename, 64, 64, true, false, 100);

					// resize (32x32)
					$this->frm->getField('avatar')->createThumbnail(FRONTEND_FILES_PATH . '/frontend_dealer/avatars/32x32/' . $filename, 32, 32, true, false, 100);
				}

				// geocode address
				$url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($item['street'] . ' ' . $item['number'] . ', ' . $item['zip'] . ' ' . $item['city'] . ', ' . SpoonLocale::getCountry($item['country'], BL::getWorkingLanguage())) . '&sensor=false';
				$geocode = json_decode(SpoonHTTP::getContent($url));
				$item['lat'] = isset($geocode->results[0]->geometry->location->lat) ? $geocode->results[0]->geometry->location->lat : null;
				$item['lng'] = isset($geocode->results[0]->geometry->location->lng) ? $geocode->results[0]->geometry->location->lng : null;

				// insert the item
				$item['id'] = BackendDealerModel::insertDealer($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $item));

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=added&var=' . urlencode($item['name']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}
