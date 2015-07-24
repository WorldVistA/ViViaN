<!DOCTYPE html>
<html>
  <head>
    <?php
      include_once "vivian_common_header.php";
      include_once "vivian_tree_layout.css";
    ?>
    <!-- JQuery Buttons -->
    <script>
      $(function(){
        $( "button" ).button().click(function(event){
          event.preventDefault();
        });
        $('#demoexamples li').each(function (i) {
          if (i === 2) {
            $(this).removeClass().addClass("active");
          }
          else {
            $(this).removeClass();
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
  <div id="toolTip" class="tooltip" style="opacity:0;">
      <div id="head" class="header"></div>
      <div  class="tooltipTail"></div>
  </div>
  <div id="dialog-modal">
    <div id='namespaces' style="display:none"></div>
    <div id='dependencies' style="display:none"></div>
    <div id="accordion">
        <h3><a href="#">Description</a></h3>
        <div id="description"></div>
        <h3 id="commentary_head" style="display:none"><a href="#">Commentary</a></h3>
        <div id="commentary"></div>
    </div>
  </div>

  <div id='title' style="position:absolute; top:100px; left:40px; font-size:.97em;">
  <p>VHA Business Function Framework Demo</p>
  </div>
  <div class='hint' style="position:absolute; top:140px; left:20px; font-size:0.9em; width:350px;">
  <p>
This tree graph represents the VHA Business Function Framework (BFF). The BFF is a hierarchical construct that describes VHA business functions or  major service areas within each core mission Line of Business (LoB) and serve as logical groupings of activities. Subfunctions represent the logical groupings of sub-activities needed to fulfill each VHA business function. Click on an item to bring a modal window with detailed description and commentary.
  </p>
  <p>This demo is based on BFF version 2.7.</p>
  </div>
  <div id="treeview_placeholder"/>
  <script src="vivian_tree_layout_common.js"></script>
  <script type="text/javascript" src="bff_demo_content.js"></script>
    <script>
      bff_main()
    </script>
  </body>
</html>

