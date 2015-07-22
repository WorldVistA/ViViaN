function menus_main() {
  chart = d3.chart.treeview()
                .height(1050)
                .width(1280*2)
                .margins({top: 42, left: 260, bottom: 0, right: 0})
                .textwidth(300)
                .nodeTextHyperLink(getOptionDetailLink);
  chart.on("text","attr","fill",color_by_type);
  selectedIndex=0;

  menuType = [
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
}
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
  })
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


};  