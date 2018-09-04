<?php
class N2SSPluginTypeCarousel extends N2SSPluginSliderType {

	protected $name = 'carousel';

	public $ordering = 3;

	public function getPath() {
		return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
	}

	public function getLabel() {
		return n2_x('Carousel', 'Slider type');
	}

	public function renderFields($form) {

		$settings = new N2Tab($form, 'carousel-slider-type', n2_('Carousel slider type') . ' - ' . n2_('Settings'));


		$slideSize = new N2ElementGroup($settings, 'carousel-slide-size', n2_('Slide size'));
		new N2ElementNumberAutocomplete($slideSize, 'slide-width', n2_('Width'), 600, array(
			'values' => array(
				400,
				600,
				800,
				1000
			),
			'unit'   => 'px',
			'style'  => 'width:30px;'
		));
		new N2ElementNumberAutocomplete($slideSize, 'slide-height', n2_('Height'), 400, array(
			'values' => array(
				300,
				400,
				600,
				800,
				1000
			),
			'unit'   => 'px',
			'style'  => 'width:30px;'
		));


		new N2ElementNumberAutocomplete($settings, 'maximum-pane-width', n2_('Maximum pane width'), 3000, array(
			'values' => array(
				300,
				600,
				980,
				3000
			),
			'unit'   => 'px',
			'style'  => 'width:30px;',
			'tip'    => n2_('Maximum width, where the slides are visible.')
		));

		new N2ElementNumberAutocomplete($settings, 'minimum-slide-gap', n2_('Minimum slide distance'), 10, array(
			'values' => array(
				10,
				50,
				100,
				200
			),
			'unit'   => 'px',
			'style'  => 'width:30px;',
			'tip'    => n2_('Maximum width, where the slides are visible.')
		));

		$backgroundImage = new N2ElementGroup($settings, 'slider-background-image', n2_('Slider background image'));
		new N2ElementColor($backgroundImage, 'background-color', n2_('Color'), 'dee3e6ff', array(
			'alpha' => true
		));
		new N2ElementImage($backgroundImage, 'background', n2_('Image'), '', array(
			'relatedFields' => array(
				'background-fixed',
				'background-size'
			)
		));
		new N2ElementOnOff($backgroundImage, 'background-fixed', n2_('Fixed'), 0);
		new N2ElementTextAutocomplete($backgroundImage, 'background-size', n2_('Size'), 'cover', array(
			'rowClass' => 'n2-expert',
			'values'   => array(
				'cover',
				'contain',
				'auto'
			)
		));


		$border = new N2ElementGroup($settings, 'slider-border', n2_('Slider border'), array(
			'rowClass' => 'n2-expert'
		));
		new N2ElementNumber($border, 'border-width', n2_('Width'), 0, array(
			'unit'  => 'px',
			'style' => 'width:30px;'
		));
		new N2ElementColor($border, 'border-color', n2_('Color'), '3E3E3Eff', array(
			'alpha' => true
		));
		new N2ElementNumber($border, 'border-radius', n2_('Border radius'), 0, array(
			'unit'  => 'px',
			'style' => 'width:30px;'
		));	

		$slide = new N2ElementGroup($settings, 'slide-style', n2_('Slide'), array(
			'rowClass' => 'n2-expert'
		));
		new N2ElementColor($slide, 'slide-background-color', n2_('Color'), 'ffffffff', array(
			'alpha' => true
		));
		new N2ElementNumber($slide, 'slide-border-radius', n2_('Border radius'), 0, array(
			'style' => 'width:30px;',
			'unit'  => 'px'
		));

		$animationSettings = new N2Tab($form, 'carousel-slider-type-animation', n2_('Carousel slider type') . ' - ' . n2_('Animation'));

		new N2ElementRadio($animationSettings, 'animation', n2_('Main animation'), 'horizontal', array(
			'options'  => array(
				'no'         => n2_('No'),
				'horizontal' => n2_('Horizontal'),
				'vertical'   => n2_('Vertical'),
				'fade'       => n2_('Fade')
			),
			'rowClass' => 'n2-expert'
		));


		$mainAnimationProperties = new N2ElementGroup($animationSettings, 'slider-main-animation', n2_('Main animation properties'));

		new N2ElementNumberAutocomplete($mainAnimationProperties, 'animation-duration', n2_('Duration'), 800, array(
			'values' => array(
				800,
				1500,
				2000
			),
			'unit'   => 'ms',
			'style'  => 'width:35px;'
		));

		new N2ElementNumber($mainAnimationProperties, 'animation-delay', n2_('Delay'), 0, array(
			'unit'     => 'ms',
			'style'    => 'width:35px;',
			'rowClass' => 'n2-expert'
		));

		new N2ElementEasing($mainAnimationProperties, 'animation-easing', n2_('Easing'), 'easeOutQuad', array(
			'rowClass' => 'n2-expert'
		));


		new N2ElementOnOff($animationSettings, 'carousel', n2_('Carousel'), 1, array(
			'tip'      => n2_('If you turn off this option, you can\'t switch to the first slide from the last one.'),
			'rowClass' => 'n2-expert'
		));

		$singleSwitch = new N2ElementGroup($animationSettings, 'single-switch-group', n2_('Single switch'));
		new N2ElementOnOff($singleSwitch, 'single-switch', n2_('Enabled'), 0, array(
			'tip'           => n2_('It switches one slide instead of moving all the visible slides.'),
			'relatedFields' => array(
				'slider-side-spacing'
			)
		));

		new N2ElementOnOff($singleSwitch, 'slider-side-spacing', n2_('Slider side spacing'), 1);

		new N2ElementOnOff($animationSettings, 'carousel-dynamic-slider-height', n2_('Dynamic slider height'), 0);

	}

	public function export($export, $slider) {
		$export->addImage($slider['params']->get('background', ''));
	}

	public function import($import, $slider) {

		$slider['params']->set('background', $import->fixImage($slider['params']->get('background', '')));
	}
}

N2SSPluginSliderType::addSliderType(new N2SSPluginTypeCarousel);
