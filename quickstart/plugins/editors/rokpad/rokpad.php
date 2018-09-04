<?php
/**
 * @version   $Id: rokpad.php 16824 2013-12-12 03:04:52Z djamil $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2018 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('_JEXEC') or die;

/**
 *
 */
class plgEditorRokPad extends JPlugin
{

	/**
	 * @var bool
	 */
	protected static $_assets = false;
	/**
	 * @var string
	 */
	protected $_version = '2.1.10';
	/**
	 * @var string
	 */
	protected $_basepath = '/plugins/editors/rokpad/';
	/**
	 * @var
	 */
	protected $_acepath;

	/**
	 * @param $subject
	 * @param $config
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * @return string
	 */
	public function onInit()
	{
		JHtml::_('behavior.framework', true);
		$document = JFactory::getDocument();

		$this->_basepath = JURI::root(true) . $this->_basepath;
		$this->_acepath  = $this->_basepath . 'ace/';

		if (!self::$_assets) {
			/*
			$document->addStyleSheet($this->_basepath . 'assets/css/rokpad.css'.$this->_appendCacheToken());
			$document->addScript($this->_acepath . 'ace.js'.$this->_appendCacheToken());
			$document->addScript($this->_basepath . 'assets/js/rokpad.js'.$this->_appendCacheToken());
			*/


			$document->addScriptDeclaration($this->getJSParams());
			$this->compileLess();
			$this->compileJS();

			self::$_assets = true;

		}

		return '';
	}

	/**
	 * @param $id
	 *
	 * @return string
	 */
	public function onSave($id)
	{
		return "RokPadData.insertion.onSave('".$id."');\n";
	}

	/**
	 * @param $id
	 *
	 * @return string
	 */
	public function onGetContent($id)
	{
		return "RokPadData.insertion.onGetContent('".$id."');\n";
	}

	/**
	 * @param $id
	 * @param $content
	 *
	 * @return string
	 */
	public function onSetContent($id, $content)
	{
		return "RokPadData.insertion.onSetContent('".$id."', ".$content.");\n";
	}

	/**
	 * @return bool
	 */
	public function onGetInsertMethod()
	{
		static $done = false;

		// Do this only once.
		if (!$done) {
			$done = true;
			$doc  = JFactory::getDocument();
			$js   = "\tfunction jInsertEditorText(text, editor) {\n
					RokPadData.insertion.onGetInsertMethod(text, editor);\n
			}\n";
			$doc->addScriptDeclaration($js);
		}

