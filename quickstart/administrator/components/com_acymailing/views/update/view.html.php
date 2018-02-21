<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.6.1
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class UpdateViewUpdate extends acymailingView{

    function display($tpl = null){

        $function = $this->getLayout();
        if(method_exists($this, $function)) $this->$function();

        parent::display($tpl);
    }

    function acysms(){
        $acyToolbar = acymailing::get('helper.toolbar');
        $acyToolbar->setTitle('AcySMS');
        $acyToolbar->display();

        $js = '
        function installAcySMS(){
            var progressbar = document.getElementById("progressbar");
            var information = document.getElementById("information");
            progressbar.style.width = "10%";
            information.innerHTML = "'.htmlspecialchars(JText::_('ACY_DOWNLOADING'), ENT_QUOTES, 'UTF-8').'";

            try {
                var ajaxCall = new Ajax("index.php?option=com_acymailing&tmpl=component&ctrl=file&task=downloadAcySMS", {
                    method: "get",
                    onComplete: function (responseText, responseXML) {
                        if(responseText == "success") {
                            progressbar.style.width = "40%";
                            document.getElementById("information").innerHTML = "'.htmlspecialchars(JText::_('ACY_INSTALLING'), ENT_QUOTES, 'UTF-8').'";
                            installPackage();
                        }else{
                            document.getElementById("information").innerHTML = "'.str_replace('"', '\"', JText::sprintf('ACY_FAILED_INSTALL', '<a href="https://www.acyba.com/download-area/download/component-acysms/level-express.html" target="_blank">', '</a>')).'";
                        }
                    }
                }).request();
            } catch (err) {
                new Request({
                    url: "index.php?option=com_acymailing&tmpl=component&ctrl=file&task=downloadAcySMS",
                    method: "get",
                    onSuccess: function (responseText, responseXML) {
                        if(responseText == "success") {
                            progressbar.style.width = "40%";
                            document.getElementById("information").innerHTML = "'.htmlspecialchars(JText::_('ACY_INSTALLING'), ENT_QUOTES, 'UTF-8').'";
                            installPackage();
                        }else{
                            document.getElementById("information").innerHTML = "'.str_replace('"', '\"', JText::sprintf('ACY_FAILED_INSTALL', '<a href="https://www.acyba.com/download-area/download/component-acysms/level-express.html" target="_blank">', '</a>')).'";
                        }
                    }
                }).send();
            }
        }

        function installPackage(){
            try{
                var ajaxCall = new Ajax("index.php?option=com_acymailing&tmpl=component&ctrl=file&task=installPackage",{
                    method: "get",
                    onComplete: function(responseText, responseXML) {
                        if(responseText == "success") {
                            progressbar.style.width = "70%";
                            document.getElementById("information").innerHTML = "'.htmlspecialchars(JText::_('ACY_INSTALLING_PLUGINS'), ENT_QUOTES, 'UTF-8').'";
                            installExtensions();
                        }else{
                            document.getElementById("information").innerHTML = "'.str_replace('"', '\"', JText::sprintf('ACY_FAILED_INSTALL', '<a href="https://www.acyba.com/download-area/download/component-acysms/level-express.html" target="_blank">', '</a>')).'";
                        }
                    }
                }).request();
            }catch(err){
                new Request({
                    url:"index.php?option=com_acymailing&tmpl=component&ctrl=file&task=installPackage",
                    method: "get",
                    onSuccess: function(responseText, responseXML) {
                        if(responseText == "success") {
                            progressbar.style.width = "70%";
                            document.getElementById("information").innerHTML = "'.htmlspecialchars(JText::_('ACY_INSTALLING_PLUGINS'), ENT_QUOTES, 'UTF-8').'";
                            installExtensions();
                        }else{
                            document.getElementById("information").innerHTML = "'.str_replace('"', '\"', JText::sprintf('ACY_FAILED_INSTALL', '<a href="https://www.acyba.com/download-area/download/component-acysms/level-express.html" target="_blank">', '</a>')).'";
                        }
                    }
                }).send();
            }
        }

        function installExtensions(){
            try{
                var ajaxCall = new Ajax("index.php?option=com_acysms&ctrl=update&task=install&fromversion=",{
                    method: "get",
                    onComplete: function(responseText, responseXML) {
                        progressbar.style.width = "100%";
                        setTimeout(function(){ 
                            document.getElementById("meter").style.display = "none"; 
                            document.getElementById("postinstall").style.display = ""; 
                        }, 2000);
                    }
                }).request();
            }catch(err){
                new Request({
                    url:"index.php?option=com_acysms&ctrl=update&task=install&fromversion=",
                    method: "get",
                    onSuccess: function(responseText, responseXML) {
                        progressbar.style.width = "100%";
                        setTimeout(function(){
                            document.getElementById("meter").style.display = "none";
                            document.getElementById("postinstall").style.display = "";
                        }, 2000);
                    }
                }).send();
            }
        }';

        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration($js);
    }
}
