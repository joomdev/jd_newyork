<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorEventsBooking extends N2SliderGeneratorPluginAbstract {

	protected $name = 'eventsbooking';

	protected $url = 'https://extensions.joomla.org/extension/event-booking/';

	public function getLabel() {
		return 'Event Booking';
	}

	public function isInstalled() {
		return N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_eventbooking' . DIRECTORY_SEPARATOR . 'eventbooking.php');
	}

	protected function loadSources() {
		new N2GeneratorEventsBookingEvents($this, 'events', n2_('Events'));
	}

	public function getPath() {
		return dirname(__FILE__) . DIRECTORY_SEPARATOR;
	}
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorEventsBooking);
