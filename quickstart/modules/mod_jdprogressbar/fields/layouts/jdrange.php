<?php
// Check to ensure this file is included in Joomla!
// Licensed under the GPL v3
defined('_JEXEC') or die('Restricted access');
extract($displayData);
?>

<style>
.range-slider {
  width: 25%;
}

.range-slider__range {
  -webkit-appearance: none;
  width: calc(100% - (73px));
  height: 10px;
  border-radius: 5px;
  background: #d7dcdf;
  outline: none;
  padding: 0;
  margin: 0;
}
.range-slider__range::-webkit-slider-thumb {
  -webkit-appearance: none;
          appearance: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #2c3e50;
  cursor: pointer;
  transition: background .15s ease-in-out;
}
.range-slider__range::-webkit-slider-thumb:hover {
  background: #1abc9c;
}
.range-slider__range:active::-webkit-slider-thumb {
  background: #1abc9c;
}
.range-slider__range::-moz-range-thumb {
  width: 20px;
  height: 20px;
  border: 0;
  border-radius: 50%;
  background: #2c3e50;
  cursor: pointer;
  transition: background .15s ease-in-out;
}
.range-slider__range::-moz-range-thumb:hover {
  background: #1abc9c;
}
.range-slider__range:active::-moz-range-thumb {
  background: #1abc9c;
}
.range-slider__range:focus::-webkit-slider-thumb {
  box-shadow: 0 0 0 3px #fff, 0 0 0 6px #1abc9c;
}

.range-slider__value {
  display: inline-block;
  position: relative;
  
  color: #fff;
  line-height: 20px;
  text-align: center;
  border-radius: 3px;
  background: #2c3e50;
  padding: 5px 10px;
  margin-left: 8px;
}
.range-slider__value:after {
  position: absolute;
  top: 8px;
  left: -7px;
  width: 0;
  height: 0;
  border-top: 7px solid transparent;
  border-right: 7px solid #2c3e50;
  border-bottom: 7px solid transparent;
  content: '';
}

::-moz-range-track {
  background: #d7dcdf;
  border: 0;
}

input::-moz-focus-inner,
input::-moz-focus-outer {
  border: 0;
}

</style>
<div class="range-slider">
  <input type="range" min="<?php echo $element['min'];  ?>" oninput="SetVlaue<?php echo $field->id; ?>()" step="<?php echo $element['step'];  ?>" max="<?php echo $element['max'];  ?>" name="<?php echo $field->name; ?>" value="<?php echo $field->value; ?>" class="range-slider__range"  id="data_<?php echo $field->id; ?>">
  <p class="range-slider__value"><span  id="demo_<?php echo $field->id; ?>">0</span><?php echo $element['postfix'];?></p>
</div>

<script>
  //  Take the current value and populate into span 
  var slider = document.getElementById("data_<?php echo $field->id; ?>");
  document.getElementById("demo_<?php echo $field->id; ?>").innerHTML = slider.value;

  // Function to Set the value
  function SetVlaue<?php echo $field->id; ?>(){
    var slider = document.getElementById("data_<?php echo $field->id; ?>");
    document.getElementById("demo_<?php echo $field->id; ?>").innerHTML = slider.value;
  }
</script>