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
  <div id='namespaces' style="display:none"></div>
  <div id='dependencies' style="display:none"></div>
  <div id="accordion">
      <h3><a href="#">Interfaces</a></h3>
      <div id="interface"></div>
      <h3><a href="#">Description</a></h3>
      <div id="description"></div>
  </div>
</div>
<div id="treeview_placeholder"/>
<script type="text/javascript">
var chart = d3.chart.treeview()
              .height(1280)
              .width(1200)
              .margins({top:42, left:180, right:0, bottom:0})
              .textwidth(220);
$("#accordion").accordion({heightStyle: 'content', collapsible: true}).hide();
<?php include_once "vivian_tree_layout_common.js" ?>

var package_link_url = "http://code.osehra.org/dox/Package_";
var toolTip = d3.select(document.getElementById("toolTip"));
var header = d3.select(document.getElementById("header1"));
var selectedIndex = 0;
var catcolors = ["black", "#FF0000", "#3300CC", "#080", "#FF00FF", "#660000"];

d3.json("packages.json", function(json) {
  resetAllNode(json);
  chart.on("node", "event","click", pkgLinkClicked)
     .on("node", "event", "mouseover", node_onMouseOver)
     .on("node", "event","mouseout", node_onMouseOut)
     .on("text", "attr", "cursor", function(d) {
        return d.hasLink !== undefined && d.hasLink ? "pointer" : "hand";
      })
     .on("text", "attr", "fill", change_node_color)
     .on("circle", "style", "fill", change_circle_color)
     .on("circle", "attr", "r", function(d) { return 7 - d.depth; });
  d3.select("#treeview_placeholder").datum(json).call(chart);
  // createLegend();
});

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
  chart.update(chart.nodes());
}


var sddesc = "<p>The VistA Scheduling package allows a user to Schedule appointments for" +
" the following types of appointments:" +
"<ul><li>Scheduled</li>" +
"<li>C and P</li>" +
"<li>Collateral</li></ul>" +
" It also allows entry of an unscheduled appointment at any time during a day" +
" on which the clinic being scheduled into meets.  From these appointments," +
" various output reports are produced such as, but not limited to, file room" +
" list, appointment list, routing slips, letters for cancellations, no-shows," +
" and pre-appointment.  There is an additional capability where additional" +
" clinic stop credits can be directly entered and associated with a particular" +
" patient and date.  AMIS reporting is handled via a set of extract routines" +
" that summarize the data found by reading through the appointments and" +
" additional clinic stops and the 10/10 and unscheduled visits (outpatient" +
" credit given to Admitting/Screening) and storing the information by patient" +
" and visit date in the OCP File.  The AMIS 223 report and the OPC" +
" file to be sent to the Austin DPC are generated using this file.</p>";

