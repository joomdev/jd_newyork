<?php
// Check to ensure this file is included in Joomla!
// Licensed under the GPL v3
defined('_JEXEC') or die('Restricted access');
extract($displayData);

$document = JFactory::getDocument();
$style = '.jd-avatar-input label{'
        . 'display: inline-block;margin-right: 4px;'
        . '}'
        . '.jd-avatar-input label input[type=radio]:checked ~ img{opacity: 1}'
        . '.jd-avatar-input label img{opacity: 0.5}';
$document->addStyleDeclaration($style);
?>
<div class="">
  <h2>Something</h2>
</div>