		return true;
	}

	/**
	 * @param       $name
	 * @param       $content
	 * @param       $width
	 * @param       $height
	 * @param       $col
	 * @param       $row
	 * @param bool  $buttons
	 * @param null  $id
	 * @param null  $asset
	 * @param null  $author
	 * @param array $params
	 *
	 * @return string
	 */
	public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array())
	{
		if (empty($id)) $id = $name;

		$buttons = $this->_displayButtons($id, $buttons, $asset, $author);

		$html   = array();
		$html[] = '<div class="rokpad-editor-wrapper" data-rokpad-editor="' . $id . '">';
		$html[] = '	<div class="rokpad-toolbar">';
		$html[] = '		<ul class="rok-left">';
		$html[] = '			<li><div class="rok-button rok-button-primary rokpad-tip" data-original-title="Ajax Save" data-placement="below-left" data-rokpad-save><i class="rokpad-icon-save"></i> save</div></li>';
		$html[] = '			<li class="rok-buttons-group">';
		$html[] = '				<div class="rok-button rok-button-disabled rokpad-tip" data-original-title="Undo" data-placement="below" data-rokpad-undo><i class="rokpad-icon-undo"></i></div>';
		$html[] = '				<div class="rok-button rok-button-disabled rokpad-tip" data-original-title="Redo" data-placement="below" data-rokpad-redo><i class="rokpad-icon-redo"></i></div>';
		$html[] = '			</li>';
		$html[] = '			<li><div class="rok-button rokpad-tip" data-original-title="Find..." data-placement="below" data-rokpad-find><i class="rokpad-icon-search"></i></div></li>';
		$html[] = '			<li>';
		$html[] = '				<div class="rok-dropdown-group">';
		$html[] = '					<div class="rok-button" data-rokpad-toggle="extras"><i class="rokpad-icon-more"></i><span class="caret"></span></div>';
		$html[] = '					<ul class="rok-dropdown" data-rokpad-dropdown="extras">';
		$html[] = '						<li><a href="#" data-rokpad-goto>Goto Line...</a></li>';
		$html[] = '						<li><a href="#" data-rokpad-find-replace>Find and Replace...</a></li>';
		$html[] = '						<li class="divider"></li>';
		$html[] = '						<li><a href="#" data-rokpad-beautify>Beautify HTML</a></li>';
		$html[] = '					</ul>';
		$html[] = '				</div>';
		$html[] = '			</li>';
		$html[] = '		</ul>';
		$html[] = '		<ul class="rok-right">';
		$html[] = '			<li>';
		$html[] = '				<div class="rok-popover-group">';
		$html[] = '					<div class="rok-button rokpad-tip" data-original-title="Editor Settings" data-placement="below" data-rokpad-toggle="settings"><i class="rokpad-icon-settings"></i></div>';
		$html[] = '					<div class="rok-popover" data-rokpad-popover="settings">';
		$html[] = '						<ul class="options">';
		$html[] = '							<li><span class="title">Theme</span><span class="input"><select data-rokpad-options="theme" class="chzn-done"></select></span></li>';
		$html[] = '							<li><span class="title">Font Size</span><span class="input"><select data-rokpad-options="font-size" class="chzn-done"></select></span></li>';
		$html[] = '							<li><span class="title">Code Folding</span><span class="input"><select data-rokpad-options="fold-style" class="chzn-done"><option value="manual">Manual</option><option value="markbegin">Mark Begin</option><option value="markbeginend">Mark Begin and End</option></select></span></li>';
		$html[] = '							<li><span class="title">Soft Wrap</span><span class="input"><select data-rokpad-options="use-wrap-mode" class="chzn-done"><option value="off">Off</option><option value="40">40 Chars</option><option value="80">80 Chars</option><option value="free">Free</option></select></span></li>';
		$html[] = '							<li><span class="title-checkbox">Full Line Selection</span><span class="input"><input type="checkbox" data-rokpad-options="selection-style" /></span></li>';
		$html[] = '							<li><span class="title-checkbox">Highlight Active Line</span><span class="input"><input type="checkbox" data-rokpad-options="highlight-active-line" /></span></li>';
		$html[] = '							<li><span class="title-checkbox">Show Invisibles</span><span class="input"><input type="checkbox" data-rokpad-options="show-invisibles" /></span></li>';
		$html[] = '							<li><span class="title-checkbox">Show Gutter</span><span class="input"><input type="checkbox" data-rokpad-options="show-gutter" /></span></li>';
		$html[] = '							<li><span class="title-checkbox">Show Print Margin</span><span class="input"><input type="checkbox" data-rokpad-options="show-print-margin" /></span></li>';
		$html[] = '							<li><span class="title-checkbox">Highlight Selected Word</span><span class="input"><input type="checkbox" data-rokpad-options="highlight-selected-word" /></span></li>';
		$html[] = '							<li><span class="title-checkbox">Autohide Fold Widgets</span><span class="input"><input type="checkbox" data-rokpad-options="fade-fold-widgets" /></span></li>';
		$html[] = '							<li><span class="title-checkbox">Autosave</span><span class="input"><input type="checkbox" data-rokpad-options="autosave-enabled" /> <input type="text" data-rokpad-options="autosave-time" /> mins</span></li>';
		$html[] = '						</ul>';
		$html[] = '						<div class="rok-popover-arrow"></div>';
		$html[] = '					</div>';
		$html[] = '				</div>';
		$html[] = '			</li>';
		$html[] = '			<li>';
		$html[] = '				<div class="rok-popover-group">';
		$html[] = '					<div class="rok-button rokpad-tip" data-original-title="Keyboard Shortcuts" data-placement="below" data-rokpad-toggle="keyboard"><i class="rokpad-icon-keyboard"></i></div>';
		$html[] = '					<div class="rok-popover rok-popover-keyboard" data-rokpad-popover="keyboard">';
		$html[] = '						<ul class="keyboard">';
		$html[] = '							<li class="rokpad-keyboard-mac">';
		$html[]	= '								<span class="rokpad-kbd-win"></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">L</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Center selection</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">U</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">U</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Change to uppser case</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">U</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">U</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Change to lower case</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">ALT</span> + <span class="rokpad-key">&darr;</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">OPT</span> + <span class="rokpad-key">&darr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Copy lines down</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">ALT</span> + <span class="rokpad-key">&uarr;</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">OPT</span> + <span class="rokpad-key">&uarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Copy lines up</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">S</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">S</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Save</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">F</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">F</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Find</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">E</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">E</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Use selection for find</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">K</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">G</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Find next</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">K</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">G</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Find previous</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">ALT</span> + <span class="rokpad-key">0</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">OPT</span> + <span class="rokpad-key">0</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Fold all</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">ALT</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">0</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">OPT</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">0</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Unfold all</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">&darr;</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&darr;</span> <br /><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">N</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Go line down</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">&uarr;</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&uarr;</span> <br /><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">P</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Go line up</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">END</span> <br /><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">&darr;</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984</span> + <span class="rokpad-key">END</span> <br /><span class="rokpad-key">&#8984</span> + <span class="rokpad-key">&darr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Go to end</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">&larr;</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&larr;</span> <br /><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">B</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Go to left</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">L</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">L</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Goto line</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">ALT</span> + <span class="rokpad-key">&rarr;</span> <br /><span class="rokpad-key">END</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">&rarr;</span> <br /><span class="rokpad-key">END</span> <br /><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">E</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Goto to line end</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">ALT</span> + <span class="rokpad-key">&larr;</span> <br /><span class="rokpad-key">HOME</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">&larr;</span> <br /><span class="rokpad-key">HOME</span> <br /><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">A</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Goto to line start</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">PAGEDOWN</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">OPT</span> + <span class="rokpad-key">PAGEDOWN</span> <br /><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">V</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Goto to page down</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">PAGEUP</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">OPT</span> + <span class="rokpad-key">PAGEUP</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Goto to page up</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">&rarr;</span></span>';
		$html[]	= '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&rarr;</span> <br /><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">F</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Go to right</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">HOME</span> <br /><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">&uarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">HOME</span> <br /><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">&uarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Go to start</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">&larr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">OPT</span> + <span class="rokpad-key">&larr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Go to word left</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">&rarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">OPT</span> + <span class="rokpad-key">&rarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Go to word right</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">TAB</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">TAB</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Indent</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">ALT</span> + <span class="rokpad-key">&darr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">OPT</span> + <span class="rokpad-key">&darr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Move lines down</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">ALT</span> + <span class="rokpad-key">&uarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">OPT</span> + <span class="rokpad-key">&uarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Move lines up</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">TAB</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">TAB</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Outdent</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">INS</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">INSERT</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Overwrite</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac">';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">PAGEDOWN</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Pagedown</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac">';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">PAGEUP</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Pageup</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">Z</span> <br /><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">Y</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">Z</span> <br /><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">Y</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Redo</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">D</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">D</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Remove line</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac">';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">K</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Remove to line end</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac">';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">BACKSPACE</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Remove to line start</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac">';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">ALT</span> + <span class="rokpad-key">BACKSPACE</span> <br /> <span class="rokpad-key">CTRL</span> + <span class="rokpad-key">ALT</span> + <span class="rokpad-key">BACKSPACE</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Remove word left</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac">';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">ALT</span> + <span class="rokpad-key">DELETE</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Remove word right</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">A</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">A</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select all</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&darr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&darr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select down</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&larr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&larr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select left</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">END</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">END</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select line end</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">HOME</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">HOME</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select line start</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">PAGEDOWN</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">PAGEDOWN</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select page down</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">PAGEUP</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">PAGEUP</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select page up</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&rarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&rarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select right</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">END</span> <br /> <span class="rokpad-key">ALT</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&darr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&darr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select to end</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">ALT</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&rarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&rarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select to line end</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">ALT</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&larr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&larr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select to line start</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">HOME</span> <br /> <span class="rokpad-key">ALT</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&uarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&uarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select to start</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&uarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&uarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select up</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&larr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">OPT</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&larr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select word left</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&rarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">OPT</span> + <span class="rokpad-key">SHIFT</span> + <span class="rokpad-key">&rarr;</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Select word right</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac">';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">O</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Split line</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">7</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">7</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Toggle comment</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">T</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">T</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Transpose letters</span>';
		$html[] = '							</li>';
		$html[] = '							<li class="rokpad-keyboard-mac rokpad-keyboard-win">';
		$html[] = '								<span class="rokpad-kbd-win"><span class="rokpad-key">CTRL</span> + <span class="rokpad-key">Z</span></span>';
		$html[] = '								<span class="rokpad-kbd-mac"><span class="rokpad-key">&#8984;</span> + <span class="rokpad-key">Z</span></span>';
		$html[] = '								<span class="rokpad-kbd-desc">Undo</span>';
		$html[] = '							</li>';
		$html[] = '						</ul>';
		$html[] = '						<div class="rok-popover-arrow"></div>';
		$html[] = '					</div>';
		$html[] = '				</div>';
		$html[] = '			</li>';
		$html[] = '			<li><div class="rok-button rokpad-tip" data-original-title="Fullscreen / Windowed" data-placement="below-right" data-rokpad-fullscreen><i class="rokpad-icon-fullscreen"></i></div></li>';
		//$html[] = '			<li><div class="rok-button rok-button-red"></div></li>';
		//$html[] = '			<li><div class="rok-button rok-button-black"></div></li>';
		$html[] = '		</ul>';
		$html[] = '	</div>';
		$html[] = '	<div class="rokpad-shortcodes">';
		$html[] = '		<ul>';
		$html[] = '			<li data-rokpad-shortcode="<h1>{data}{cur}</h1>" class="rokpad-tip" data-original-title="Heading 1" data-placement="below-left"><i class="rokpad-icon-h1"></i></li>';
		$html[] = '			<li data-rokpad-shortcode="<h2>{data}{cur}</h2>" class="rokpad-tip" data-original-title="Heading 2" data-placement="below"><i class="rokpad-icon-h2"></i></li>';
		$html[] = '			<li data-rokpad-shortcode="<h3>{data}{cur}</h3>" class="rokpad-tip" data-original-title="Heading 3" data-placement="below"><i class="rokpad-icon-h3"></i></li>';
		$html[] = '			<li data-rokpad-shortcode="<strong>{data}{cur}</strong>" class="rokpad-tip" data-original-title="Bold/Strong Text" data-placement="below"><i class="rokpad-icon-bold"></i></li>';
		$html[] = '			<li data-rokpad-shortcode="<em>{data}{cur}</em>" class="rokpad-tip" data-original-title="Emphasized Text" data-placement="below"><i class="rokpad-icon-italic"></i></li>';
		$html[] = '			<li data-rokpad-shortcode="<u>{data}{cur}</u>" class="rokpad-tip" data-original-title="Underlined Text" data-placement="below"><i class="rokpad-icon-underline"></i></li>';
		$html[] = '			<li data-rokpad-shortcode="<ol>{n}{t}<li>{data}{cur}</li>{n}</ol>" class="rokpad-tip" data-original-title="Ordered List" data-placement="below"><i class="rokpad-icon-olist"></i></li>';
		$html[] = '			<li data-rokpad-shortcode="<ul>{n}{t}<li>{data}{cur}</li>{n}</ul>" class="rokpad-tip" data-original-title="Unordered List" data-placement="below"><i class="rokpad-icon-ulist"></i></li>';
		$html[] = '			<li data-rokpad-shortcode="<img src=\'{cur}\' alt=\'{data}\' />" class="rokpad-tip" data-original-title="Image" data-placement="below"><i class="rokpad-icon-image"></i></li>';
		$html[] = '			<li data-rokpad-shortcode="<p>{data}{cur}</p>" class="rokpad-tip" data-original-title="Paragraph" data-placement="below"><i class="rokpad-icon-paragraph"></i></li>';
		$html[] = '			<li data-rokpad-shortcode="<a href=\'{cur}\'>{data}</a>" class="rokpad-tip" data-original-title="Link" data-placement="below"><i class="rokpad-icon-link"></i></li>';
		$html[] = '			<li data-rokpad-shortcode="<hr />" class="rokpad-tip" data-original-title="Horizontal Rule (<hr />)" data-placement="below"><i class="rokpad-icon-hr"></i></li>';
		$html[] = '			<li data-rokpad-shortcode="<{cur}>{data}</{cur}>" class="rokpad-tip" data-original-title="Universal Tag. Click and start typing the desired tag (ie, div). Hit ESC when done." data-placement="below"><i class="rokpad-icon-universal"></i></li>';
		$html[] = '		</ul>';
		$html[] = '	</div>';
		$html[] = '	<div class="rokpad-editor-container" data-rokpad-container>';
		$html[] = '		<div id="' . $id . '-rokpad-editor" class="rokpad-editor"></div>';
		$html[] = '		<textarea data-rokpad-original class="rokpad-editor-original" name="' . $name . '" id="' . $id . '" cols="' . $col . '" rows="' . $row . '">' . $content . '</textarea>';
		$html[] = '	</div>';
		$html[] = '	<div class="rokpad-actionbar" data-rokpad-actionbar>';
		$html[] = '		<ul>';
		$html[] = '			<li class="rokpad-column-1">';
		$html[] = '				<div class="rok-buttons-group">';
		$html[] = '					<div class="rok-button rok-button-unchecked rokpad-tip" data-original-title="Regular Expression" data-placement="above-left" data-rokpad-action-setting="regExp"><i class="rokpad-icon-regexp"></i></div>';
		$html[] = '					<div class="rok-button rok-button-unchecked rokpad-tip" data-original-title="Case Sensitive" data-placement="above" data-rokpad-action-setting="caseSensitive"><i class="rokpad-icon-casesensi"></i></div>';
		$html[] = '					<div class="rok-button rok-button-unchecked rokpad-tip" data-original-title="Whole Word" data-placement="above" data-rokpad-action-setting="wholeWord"><i class="rokpad-icon-wholeword"></i></div>';
		$html[] = '				</div>';
		$html[] = '				<div class="rok-buttons-group">';
		$html[] = '					<div class="rok-button rok-button-unchecked rokpad-tip" data-original-title="Reverse Direction" data-placement="above" data-rokpad-action-setting="backwards"><i class="rokpad-icon-reversedir"></i></div>';
		$html[] = '					<div class="rok-button rok-button-unchecked rokpad-tip" data-original-title="Wrap" data-placement="above" data-rokpad-action-setting="wrap"><i class="rokpad-icon-wrap"></i></div>';
		$html[] = '					<div class="rok-button rok-button-unchecked rokpad-tip" data-original-title="In Selection" data-placement="above" data-rokpad-action-setting="scope"><i class="rokpad-icon-inselection"></i></div>';
		$html[] = '				</div>';
		$html[] = '			</li>';
		$html[] = '			<li class="rokpad-column-2">';
		$html[] = '				<div class="rok-input-wrapper rok-input-row-1" data-rokpad-action-method="find"><input type="text" placeholder="Find..." /></div>';
		$html[] = '				<div class="rok-input-wrapper rok-input-row-1" data-rokpad-action-method="goto"><input type="text" placeholder="Goto Line..." /></div>';
		$html[] = '				<div class="rok-input-wrapper rok-input-row-2" data-rokpad-action-method="replace"><input type="text" placeholder="Replace..." /></div>';
		$html[] = '			</li>';
		$html[] = '			<li class="rokpad-column-3">';
		$html[] = '				<div class="rok-input-row-1">';
		$html[] = '					<div class="rok-buttons-group">';
		$html[] = '						<div class="rok-button rok-button-noicon" data-rokpad-action="find">Find</div>';
		$html[] = '						<div class="rok-button rok-button-noicon" data-rokpad-action="findAll">All</div>';
		$html[] = '					</div>';
		$html[] = '					<div class="rok-buttons-group">';
		$html[] = '						<div class="rok-button rokpad-tip" data-original-title="Find Previous" data-placement="above" data-rokpad-action="findPrevious"><i class="rokpad-icon-prev"></i></div>';
		$html[] = '						<div class="rok-button rokpad-tip" data-original-title="Find Next" data-placement="above-right" data-rokpad-action="findNext"><i class="rokpad-icon-next"></i></div>';
		$html[] = '					</div>';
		$html[] = '					<div class="rok-button rok-button-noicon" data-rokpad-action="goto">Goto Line</div>';
		$html[] = '				</div>';
		$html[] = '				<div class="rok-input-row-2">';
		$html[] = '					<div class="rok-buttons-group">';
		$html[] = '						<div class="rok-button rok-button-noicon" data-rokpad-action="replace">Replace</div>';
		$html[] = '						<div class="rok-button rok-button-noicon" data-rokpad-action="replaceAll">All</div>';
		$html[] = '					</div>';
		$html[] = '				</div>';
		$html[] = '			</li>';
		$html[] = '		</ul>';
		$html[] = '	</div>';
		$html[] = '	<div class="rokpad-statusbar">';
		$html[] = '		<ul data-rokpad-dropdown="mode" class="rok-dropdown">';
		$html[] = '			<li data-rokpad-mode="css"><a href="#">CSS</a></li>';
		$html[] = '			<li data-rokpad-mode="html"><a href="#">HTML</a></li>';
		$html[] = '			<li data-rokpad-mode="javascript"><a href="#">JavaScript</a></li>';
		$html[] = '			<li data-rokpad-mode="json"><a href="#">JSON</a></li>';
		$html[] = '			<li data-rokpad-mode="less"><a href="#">LESS</a></li>';
		$html[] = '			<li data-rokpad-mode="markdown"><a href="#">Markdown</a></li>';
		$html[] = '			<li data-rokpad-mode="php"><a href="#">PHP</a></li>';
		$html[] = '			<li data-rokpad-mode="sql"><a href="#">SQL</a></li>';
		$html[] = '			<li data-rokpad-mode="text"><a href="#">Plain Text</a></li>';
		$html[] = '			<li data-rokpad-mode="textile"><a href="#">Textile</a></li>';
		$html[] = '			<li data-rokpad-mode="twig"><a href="#">Twig</a></li>';
		$html[] = '			<li data-rokpad-mode="xml"><a href="#">XML</a></li>';
		$html[] = '		</ul>';
		$html[] = '		<ul data-rokpad-dropdown="tabs" class="rok-dropdown">';
		$html[] = '			<li data-rokpad-softtabs="0"><a href="#">Indent Using Spaces</a></li>';
		$html[] = '			<li class="divider"></li>';
		$html[] = '			<li data-rokpad-tabsize="1"><a href="#">Tab Width: 1</a></li>';
		$html[] = '			<li data-rokpad-tabsize="2"><a href="#">Tab Width: 2</a></li>';
		$html[] = '			<li data-rokpad-tabsize="3"><a href="#">Tab Width: 3</a></li>';
		$html[] = '			<li data-rokpad-tabsize="4"><a href="#">Tab Width: 4</a></li>';
		$html[] = '			<li data-rokpad-tabsize="5"><a href="#">Tab Width: 5</a></li>';
		$html[] = '			<li data-rokpad-tabsize="6"><a href="#">Tab Width: 6</a></li>';
		$html[] = '			<li data-rokpad-tabsize="7"><a href="#">Tab Width: 7</a></li>';
		$html[] = '			<li data-rokpad-tabsize="8"><a href="#">Tab Width: 8</a></li>';
		$html[] = '		</ul>';
		$html[] = '		<ul class="rok-left">';
		$html[] = '			<li data-rokpad-lastsave>Last save: <span data-rokpad-savedate>never</span></li>';
		$html[] = '		</ul>';
		$html[] = '		<ul class="rok-right">';
		$html[] = '			<li data-rokpad-tabsize data-rokpad-toggle="tabs">Tab Size: <span>4</span></li>';
		$html[] = '			<li class="divider"></li>';
		$html[] = '			<li data-rokpad-mode data-rokpad-toggle="mode">Mode: <span>HTML</span></li>';
		$html[] = '		</ul>';
		$html[] = '	</div>';
		$html[] = '</div>';
		$html[] = $buttons;

		$output = "";
		$output .= implode("\n", $html);

		return $output;
	}

	/**
	 * @param $name
	 * @param $buttons
	 * @param $asset
	 * @param $author
	 *
	 * @return string
	 */
	protected function _displayButtons($name, $buttons, $asset, $author)
	{
		// Load modal popup behavior
		JHtml::_('behavior.modal', 'a.modal-button');

		$args['name']  = $name;
		$args['event'] = 'onGetInsertMethod';

        $return      = '';
		$results[] = $this->update($args);
		foreach ($results as $result) {
			if (is_string($result) && trim($result)) {
                $return .= $result;
			}
		}

		if (is_array($buttons) || (is_bool($buttons) && $buttons)) {
			$results = $this->_subject->getButtons($name, $buttons, $asset, $author);

            $version = new JVersion();
            if (version_compare($version->getShortVersion(), '3.0', '>=')) {

                /*
                 * This will allow plugins to attach buttons or change the behavior on the fly using AJAX
                 */
                $return .= "\n<div id=\"editor-xtd-buttons\" class=\"btn-toolbar pull-left\">\n";
                $return .= "\n<div class=\"btn-toolbar\">\n";

                foreach ($results as $button)
                {
                    /*
                     * Results should be an object
                     */
                    if ( $button->get('name') ) {
                        $modal		= ($button->get('modal')) ? ' class="modal-button btn"' : null;
                        $href		= ($button->get('link')) ? ' class="btn" href="'.JURI::base().$button->get('link').'"' : null;
                        $onclick	= ($button->get('onclick')) ? ' onclick="'.$button->get('onclick').'"' : '';
                        $title      = ($button->get('title')) ? $button->get('title') : $button->get('text');
                        $return .= '<a' . $modal . ' title="' . $title . '"' . $href . $onclick . ' rel="' . $button->get('options')
                            . '"><i class="icon-' . $button->get('name'). '"></i> ' . $button->get('text') . "</a>\n";
                    }
                }

                $return .= "</div>\n";
                $return .= "</div>\n";

            } else {

                // This will allow plugins to attach buttons or change the behavior on the fly using AJAX
                $return .= "\n".'<div id="editor-xtd-buttons"><div class="btn-toolbar pull-left rokpad-clearfix">'."\n";

                foreach ($results as $button) {
                    // Results should be an object
                    if ($button->get('name')) {
                        $modal   = ($button->get('modal')) ? 'class="modal-button"' : null;
                        $href    = ($button->get('link')) ? 'href="' . JURI::base() . $button->get('link') . '"' : null;
                        $onclick = ($button->get('onclick')) ? 'onclick="' . $button->get('onclick') . '"' : null;
                        $title   = ($button->get('title')) ? $button->get('title') : $button->get('text');
                        $return .= "\n".'<div class="button2-left"><div class="' . $button->get('name') . '">';
                        $return .= '<a ' . $modal . ' title="' . $title . '" ' . $href . ' ' . $onclick . ' rel="' . $button->get('options') . '">';
                        $return .= $button->get('text') . '</a>'."\n".'</div>'."\n".'</div>'."\n";
                    }
                }

                $return .= '</div></div>'."\n";
            }
		}

		return $return;
	}

	/**
	 * @return string
	 */
	protected function _appendCacheToken()
	{
		return '?cache=' . $this->_version;
	}

	/**
	 *
	 */
	protected function compileLess()
	{
		$document = JFactory::getDocument();
		$assets   = JPATH_PLUGINS . DIRECTORY_SEPARATOR . 'editors' . DIRECTORY_SEPARATOR . 'rokpad' . DIRECTORY_SEPARATOR . 'assets';
		@include_once($assets . '/less/mixins/lessc.inc.php');

		if (defined('DEV') && DEV) {
			try {
				$css_file = $assets . '/styles/rokpad.css';
				@unlink($css_file);
				lessc::ccompile($assets . '/less/global.less', $css_file);
			} catch (exception $e) {
				JError::raiseError('LESS Compiler', $e->getMessage());
			}
		}

		$document->addStyleSheet($this->_basepath . 'assets/styles/rokpad.css' . $this->_appendCacheToken());
	}

	/**
	 *
	 */
	protected function compileJS()
	{
		$document = JFactory::getDocument();
		$rokpad   = JPATH_PLUGINS . DIRECTORY_SEPARATOR . 'editors' . DIRECTORY_SEPARATOR . 'rokpad';

		if (defined('DEV') && DEV) {
			$buffer = "";
			$assets = $rokpad . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR;
			$app    = $rokpad . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR;

			$files = array(
				$assets . 'moofx',
				$app. 'respond', $app . 'jstorage', $app . 'beautify-html', $app . 'Twipsy',
				$app . 'RokPad', $app . 'RokPad.ACE', $app. 'RokPad.Functs'
			);

			foreach ($files as $file) {
				$file    = $file . '.js';
				$content = false;

				if (file_exists($file)) $content = file_get_contents($file);

				$buffer .= (!$content) ? "\n\n !!! File not Found: " . $file . " !!! \n\n" : $content;
			}

			file_put_contents($assets . 'rokpad.js', $buffer);
		}

		$document->addScript($this->_basepath . 'ace/ace.js' . $this->_appendCacheToken());
		$document->addScript($this->_basepath . 'assets/js/rokpad.js' . $this->_appendCacheToken());

	}

	/**
	 * @return string
	 */
	protected function getJSParams()
	{
		$document = JFactory::getDocument();
		$params   = $this->params->toArray();
		unset($params['syntax']);

		if (!array_key_exists('theme', $params)) {
			$params = array(
				'theme'                   => 'fluidvision',
				'font-size'               => '12px',
				'fold-style'              => 'markbeginend',
				'use-wrap-mode'           => 'free',
				'selection-style'         => '1',
				'highlight-active-line'   => '1',
				'highlight-selected-word' => '1',
				'show-invisibles'         => '0',
				'show-gutter'             => '1',
				'show-print-margin'       => '1',
				'fade-fold-widgets'       => '0',
				'autosave-enabled'		  => '0',
				'autosave-time'			  => '5'
			);
		}

		$data = "";
		$data .= "var RokPadDefaultSettings = {";
		foreach ($params as $param => $value) {
			$data .= "'" . $param . "': '" . $value . "', ";
		}
		$data = substr($data, 0, strlen($data) - 2);
		$data .= "}, RokPadAcePath = '".$this->_acepath."';";

		return $data;
	}
}
