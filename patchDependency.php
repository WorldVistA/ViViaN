<!DOCTYPE html>
<html>
  <title>Install Dependency Tree</title>
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

        var vivianDataPath = FILES_URL;

        d3.json(vivianDataPath + 'packages_autocomplete.json', function(json) {
          json.push("All Patches");
          var sortedjson = json.sort(function(a,b) { return a.localeCompare(b); });
          $("#package_autocomplete").autocomplete({
            source: sortedjson,
            select: packageAutocompleteChanged
          }).data('autocomplete')/*._trigger('select')*/;
        });


      });

    </script>
  </head>

<body>
  <script src="jquery-ui.min.js"></script>
  <div>

    <!-- <select id="category"></select> -->
  </div>
  <!-- Tooltip -->
  <!-- Tooltip -->
     <div id="toolTip" class="tooltip" style="opacity:0;">
      <div id="header1" class="header"></div>
      <div id="installDate" ></div>
      <div class="tooltipTail"></div>
    </div>
  </div>
  <div id="dialog-modal">
    <div id="accordion">
      <div id="description"></div>
    </div>
  </div>
  <div id="descrHeader" style="position:relative; left:20px; top: -10px;">
    <p>The information in this visualization is not guaranteed to be complete.
       <b>Please note: Installs without dependencies may actually have dependencies due to
       page display limits.</b>
    </p>
    <div id="legend_placeholder" style="position:relative; left:20px;"></div>

    <div>
      <label title="Search for an option by entering the name of the option that you wish to find.">Package:</label>
      <input id="package_autocomplete" size="40"></br>
    </div>
    <div id="installEntryAuto" style="margin-top:10px;">
      <label for ="install_autocomplete" title="Select Entry">Install:</label>
      <input id="install_autocomplete" size="40"></br>
      <label class="btn-primary btn-sm">
        <input type="checkbox" id="depPatches" onclick="javascript:swapDependencyScheme()"> Show dependent patches? </input>
      </label>
    </div>
    </br>
    <div id="buttons">
        <button onclick="_resetAllNode()">Reset</button>
        <button onclick="_centerDisplay()">Center</button>
        <img id="loadingImg" style="display:none;" src="./images/loading-big.gif" alt="Loading Data"></img>
    </div>
  </div>

<div id="treeview_placeholder"/>

<script type="text/javascript">

<?php include_once "vivian_tree_layout_common.js" ?>

var chart = d3.chart.treeview()
              .height(700)
              .width(2000)
              .margins({top:0, left:180, right:0, bottom:0})
              .textwidth(220)
              .pannableTree(true);
var legendShapeChart = d3.chart.treeview()
              .height(50)
              .width(1050)
              .margins({top:10, left:10, right:0, bottom:0})
              .textwidth(110);
var initPackage = "Barcode Medication Administration";
var initInstall = "PSB*3.0*68";
var targetPackage = initPackage;
var header = d3.select(document.getElementById("header1"));
var installDateTip = d3.select(document.getElementById("installDate"));
var vivianDataPath = FILES_URL;
var originalTransform = [300,300];
var patchListing;
var jsonFileVal = {"forward": vivianDataPath +"install_information.json","backward": vivianDataPath +"install_dependent_information.json"};
var jsonFileValKey = "forward";
var shapeLegend = [{name: "Install(with Dependencies)", shape: "triangle-up", color: "green", fill: "green"},
                   {name: "Install(without Dependencies)", shape:"circle", color: "green", fill: "white"},
                   {name: "Duplicate Install(with Dependencies)", shape:"diamond", color: "red", fill: "red"},
                   {name: "Duplicate Install(without Dependencies)", shape:"diamond", color: "red", fill: "white"}]
d3.select("#legend_placeholder").datum(null).call(legendShapeChart);

/*
*  Function to handle the graph when selecting a new package
*  from the package autocomplete.  Redraw the graph with the values
*  from the date selectors and the value of the new package
*/
function packageAutocompleteChanged(eve, ui) {
  d3.json(jsonFileVal[jsonFileValKey], function(json) {
    targetPackage = ui.item.label
    $("#installEntryAuto").show();
    $("#installEntryDrop").hide();
    $("#install_autocomplete").val("");
    if (ui.item.label == 'All Patches') {
      var alljson = []
      Object.keys(json).forEach(function(vistaPackage) {
         Object.keys(json[vistaPackage]).forEach(function (vistaPatch) {
          alljson = alljson.concat(json[vistaPackage][vistaPatch]);
        });
      });
      $("#install_autocomplete").autocomplete({
        source: alljson,
        select: installAutocompleteChanged
        })
        .data('autocomplete')/*._trigger('select')*/;
      }
    else {
      var sortedjson = Object.keys(json[ui.item.label]).sort(function(a,b) { return a.localeCompare(b); });
      $("#install_autocomplete").autocomplete({
        source: sortedjson,
        select: installAutocompleteChanged
        })
        .data('autocomplete')/*._trigger('select')*/;
    }
  })
}

function installAutocompleteChanged(eve, ui) {
  $("#install_autocomplete").val(ui.item.label);
  if (typeof ui.item.parent === "undefined") ui.item.parent=targetPackage
  showDependency(ui.item.parent,ui.item.label);
}

function swapDependencyScheme() {
  if (jsonFileValKey == "forward") { jsonFileValKey = "backward"}
  else { jsonFileValKey = "forward"}
  packageAutocompleteChanged('', {item: {label: targetPackage, value: targetPackage}})
  showDependency(targetPackage,$('#install_autocomplete').val());
}

