<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die();

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
$doc    = JFactory::getDocument();
$app        = JFactory::getApplication('site');
$input      = $app -> input;
$params = &$this -> params;

$tzTemplate = TZ_Portfolio_PlusTemplate::getTemplate(true);
$tplParams = $tzTemplate->params;
$ratio      =   $tplParams->get('ratio','5:3');
$bgcolor    =   $tplParams->get('bgcolor','rgba(0, 0, 0, 0.5)');
$textcolor  =   $tplParams->get('textcolor','#ffffff');
list($rwidth,$rheight)  =   explode(':', $ratio);
$doc -> addStyleSheet('components/com_tz_portfolio_plus/css/jquery.fancybox.min.css');
$doc -> addScript('components/com_tz_portfolio_plus/js/jquery.fancybox.min.js');
$doc -> addScript(TZ_Portfolio_PlusUri::base(true).'/templates/besley/js/template.min.js');
$doc -> addScript(TZ_Portfolio_PlusUri::base(true).'/templates/besley/js/lightbox.min.js');
$doc -> addScriptDeclaration('besley('.$this->params.','.$rwidth.','.$rheight.');');
$doc -> addStyleDeclaration (
        '.tplBesley .element .TzInner .TzArticleMedia > div .ImageOverlayMg {background-color:'.$bgcolor.';color:'.$textcolor.'}'
        .'.tplBesley .element .TzInner .TzArticleMedia > div .besley-title .TzPortfolioTitle a {color:'.$textcolor.' !important}'
);
?>

<?php if($this -> items):?>

    <?php
    $params = &$this -> params;
    $bootstrap4 = ($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4);

    $bootstrapClass = '';
    if($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4){
        $bootstrapClass = 'tpp-bootstrap ';
    }elseif($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 3){
        $bootstrapClass = 'tzpp_bootstrap3 ';
    }
?>
<div id="tpPortfolio" class="<?php echo $bootstrapClass;?>tplBesley<?php echo $this->pageclass_sfx;?>">
    <?php if ($params->get('show_page_heading', 1)) : ?>
        <h1 class="page-heading">
            <?php echo $this->escape($params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>

    <?php
    // Display category about when the portfolio has filter category by category id
    echo $this -> loadTemplate('category_about');
    ?>

    <?php
    // Display tag about when the portfolio has filter tag by tag id
    echo $this -> loadTemplate('tag_about');
    ?>

    <?php
    // Display author about when the portfolio has filter user by user id
    echo $this -> loadTemplate('author_about');
    ?>

    <?php if($params -> get('use_filter_first_letter',0)):?>
        <div class="TzLetters">
            <?php echo $this -> loadTemplate('letters');?>
        </div>
    <?php endif;?>

    <div id="tz_options" class="clearfix">
        <?php if($params -> get('tz_show_filter',1)):?>
            <div class="option-combo">
                <div id="filter" class="option-set clearfix" data-option-key="filter">
                    <a href="#show-all" data-option-value="*" class="btn btn-default btn-outline-secondary selected"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SHOW_ALL');?></a>
                    <?php if($params -> get('tz_filter_type','tags') == 'tags'):?>
                        <?php echo $this -> loadTemplate('filter_tags');?>
                    <?php endif;?>
                    <?php if($params -> get('tz_filter_type','tags') == 'categories'):?>
                        <?php echo $this -> loadTemplate('filter_categories');?>
                    <?php endif;?>
                </div>
            </div>
        <?php endif;?>

        <?php if($params -> get('show_sort',0) AND $sortfields = $params -> get('sort_fields',array('date','hits','title'))):
            $sort   = $params -> get('orderby_sec','rdate');
            ?>
            <div class="option-combo">
                <div class="filter-title"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_SORT')?></div>

                <div id="sort" class="option-set clearfix" data-option-key="sortBy">
                    <?php
                    foreach($sortfields as $sortfield):
                        switch($sortfield):
                            case 'title':
                                ?>
                                <a class="btn btn-default btn-sm<?php echo ($sort == 'alpha' || $sort == 'ralpha')?' selected':''?>"
                                   href="#title" data-option-value="name"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_TITLE');?></a>
                                <?php
                                break;
                            case 'date':
                                ?>
                                <a class="btn btn-default btn-sm<?php echo ($sort == 'date' || $sort == 'rdate')?' selected':''?>"
                                   href="#date" data-option-value="date"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_DATE');?></a>
                                <?php
                                break;
                            case 'hits':
                                ?>
                                <a class="btn btn-default btn-sm<?php echo ($sort == 'hits' || $sort == 'rhits')?' selected':''?>"
                                   href="#hits" data-option-value="hits"><?php echo JText::_('JGLOBAL_HITS');?></a>
                                <?php
                                break;
                        endswitch;
                    endforeach;
                    ?>
                </div>
            </div>
        <?php endif;?>

        <?php if($params -> get('show_layout',0)):?>
            <div class="option-combo">
                <div class="filter-title"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LAYOUT');?></div>
                <div id="layouts" class="option-set clearfix" data-option-key="layoutMode">
                    <?php
                    if(count($params -> get('layout_type',array('masonry','fitRows','straightDown')))>0):
                        foreach($params -> get('layout_type',array('masonry','fitRows','straightDown')) as $i => $param):
                            ?>
                            <a class="btn btn-default btn-sm<?php if($i == 0) echo ' selected';?>" href="#<?php echo $param?>" data-option-value="<?php echo $param?>">
                                <?php echo $param?>
                            </a>
                        <?php endforeach;?>
                    <?php endif;?>
                </div>
            </div>
        <?php endif;?>

        <?php if($params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'default'):?>
            <?php if($params -> get('show_limit_box',1)):?>
                <div class="TzShow">
                    <span class="title"><?php echo strtoupper(JText::_('JSHOW'));?></span>
                    <form name="adminForm" method="post" id="TzShowItems"
                          action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=portfolio&Itemid='.$this -> Itemid);?>">
                        <?php echo $this -> pagination -> getLimitBox();?>
                    </form>
                </div>
            <?php endif;?>
        <?php endif;?>
    </div>

    <div id="portfolio" class="super-list variable-sizes clearfix"
         itemscope itemtype="http://schema.org/Blog">
        <?php echo $this -> loadTemplate('item');?>
    </div>

    <?php if($params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'default'):?>
        <?php if (($params->def('show_pagination', 1) == 1  || ($params->get('show_pagination', 1) == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
            <div class="pagination">
                <?php  if ($params->def('show_pagination_results', 1)) : ?>
                    <p class="counter">
                        <?php echo $this->pagination->getPagesCounter(); ?>
                    </p>
                <?php endif; ?>

                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        <?php endif;?>
    <?php endif;?>

    <?php if($params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'ajaxButton'
        || $params -> get('tz_portfolio_plus_layout', 'ajaxButton') == 'ajaxInfiScroll'):?>
        <?php echo $this -> loadTemplate('infinite_scroll');?>
    <?php endif;?>


</div>
<?php
endif;