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
          if (i === 1) {
            $(this).removeClass().addClass("active");
          }
          else {
            $(this).removeClass();
          }
        });
        d3.json('menu_autocomplete.json', function(json) {
          var sortedjson = json.sort(function(a,b) { return a.label.localeCompare(b.label); });
          $("#autocomplete").autocomplete({
            source: sortedjson,
            select: autoCompleteChanged
            //change: autoCompleteChanged
          }).val('EVE: System Manager Menu').data('autocomplete')._trigger('select');
        });
      });
    </script>
    <?php include_once "vivian_google_analytics.php" ?>
    <style>
      .ui-autocomplete {
          max-height: 400px;
          font-size: 0.9em;
          overflow-y: auto;   /* prevent horizontal scrollbar */
          overflow-x: hidden; /* add padding to account for vertical scrollbar */
          z-index:1000 !important;
      }
    </style>
  </head>

<body >
  <div>
    <?php include_once "vivian_osehra_image.php" ?>
    <!-- <select id="category"></select> -->
  </div>
    <!-- Tooltip -->
  <div id="toolTip" class="tooltip" style="opacity:0;">
      <div id="head" class="header"></div>
      <div  class="tooltipTail"></div>
  </div>

  <div style="position:absolute; left:20px; top:100px;">
    <label for="autocomplete">Select a top level menu: </label>
  </div>
  <div style="position:absolute; left:20px; top:120px;">
    <input id="autocomplete" size="40">
  </div>
  <div style="font-size:10px; position:absolute; left:220px; top:240px;">
    <button onclick="_collapseAllNode()">Collapse All</button>
    <button onclick="_resetAllNode()">Reset</button>
  </div>
  <div class='hint' style="position:absolute; top:160px; left:20px; font-size:0.9em; width:350px;">
  <p>
This tree visualization represents the menu hierarchy of VistA. Mouse over any of the entries in the tree to see the menu option name and the security key (if any). Click on an item to see the menu option details.
  </p>
  </div>
  <div id="treeview_placeholder"/>
<script type="text/javascript">
var chart = d3.chart.treeview()
              .height(1050)
              .width(1280*2)
              .margins({top: 42, left: 260, bottom: 0, right: 0})
              .textwidth(300)
              .nodeTextHyperLink(getOptionDetailLink);
chart.on("text","attr","fill",color_by_type);
var selectedIndex=0;

var menuType = [
  {iName: "legend",color: "black",dName: "All Types"},
  {iName: "menu",color :"gray",dName: "Menu"},
  {iName: "run routine",color :"#ff7f0e",dName: "Run Routine"},
  {iName: "Broker (Client/Server)" , color : "#17becf", dName: "Broker (Client/Server)"},
  {iName: "edit",color :"#2ca02c",dName: "Edit"},
  {iName: "server",color :"#d62728",dName: "Server"},
  {iName: "print",color :"#9467bd",dName: "Print"},
  {iName: "action",color :"#8c564b",dName: "Action"},
  {iName: "ScreenMan",color :"#e377c2",dName: "ScreenMan"},
  {iName: "inquire" , color : "#bcbd22",dName: "Inquire" }
];

function color_by_type(node) {
  if(node.type == undefined){
    return node.color
  };
  for(var i=0;i<menuType.length; i++) {
    if  (node.type == menuType[i].iName) {
      return menuType[i].color;
    }
  }
  return "#E0E0E0";
}

function color_filter(d) {
  if(d.type == undefined){
    return d.color
  };
  if (d.type != menuType[selectedIndex].iName) {
    return "#E0E0E0";
  }
  else {
    return menuType[selectedIndex].color;
  };
}

function autoCompleteChanged(eve, ui) {
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
  d3.json(menuFile, function(json) {
    resetAllNode(json);
    chart.on("node", "event", "mouseover", node_onMouseOver)
       .on("node", "event","mouseout", node_onMouseOut)
       .on("text", "attr", "cursor", function(d) { return "pointer"; })
       //.on("text", "event","click", node_onMouseClick)
       .on("circle", "event", "click", node_onMouseClick)
       .on("circle", "attr", "r", function(d) { return 7 - d.depth/2; });
    d3.select("#treeview_placeholder").datum(json).call(chart);
    generate_legend();
  });
}

var toolTip = d3.select(document.getElementById("toolTip"));
var header = d3.select(document.getElementById("head"));

function node_onMouseClick(d) {
  chart.onNodeClick(d);
  console.log()
  if(selectedIndex !== 0){
    d3.selectAll("text")
      .attr("fill", function (d) {
        return color_filter(d);
      });
  }
}


function getOptionDetailLink(node) {
  return "files/19-" + node.ien + ".html";
}

function node_onMouseOver(d) {
  var headText = "Option Name: " + d.option;
  if (d.lock !== undefined){
    headText = headText + "<br>" + "Security Key: " + d.lock + "</br>";
  }
  header.html(headText);
  toolTip.style("left", (d3.event.pageX + 20) + "px")
         .style("top", (d3.event.pageY + 5) + "px")
         .style("opacity", ".9");
}

function node_onMouseOut(d) {
  header.text("");
  toolTip.style("opacity", "0");
}

function generate_legend() {
  var legend = chart.svg().selectAll("g.legend")
    .data(menuType)
    .enter().append("svg:g")
    .attr("class", "legend")
    .attr("transform", function(d, i) { return "translate(-250," + (i * 30 + 180) + ")"; })
    .on("click", function(d) {
      selectedIndex = menuType.indexOf(d);
      if(selectedIndex !== 0){
        d3.selectAll("text")
          .attr("fill", function (d) {
            return color_filter(d);
          });
      }
      else {
        d3.selectAll("text")
          .attr("fill", function (d) {
            return color_by_type(d);
          });
      }
    });

  legend.append("svg:text")
    .attr("x", 13)
    .attr("dy", ".31em")
    .attr("fill",function(d) {return d.color;})
    .text(function(d) {return  d.dName; });

}
    </script>
  </body>
</html>

