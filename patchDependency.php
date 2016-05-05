<!DOCTYPE html>
<html>
  <title>VistA Patch Dependency Tree</title>
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
          if (i === 4) {
            $(this).removeClass("active").addClass("active");
          }
          else {
            $(this).removeClass("active");
          }
        });

        d3.json('packages_autocomplete.json', function(json) {
          var sortedjson = json.sort(function(a,b) { return a.localeCompare(b); });
          $("#package_autocomplete").autocomplete({
            source: sortedjson,
            select: packageAutocompleteChanged
          }).data('autocomplete')/*._trigger('select')*/;
        });


      });

    </script>
    <?php include_once "vivian_google_analytics.php" ?>
  </head>

<body>
  <script src="jquery-ui.min.js"></script>
  <div>
    <?php include_once "vivian_osehra_image.php" ?>
    <!-- <select id="category"></select> -->
  </div>
  <!-- Tooltip -->
     <div id="toolTip" class="tooltip" style="opacity:0;">
      <div id="header1" class="header"></div>
      <div id="installDate" ></div>
      <div class="tooltipTail"></div>
    </div>
  </div>
  </br>
  </br>
  <div id="descrHeader" >
    <p>The information in this visualization is not complete.  The majority of the installs may
       not have dependency information.  For the best examples of the dependency display, select
       the following "Package" and "Install" pairs:
    </p>
    <ul>
      <li> Barcode Medication Administration: PSB*3.0*68  </li>
      <li> Pharmacy Data Management: PSS*1.0*168 </li>
      <li> Scheduling: SD*5.3*581 </li>
      <li> Registration: DG*5.3*841 </li>
      <li> Integrated Billing: IB*2.0*497 </li>
    </ul>
  </div>
  </br>
   <div>
    <label title="Search for an option by entering the name of the option that you wish to find."> Install information for package:</label>
    <input id="package_autocomplete" size="40"></br>
    <label title="Select Patch"> Patch Dependency:</label>
    <input id="install_autocomplete" size="40"></br>
    <div id="buttons">
        <button onclick="_expandAllNode()">Expand All</button>
        <button onclick="_collapseAllNode()">Collapse All</button>
        <button onclick="_resetAllNode()">Reset</button>
        <button onclick="_centerDisplay()">Center</button>
    </div>
  </div>

<div id="treeview_placeholder"/>

<script type="text/javascript">

var chart = d3.chart.treeview()
              .height(700)
              .width(2000)
              .margins({top:0, left:180, right:0, bottom:0})
              .textwidth(220)
              .nodeTextHyperLink(getInstallDetailLink)
              .pannableTree(true);
var initPackage = "Barcode Medication Administration";
var initInstall = "PSB*3.0*68" ;
var targetPackage = initPackage;
var toolTip = d3.select(document.getElementById("toolTip"));
var header = d3.select(document.getElementById("header1"));
var installDateTip = d3.select(document.getElementById("installDate"));
var originalTransform = [180,300];
<?php include_once "vivian_tree_layout_common.js" ?>
/*
*  Function to handle the graph when selecting a new package
*  from the package autocomplete.  Redraw the graph with the values
*  from the date selectors and the value of the new package
*/
function packageAutocompleteChanged(eve, ui) {
          targetPackage = ui.item.label
          $("#install_autocomplete").val("");
          d3.json('install_information.json', function(json) {
          var sortedjson = Object.keys(json[ui.item.label]).sort(function(a,b) { return a.localeCompare(b); });
          $("#install_autocomplete").autocomplete({
            source: sortedjson,
            select: function(event, ui) {
              event.preventDefault();
              $("#install_autocomplete").val(ui.item.label);
              installAutocompleteChanged(event,ui);
            }
          }).data('autocomplete')/*._trigger('select')*/;
        });
}

function installAutocompleteChanged(eve, ui) {
  $("#install_autocomplete").val(ui.item.label);
  showDependency(ui.item.value);
}

function appendPackageInformation (d,json){
  var id = ''
  d.forEach(function (child) {
      var target = '';
      var packageInformation = json[child.package];
      var target = $.extend(true, {},packageInformation[child.name]);
      if(target) {
        child.installDate= target.installDate;
        child.ien  = target.ien;
        if (target.children) {
          child.children = appendPackageInformation(target.children,json);
        }
      }
  });
  return d;
}

function node_onMouseOver(d) {
  header.text("Install Name: " + d.name + "\r\n");
  if (d.installDate) {  installDateTip.text("Install Date: " + d.installDate);}
  toolTip.style("left", (d3.event.pageX + 20) + "px")
         .style("top", (d3.event.pageY + 5) + "px")
         .style("opacity", ".9");

  var nodes = d3.selectAll("g.node")
                .filter( function (node) {return (node.name == d.name)})
                .classed('active',true);

}

function node_onMouseOut(d) {
  header.text("");
  installDateTip.text("");
  toolTip.style("opacity", "0");
  var nodes = d3.selectAll("g.node")
                .filter( function (node) {return (node.name == d.name)})
                .classed('active',false);
}


function getInstallDetailLink(node) {
  return "files/9.7-" + node.ien + ".html";
}


function _expandAllNode() {
  expandAllNode(chart.nodes());
  chart.update(chart.nodes());
}

function _collapseAllNode() {
  collapseAllNode(chart.nodes());
  chart.update(chart.nodes());
}

function _resetAllNode() {
  resetAllNode(chart.nodes());
  _centerDisplay();
  chart.update(chart.nodes());
}

function _centerDisplay() {
  chart.centerDisplay();
}

function showDependency(entryNo) {
  d3.json("install_information.json", function(json) {

    chart.on("path", "event","click", chart.onNodeClick)
      .on("node", "event", "mouseover", node_onMouseOver)
      .on("node", "event","mouseout", node_onMouseOut)
      /*.on("text", "attr", "cursor", function(d) {
         return d.hasLink !== undefined && d.hasLink ? "pointer" : "hand";
       })
      .on("text", "attr", "fill", change_node_color)
      .on("path", "style", "fill", change_circle_color)*/
      .on("path", "attr", "r", function(d) { return 7 - d.depth; });
    var root = json[targetPackage][entryNo];
    if(root.hasOwnProperty("children")) {
      root.children = appendPackageInformation(root.children,json)
    }
    resetAllNode(root);
    d3.select("#treeview_placeholder").datum(root).call(chart);
    chart.tree().nodeSize([15,0]);

    chart.svg().attr("transform","translate("+originalTransform+")")
    chart.update(chart.nodes())
  });
}
$("#package_autocomplete").val(targetPackage);
showDependency(initInstall)

    </script>
  </body>
</html>