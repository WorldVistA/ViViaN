<!DOCTYPE html>
<html>
  <title>ViViaN</title>
  <head>
    <?php
      include_once "vivian_tree_layout.css";
      include_once "vivian_common.php";
    ?>
    <!-- JQuery Buttons -->
    <script>
      $(function() {
        $( "button" ).button().click(function(event){
          event.preventDefault();
        });
        fileName = window.location.href.substring(window.location.href.lastIndexOf('/')+1)
        $('a[href="'+fileName+'"]').parents("#navigation_buttons li").each(function (i) {
            $(this).removeClass().addClass("active");
        });

        var vivianDataPath =  FILES_URL;
        d3.json(vivianDataPath + 'packages_autocomplete.json', function(json) {
          // Note: vivian_tree_layout_common expects this control
          // to be called 'option_autocomplete'.
          $("#option_autocomplete").autocomplete({
            source: json,
            select: packageAutoCompleteChanged
          }).data('autocomplete');
        });
      });

    </script>
  </head>

<body >
  <div>
    <!-- <select id="category"></select> -->
  </div>
  <!-- Tooltip -->
  <div id="toolTip" class="tooltip" style="opacity:0;">
      <div id="header1" class="header"></div>
      <div  class="tooltipTail"></div>
  </div>

  <div id="dialog-modal">
    <div id="accordion"> </div>
  </div>
</div>
<div id="legend_placeholder" style="position:relative; left:20px; margin-top: -10px;"></div>
<div style="position:relative; width:400px; left:20px;">
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

<?php include_once "vivian_tree_layout_common.js" ?>

// Note: vivian_tree_layout_common expects this variable
// to be called 'chart'.
var chart = d3.chart.treeview()
              .height(1280)
              .width(1500)
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

var toolTip = d3.select(document.getElementById("toolTip"));
var header = d3.select(document.getElementById("header1"));
var himJSON  = {}
var selectedIndex = 0;
var distProp = [ // constants to store property of each distribution
  { color: "black", distribution: 'All' }
  /**,
  { color: "#FF0000", distribution: 'OSEHRA VistA', doxlink: "http://code.osehra.org/OSEHRA_dox/" },
  { color: "#3300CC", distribution: 'VA FOIA VistA', doxlink: "http://code.osehra.org/dox/" },
  { color: "#080", distribution: 'DSS vxVistA'}
  ,{
    distribution: "Medsphere",
    color: "#FF00FF"
  },
  {
    distribution: "Oroville",
    color: "#660000"
  } **/
];

var packageInfoProp = {
  "namespaces": {"func": getNamespaceHtml, "title": "Namespaces"},
  "dependencies": {"func": getDependencyContentHtml, "title": "Dependencies"},
  "interface": {"func": getInterfaceHtml, "title": "Interfaces"},
  "himInfo": {"func": getHIMLink, "title": "HIM Info"},
  "description": {"func": getDescriptionHtml, "title": "Description"},
  "status": {"func": getStatusHtml, "title": "Status"},
}
var shapeLegend = [{name: "Package Category", shape: "triangle-up"},
                   {name: "Package", shape:"circle"}]