function pkgLinkClicked(d) {
  if (d.hasLink) {
    var overlayDialogObj = {
      autoOpen: true,
      height: 'auto',
      width: 700,
      modal: true,
      position: ["center","center-50"],
      title: "Package: " + d.name,
      open: function(){
          htmlLnk = getInterfaceHtml(d);
          $('#interface').html(htmlLnk);
          $('#namespaces').html(getNamespaceHtml(d.prefixes))
          $('#namespaces').show();
          if (d.name === 'Scheduling'){
            $('#description').html(sddesc);
          }
          else{
            $('#description').html(d.name);
          }
          depLink = getDependencyContentHtml(d.name)
          $('#dependencies').html(depLink);
          $('#dependencies').show();
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
  else{
    chart.onNodeClick(d);
  }
}


function getPackageDoxLink(pkgName) {
  var doxLinkName = pkgName.replace(/ /g, '_').replace(/-/g, '_')
  return package_link_url + doxLinkName + ".html";
}

function getNamespaceHtml(namespace) {
  var i=0, len=namespace.length;
  var htmlLnk = "<h4>Namespaces: </h4>";
  for (; i<len-1; i++) {
    htmlLnk += "&nbsp;" + namespace[i] + ",&nbsp;";
  }
  htmlLnk += "&nbsp;" + namespace[i];
  return htmlLnk;
}

function getRPCLinkByPackageName(pkgName) {
  return "<a href=\"files/" + pkgName + "-RPC.html\" target=\"_blank\">Remote Procedure Call</a>";
}

function getHL7LinkByPackageName(pkgName) {
  return "<a href=\"files/" + pkgName + "-HL7.html\" target=\"_blank\">HL7</a>";
}

function getInterfaceHtml(node) {
  pkgName = node.name
  var htmlLnk = "<ul>";
  var rpcLink = "";
  var hl7Link = "";
  if (node.interfaces !== undefined){
    var index = node.interfaces.indexOf("RPC");
    if (index >= 0){
      rpcLink = getRPCLinkByPackageName(pkgName);
    }
    index = node.interfaces.indexOf("HL7");
    if (index >= 0){
      hl7Link = getHL7LinkByPackageName(pkgName);
    }
  }
  if (pkgName === 'Order Entry Results Reporting'){
    htmlLnk += "<li><a href=\"http://www.osehra.org/content/vista-api?quicktabs_vista_api=0#quicktabs-vista_api\" target=\"_blank\">M API</a></li>";
    htmlLnk += "<li>" + rpcLink + "</li>";
    htmlLnk += "<li><a href=\"http://www.osehra.org/content/vista-api?quicktabs_vista_api=2#quicktabs-vista_api\" target=\"_blank\">Web Service API</a></li>";
    htmlLnk += "<li>" + hl7Link + "</li>";
    htmlLnk += "</ul>";
  }
  else{
    htmlLnk += "<li>M API</li>";
    if (rpcLink.length > 0) {
      htmlLnk += "<li>" + rpcLink + "</li>";
    }
    htmlLnk += "<li>Web Service API</li>";
    if (hl7Link.length > 0){
      htmlLnk += "<li>" + hl7Link + "</li>";
    }
    htmlLnk += "</ul>";
  }
  return htmlLnk;
}

function getDependencyContentHtml(pkgName) {
  var pkgUrl = getPackageDoxLink(pkgName)
  depLink = "<h4><a href=\"" + pkgUrl + "\" target=\"_blank\">";
  depLink += "Dependencies & Code View" + "</a></h4>";
  return depLink;
}

function change_node_color(node) {
  if (categories.length === 0) {
    return "black";
  }
  var category = categories[selectedIndex] + " Packages";
  if (category == "All Packages" || node.hasLink === undefined) {
    return "black";
  }
  if (node.category) {
    var index = node.category.indexOf(category);
    if (index >= 0) {
      return catcolors[selectedIndex];
    }
  }
  return "#E0E0E0";
}

function change_circle_color(d){
  if (d._children){
    return "lightsteelblue";
  }
  else {
    if (d.hasLink !== undefined && selectedIndex > 0){
      var category = categories[selectedIndex] + " Packages";
      var index = d.category.indexOf(category);
      if (index >= 0) {
        return catcolors[selectedIndex];
      }
    }
    return "#fff";
  }
}

function node_onMouseOver(d) {
  if (d.hasLink === undefined || !d.hasLink) {
    return;
  }
  if (d.prefixes !== undefined){
    header.text("Namespace: " + d.prefixes);
  }
  else{
    return;
  }
  toolTip.style("left", (d3.event.pageX + 20) + "px")
         .style("top", (d3.event.pageY + 5) + "px")
         .style("opacity", ".9");
}

function node_onMouseOut(d) {
  header.text("");
  toolTip.style("opacity", "0");
}


// var categories = ["All", "OSEHRA", "VA", "DSS", "Medsphere", "Oroville"];
var categories = [];
// Legend.
function createLegend() {
  var legend = chart.svg().selectAll("g.legend")
      .data(categories)
    .enter().append("svg:g")
      .attr("class", "legend")
      .attr("transform", function(d, i) { return "translate(-100," + (i * 30 + 80) + ")"; })
      .on("click", function(d) {
        selectedIndex = categories.indexOf(d);
        d3.selectAll("text")
          .filter(function (d) { return d.hasLink != undefined;})
          .attr("fill", function (node) {
            return change_node_color(node);
          });
        d3.selectAll("circle")
          .filter(function (d) { return d.hasLink != undefined;})
          .style("fill", function (d) {
            return change_circle_color(d);
          });

      });

  legend.append("svg:circle")
      .attr("class", String)
      .attr("r", 3);

  legend.append("svg:text")
      .attr("class", String)
      .attr("x", 13)
      .attr("dy", ".31em")
      .text(function(d) { return  d + " Packages"; });
}
    </script>
  </body>
</html>
