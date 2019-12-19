<?php
/**
 * @package   Astroid Framework
 * @author    JoomDev https://www.joomdev.com
 * @copyright Copyright (C) 2009 - 2019 JoomDev.
 * @license   GNU/GPLv2 and later
 */
// no direct access
defined('_JEXEC') or die;

class pkg_astroidInstallerScript {

   /**
    * 
    * Function to run before installing the component	 
    */
   public function preflight($type, $parent) {
      
   }

   /**
    *
    * Function to run when installing the component
    * @return void
    */
   public function install($parent) {
      $this->getJoomlaVersion();
      $this->displayAstroidWelcome($parent);
   }

   /**
    *
    * Function to run when un-installing the component
    * @return void
    */
   public function uninstall($parent) {
      
   }

   /**
    * 
    * Function to run when updating the component
    * @return void
    */
   function update($parent) {
      $this->displayAstroidWelcome($parent);
   }

   /**
    * 
    * Function to update database schema
    */
   public function updateDatabaseSchema($update) {
      
   }

   public function getJoomlaVersion() {
      $version = new \JVersion;
      $version = $version->getShortVersion();
      $version = substr($version, 0, 1);
      define('ASTROID_JOOMLA_VERSION', $version);
   }

   /**
    * 
    * Function to display welcome page after installing
    */
   public function displayAstroidWelcome($parent) {
      ?>
      <style>
         .astroid-install {
            margin: 20px 0;
            padding: 40px 0;
            text-align: center;
            border-radius: 0;
            position: relative;
            border: 1px solid #f8f8f8;
            background:#fff url(<?php echo JURI::root(); ?>media/astroid/assets/images/moon-surface.png); 
            background-repeat: no-repeat; 
            background-position: bottom;
         }
         .astroid-install p {
            margin: 0;
            font-size: 16px;
            line-height: 1.5;
         }
         .astroid-install .install-message {
            width: 90%;
            max-width: 800px;
            margin: 50px auto;
         }
         .astroid-install .install-message h3 {
            display: block;
            font-size: 20px;
            line-height: 27px;
            margin: 25px 0;
         }
         .astroid-install .install-message h3 span {
            display: block;
            color: #7f7f7f;
            font-size: 13px;
            font-weight: 600;
            line-height: normal;
         }
         .astroid-install-actions .btn {
            color: #fff;
            overflow: hidden;
            font-size: 18px;
            box-shadow: none;
            font-weight: 400;
            padding: 15px 50px;
            border-radius: 50px;
            background: transparent linear-gradient(to right, #8E2DE2, #4A00E0) repeat scroll 0 0 !important;
            line-height: normal;
            border: none;
            font-weight: bold;
            position: relative;
            box-shadow:0 0 30px #b0b7e2;
            transition: linear .1s;
         }
         .astroid-install-actions .btn:after{
            top: 50%;
            width: 20px;
            opacity: 0;
            content:"";
            right: 80px;
            height: 17px;
            display: block;
            position: absolute;
            transform: translateY(-50%);
            -moz-transform: translateY(-50%);
            -webkit-transform: translateY(-50%);
            background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAARCAYAAADdRIy+AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OERDRjlBMjY0OTIzMTFFODkyQTI4MzYzN0ZGQ0Y1NTMiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OERDRjlBMjc0OTIzMTFFODkyQTI4MzYzN0ZGQ0Y1NTMiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo4RENGOUEyNDQ5MjMxMUU4OTJBMjgzNjM3RkZDRjU1MyIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo4RENGOUEyNTQ5MjMxMUU4OTJBMjgzNjM3RkZDRjU1MyIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PvXGU3IAAADpSURBVHjarNShCsJAHMfxO1iyyEw+gsFoEIMKA5uoxSfwKQSjYWASq4JVEKPggwgmi2ASFcMUdefvj3/QsMEdd3/4lO32Zey2SaWU0JwsLOEGndRVFNTkw0N9Z562ziRIAog4OnMRJDV4c3TsIki6EHM0dBEkbfWbgYsgqcKRo3065mGjm1CHM0ihP084wA7yMIRIokonPOFoKNjjO4zptTS4ltZuoQUVPhbaPsMC7PkZjmw3pQx3jk1sdzn4i01t38MiXDi2sP1SSnDl2Mr2W87BiWNryCStkwb/Qx82/D9swCtp0UeAAQDi4gvA12LkbAAAAABJRU5ErkJggg==') no-repeat;
            -webkit-transition: all .4s;
            -moz-transition: all .4s;
            transition: all .4s;
         }
         .astroid-install-actions .btn:hover{
            transition: linear .1s;
            box-shadow:0 0 30px #4b57d9;
         }
         .astroid-install-actions .btn:hover:after{
            opacity: 1;
            right: 20px;
            margin-left: 0;
         }
         .astroid-support-link{
            color: #8E2DE2;
            padding: 30px 0 10px;
         }
         .astroid-support-link a{
            color: #8E2DE2;
            text-decoration: none;
         }
         .astroid-support-link a:hover {
            text-decoration: underline;
         }
         .astroid-poweredby{
            right: 20px;
            width: 150px;
            height: 25px;
            bottom: 20px;
            position: absolute;
            background: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAAAZCAYAAADT59fvAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NjBBMzcwNEU0N0YzMTFFOEE0ODFCNkJDMkNBMDVFNDIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NjBBMzcwNEY0N0YzMTFFOEE0ODFCNkJDMkNBMDVFNDIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo2MEEzNzA0QzQ3RjMxMUU4QTQ4MUI2QkMyQ0EwNUU0MiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo2MEEzNzA0RDQ3RjMxMUU4QTQ4MUI2QkMyQ0EwNUU0MiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PgETteYAAA5xSURBVHja7Ft5eBRV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') no-repeat 0 0;
         }
         .astroid-poweredby a{
            bottom: 0;
            display: block;
            font-size: 0;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
         }
         .astroid-poweredby span{
            font-size: 0;
         }

      </style>
      <div class="astroid-install">
         <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAANIAAABRCAYAAACnvfg0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RDlEQjEwRkVCRkJBMTFFN0IyQUFDQjRFQTM1QjczOEEiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RDlEQjEwRkZCRkJBMTFFN0IyQUFDQjRFQTM1QjczOEEiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpEOURCMTBGQ0JGQkExMUU3QjJBQUNCNEVBMzVCNzM4QSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpEOURCMTBGREJGQkExMUU3QjJBQUNCNEVBMzVCNzM4QSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PitA054AACQJSURBVHja7F0HeBzV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
" alt="astroid-logo" />
         <div class="install-message">
            <h3>JD NewYork - JD NewYork - Free Multipurpose Joomla Template<span>v <?php echo $parent->get('manifest')->version; ?></span></h3>
         </div>
         <div class="astroid-install-actions">
            <a href="index.php?option=com_templates" class="btn btn-default">Get started</a>
         </div>
         <div class="astroid-support-link shake-trigger">
            <a href="https://docs.joomdev.com/category/astroid-user-manual/" target="_blank">Documentation</a> <span>|</span> <a href="https://github.com/joomdev/Astroid-Framework/releases" target="_blank">Changelog</a> <span>|</span> <a href="https://www.joomdev.com/forum/astroid-framework" target="_blank">Forum</a> <span>|</span> <a href="https://www.youtube.com/playlist?list=PLv9TlpLcSZTBBVpJqe3SdJ34A6VvicXqM" target="_blank">Tutorials</a> <span>|</span> <a href="https://www.joomdev.com/about-us" target="_blank">Credits</a> <span>|</span> <a class="shake" href="https://www.joomdev.com/jd-builder?utm_campaign=astroid_install_screen" target="_blank"><img src="data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAABkAAD/4QMraHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjMtYzAxMSA2Ni4xNDU2NjEsIDIwMTIvMDIvMDYtMTQ6NTY6MjcgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDUzYgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkRERDU1MzI1QUM2RTExRTlCMjE1RDY2MDk3RjU1NjJBIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkRERDU1MzI2QUM2RTExRTlCMjE1RDY2MDk3RjU1NjJBIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RERENTUzMjNBQzZFMTFFOUIyMTVENjYwOTdGNTU2MkEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RERENTUzMjRBQzZFMTFFOUIyMTVENjYwOTdGNTU2MkEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7/7gAOQWRvYmUAZMAAAAAB/9sAhAABAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAgICAgICAgICAgIDAwMDAwMDAwMDAQEBAQEBAQIBAQICAgECAgMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwP/wAARCAAdAB4DAREAAhEBAxEB/8QAaAABAAMBAAAAAAAAAAAAAAAACwgJCgcBAQAAAAAAAAAAAAAAAAAAAAAQAAAGAQIFAgUFAQAAAAAAAAECAwQFBgcVCAAREhMJIxQhIhYZCkEnGLg5eREBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8A38cBgzomyXzasvPcjl+YZ5oTpye6Fe3WbPjqek1cETO1D66UlHtLYSRpF3XVYOUxOfSY+nkAH7B0okiKLVRAXKIcq3vMvyO1N5m7A2Bj+RoMIn3HZoHEAUt/d0qYGMjZEsI0YKgWJcljiVUKyLbTwTAOTPtgb5urgKfGPks8wS1/mMRG3lbxgyo1syWPE6CGTLea0DkI10i6aNPGKUdHeEntecmZ9gnQt7n0+fITFEFpOAO9295mzG6/J/n8cu8vZUeY/Jv93OsSUh5ka5O6kSPaKZYXZxhK85mlYkkUxMimVBqCQN0UkyJkIVMpSgCIXAEgxn+wdh/6NIf3Fg+ARQ21+cPxxbr8wr4LxJmiS+v20VYZhZC9Ua145hCM6uqilLEGxXSNhogHiZ1g7aAq95QAMJSj0m5BjX2ySEfLflRykpEv2UpGSO/zc4+j5KNdt3zB8zdIZbWbO2bxqoq2ct10jgYhyGMUwDzAeARMf5nw9FP3kVKZXxrGykc6WYyEa/vVXZv2L1soZFwzeM3Eom4bOkFiiU6ZylOUwCAgA8AUHGPY/wC7xYZHU4rSvuJIPdZ1WP0PT/5gwa+p637nSdK9t6vuu97fs+p19HzcBAe/N66F7uoLTE0ZULbY+6ZOtsSJmU1h51imU1qOYpBNz5AIiIB+o8BaN4HEIIvly2SGZycsq5DJFhFNNzBs26B/20vPWU66VhcqJcyc+Qgmf48vhwEdfKA3rQ+SXf6JpaRKoO8zcuZcjCvsDtk3Jsx3EXSRVDWYhllUnAmKocSkFRUDGEhBHpAIVx7au+0neiZmgKEUiKoGrTExjJ63DABUxC1lBM/dEo8xAwdICHLmICAf/9k=" style="width:16px;margin-top:-4px;margin-right:3px;"> Builder</a>         
	</div>
         <div class="astroid-poweredby">
            <a href="https://www.joomdev.com" target="_blank">
               <span>JoomDev</span>
            </a>
         </div>
      </div>
      <?php
   }

   /**
    * 
    * Function to run after installing the component	 
    */
   public function postflight($type, $parent) {
      
   }
}