var doxPath = DOX_URL;
var vivianDataPath = FILES_URL;
d3.json(vivianDataPath + "packages.json", function(json) {
  chart.on("node", "event","click", pkgLinkClicked)
     .on("node", "event", "mouseover", node_onMouseOver)
     .on("node", "event","mouseout", node_onMouseOut)
     .on("text", "attr", "cursor", function(d) {
        return d.hasLink !== undefined && d.hasLink ? "pointer" : "hand";
      })
     .on("text", "attr", "fill", changeNodeColor)
     .on("path", "style", "fill", changeCircleColor)
     .on("path", "attr", "r", function(d) { return 7 - d.depth; });

  d3.select("#treeview_placeholder").datum(json).call(chart);
  d3.select("#legend_placeholder").datum(null).call(legendShapeChart);
  d3.select("#legend_placeholder").datum(null).call(legendDistChart);
  resetAllNode(chart.nodes());
  chart.update(chart.nodes())
  clearAutocomplete();
  createLegend();
  createShapeLegend();
  d3.json(vivianDataPath + "himData.json", function(json) {
    himJSON = json;
  });
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
          $('#accordion').empty()
          Object.keys(packageInfoProp).forEach(function(key) {
              var accordionText = packageInfoProp[key]["func"](d.name, d)
              if (accordionText) {
                $('#accordion').append("<h4>" + packageInfoProp[key]["title"]+ "</h4>");
                $('#accordion').append("<div id="+key+"></div>");
                $('#'+key).append(accordionText)
              }
          });
          $('#accordion').accordion("option", "active", 0);
          $('#accordion').accordion("refresh");
          $('#accordion').accordion({heightStyle: 'content'}).show();
      },
   };
   $('#dialog-modal').dialog(overlayDialogObj).show();
  }
  else {
    if (d.depth == 0) {
      clearAutocomplete();
    }
    chart.onNodeClick(d);
  }
}

function getDescriptionHtml(pkgName, d) {
  var outtext = d.name;
  if (d.des) {
    if (d.des instanceof Array) {
      for (var idx=0, len=d.des.length; idx < len; idx++){
        outtext += d.des[idx] + "<br/>";
      }
    }
    else {
       outtext += d.des;
    }
  }
  if (d.description) {
    outtext += "<br/><br/><b>From VDL:</b><br/>";
    outtext += d.description;
  }
  return outtext
}

function getStatusHtml(pkgName, d) {
  return d.status
}


