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

        $('#navigation_buttons li').each(function (i) {
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
          }).val('EVE: Systems Manager Menu').data('autocomplete')/*._trigger('select')*/;
        });

        d3.json('option_autocomplete.json', function(json) {
          var sortedjson = json.sort(function(a,b) { return a.label.localeCompare(b.label); });
          $("#option_autocomplete").autocomplete({
            source: sortedjson,
            select: optionAutoCompleteChanged
          }).data('autocomplete')/*._trigger('select')*/;
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

  <body>
    <?php include_once "vivian_osehra_image.php" ?>

    <!-- Tooltip -->
  <div id="toolTip" class="tooltip" style="opacity:0;">
      <div id="head" class="header"></div>
      <div  class="tooltipTail"></div>
  </div>
<div id='title' style="position:relative; top:10px; left:30px; font-size:.97em;">
 <p title="This tree visualization represents the menu hierarchy of VistA. Mouse over any of the entries in the tree to see the menu option name and the security key (if any). Click on an item to see the menu option details."> VistA Menus </p>
  <div style="position:relative;" >
    <label title="Show the structure of a top level menu by entering the name of the option." style="width:200;" for="autocomplete">Select a top level menu: </label>
    <input id="autocomplete" size="40">
  </div>
  <div>
    <label title="Search for an option by entering the name of the option that you wish to find."> Search for an Option:</label>
    <input id="option_autocomplete" size="40">
  </div>
  <div>
    <button onclick="_collapseAllNode()">Collapse All</button>
    <button onclick="_resetAllNode()">Reset</button>
  </div>
</div>
</br>
<div id="legend_placeholder"></div>
<div id="treeview_placeholder"></div>

<script type="text/javascript">
var chart = d3.chart.treeview()
              .height(1050)
              .width(1280*2)
              .margins({top: 60, left: 260, bottom: 0, right: 0})
              .textwidth(300)
              .nodeTextHyperLink(getOptionDetailLink);
var legendShapeChart = d3.chart.treeview()
              .height(50)
              .width(350)
              .margins({top:42, left:10, right:0, bottom:0})
              .textwidth(110);
var legendTypeChart = d3.chart.treeview()
              .height(50)
              .width(1100)
              .margins({top:42, left:0, right:0, bottom:0})
              .textwidth(110);

var shapeLegend = [{name: "Menu", shape: "triangle-up"},
                   {name: "Option", shape:"circle"}]

chart.on("text","attr","fill",color_by_type);
var selectedIndex=0;

var target_option='';
var target_node;
var target_path = [];

var menuType = [
  {iName: "legend",color: "black",dName: "All Types"},
  {iName: "menu",color :"gray",dName: "Menu"},
  {iName: "run routine",color :"#ff7f0e",dName: "Run Routine"},
  {iName: "Broker (Client/Server)" , color : "#17becf", dName: "Broker"},
  {iName: "edit",color :"#2ca02c",dName: "Edit"},
  {iName: "server",color :"#d62728",dName: "Server"},
  {iName: "print",color :"#9467bd",dName: "Print"},
  {iName: "action",color :"#8c564b",dName: "Action"},
  {iName: "ScreenMan",color :"#e377c2",dName: "ScreenMan"},
  {iName: "inquire" , color : "#bcbd22",dName: "Inquire" }
];

function color_by_type(node) {
  if(node) {
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
}

function color_filter(d) {
  if(d) {
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
}

function autoCompleteChanged(eve, ui) {
  var menuFile = "menus/VistAMenu-" + ui.item.id + ".json";
  resetMenuFile(menuFile);
}

function optionAutoCompleteChanged(eve, ui) {
  var menuFile = "menus/VistAMenu-" + ui.item.parent_id + ".json";
  d3.json('menu_autocomplete.json', function(json) {
    for ( var i = 0; i < json.length; i++) {
      if( json[i].id == ui.item.parent_id) {
      $("#autocomplete")[0].value = json[i].label;
      break;
      }
    }
  });
  target_option = $.trim(ui.item.value.split(":")[1]);
  resetMenuFile(menuFile);
}

<?php include_once "vivian_tree_layout_common.js" ?>

function _collapseAllNode() {
  clearAutocomplete();
  collapseAllNode(chart.nodes());
  chart.update(chart.nodes());
}

function _resetAllNode() {
  clearAutocomplete();
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
       .on("path", "event", "click", node_onMouseClick)
       .on("path", "attr", "r", function(d) { return 7 - d.depth/2; });
    d3.select("#treeview_placeholder").datum(json).call(chart);
    d3.select("#legend_placeholder").datum(null).call(legendShapeChart);
    d3.select("#legend_placeholder").datum(null).call(legendTypeChart);
    createShapeLegend();
    createLegend();

    if(target_option != '') {
      openSpecificOption(chart.nodes());
      setTimeout(highlight_path,300,chart,json);
    }
    else {
      clearAutocomplete();
    }
  });
}

var toolTip = d3.select(document.getElementById("toolTip"));
var header = d3.select(document.getElementById("head"));

function node_onMouseClick(d) {
  chart.onNodeClick(d);
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

function createLegend() {
  var legend = legendTypeChart.svg().selectAll("g.legend")
    .data(menuType)
    .enter().append("svg:g")
    .attr("class", "legend")
    .attr("transform", function(d, i) { return "translate(" + (i * 110) +",-10)"; })
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

  var legend = legendTypeChart.svg()
  legend.append("text")
          .attr("x", 0)
          .attr("y", -28 )
          .attr("text-anchor", "left")
          .style("font-size", "16px")
          .text("Option Type Filter Menu");
}

//Enter Cost Information for Procedures
function searchForOption(d) {
  if (d._children) {
    for(var i=0; i<d._children.length;i++) {
      var ret = searchForOption(d._children[i])
      if(ret) {
         expand(d);
         return true;
      }
    }
  }
  if( d.name.toUpperCase() == target_option.toUpperCase()) {
    expand(d);
    target_node = d;
    return true;
  }
  return false;
}

function openSpecificOption(root) {
  collapseAllNode(root);
  searchForOption(root);
}

function highlight_path(chart, json) {
  var tree = d3.layout.tree()
  var nodes = tree.nodes(chart.nodes());
  var links = tree.links(nodes);
  var target = target_node;

  while (target.name != nodes[0].name) {
    var link = chart.svg().selectAll("path.link").data(links, function(d) {
      if(d.target == target) {
        target = d.source;
        target_path.push(d)
      }
    });

    if(target == target_node){
      $("#option_autocomplete")[0].style.border="solid 4px orange";
      $("#search_result").html("<h5>Target option found in menu, but couldn't be matched.</h5>");
      resetAllNode(json)
      break;
    }
  }

  chart.svg().selectAll("path.link").data(target_path).forEach(highlight);
  d3.select("#treeview_placeholder").datum(json).call(chart);
}

function highlight(d) {
  for(var i =0; i< d.length; i++) {
    d[i].classList.add("target");
  }
}

function createShapeLegend() {
  var shapeLegendDisplay = legendShapeChart.svg().selectAll("g.shapeLegend")
      .data(shapeLegend)
    .enter().append("svg:g")
      .attr("class", "shapeLegend")
      .attr("transform", function(d, i) { return "translate("+(i * 200) +", -10)"; });
  shapeLegendDisplay.append("path")
      .attr("class", function(d) {return d.name;})
      .attr("d", d3.svg.symbol().type(function(d) { return d.shape;}))
      .attr("r", 3);

  shapeLegendDisplay.append("svg:text")
      .attr("class", function(d) {return d.name;})
      .attr("x", 13)
      .attr("dy", ".31em")
      .text(function(d) {
        return  d.name;
      });

  var shapeLegendDisplay = legendShapeChart.svg();
  shapeLegendDisplay.append("text")
          .attr("x", 0)
          .attr("y", -28 )
          .attr("text-anchor", "left")
          .style("font-size", "16px")
          .text("Shape Legend");
}
function clearAutocomplete() {
  console.log("clearAutocomplete");
  document.getElementById("option_autocomplete").value= '';
  chart.svg().selectAll("path.link").data(target_path).forEach(function(d) {
      for(var i =0; i< d.length; i++) {
        d[i].classList.remove("target");
      }
  });

  target_path = [];
  target_option='';

}
    </script>
    </div>
  </body>
</html>
