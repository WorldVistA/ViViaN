<!DOCTYPE html>
<html>
  <title>ViViaN</title>
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
          if (i === 0) {
            $(this).removeClass("active").addClass("active");
          }
          else {
            $(this).removeClass("active");
          }
        });

        d3.json('files/packages_autocomplete.json', function(json) {
          // Note: vivian_tree_layout_common expects this control
          // to be called 'option_autocomplete'.
          $("#option_autocomplete").autocomplete({
            source: json,
            select: packageAutoCompleteChanged
          }).data('autocomplete');
        });
      });

    </script>
    <?php include_once "vivian_google_analytics.php" ?>
  </head>

<body >
  <div>
    <?php include_once "vivian_osehra_image.php" ?>
    <!-- <select id="category"></select> -->
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
        <h3><a href="#">HIM Info</a></h3>
        <div id="himInfo"></div>
        <h3><a href="#">Description</a></h3>
        <div id="description"></div>
    </div>
  </div>
</div>
<div id="legend_placeholder" style="position:relative; left:20px; top:50px;"></div>
<div style="position:relative; width:400px; left:20px; top:65px;">
  <div id="packageSearch">
    <div><label for="option_autocomplete"> Search for a package:</label></div>
    <div><input id="option_autocomplete" size="40" onfocus="clearAutocomplete()"></div>
    <div id="search_result"> </div>
  </div>
  <div id="buttons" style="position:relative; top:10px;">
      <button onclick="_expandAllNode()">Expand All</button>
      <button onclick="_collapseAllNode()">Collapse All</button>
      <button onclick="_resetAllNode()">Reset</button>
  </div>
</div>

<div id="treeview_placeholder"/>

<script type="text/javascript">

// Note: vivian_tree_layout_common expects this variable
// to be called 'chart'.
var chart = d3.chart.treeview()
              .height(1280)
              .width(1200)
              .margins({top:0, left:100, right:0, bottom:0})
              .textwidth(220);
var legendShapeChart = d3.chart.treeview()
              .height(50)
              .width(350)
              .margins({top:42, left:10, right:0, bottom:0})
              .textwidth(110);
var legendDistChart = d3.chart.treeview()
              .height(50)
              .width(800)
              .margins({top:42, left:10, right:0, bottom:0})
              .textwidth(110);
$("#accordion").accordion({heightStyle: 'content', collapsible: true}).hide();

<?php include_once "vivian_tree_layout_common.js" ?>

var package_link_url = "http://code.osehra.org/dox/";
var toolTip = d3.select(document.getElementById("toolTip"));
var header = d3.select(document.getElementById("header1"));
var selectedIndex = 0;
var distProp = [ // constants to store property of each distribution
  { name: "All", color: "black", distribution: 'All', doxlink: package_link_url},
  { name: "OSEHRA", color: "#FF0000", distribution: 'OSEHRA VistA', doxlink: "http://code.osehra.org/OSEHRA_dox/"},
  { name: "VA", color: "#3300CC", distribution: 'VA FOIA VistA' ,doxlink: package_link_url},
  { name: "DSS", color: "#080", distribution: 'DSS vxVistA' , doxlink: "http://code.osehra.org/dox_alpha/vxvista/"}

  /**
  ,{
    name: "Medsphere",
    color: "#FF00FF"
  },
  {
    name: "Oroville",
    color: "#660000"
  } **/
];

var shapeLegend = [{name: "Package Category", shape: "triangle-up"},
                   {name: "Package", shape:"circle"}]
var himInfoJSON;