function getNamespaceHtml(pkgName, pkg) {
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

function createLink(link, text) {
  return "<a href=\"" + link + "\" target=\"_blank\">" + text + "</a>";
}

function getHIMLink(pkgName, pkg) {
    var htmlLnk = ''
    var himPath = himJSON[pkg.name];
    if (himPath != null) {
      var himLink = "http://him.osehra.org/content/" + himPath;
      var himText = "HIM Visualization for " + pkgName;
      htmlLnk = createLink(himLink, himText);
    }
    return htmlLnk
};

function getRPCLinkByPackageName(pkgName) {
  var link = vivianDataPath + "8994" + "/" + pkgName + "-RPC.html";
  var text = "Remote Procedure Call";
  return "<li>" + createLink(link, text) +"</li>";
}

function getHL7LinkByPackageName(pkgName) {
  var link = vivianDataPath + "101" + "/" + pkgName + "-HL7.html";
  var text = "HL7";
  return "<li>" + createLink(link, text) +"</li>";
}

function getProtocolLinkByPackageName(pkgName) {
  var link = vivianDataPath + "101" + "/" + pkgName + "-Protocols.html";
  var text = "Protocols";
  return "<li>" + createLink(link, text) +"</li>";
}

function getHLOLinkByPackageName(pkgName) {
  var link = vivianDataPath + "779_2" + "/" + pkgName + "-HLO.html";
  var text = "HLO";
  return "<li>" + createLink(link, text) +"</li>";
}

function getICRLinkByPackageName(pkgName) {
  var link = vivianDataPath + "ICR" + "/" + pkgName + "-ICR.html";
  var text = "ICR";
  return "<li>" + createLink(link, text) +"</li>";
}

function getMAPILinkyByPackageName(pkgName) {
  console.log(pkgName);
  if (pkgName === 'Order Entry Results Reporting') {
    var link = "http://www.osehra.org/content/vista-api?quicktabs_vista_api=0#quicktabs-vista_api";
    var text = "M API";
    return "<li>" + createLink(link, text) +"</li>";
  } else {
    return "";
  }
}

function getWebServiceAPILinkyByPackageName(pkgName) {
  console.log(pkgName);
  if (pkgName === 'Order Entry Results Reporting') {
    var link = "http://www.osehra.org/content/vista-api?quicktabs_vista_api=2#quicktabs-vista_api";
    var text = "Web Service API";
    return "<li>" + createLink(link, text) +"</li>";
  } else {
    return "";
  }
}

function getInterfaceHtml(pkgName, node) {
  var htmlLnk = "<ul>";
  htmlLnk += getMAPILinkyByPackageName(pkgName);
  if (node.interfaces !== undefined) {
    if (node.interfaces.includes("RPC")) {
      htmlLnk += getRPCLinkByPackageName(pkgName);
    }
  }
  htmlLnk += getWebServiceAPILinkyByPackageName(pkgName);
  if (node.interfaces !== undefined) {
    if (node.interfaces.includes("HL7")) {
      htmlLnk += getHL7LinkByPackageName(pkgName);
    }
    if (node.interfaces.includes("Protocols")) {
      htmlLnk += getProtocolLinkByPackageName(pkgName);
    }
    if (node.interfaces.includes("HLO")) {
      htmlLnk += getHLOLinkByPackageName(pkgName);
    }
    if (node.interfaces.includes("ICR")) {
      htmlLnk += getICRLinkByPackageName(pkgName);
    }
  }
  htmlLnk += "</ul>";
  return htmlLnk;
}

function getDependencyContentHtml(pkgName, node) {
  var packagePage = "Package_" + pkgName.replace(/ /g, '_').replace(/-/g, '_') + ".html";
  var depLink = createLink(doxPath + packagePage, "Dependencies & Code View");

  var otherDistributions = "";
  var category = distProp[selectedIndex];
  if (category.distribution === "All") {
    for(var i = 0; i < distProp.length; i++) {
      if ("doxlink" in distProp[i]) {
        otherDistributions += "<li>";
        otherDistributions += createLink(distProp[i].doxlink + packagePage, distProp[i].distribution);
        otherDistributions += "</li>";
      }
    }
  } else if ("doxlink" in category) {
    otherDistributions += "<li>";
    otherDistributions += createLink(category.doxlink + packagePage, category.distribution);
    otherDistributions += "</li>";
  }

  if (otherDistributions) {
    depLink += "<br><br>"
    depLink += "Other Distributions"
    depLink += "<ul>";
    depLink += otherDistributions;
    depLink += "</ul>";
  }

  return depLink;
}

function changeNodeColor(node) {
  var category = distProp[selectedIndex];
  if (category.distribution === "All" || node.hasLink === undefined) {
    return "black";
  }
  if (node.distribution) {
    var category_name = category.distribution.split(" ")[0];
    if (node.distribution.includes(category_name)) {
      return category.color;
    }
  }
  return "#E0E0E0";
}

function changeCircleColor(d) {
  if (d._children) {
    return "lightsteelblue";
  }

  var category = distProp[selectedIndex];
  if (d.hasLink !== undefined && category.distribution != "All") {
    return category.color;
  } else {
    return "#fff";
  }
}

function node_onMouseOver(d) {
  if (d.hasLink === undefined || !d.hasLink) {
    return;
  } else if (d.Posprefixes !== undefined && d.Negprefixes !== undefined) {
    tooltipString = "Namespace: Includes: " + d.Posprefixes + "\r\n";
    tooltipString += "Excludes: " + d.Negprefixes;
    header.text(tooltipString)
    $( document ).uitooltip('option', 'content', $("#toolTip").html())
  }
  else{
    return;
  }
}

function node_onMouseOut(d) {
  $( document ).uitooltip('option', 'content', "")
}

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
            return changeNodeColor(node);
          });
        d3.selectAll("path")
          .filter(function (d) { return d.hasLink != undefined;})
          .style("fill", function (d) {
            return changeCircleColor(d);
          });

      });
  legend.append("svg:circle")
      .attr("class", function(d) {return d.distribution.split(" ")[0];})
      .attr("r", 3);

  legend.append("svg:text")
      .attr("class", function(d) {return d.distribution.split(" ")[0];})
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
