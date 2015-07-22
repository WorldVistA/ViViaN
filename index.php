<!DOCTYPE html>
<html>
  <head>
    <?php
      include_once "vivian_common_header.php";
      include_once "vivian_tree_layout.css";
    ?>
    <!-- JQuery Buttons -->
    <script>
      $(function() {
        $( "button" ).button().click(function(event){
          event.preventDefault();
        });
        $('#demoexamples li').each(function (i) {
          if (i === 0) {
            $(this).removeClass("active").addClass("active");
          }
          else {
            $(this).removeClass("active");
          }
        });
      });
    </script>
    <?php include_once "vivian_google_analytics.php" ?>
  </head>

<body >
  <div>
    <?php include_once "vivian_osehra_image.php" ?>
    <!-- <select id="category"></select> -->
    <div style="font-size:10px; position:absolute; left:60px; top:100px">
      <button onclick="_expandAllNode()">Expand All</button>
      <button onclick="_collapseAllNode()">Collapse All</button>
      <button onclick="_resetAllNode()">Reset</button>
    </div>
  </div>
  <!-- Tooltip -->
<div id="toolTip" class="tooltip" style="opacity:0;">
    <div id="header1" class="header"></div>
    <div  class="tooltipTail"></div>
</div>

<div id="dialog-modal">
  <div id="accordion">
      <h3><a href="#">Namespaces</a></h3>
      <div id='namespaces' style="display:none"></div>
      <h3><a href="#">Dependencies</a></h3>
      <div id='dependencies' style="display:none"></div>
      <h3><a href="#">Interfaces</a></h3>
      <div id="interface"></div>
      <h3><a href="#">Description</a></h3>
      <div id="description"></div>
  </div>
</div>
<div id="treeview_placeholder"/>
  <script src="vivian_tree_layout_common.js"></script>
  <script type="text/javascript" src="index_content.js"></script>
    <script>
      index_main()
    </script>
  </body>
</html>
