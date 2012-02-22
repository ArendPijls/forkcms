<?php

/**
 * Edit a dealer.
 *
 * @author Arend Pijls <arend.pijls@netlash.com>
 */

class BackendDealerEdit extends BackendBaseActionEdit
{
	/**
	 * Execute the action.
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the dealer exist
		if($this->id !== null && BackendDealerModel::existsDealer($this->id))
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

		// no dealer found
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}

	/**
	 * Get the data.
	 */
	private function getData()
	{
		$this->record = BackendDealerModel::getDealer($this->id);
		$this->dealerBrands = BackendDealerModel::getDealerBrands($this->id);
		$this->brands = BackendDealerModel::getAllBrands();
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

		// init some vars
		$checked = array();
		$values = array();
		$values2 = array();

		// get brand ids and put them in an array
		foreach($this->brands as $value)
		{
			$values[] = array('label' => $value['name'], 'value' => $value['id']);
		}

		// get dealer brands and put them in a arracy
		foreach($this->dealerBrands as $value2)
		{
			$checked[] = $value2['brand_id'];
		}

		$this->frm->addText('name', $this->record['name'], 255, 'inputText title', 'inputTextError, title');
		$this->frm->addMultiCheckbox('type', $values, $checked);
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
		$this->frm->addText('street', $this->record['street']);
		$this->frm->addText('number', $this->record['number']);
		$this->frm->addText('zip', $this->record['zip']);
		$this->frm->addText('city', $this->record['city']);
		$this->frm->addDropdown('country', SpoonLocale::getCountries(BL::getInterfaceLanguage()), $this->record['country']);
		$this->frm->addText('tel', $this->record['tel']);
		$this->frm->addText('fax', $this->record['fax']);
		$this->frm->addText('email', $this->record['email']);
		$this->frm->addText('site', $this->record['site']);
		$this->frm->addImage('avatar');
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
				$item['id'] = $this->id;
				$item['name'] = $this->frm->getField('name')->getValue();
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
				$item['edited_on'] = BackendModel::getUTCDate();

				// create array item with all brands in
				$values = array();
				foreach($this->brands as $value)
				{
					// if checkbox is checked save id in array values
					if(in_array($value['id'], (array) $this->frm->getField('type')->getValue())) $values[] = $value['id'];
				}

				//$item['brands'] =  implode(";", $values);
				//$item['brands'] = ";".$item['brands'].";";

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
					$filename = rand(0,3) . '_' . SpoonFilter::urlise($item['name']) . '.' . $this->frm->getField('avatar')->getExtension();

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

				// update the dealer
				BackendDealerModel::updateDealer($item);
				BackendDealerModel::updateBrandsForDealer($this->id, $values);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_edit', array('item' => $item));

				// everything has been saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=edited&var=' . urlencode($item['name']) . '&highlight=row-' . $item['id']);
			}
		}
	}
}