d3.json("files/packages.json", function(json) {
  chart.on("node", "event","click", pkgLinkClicked)
     .on("node", "event", "mouseover", node_onMouseOver)
     .on("node", "event","mouseout", node_onMouseOut)
     .on("text", "attr", "cursor", function(d) {
        return d.hasLink !== undefined && d.hasLink ? "pointer" : "hand";
      })
     .on("text", "attr", "fill", change_node_color)
     .on("path", "style", "fill", change_circle_color)
     .on("path", "attr", "r", function(d) { return 7 - d.depth; });

  d3.select("#treeview_placeholder").datum(json).call(chart);
  d3.select("#legend_placeholder").datum(null).call(legendShapeChart);
  d3.select("#legend_placeholder").datum(null).call(legendDistChart);
  resetAllNode(chart.nodes());
  chart.update(chart.nodes())
  clearAutocomplete();
  createLegend();
  createShapeLegend();
});

function packageAutoCompleteChanged(event, ui) {
  if (chart.nodes()._children) { // collapsed
    _expandAllNode();
  } else {
    clearHighlightedPath();
  }
  var target = ui.item.value;
  openSpecificNode(target, chart.nodes());
  setTimeout(highlightPath,300,chart);
}

function pkgLinkClicked(d) {
  if (d.hasLink) {
    var overlayDialogObj = {
      autoOpen: true,
      height: 'auto',
      width: 700,
      modal: true,
      position: {my: "center center", of: window},
      title: "Package: " + d.name,
      open: function(){
          htmlLnk = getInterfaceHtml(d);
          $('#interface').html(htmlLnk);
          $('#namespaces').html(getNamespaceHtml(d))
          $('#namespaces').show();
          $('#description').html(getDescriptionHtml(d))
          depLink = getDependencyContentHtml(d.name, d)
          var depLink_html = "";
          for(var i = 0; i < depLink.length;i++) {
            depLink_html += depLink[i] + " ";
          }
          $('#dependencies').html(depLink_html);
          $('#dependencies').show();
          getHIMLink(d);
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
  else {
    if (d.depth == 0) {
      clearAutocomplete();
    }
    chart.onNodeClick(d);
  }
}

function getDescriptionHtml(d) {
  if (d.des) {
    if (d.des instanceof Array) {
      var outtext = '';
      for (var idx=0, len=d.des.length; idx < len; idx++){
        outtext += d.des[idx] + "<br />";
      }
      return outtext;
    }
    else {
      return d.des;
    }
  }
  else {
    return d.name;
  }
}

function getPackageDoxLink(pkgName, node) {
  var doxUrl = [];
  var doxLinkName = pkgName.replace(/ /g, '_').replace(/-/g, '_')
  var category = distProp[selectedIndex];
  var index = node.distribution.indexOf(category.name);

  if (category.name == "All") {
    for(var i = 1; i < distProp.length;i++) {
      doxUrl.push(distProp[i].doxlink + "Package_" + doxLinkName + ".html");
    }
  }
  else if (index >= 0) {
    doxUrl.push(category.doxlink + "Package_" + doxLinkName + ".html");
  }
  return doxUrl;  // + "Package_" + doxLinkName + ".html";
}

function getDistributionPropByName(distName){
  var index = 0;
  for (index = 0; index < distProp.length; index++) {
    if (distProp[index].name === distName) {
      return distProp[index];
    }
  }
  return null;
}

function getNamespaceHtml(pkg) {
  var i=0, len=pkg.Posprefixes.length;
  var htmlLnk = "Includes:";
  for (; i<len-1; i++) {
    htmlLnk += "&nbsp;" + pkg.Posprefixes[i] + ",&nbsp;";
  }
  htmlLnk += "&nbsp;" + pkg.Posprefixes[i];

  var i=0, len=pkg.Negprefixes.length;
  htmlLnk += "</br>Excludes:"
  if(len > 0) {
    for (; i<len-1; i++) {
      htmlLnk += "&nbsp;" + pkg.Negprefixes[i] + ",&nbsp;";
    }
    htmlLnk += "&nbsp;" + pkg.Negprefixes[i];
  }
  return htmlLnk;
}

function getHIMLink(pkg) {
  d3.json("files/himData.json", function(json) {

    var himPath = json[pkg.name];
    if (himPath == null) {
      $("#himInfo").html("")
    }
    else {
      var htmlLnk = "<a href='http://him.osehra.org/content/" + himPath +"'>HIM Visualization for "+ pkg.name +"</a>";
      $("#himInfo").html(htmlLnk);
    }
  });
}

function getRPCLinkByPackageName(pkgName, linkUrl) {
  var defLnk = "files/8994";
  if (linkUrl){
    defLnk = linkUrl + '/' + defLnk;
  }
  return "<a href=\"" + defLnk + "/" + pkgName + "-RPC.html\" target=\"_blank\">Remote Procedure Call</a>";
}

function getHL7LinkByPackageName(pkgName, linkUrl) {
  var defLnk = "files/101";
  if (linkUrl){
    defLnk = linkUrl + '/' + defLnk;
  }
  return "<a href=\"" + defLnk + "/" + pkgName + "-HL7.html\" target=\"_blank\">HL7</a>";
}

function getProtocolLinkByPackageName(pkgName, linkUrl) {
  var defLnk = "files/101";
  if (linkUrl){
    defLnk = linkUrl + '/' + defLnk;
  }
  return "<a href=\"" + defLnk + "/" + pkgName + "-Protocols.html\" target=\"_blank\">Protocols</a>";
}

function getHLOLinkByPackageName(pkgName, linkUrl) {
  var defLnk = "files/779_2";
  if (linkUrl){
    defLnk = linkUrl + '/' + defLnk;
  }
  return "<a href=\"" + defLnk + "/" + pkgName + "-HLO.html\" target=\"_blank\">HLO</a>";
}

function getICRLinkByPackageName(pkgName, linkUrl) {
  var defLnk = "files/ICR";
  if (linkUrl){
    defLnk = linkUrl + '/' + defLnk;
  }
  return "<a href=\"" + defLnk + "/" + pkgName + "-ICR.html\" target=\"_blank\">ICR</a>";
}

function getInterfaceHtml(node) {
  pkgName = node.name
  var htmlLnk = "<ul>";
  var rpcLink = "";
  var hl7Link = "";
  var icrLink = "";
  var protocolLink = "";
  var hloLink = "";
  var extraLink = "";
  if (node.interfaces !== undefined){
    if (selectedIndex === 3) {
      category = distProp[selectedIndex].name;
      if (node.distribution && node.distribution.indexOf(category) >=0) {
        extraLink = "vxvista";
      }
    }
    var index = node.interfaces.indexOf("RPC");
    if (index >= 0){
      rpcLink = getRPCLinkByPackageName(pkgName, extraLink);
    }
    index = node.interfaces.indexOf("HL7");
    if (index >= 0){
      hl7Link = getHL7LinkByPackageName(pkgName, extraLink);
    }
    index = node.interfaces.indexOf("Protocols");
    if (index >= 0){
      protocolLink = getProtocolLinkByPackageName(pkgName, extraLink);
    }
    index = node.interfaces.indexOf("HLO");
    if (index >= 0){
      hloLink = getHLOLinkByPackageName(pkgName, extraLink);
    }
    index = node.interfaces.indexOf("ICR");
    if (index >= 0){
      icrLink = getICRLinkByPackageName(pkgName);
    }
  }
  if (pkgName === 'Order Entry Results Reporting'){
    htmlLnk += "<li><a href=\"http://www.osehra.org/content/vista-api?quicktabs_vista_api=0#quicktabs-vista_api\" target=\"_blank\">M API</a></li>";
    htmlLnk += "<li>" + rpcLink + "</li>";
    htmlLnk += "<li><a href=\"http://www.osehra.org/content/vista-api?quicktabs_vista_api=2#quicktabs-vista_api\" target=\"_blank\">Web Service API</a></li>";
    htmlLnk += "<li>" + hl7Link + "</li>";
    htmlLnk += "<li>" + icrLink + "</li>";
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
    if (protocolLink.length > 0){
      htmlLnk += "<li>" + protocolLink + "</li>";
    }
    if (hloLink.length > 0){
      htmlLnk += "<li>" + hloLink + "</li>";
    }
    if (icrLink.length > 0){
      htmlLnk += "<li>" + icrLink + "</li>";
    }
    htmlLnk += "</ul>";
  }
  return htmlLnk;
}

function getDependencyContentHtml(pkgName, node) {
  var pkgUrl = getPackageDoxLink(pkgName, node)
  var depLink = []
  for(var i = 0; i < pkgUrl.length;i++) {
    depLink_str = "<h4><a href=\"" + pkgUrl[i] + "\" target=\"_blank\">";
    if( selectedIndex==0 ) {depLink_str += distProp[i+1].name +" Dependencies & Code View" + "</a></h4>";}
    else {depLink_str += distProp[selectedIndex].name +" Dependencies & Code View" + "</a></h4>";}
    depLink.push(depLink_str);
  }
  return depLink;
}

function change_node_color(node) {
  if (distProp.length === 0) {
    return "black";
  }
  var category = distProp[selectedIndex];
  if (category.name === "All" || node.hasLink === undefined) {
    return "black";
  }
  if (node.distribution) {
    var index = node.distribution.indexOf(category.name);
    if (index >= 0) {
      return category.color;
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
      var category = distProp[selectedIndex];
      if (d.distribution !== undefined){
        var index = d.distribution.indexOf(category.name);
        if (index >= 0) {
          return category.color;
        }
      }
      else { 
        return category.color;
      }
    }
    return "#fff";
  }
}

function node_onMouseOver(d) {
  if (d.hasLink === undefined || !d.hasLink) {
    return;
  }
  if (d.Posprefixes !== undefined){
    tooltipString = "Namespace: Includes: " + d.Posprefixes + ".\r\n";
  }
  if (d.Negprefixes !== undefined){
    tooltipString += "Excludes: " + d.Negprefixes;
  }
  else{
    return;
  }
  header.text(tooltipString)
  toolTip.style("left", (d3.event.pageX + 20) + "px")
         .style("top", (d3.event.pageY + 5) + "px")
         .style("opacity", ".9");
}

function node_onMouseOut(d) {
  header.text("");
  toolTip.style("opacity", "0");
}

// var categories = ["All", "OSEHRA", "VA", "DSS", "Medsphere", "Oroville"];
// Legend.
function createLegend() {

  var legend = legendDistChart.svg().selectAll("g.legend")
      .data(distProp)
      .enter().append("svg:g")
      .attr("class", "legend")
      .attr("transform", function(d, i) { return "translate("+ (i * 200) + ",-10)"; })
      .on("click", function(d) {
        selectedIndex = distProp.indexOf(d);
        d3.selectAll("text")
          .filter(function (d) {if(d) { return d.hasLink != undefined;}})
          .attr("fill", function (node) {
            return change_node_color(node);
          });
        d3.selectAll("path")
          .filter(function (d) { return d.hasLink != undefined;})
          .style("fill", function (d) {
            return change_circle_color(d);
          });

      });

  legend.append("svg:circle")
      .attr("class", function(d) {return d.name;})
      .attr("r", 3);

  legend.append("svg:text")
      .attr("class", function(d) {return d.name;})
      .attr("x", 13)
      .attr("dy", ".31em")
      .text(function(d) {
        return  d.distribution; 
      });

  var legend = legendDistChart.svg()
  legend.append("text")
          .attr("x", 0)
          .attr("y", -28 )
          .attr("text-anchor", "left")
          .style("font-size", "16px")
          .text("Distribution Filter Menu");
}

function createShapeLegend() {
  var shapeLegendDisplay = legendShapeChart.svg().selectAll("g.shapeLegend")
      .data(shapeLegend)
      .enter().append("svg:g")
      .attr("class", "shapeLegend")
      .attr("transform", function(d, i) { return "translate("+(i * 200) +", -10)"; })

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

    </script>
  </body>
</html>
