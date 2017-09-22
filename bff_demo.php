<!DOCTYPE html>
<html>
  <title>VHA Business Function Framework and Requirements</title>
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
      <div id="accordion">
        <h3><a href="#">Description</a></h3>
        <div id="description"></div>
        <h3 id="commentary_head" style="display:none"><a href="#">Commentary</a></h3>
        <div id="commentary"></div>
        <h3 id="requirements_head" style="display:none"><a href="#">Requirements</a></h3>
        <div id="requirements"></div>
        <h3 id="bdID_head" style="display:none"><a href="#">Business Need: ID</a></h3>
        <div id="bnID"></div>
        <h3 id="needBFF_head" style="display:none"><a href="#">Business Need: BFF Links</a></h3>
        <div id="bnBFFLink"></div>
        <h3 id="bnNSR_head" style="display:none"><a href="#">Business Need: New Service Request</a></h3>
        <div id="bnNSR"></div>
        <h3 id="bnDofUpdate_head" style="display:none"><a href="#">Business Need: Date of Update</a></h3>
        <div id="bnDofUpdate"></div>
        <h3 id="allReq" style="display:none"><a href="#">All Business Needs</a></h3>
        <div id="nsNSR"><a target='_blank' href="files/requirements/All-Requirement%20List.html">All Needs</a></div>
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

    See the legend for an explanation of the symbols and colors of the display.
    The "collapsed" nodes can be expanded to show the "children" of that node.
    </p>
    <p><b>Note:</b> Not all Business Needs can be found connected to a BFF Entry.  To see the listing
    of all Business Needs, click <a target='_blank' href="files/requirements/All-Requirement%20List.html">here</a> </p>
    <p>The current information is based on BFF version 2.12.</p>
  </div>

  <div id="buttons" style="position:relative; top:10px; right:-20px">
    <button onclick="_collapseAllNode()">Collapse All</button>
    <button onclick="_resetAllNode()">Reset</button>
    <input type="checkbox" id="showUpdates" onclick="renderWindow()"> Show new and updated requirements</input>
  </div>
  <div id="legend_placeholder" style="position:relative; left:20px; top:20px;"></div>
  </br>
  <div id="treeview_placeholder"></div>

<script type="text/javascript">
$("#accordion").accordion({heightStyle: 'content', collapsible: true}).hide();
var chart = d3.chart.treeview()
              .height(2000)
              .width(1880)
              .textwidth(280);
var legendShapeChart = d3.chart.treeview()
              .height(115)
              .width(1300)
              .margins({top:42, left:10, right:0, bottom:0})
              .textwidth(110);

<?php include_once "vivian_tree_layout_common.js" ?>

var shapeLegend = [{name: "Framework Grouping (Collapsed)", shape: "triangle-up","_children":[],"hasRequirements": false, "index":0, "depth": -10},
                   {name: "Framework Grouping (Expanded)", shape: "triangle-up", "depth": -10,"index":1},
                   {name: "Framework Grouping with Needs (Collapsed)", shape: "triangle-up","_children":[],"hasRequirements": true, "depth": -10,"index":2},
                   {name: "Framework Grouping with Needs (Expanded)", shape: "triangle-up","children":[],"hasRequirements": true, "depth": -10,"index":3},
                   {name: "Business Function", shape:"circle","hasRequirements": false, "depth": 20,"index":0},
                   {name: "Business Function with Needs (Collapsed)", shape:"circle","hasRequirements": true,"_children":[], "depth": 20,"index":1},
                   {name: "Business Function with Needs (Expanded)", shape:"circle","hasRequirements": true, "depth": 20,"index":2},
                   {name: "Business Need", shape:"cross", "isRequirement": true, "depth": 50,"index":0},
                   {name: "Business Need (Recently Updated)", shape:"cross", "isRequirement": true, "recentUpdate":"Update","depth": 50,"index":1},
                   {name: "Business Need (New)", shape:"cross", "isRequirement": true, "recentUpdate":"New Requirement","depth": 50,"index":2}
                   ]
renderWindow();
function renderWindow() {
  d3.json("files/bff.json", function(BFFjson) {
    d3.json("files/Requirements.json", function(reqjson) {
      resetAllNode(BFFjson);
      chart.on("node", "event", "mouseover", node_onMouseOver)
         .on("node", "event","mouseout", node_onMouseOut)
         .on("node", "event","click", node_onClick)
         .on("text", "attr", "cursor", function(d) {
            return d.description !== undefined && d.description ? "pointer" : "hand";
          })
         .on("text", "event", "click", text_onMouseClick)
         .on("path", "attr", "r", function(d) { return 7 - d.depth/2; });

      var combinedJSON = combineData(BFFjson,reqjson,"children")
      if($("#showUpdates").is(":checked")) {
         combinedJSON = removeNonRequirementNodes(combinedJSON,"children")
         combinedJSON.children = combinedJSON.children.filter(function(object) {return object.filteredReq})
      }
      var test = d3.select("#treeview_placeholder").datum(combinedJSON).call(chart);
      d3.select("#legend_placeholder").datum(null).call(legendShapeChart);
      createShapeLegend();
    });
  });
}

var toolTip = d3.select(document.getElementById("toolTip"));
var header = d3.select(document.getElementById("head"));
/*
 *  Remove the nodes that do not contain any requirements
 *  Should only be run when filtering to show only the "recently updated" requirements
 */
