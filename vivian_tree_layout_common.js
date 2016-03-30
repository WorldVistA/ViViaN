

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

