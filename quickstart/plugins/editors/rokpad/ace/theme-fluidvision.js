/* ***** BEGIN LICENSE BLOCK *****
 * Version: MPL 1.1/GPL 2.0/LGPL 2.1
 *
 * The contents of this file are subject to the Mozilla Public License Version
 * 1.1 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is Ajax.org Code Editor (ACE).
 *
 * The Initial Developer of the Original Code is
 * Ajax.org B.V.
 * Portions created by the Initial Developer are Copyright (C) 2010
 * the Initial Developer. All Rights Reserved.
 *
 * Contributor(s):
 *      Fabian Jakobs <fabian AT ajax DOT org>
 *
 * Alternatively, the contents of this file may be used under the terms of
 * either the GNU General Public License Version 2 or later (the "GPL"), or
 * the GNU Lesser General Public License Version 2.1 or later (the "LGPL"),
 * in which case the provisions of the GPL or the LGPL are applicable instead
 * of those above. If you wish to allow use of your version of this file only
 * under the terms of either the GPL or the LGPL, and not to allow others to
 * use your version of this file under the terms of the MPL, indicate your
 * decision by deleting the provisions above and replace them with the notice
 * and other provisions required by the GPL or the LGPL. If you do not delete
 * the provisions above, a recipient may use your version of this file under
 * the terms of any one of the MPL, the GPL or the LGPL.
 *
 * ***** END LICENSE BLOCK ***** */

ace.define('ace/theme/fluidvision', ['require', 'exports', 'module', 'ace/lib/dom'], function(require, exports, module) {


exports.isDark = false;
exports.cssText = ".ace-fluidvision .ace_editor {\
  border: 2px solid rgb(159, 159, 159);\
}\
\
.ace-fluidvision .ace_editor.ace_focus {\
  border: 2px solid #327fbd;\
}\
\
.ace-fluidvision .ace_gutter {\
  background: #e8e8e8;\
  color: #333;\
}\
\
.ace-fluidvision .ace_print_margin {\
  width: 1px;\
  background: #e8e8e8;\
}\
\
.ace-fluidvision .ace_scroller {\
  background-color: rgba(244, 244, 244, 0.95);\
}\
\
.ace-fluidvision .ace_text-layer {\
  color: #000000;\
}\
\
.ace-fluidvision .ace_cursor {\
  border-left: 2px solid #000000;\
}\
\
.ace-fluidvision .ace_cursor.ace_overwrite {\
  border-left: 0px;\
  border-bottom: 1px solid #000000;\
}\
\
.ace-fluidvision .ace_marker-layer .ace_selection {\
  background: #FFD793;\
}\
\
.ace-fluidvision.multiselect .ace_selection.start {\
  box-shadow: 0 0 3px 0px rgba(244, 244, 244, 0.95);\
  border-radius: 2px;\
}\
\
.ace-fluidvision .ace_marker-layer .ace_step {\
  background: rgb(198, 219, 174);\
}\
\
.ace-fluidvision .ace_marker-layer .ace_bracket {\
  margin: -1px 0 0 -1px;\
  border: 1px solid #BFBFBF;\
}\
\
.ace-fluidvision .ace_marker-layer .ace_active_line {\
  background: rgba(0, 0, 0, 0.071);\
}\
\
.ace-fluidvision .ace_gutter_active_line {\
  background-color: rgba(0, 0, 0, 0.071);\
}\
\
.ace-fluidvision .ace_marker-layer .ace_selected_word {\
  border: 1px solid #FFD793;\
}\
\
.ace-fluidvision .ace_invisible {\
  color: #BFBFBF;\
}\
\
.ace-fluidvision .ace_keyword, .ace-fluidvision .ace_meta {\
  color:#5B91E1;\
}\
\
.ace-fluidvision .ace_constant, .ace-fluidvision .ace_constant.ace_other {\
  font-style:italic;\
color:#C5060B;\
}\
\
.ace-fluidvision .ace_constant.ace_character,  {\
  font-style:italic;\
color:#C5060B;\
}\
\
.ace-fluidvision .ace_constant.ace_character.ace_escape,  {\
  font-style:italic;\
color:#C5060B;\
}\
\
.ace-fluidvision .ace_constant.ace_language {\
  font-style:italic;\
color:#585CF6;\
}\
\
.ace-fluidvision .ace_constant.ace_numeric {\
  color:#C34F0A;\
}\
\
.ace-fluidvision .ace_invalid {\
  color:#FFFFFF;\
background-color:#990000;\
}\
\
.ace-fluidvision .ace_support.ace_constant {\
  color:#619A1C;\
}\
\
.ace-fluidvision .ace_fold {\
    background-color: #1B4B9D;\
    border-color: #000000;\
}\
\
.ace-fluidvision .ace_support.ace_function {\
  color:#3C4C72;\
}\
\
.ace-fluidvision .ace_variable {\
  color:#1B4B9D;\
}\
\
.ace-fluidvision .ace_variable.ace_parameter {\
  font-style:italic;\
}\
\
.ace-fluidvision .ace_string {\
  color:#840E0B;\
}\
\
.ace-fluidvision .ace_comment {\
  color:#386F90;\
background-color:rgba(221, 238, 254, 0.95);\
}\
\
.ace-fluidvision .ace_variable {\
  font-style:italic;\
color:#20498D;\
}\
\
.ace-fluidvision .ace_meta.ace_tag {\
  color:#1C3981;\
}\
\
.ace-fluidvision .ace_entity.ace_other.ace_attribute-name {\
  font-style:italic;\
color:#000000;\
}\
\
.ace-fluidvision .ace_entity.ace_name.ace_function {\
  color:#1B4B9D;\
}\
\
.ace-fluidvision .ace_markup.ace_underline {\
    text-decoration:underline;\
}\
\
.ace-fluidvision .ace_markup.ace_heading {\
  color:#0C07FF;\
}\
\
.ace-fluidvision .ace_markup.ace_list {\
  color:#B90690;\
}";


exports.cssClass = "ace-fluidvision";

var dom = require("../lib/dom");
dom.importCssString(exports.cssText, exports.cssClass);
});