function removeNonRequirementNodes(bffData, parameter) {
  //Go through combined data
  bffData[parameter].forEach( function(child) {
      // If child object is requirement, parent object is set to be shown
      if (child.isRequirement) {bffData.filteredReq=true}
      // Check child's children for objects
      if (child.children) {
         child = removeNonRequirementNodes(child,"children")
         child.children = child.children.filter(function(test) {return test.filteredReq  || test.isRequirement})
      }
      if (child._children) {
         child = removeNonRequirementNodes(child,"_children");
         child._children = child._children.filter(function(test) {return test.filteredReq || test.isRequirement})
      }
      //If any "child of a child" is marked as having requirements, add the parent object as well
      if (child.filteredReq) {bffData.filteredReq=true}
  })
  return bffData
}

function combineData(bffData, reqData,parameter) {
  bffData[parameter].forEach(function(d) {
    if(d3.keys(reqData).indexOf(d.name) != -1) {
      var reqDataToShow = reqData[d.name]
      d.hasRequirements = false;
      if($("#showUpdates").is(":checked")) {
        reqDataToShow = reqData[d.name].filter(function(d) {return d.recentUpdate != "None"})
      }
      if (reqDataToShow.length) {
        d.hasRequirements = true;
        if(d._children) {
          d._children = d._children.concat(reqDataToShow)
        }
        else {
          d.leafFunction= true
          d._children = reqDataToShow
        }
      }
    }
    //compare objects to see if anything was added to the array
    // i.e. requirements are found
    if (d.children)  { combineData(d, reqData,"children")}
    if (d._children) {combineData(d, reqData,"_children")}
  });
  return bffData
}

function node_onClick(d) {
    //Check for the overall number of nodes shown to make sure screen isn't crowded
    var addNodes = 0;
    var rmNodes  = 0;
    if(d._children) {  addNodes = d._children.length;}
    if(d.children)  {  rmNodes = d.children.length;}
    var newNodeSum = d3.selectAll(".node")[0].length + addNodes - rmNodes
    if (newNodeSum < 350) {
      chart.onNodeClick(d)
    }
    else {
       alert("Adding any more nodes may cause the shown information to overlap.  Please close other opened nodes first")
    }
  };

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
  var outstring = "<a target='_blank' href='files/requirements/"+d.name.replace('/','_')+"-Req.html'>Requirements for "+d.name+"</a>"
  if(d.isRequirement) {
    outstring="<ul>"
    d.BFFlink.forEach(function(d) {
       outstring += "<li> <a target='_blank' href='files/requirements/"+d.replace('/','_')+"-Req.html'>Requirements for "+d+"</a></li>"
    });
    outstring += "</ul>"
  }

  return outstring
}

function generateNSRURL(d) {
  returnURLS = '<ul>'
  d.NSRLink.forEach(function(nsrEntry) {
    nsrVal = nsrEntry.split(":")[0]
    returnURLS += "<li><a target='_blank' href='files/requirements/"+nsrVal+"-Req.html'>"+d.NSRLink+"</a></li>"
  });
  returnURLS += '</ul>'
  return returnURLS

}
function getBusinessNeedURL(d) {
  return "<a target='_blank' href='files/requirements/BFFReq-"+d.busNeedId+".html'>"+d.busNeedId+"</a>"
}

function modalForBFFGroup(d) {
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
}

function modalForBusinessNeed(d) {
          if (d.isRequirement){
            $('#busNeedLink').html(getBusinessNeedURL(d));
            $('#needLink_head').show();
            $('#busNeedLink').show();
            $("#bnID").html(getBusinessNeedURL(d))
            $("#bdID_head").show();
            $("#bnID").show();
            $("#bnBFFLink").html(getRequirementsURL(d))
            $("#needBFF_head").show();
            $("#bnBFFLink").show();
            $("#bnNSR").html(generateNSRURL(d))
            $("#bnNSR_head").show();
            $("#bnNSR").show();
            $("#bnDofUpdate_head").show();
            $("#bnDofUpdate").html(d.dateUpdated);
            $("#bnDofUpdate").show();
          }
          else {
            $('#busNeedLink').html('');
            $('#needLink_head').hide();
            $('#busNeedLink').hide();
            $("#bdID_head").hide();
            $("#bnID").hide();
            $("#needBFF_head").hide();
            $("#bnBFFLink").hide();
            $("#bnNSR_head").hide();
            $("#bnNSR").hide();
            $("#bnDofUpdate_head").hide();
            $("#bnDofUpdate").html('');
            $("#bnDofUpdate").hide();
          }

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
          modalForBFFGroup(d);
          modalForBusinessNeed(d);
          if (d.hasRequirements || d.isRequirement) {
            $("#allReq").show();
          }
          else{
           $("#allReq").hide();
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
      .attr("transform", function(d, i) { return "translate("+(d.index * 310) +","+ d.depth+")"; })
  shapeLegendDisplay.append("path")
      .attr("class", function(d) {return d.name;})
      .attr("d", d3.svg.symbol().type(function(d) { return d.shape;}))
      .style("fill", function(d) {
        var color = "#1bb15c"
        if (d.hasRequirements) { color = "#7C84DE"}
        return d._children ? color : "#FFF";
      })
      .style("stroke", function(d) {
        var color = "#1bb15c"
        if (d.hasRequirements || d.isRequirement) { color = "#7C84DE"}
        if (d.recentUpdate == "Update") {color = "#DC7A20"}
        if (d.recentUpdate == "New Requirement") {color = "#B03A57 "}
        return color
      })
      //      "stroke": function(d) {chart.findNodeStroke(d)},
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
          .text("Legend");
}
    </script>
  </body>
</html>