function appendPackageInformation (d, json, depth, orientation){
  var id = ''
  if(depth > 15) {
      return d;
  }
  d.forEach(function (child) {
      var target = '';
      var packageInformation = json[child.package];
      var target = $.extend(true, {},packageInformation[child.name]);
      if(target) {
        child.installDate= target.installDate;
        child.ien  = target.ien;
        child.orientation = orientation;
        child['BUILD_ien']  = target['BUILD_ien'];
        child.multi = target.multi;
        if (target.children) {
          depth++
          child.children = appendPackageInformation(target.children,json, depth, orientation);
        }
      }
  });
  return d;
}

function node_onMouseOver(d) {
  header.text("Install Name: " + d.name + "\r\n");
  if (d.installDate) {  installDateTip.text("Install Date: " + d.installDate);}

  $( document ).uitooltip('option', 'content', $("#toolTip").html())
  var nodes = d3.selectAll("g.node")
                .filter( function (node) {return (node.name == d.name)})
                .classed('active',true);

}

function node_onMouseOut(d) {
  $( document ).uitooltip('option', 'content', "")
  var nodes = d3.selectAll("g.node")
                .filter( function (node) {return (node.name == d.name)})
                .classed('active',false);
}
function toggle(d) {
    if (d.children) {
        d._children = d.children;
        d.children = null;
    } else {
        d.children = d._children;
        d._children = null;
    }
  }

function  node_onNodeClick(d) {
    toggle(d);
    chart.update(chart.nodes())
}

function _resetAllNode() {
  resetAllNode(chart.nodes());
  _centerDisplay();
  chart.update(chart.nodes());
}

function _centerDisplay() {
  chart.centerDisplay();
}

function showDependency(parent, entryNo) {
  $("#loadingImg").show()
  d3.json(jsonFileVal[jsonFileValKey], function(json) {
    chart.on("path", "event","click", node_onNodeClick)
      .on("node", "event", "mouseover", node_onMouseOver)
      .on("node", "event","mouseout", node_onMouseOut)
      /*.on("text", "attr", "cursor", function(d) {
         return d.hasLink !== undefined && d.hasLink ? "pointer" : "hand";
       })
      .on("text", "attr", "fill", change_node_color)
      .on("path", "style", "fill", change_circle_color)
      */
      .on("text", "event", "click", text_onMouseClick)
      .on("path", "attr", "r", function(d) { return 7 - d.depth; });
    var root = json[parent][entryNo];
    patchListing = []
    if(root) {
      root.orientation = jsonFileValKey;
      console.log(root)
      if(root.hasOwnProperty("children")) {
        root.children = appendPackageInformation(root.children,json,0,jsonFileValKey)
      }

      d3.select("#treeview_placeholder").datum(root).call(chart);
      chart.tree().nodeSize([15,0]);
      var nodes = d3.selectAll('.node');
      var nodeDepth = 0;
      while (true) {
        var newNames = []
        var subset = nodes.filter(function(x) { return x.depth == nodeDepth })
        if ((subset[0].length == 0)) {break;}
        subset.each(function(d) {
          if (patchListing.indexOf(d.name) == -1) {
              newNames.push(d.name);
          } else {
              d.isDuplicate = true;
          }
        })
        nodeDepth++
        patchListing = patchListing.concat(newNames);
      }
      chart.svg().attr("transform","translate("+originalTransform+")")
      resetAllNode(chart.nodes());
      chart.update(chart.nodes())
      $("#loadingImg").hide()
      $("#install_autocomplete").val(entryNo);
    } else {
      alert("No information for that install.  Please select again")
      $("#loadingImg").hide()
    }
  });
}

function text_onMouseClick(d) {
  var modalTitle = d.name;
  if (d.number){modalTitle = "" + d.number + ": " + modalTitle }
  var overlayDialogObj = {
    autoOpen: true,
    height: 'auto',
    width: 700,
    modal: true,
    position: {my: "center center-50", of: window},
    title: modalTitle,
    open: function(){
        $('#description').html(
          "<a target=\"_blank\"  href=\"" + vivianDataPath + `9_6/9.6-${d['BUILD_ien']}.html">BUILD(#9.6) Information</a><br><a target="_blank"  href="` + vivianDataPath + `9_7/9.7-${d.ien}.html">INSTALL(#9.7) Information</a>`
        );
    },
  };
  $('#dialog-modal').dialog(overlayDialogObj).show();
  d3.event.preventDefault();
  d3.event.stopPropagation();
}
function createShapeLegend() {
  var shapeLegendDisplay = legendShapeChart.svg().selectAll("g.shapeLegend")
      .data(shapeLegend)
      .enter().append("svg:g")
      .attr("class", "shapeLegend")
      .attr("transform", function(d, i) { return "translate("+(i * 250) +", 25)"; });

  shapeLegendDisplay.append("path")
      .attr("class", function(d) {return d.name;})
      .attr("d", d3.svg.symbol().type(function(d) { return d.shape;}))
      .attr("fill",function(d) {return d.fill;})
      .attr("stroke",function(d) {return d.color;})
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
          .attr("y", 10 )
          .attr("text-anchor", "left")
          .style("font-size", "16px")
          .text("Shape Legend");
}
$("#package_autocomplete").val(initPackage);
packageAutocompleteChanged('', {item: {label: initPackage, value: initPackage}})
showDependency(initPackage,initInstall)
createShapeLegend()
    </script>
  </body>
</html>