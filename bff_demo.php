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
        $('#navigation_buttons li').each(function (i) {
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

    <div style="position:relative; left 20px; top:40px;">
      <div id='title' style="position:absolute; top:0px; left:20px; font-size:.97em;">
        <p>VHA Business Function Framework Demo</p>
      </div>

      <div class='hint' style="position:absolute; top:40px; left:20px; font-size:0.9em; width:350px;">
        <p>
        This tree graph represents the VHA Business Function Framework (BFF).
        The BFF is a hierarchical construct that describes VHA business functions or
        major service areas within each core mission Line of Business (LoB) and
        serve as logical groupings of activities. Subfunctions represent the logical
        groupings of sub-activities needed to fulfill each VHA business function.
        Click on an item to bring a modal window with detailed description and commentary.
        </p>
        <p>This demo is based on BFF version 2.7.</p>
      </div>
    </div>

  <div id="treeview_placeholder"/>

<script type="text/javascript">
$("#accordion").accordion({heightStyle: 'content', collapsible: true}).hide();
var chart = d3.chart.treeview()
              .height(940)
              .width(1880)
              .margins({top: 45, left: 280, bottom: 0, right: 0})
              .textwidth(280);

<?php include_once "vivian_tree_layout_common.js" ?>

d3.json("bff.json", function(json) {
  resetAllNode(json);
  chart.on("node", "event", "mouseover", node_onMouseOver)
     .on("node", "event","mouseout", node_onMouseOut)
     .on("node", "event","click", chart.onNodeClick)
     .on("text", "attr", "cursor", function(d) {
        return d.description !== undefined && d.description ? "pointer" : "hand";
      })
     .on("text", "event", "click", text_onMouseClick)
     .on("circle", "attr", "r", function(d) { return 7 - d.depth/2; });
  d3.select("#treeview_placeholder").datum(json).call(chart);
});

var toolTip = d3.select(document.getElementById("toolTip"));
var header = d3.select(document.getElementById("head"));

function node_onMouseOver(d) {
    if (d.number !== undefined){
      header.text("" + d.number);
    }
    else{
      return;
    }
    toolTip.style("left", (d3.event.pageX + 20) + "px")
           .style("top", (d3.event.pageY + 5) + "px")
           .style("opacity", ".9");
}

function text_onMouseClick(d) {
  if (d.description) {
    var overlayDialogObj = {
      autoOpen: true,
      height: 'auto',
      width: 700,
      modal: true,
      position: ["center","center-50"],
      title: "" + d.number + ": " + d.name,
      open: function(){
          $('#description').html(d.description);
          if (d.commentary){
            $('#commentary').html(d.commentary);
            $('#commentary').show();
            $('#commentary_head').show();
          }
          else{
            $('#commentary').html('');
            $('#commentary_head').hide();
            $('#commentary').hide();
          }
          $('#accordion').accordion("option", "active", 0);
          $('#accordion').accordion("refresh");
          $('#accordion').accordion({heightStyle: 'content'}).show();
      },
   };
   $('#dialog-modal').dialog(overlayDialogObj).show();
    // var pkgUrl = package_link_url + d.name.replace(/ /g, '_') + ".html";
    // var win = window.open(pkgUrl, '_black');
    // win.focus();
  }
  d3.event.preventDefault();
  d3.event.stopPropagation();
}

function node_onMouseOut(d) {
  header.text("");
  toolTip.style("opacity", "0");
}

    </script>
  </body>
</html>

