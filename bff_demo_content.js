function bff_main () {

  $("#accordion").accordion({heightStyle: 'content', collapsible: true}).hide();
  chart = d3.chart.treeview()
                .height(940)
                .width(1880)
                .margins({top: 45, left: 280, bottom: 0, right: 0})
                .textwidth(280);

  d3.json("bff.json", function(json) {
    resetAllNode(json);
    chart.on("node", "event", "mouseover", node_onMouseOver)
       .on("node", "event","mouseout", node_onMouseOut)
       .on("node", "event","click", chart.onNodeClick)
       .on("text", "attr", "cursor", function(d) {
          return d.description !== undefined && d.description ? "pointer" : "hand";
        })
       .on("text", "event", "click", text_onMouseClick)
       .on("circle", "attr", "r", function(d) { return 7 - d.depth/2; });
    d3.select("#treeview_placeholder").datum(json).call(chart);
  });

  toolTip = d3.select(document.getElementById("toolTip"));
  header = d3.select(document.getElementById("head"));
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

function text_onMouseClick(d) {
  if (d.description) {
    var overlayDialogObj = {
      autoOpen: true,
      height: 'auto',
      width: 700,
      modal: true,
      position: ["center","center-50"],
      title: "" + d.number + ": " + d.name,
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
