<?php
    $target_string = $_GET['option_name'];
    $Filepath =  $_GET['path'];
    $target_file = '';
    // For each file in menus/*.json
    $Filepath = $Filepath . '*.json';
    $filelist = glob($Filepath);
    
    foreach($filelist as &$file_name) {
      // Read in all contents into one array
      $filecontents = file($file_name);
      // preg_grep through the array for the target menu
      $tmp = preg_grep("/". preg_quote($target_string,"/") . "/i",$filecontents) ;
      if(!empty($tmp)) {
        // echo implode($tmp);
        echo $file_name;
        break;
      }
      unset($filecontents);
      unset($tmp);
    };
    //echo $target_file;
   ?>