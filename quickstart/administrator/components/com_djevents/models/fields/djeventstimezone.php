<?php
/**
 * @package DJ-Events
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('timezone');

/**
 * Form Field class for the Joomla Platform.
 *
 * @since  11.1
 */
class JFormFieldDJEventsTimezone extends JFormFieldTimezone
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Timezone';

	/**
	 * Method to get the time zone field option groups.
	 *
	 * @return  array  The field option objects as a nested array in groups.
	 *
	 * @since   11.1
	 */
	protected function getGroups()
	{
		$groups = array();
		
		$keyField = !empty($this->keyField) ? $this->keyField : 'id';
		$keyValue = $this->form->getValue($keyField);

		// If the timezone is not set use the server setting.
		if (strlen($this->value) == 0 && empty($keyValue))
		{
			$this->value = JFactory::getConfig()->get('offset');
		}

		// Get the list of time zones from the server.
		$zones = DateTimeZone::listIdentifiers();
		
		// Build the group lists.
		foreach ($zones as $zone)
		{
			// Time zones not in a group we will ignore.
			if (strpos($zone, '/') === false)
			{
				continue;
			}

			// Get the group/locale from the timezone.
			list ($group, $locale) = explode('/', $zone, 2);

			// Only use known groups.
			if (in_array($group, self::$zones))
			{
				// Initialize the group if necessary.
				if (!isset($groups[$group]))
				{
					$groups[$group] = array();
				}

				// Only add options where a locale exists.
				if (!empty($locale))
				{
					$offset = JFactory::getDate('now', $zone)->getOffsetFromGmt();
					$locale .= ' (UTC' . ($offset >= 0 ? '+'.floor($offset/3600).':'.str_pad(abs($offset%3600/60), 2, 0, STR_PAD_LEFT) : floor($offset/3600).':'.str_pad(abs($offset%3600/60), 2, 0, STR_PAD_LEFT)) .')';
					$groups[$group][$zone] = JHtml::_('select.option', $zone, str_replace('_', ' ', $locale), 'value', 'text', false);
				}
			}
		}

		// Sort the group lists.
		ksort($groups);

		foreach ($groups as &$location)
		{
			sort($location);
		}

		// Merge any additional groups in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);
		
		// default timezone
		$group = JText::_('COM_DJEVENTS_DEFAULT_TIMEZONE');
		$zone = JFactory::getConfig()->get('offset');
		$offset = JFactory::getDate('now', $zone)->getOffsetFromGmt();
		$locale = $zone .' (UTC' . ($offset >= 0 ? '+'.floor($offset/3600).':'.str_pad(abs($offset%3600/60), 2, 0, STR_PAD_LEFT) : floor($offset/3600).':'.str_pad(abs($offset%3600/60), 2, 0, STR_PAD_LEFT)) .')';
		$default = array($group => array(JHtml::_('select.option', $zone, $locale, 'value', 'text', false)));
		
		$groups = array_merge($default, $groups);
		
		//JFactory::getApplication()->enqueueMessage('<pre>' . print_r($groups, true) . '</pre>');
		return $groups;
	}
}
