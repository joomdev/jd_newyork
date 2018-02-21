/*
 * Copyright (c) 2010, Ajax.org B.V.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of Ajax.org B.V. nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL AJAX.ORG B.V. BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
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
