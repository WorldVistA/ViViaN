
function _expandAllNode() {
  clearAutocomplete();
  expandAllNode(chart.nodes());
  chart.update(chart.nodes());
}

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

function expandAllNode(root) {
  expand(root)
  root.children.forEach(expandAll);
}

function collapseAllNode(root) {
  if (root.children) {
    root.children.forEach(collapseAll);
  }
  collapse(root);
}

function resetAllNode(root) {
  expand(root);
  if (root.children !== undefined && root.children) {
    root.children.forEach(collapseAll);
    // Initialize the display to show a few nodes.
    expandAll(root.children[0]);
  }
}

// Helper functions
function expandAll(d) {
  expand(d);
  if (d.children) {
    d.children.forEach(expandAll);
  }
}

function expand(d) {
  if (d._children) {
    d.children = d._children;
    d._children = null;
  }
}

function collapseAll(d) {
  if (d.children) {
    d.children.forEach(collapseAll);
    collapse(d);
  }
}

function collapse(d) {
  if (d.children) {
    d._children = d.children;
    d.children = null;
  }
}

// Highlight path
var target_node;
var target_path = [];

function openSpecificNode(target, root) {
  collapseAllNode(root);
  searchForNode(target, root);
}

function searchForNode(target, d) {
  if (d._children) {
    for(var i=0; i<d._children.length;i++) {
      var ret = searchForNode(target, d._children[i])
      if (ret) {
         expand(d);
         return true;
      }
    }
  }

  if (d.name.toUpperCase() == target.toUpperCase()) {
    expand(d);
    target_node = d;
    return true;
  } else {
    return false;
  }
}

function highlightPath(chart) {
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

    if(target == target_node) {
      $("#option_autocomplete")[0].style.border="solid 4px blue";
      $("#search_result").html("<h5>Target option found in menu, but couldn't be matched.</h5>");
      resetAllNode(chart.nodes())
      break;
      }
  }
  chart.svg().selectAll("path.link").data(target_path).forEach(highlight);
  d3.select("#treeview_placeholder").datum(chart.nodes()).call(chart);
}

function highlight(d) {
  for(var i = 0; i < d.length; i++) {
    d[i].classList.add("target");
  }
}

function clearAutocomplete() {
  document.getElementById("option_autocomplete").value= '';
  clearHighlightedPath();
}

function clearHighlightedPath() {
  chart.svg().selectAll("path.link").data(target_path).forEach(function(d) {
    for(var i =0; i< d.length; i++) {
      d[i].classList.remove("target");
    }
  });

  target_path = [];
}

