<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;
$adoTotal       = 0;
$stlTotal       = 0;
$stlInstTotal   = 0;
$adoInstTotal   = 0;
$adosUpdateTotal= 0;
$stlsUpdateTotal= 0;
if($adoModels = JModelLegacy::getInstance('AddOns', 'TZ_Portfolio_PlusModel')) {
    $adoInstTotal   = $adoModels -> getTotal();
    if($adosUpdate = $adoModels -> getItemsUpdate()) {
        $adosUpdateTotal = count($adosUpdate);
    }
}
if($adoModel = JModelLegacy::getInstance('AddOn', 'TZ_Portfolio_PlusModel')) {
    $addon      = $adoModel->getItemsFromServer();
    $adoTotal   = $adoModel->getState('list.total', 0);
}
if($stlModel = JModelLegacy::getInstance('Template', 'TZ_Portfolio_PlusModel')) {
    $style      = $stlModel->getItemsFromServer();
    $stlTotal   = $stlModel->getState('list.total', 0);
}
if($stlModels = JModelLegacy::getInstance('Templates', 'TZ_Portfolio_PlusModel')) {
    $stlInstTotal   = $stlModels -> getTotal();
    if($stlsUpdate = $stlModels -> getItemsUpdate()) {
        $stlsUpdateTotal = count($stlsUpdate);
    }
}
?>
<div class="tp-statistic">
    <?php echo JHtml::_('tzbootstrap.addrow');?>
        <div class="span6 col-md-6">
            <div class="tp-widget">
                <h4 class="title text-uppercase"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON_STATISTICS'); ?></h4>
                <ul class="inside">
                    <li>
                        <span class="name"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_STATISTIC_TOTAL',
                                JText::_('COM_TZ_PORTFOLIO_PLUS_ADDONS'))?>:</span>
                        <span class="value badge badge-info"><?php echo $adoTotal - 1
                                + TZ_Portfolio_PlusHelperAddons::getTotal(array('protected' => 1)); ?></span>
                    </li>
                    <li>
                        <span class="name"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_STATISTIC_TOTAL_INSTALLED',
                                JText::_('COM_TZ_PORTFOLIO_PLUS_ADDONS'))?>:</span>
                        <a href="index.php?option=com_tz_portfolio_plus&view=addons" class="value badge badge-success"><?php
                            echo $adoInstTotal;?></a>
                    </li>
                    <li>
                        <span class="name"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_STATISTIC_TOTAL_NEED_UPDATE',
                                JText::_('COM_TZ_PORTFOLIO_PLUS_ADDONS'))?>:</span>
                        <a href="index.php?option=com_tz_portfolio_plus&view=addon&layout=upload" class="value badge badge-important"><?php
                            echo $adosUpdateTotal; ?></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="span6 col-md-6">
            <div class="tp-widget">
                <h4 class="title text-uppercase"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_STYLE_STATISTICS'); ?></h4>
                <ul class="inside">
                    <li>
                        <span class="name"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_STATISTIC_TOTAL',
                                JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATES'))?>:</span>
                        <span class="value badge badge-info"><?php echo $stlTotal
                                + TZ_Portfolio_PlusHelperTemplates::getTotal(array('protected' => 1)); ?></span>
                    </li>
                    <li>
                        <span class="name"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_STATISTIC_TOTAL_INSTALLED',
                                JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATES'))?>:</span>
                        <a href="index.php?option=com_tz_portfolio_plus&view=templates" class="value badge badge-success"><?php
                            echo $stlInstTotal;?></a>
                    </li>
                    <li>
                        <span class="name"><?php echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_STATISTIC_TOTAL_NEED_UPDATE',
                                JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATES'))?>:</span>
                        <a href="index.php?option=com_tz_portfolio_plus&view=template&layout=upload" class="value badge badge-important badge-danger"><?php
                            echo $stlsUpdateTotal; ?></a>
                    </li>
                </ul>
            </div>
        </div>
    <?php echo JHtml::_('tzbootstrap.endrow');?>
</div>


