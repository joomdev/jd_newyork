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

<style>
 input:checked + .slider {
   background-color: <?php echo (!empty($element['bgcolor'])) ? $element['bgcolor'] : '#2196F3';  ?>;
 }
 
 input:focus + .slider {
   box-shadow: 0 0 1px <?php echo (!empty($element['bgcolor'])) ? $element['bgcolor'] : '#2196F3';  ?>;
 }
 
 .switch {
   position: relative;
   display: inline-block;
   width: 60px;
   height: 34px;
 }
 
 .switch input { 
   opacity: 0;
   width: 0;
   height: 0;
 }
 
 .slider {
   position: absolute;
   cursor: pointer;
   top: 0;
   left: 0;
   right: 0;
   bottom: 0;
   background-color: #ccc;
   -webkit-transition: .4s;
   transition: .4s;
 }
 
 .slider:before {
   position: absolute;
   content: "";
   height: 26px;
   width: 26px;
   left: 4px;
   bottom: 4px;
   background-color: white;
   -webkit-transition: .4s;
   transition: .4s;
 }
 input:checked + .slider:before {
   -webkit-transform: translateX(26px);
   -ms-transform: translateX(26px);
   transform: translateX(26px);
 }
 
 /* Rounded sliders */
 .slider.round {
   border-radius: 34px;
 }
 
 .slider.round:before {
  border-radius: 50%;
}
</style>

<label class="switch">
<input type="checkbox" <?php  echo ($field->value == 1) ? 'checked' : '';?> id="jd-<?php echo  $field->id; ?>"  onclick="myFunction<?php echo  $field->id; ?>()">
  <span class="slider <?php  echo $element['styletype'];?>"></span>
</label>


	
<input type="text" style="display:none" value="<?php echo  $field->value; ?>"  name="<?php echo  $field->name; ?>" id="<?php echo  $field->id; ?>" />


<script>
function myFunction<?php echo  $field->id; ?>() {
  var checkBox	 = document.getElementById("jd-<?php echo  $field->id; ?>");
  var hidden 		= document.getElementById("<?php echo  $field->id; ?>");
  if (checkBox.checked == true){
    hidden.value = 1;
  } else {
		hidden.value = 0;
  }
}
</script>
