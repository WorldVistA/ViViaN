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
        var test = [
                    {label:"eve: System Manager Menu", id:9},
                    {label:"test", id:1933},
                    {label:"test1", id:5038},
                    {label:"Combined A&MM Menus", id:2575}
                   ];
        $("#autocomplete").autocomplete({
          source: test,
          select: autoCompleteChanged,
          change: autoCompleteChanged
        }).val('eve').data('autocomplete')._trigger('select');
      });
      var btn = $.fn.button.noConflict() // reverts $.fn.button to jqueryui btn
      $.fn.btn = btn // assigns bootstrap button functionality to $.fn.btn

    </script>
    <?php include_once "vivian_google_analytics.php" ?>
  </head>

<body >
  <div>
    <?php include_once "vivian_osehra_image.php" ?>
    <!-- <select id="category"></select> -->
    <div style="font-size:10px; position:absolute; right:20px; top:40;">
      <button onclick="_collapseAllNode()">Collapse All</button>
      <button onclick="_resetAllNode()">Reset</button>
    </div>
    <div style="font-size:12px; position:absolute; right:20px; top:10px;">
      <label for="autocomplete">Select a top level menu: </label>
      <input id="autocomplete" size="30">
    </div>
  </div>
    <!-- Tooltip -->
  <div id="toolTip" class="tooltip" style="opacity:0;">
      <div id="head" class="header"></div>
      <div  class="tooltipTail"></div>
  </div>

  <div id="treeview_placeholder"/>
<script type="text/javascript">
var chart = d3.chart.treeview()
              .height(1050)
              .width(1280*2)
              .margins({top: 10, left: 150, bottom: 10, right: 150})
              .textwidth(300);

function autoCompleteChanged(eve, ui) {
  console.log("label selected is " + ui.item.id);
  var menuFile = "menus/VistAMenu-" + ui.item.id + ".json";
  console.log("Menu file is " + menuFile);
  resetMenuFile(menuFile);
}

<?php include_once "vivian_tree_layout_common.js" ?>
function _collapseAllNode() {
  collapseAllNode(chart.nodes());
  chart.update(chart.nodes());
}

function _resetAllNode() {
  resetAllNode(chart.nodes());
  chart.update(chart.nodes());
}

resetMenuFile("menus/VistAMenu-9.json");
function resetMenuFile(menuFile) {
  var clickFunc = chart.onNodeClick;
  d3.json(menuFile, function(json) {
    resetAllNode(json);
    chart.on("node", "event","click", node_onMouseClick)
       .on("node", "event", "mouseover", node_onMouseOver)
       .on("node", "event","mouseout", node_onMouseOut)
       .on("text", "attr", "cursor", function(d) { return "pointer"; })
       .on("circle", "event", "click", clickFunc)
       .on("circle", "attr", "r", function(d) { return 7 - d.depth/2; });
    d3.select("#treeview_placeholder").datum(json).call(chart);
  });
}

var toolTip = d3.select(document.getElementById("toolTip"));
var header = d3.select(document.getElementById("head"));

function node_onMouseClick(d) {
  console.log("Node: " + d.name + " ien: " + d.ien + " clicked!");
  var optionLink = getOptionDetailLink(d)
  var win = window.open(optionLink, '_black');
  win.focus();
}

function getOptionDetailLink(node) {
  return "files/19-" + node.ien + ".html"
}

function node_onMouseOver(d) {
  var headText = "Option Name: " + d.option;
  if (d.lock !== undefined){
    headText = headText + "<br>" + "Security Key: " + d.lock + "</br>";
  }
  header.html(headText);
  toolTip.transition()
          .duration(200)
          .style("opacity", ".9");
  toolTip.style("left", (d3.event.pageX + 20) + "px")
          .style("top", (d3.event.pageY + 5) + "px");
}

function node_onMouseOut(d) {
  header.text("");
  toolTip.transition()
         .duration(500)
         .style("opacity", "0");
}

    </script>
  </body>
</html>

