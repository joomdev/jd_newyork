<?php
/**
 * @package   JD Progress Bar
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2019 JoomDev.
 * @license   GNU/GPLv2 and later
 */
// no direct access
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//jimport('joomla.form.formfield');

class JFormFieldJDHeading extends JFormField {

   protected $type = 'jdheading';

   public function getInput() {
   
    	//  $layout = new JLayoutFile('jdheading', JPATH_ROOT . '/modules/mod_jdprogressbar/fields/layouts');
      //return $layout->render(['field' => $this,'element'=> $this->element]);
      return '';
   }

	public function getLabel()
	{
		$html = array();
		$class = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$html[] = '<span class="spacer">';
		$html[] = '<span' . $class . '>';

		if ((string) $this->element['hr'] == 'true')
		{
			$html[] = '<hr' . $class . ' />';
		}
		else
		{
			$label = '';

			// Get the label text from the XML element, defaulting to the element name.
			$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
			$text = $this->translateLabel ? JText::_($text) : $text;

			// Build the class for the label.
			$class = !empty($this->description) ? 'hasPopover' : '';
			$class = $this->required == true ? $class . ' required' : $class;

			// Add the opening label tag and main attributes attributes.
			$label .= '<h2 id="' . $this->id . '-lbl" class="' . $class . '"';

			// If a description is specified, use it to build a tooltip.
			if (!empty($this->description))
			{
				JHtml::_('bootstrap.popover');
				$label .= ' title="' . htmlspecialchars(trim($text, ':'), ENT_COMPAT, 'UTF-8') . '"';
				$label .= ' data-content="' . htmlspecialchars(
					$this->translateDescription ? JText::_($this->description) : $this->description,
					ENT_COMPAT,
					'UTF-8'
				) . '"';

				if (JFactory::getLanguage()->isRtl())
				{
					$label .= ' data-placement="left"';
				}
         }
         
         $icon = ((string) $this->element['icon']) ? '<span class="'.$this->element['icon'].'" style="font-size:20px;"> </span>   ' :  '';  

			// Add the label text and closing tag.
			$label .= '>'. $icon . $text . '</h2>';
			$html[] = $label;
		}
      $html[] = '<hr style=" margin: 5px 0;
      border: 0;
      border-top: 1px solid #eee;
      border-bottom: 1px solid #fff">';
		$html[] = '</span>';
		$html[] = '</span>';

		return implode('', $html);
   }
}