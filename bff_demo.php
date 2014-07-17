<!DOCTYPE html>
<html>
  <head>
    <?php
      include_once "vivian_common_header.php";
      include_once "vivian_tree_layout.css";
    ?>
    <!-- JQuery Buttons -->
    <script>
      var btn = $.fn.button.noConflict() // reverts $.fn.button to jqueryui btn
      $.fn.btn = btn // assigns bootstrap button functionality to $.fn.btn

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
  <div id="treeview_placeholder"/>
<script type="text/javascript">
$("#accordion").accordion({heightStyle: 'content', collapsible: true}).hide();
var chart = d3.chart.treeview()
              .height(940)
              .width(1480)
              .margins({top: 0, left: 240, bottom: 0, right: 0})
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
    toolTip.transition()
            .duration(100)
            .style("opacity", ".9");
    toolTip.style("left", (d3.event.pageX + 20) + "px")
            .style("top", (d3.event.pageY + 5) + "px");
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
  toolTip.transition()
         .duration(20)
         .style("opacity", "0");
}

    </script>
  </body>
</html>

