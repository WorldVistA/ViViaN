<!DOCTYPE html>
<html>
  <title>VHA Business Function Framework Demo</title>
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
        <h3 id="requirements_head" style="display:none"><a href="#">Requirements</a></h3>
        <div id="requirements"></div>
        <h3 id="needLink_head" style="display:none"><a href="#">Business Need</a></h3>
        <div id="busNeedLink"></div>
      </div>
    </div>
  </br>
  </br>
  <div id='pageDescription' class='hint'  style="position:relative; top:10px; left:20px; margin-right:200px;">
    <p>
    This tree graph represents the VHA Business Function Framework (BFF).
    The BFF is a hierarchical construct that describes VHA business functions
    or major service areas within each core mission Line of Business (LoB) and
    serve as logical groupings of activities. Subfunctions represent the
    logical groupings of sub-activities needed to fulfill each VHA business
    function. Click on an item to bring a modal window with detailed
    description and commentary.
    </p>
    <p>This demo is based on BFF version 2.12.</p>
  </div>
  <div id="legend_placeholder" style="position:relative; left:20px; top:20px;"></div>
  <div id="treeview_placeholder"></div>

<script type="text/javascript">
$("#accordion").accordion({heightStyle: 'content', collapsible: true}).hide();
var chart = d3.chart.treeview()
              .height(940)
              .width(1880)
              .textwidth(280);
var legendShapeChart = d3.chart.treeview()
              .height(50)
              .width(660)
              .margins({top:42, left:10, right:0, bottom:0})
              .textwidth(110);

<?php include_once "vivian_tree_layout_common.js" ?>

var shapeLegend = [{name: "Framework Grouping", shape: "triangle-up"},
                   {name: "Business Function", shape:"circle"},
                   {name: "Business Need", shape:"cross"}]

d3.json("files/bff.json", function(BFFjson) {
  d3.json("files/Requirements.json", function(reqjson) {
    resetAllNode(BFFjson);
    chart.on("node", "event", "mouseover", node_onMouseOver)
       .on("node", "event","mouseout", node_onMouseOut)
       .on("node", "event","click", chart.onNodeClick)
       .on("text", "attr", "cursor", function(d) {
          return d.description !== undefined && d.description ? "pointer" : "hand";
        })
       .on("text", "event", "click", text_onMouseClick)
       .on("path", "attr", "r", function(d) { return 7 - d.depth/2; });

    var combinedJSON = combineData(BFFjson,reqjson,"children")
    var test = d3.select("#treeview_placeholder").datum(combinedJSON).call(chart);
    d3.select("#legend_placeholder").datum(null).call(legendShapeChart);
    createShapeLegend();
  });
});

var toolTip = d3.select(document.getElementById("toolTip"));
var header = d3.select(document.getElementById("head"));

function combineData(bffData, reqData,parameter) {
  bffData[parameter].forEach(function(d) {

    if(d3.keys(reqData).indexOf(d.name) != -1) {
      d.hasRequirements = true;
      if(d._children) {
        d._children = d._children.concat(reqData[d.name])
      }
      else {
        d._children = reqData[d.name]
      }
    }
    else {
      if (d.children)  { combineData(d, reqData,"children")}
      if (d._children) { combineData(d, reqData,"_children")}
    }
  });
  return bffData
}
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

function getRequirementsURL(d){
  var outstring = "<a href='files/requirements/"+d.name.replace('/','_')+"-Req.html'>Requirements for "+d.name+"</a>"
  return outstring
}

function getBusinessNeedURL(d) {
  return "<a href='files/requirements/BFFReq-"+d.id+".html'>Business Need Page</a>"
}

function text_onMouseClick(d) {
  if (d.description) {
    var modalTitle = d.name;
    if (d.number){modalTitle = "" + d.number + ": " + modalTitle }
    var overlayDialogObj = {
      autoOpen: true,
      height: 'auto',
      width: 700,
      modal: true,
      position: {my: "center center-50", of: window},
      title:modalTitle,
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
          if (d.hasRequirements){
            $('#requirements').show();
            $('#requirements_head').show();
            $('#requirements').html(getRequirementsURL(d));
          }
          else{
            $('#requirements').html('');
            $('#requirements_head').hide();
            $('#requirements').hide();
          }
          if (d.isRequirement){
            $('#busNeedLink').html(getBusinessNeedURL(d));
            $('#needLink_head').show();
            $('#busNeedLink').show();
          }
          else {
            $('#busNeedLink').html('');
            $('#needLink_head').hide();
            $('#busNeedLink').hide();
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
        return d.name;
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

