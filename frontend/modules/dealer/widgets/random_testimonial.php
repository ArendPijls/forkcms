<?php

/**
 * Show a random testimonial.
 *
 * @author Jan Moesen <jan.moesen@netlash.com>
 */
class FrontendTestimonialsWidgetRandomTestimonial extends FrontendBaseWidget
{
	/**
	 * Execute the extra.
	 */
	public function execute()
	{
		// call parent
		parent::execute();

		// load template
		$this->loadTemplate();

		// parse
		$this->parse();
	}

	/**
	 * Parse the template.
	 */
	private function parse()
	{
		// assign the random testimonial
		if(Spoon::exists('usedRandomIds')) $usedRandomIds = (array) Spoon::get('usedRandomIds');
		else $usedRandomIds = array();
		$randomTestimonial = FrontendTestimonialsModel::getRandom($usedRandomIds);
		$this->tpl->assign('widgetTestimonialsRandomTestimonial', $randomTestimonial);
		if(!empty($randomTestimonial['id'])) $usedRandomIds[] = $randomTestimonial['id'];
		Spoon::set('usedRandomIds', $usedRandomIds);
	}
}
