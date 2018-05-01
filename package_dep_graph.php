<!DOCTYPE html>
<html>
 <title>VistA Package Dependency Force-Directed Graph</title>
  <head>
  <?php
      include_once "vivian_common_header.php";
    ?>
    <script src="https://d3js.org/d3-queue.v3.min.js"></script>
    <?php include_once "vivian_google_analytics.php" ?>
    <!-- JQuery Buttons -->
    <script>
      $(function() {
        fileName = window.location.href.substring(window.location.href.lastIndexOf('/')+1)
        $('a[href="'+fileName+'"]').parents("#navigation_buttons li").each(function (i) {
            $(this).removeClass().addClass("active");
        });
      });
    </script>
  </head>

<body>
<?php include_once "vivian_osehra_image.php" ?>

<div class="grid" style="padding: 50px 10px 10px 10px;">
  <div style="width: 20%; float: left;">
    <div class="row" style="padding-left: 10px; padding-bottom: 10px;">
      <div><label for ="search" title="Select Package">Search for a package:</label></div>
      <div><input id="search" style="width: 300px;"></div>
    </div>

    <label class="btn-primary btn-sm">
      <input type="checkbox" id="colorMode"> Colorblind Mode </input>
    </label>

    <div class="row" style="padding-left: 10px; padding-bottom: 10px; padding-top: 10px;">
      <button id="selectAll">Select All</button>
      <button id="reset">Clear</button>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">Groups</div>
      <div id="checkBoxDiv" class="panel-body" style="overflow:auto; overflow-x:hidden; height:400px; padding: 5px;"></div>
    </div>

  </div>

  <div id="chart_placeholder" style="width: 80%; height: 600; float: left;"></div>

</div>

<script type="text/javascript">
///////////////////////////////////////////////////////////////////////////////
  var svg = d3.select("#chart_placeholder")
    .append("svg");

  //Set-up the force layout
  var force = d3.layout.force()
    .charge(-100)
    .linkDistance(400);

  resize();
  d3.select(window).on("resize", resize);
  
  // Read input files
  d3.queue()
    .defer(d3.json, 'files/pkgdep.json')
    .defer(d3.json, 'PackageCategories.json')
    .await(plot);

function resize() {
  // TODO: Where do these multipliers come from?
  width = window.innerWidth * .80 - 28, height = window.innerHeight*.9 ;
  svg.attr("width", width).attr("height", height);
  force.size([width, height]).resume();
}

function plot(error, packages, categories) {
  // Set-up the color scales
  var color1 = d3.scale.category20();
  var color2 = d3.scale.category20b();
  function getNodeColor(d) {
    if (d.group_index < 20) return color1(d.group_index);
    else return color2(d.group_index - 20);
  }

  // Organize input data into the expected format

  // First, get a mapping of package to group
  var groups = [""],  // First entry reserved for packages without a group
      group_map = {}; // package name --> group index

  var parent;
  function getChildren(group) {
    if (group.children) {
      parent = group.name;
      group.children.forEach(getChildren);
    }
    else {
      if (groups.indexOf(parent) < 0) groups.push(parent);
      var group_index = groups.indexOf(parent);
      var package_name = group.name;
      group_map[package_name] = group_index;
    }
  }
  getChildren(categories);

  // Get a list of groups
  var sortedGroups = groups.slice(1).sort();  // Skip first, empty element
  d3.select("#checkBoxDiv").selectAll("input")
      .data(sortedGroups)
    .enter().append("div")
      .append('label')
        .attr("id", function(d,i) { return 'group_label'; })
      .append("input")
        .attr("class", function(d,i) { return 'group_checkbox'; })
        .attr("value", function(d) { return d; })
        .attr("type", "checkbox");
  d3.selectAll(".group_checkbox").on("change", filterGroups);

  d3.selectAll("#group_label")
    .data(sortedGroups)
    .append("text")
      .text(function(d) { return d; })
      .style("color", function(d) {
        // TODO: Copy + paste
        var group_index = groups.indexOf(d);
        if (group_index < 20) return color1(group_index);
        else return color2(group_index - 20);
      });

  // Then, find the nodes (packages)
  var nodes = [],
      package_names = [],
      sorted_package_names = [];  // For autocomplete
  packages.forEach(function(pkg, num) {
    package_names.push(pkg.name);

    var group_index = group_map[pkg.name];
    if (group_index === undefined) { group_index = 0; }
    var group_name = groups[group_index];
    var node = {
      name: pkg.name,
      package_num: package_names.indexOf(pkg.name),
      group_index: group_index,
      group: group_name,
      dependencies: pkg.depends,
      dependents: pkg.dependents,
    };
    nodes.push(node);
  });

  // Finally, set-up the links between nodes
  var links = [];
  packages.forEach(function(pkg, num) {
    if (pkg.depends) {
      pkg.depends.forEach(function(depend_name) {
        var source_index = package_names.indexOf(pkg.name);
        var target_index = package_names.indexOf(depend_name);
        var link = {
          target: target_index,
          source: source_index,
          link_index: links.length
        };
        links.push(link);
      });
    }
  });

  // Per-type markers, as they don't inherit styles.
  svg.append("defs").selectAll("marker")
      .data(links)
    .enter().append("marker")
      // Create a unique id for each marker
      .attr("id", function(d) { return "marker" + d.link_index; })
      .attr("viewBox", "0 -5 10 10")
      .attr("refX", 10)
      .attr("refY", 0)
      .attr("markerWidth", 6)
      .attr("markerHeight", 6)
      .attr("orient", "auto")
    .append("path")
      .attr("d", "M0,-5L10,0L0,5");

  // Create the graph data structure out of the json data
  force.nodes(nodes)
    .links(links)
    .start();

  var link = svg.selectAll(".link");
  var node = svg.selectAll(".node");

  d3.selectAll(".group_checkbox").each(function(d, i) {
    if (i === 0) {
      cb = d3.select(this);
      cb.property("checked", true);
    }
  });
  filterGroups();

  function getDependencyLinkColor() {
    if (palette == "rg") return "#d62728";  // red
    else return "#ff9933";  // orange
  }

  function getDependentLinkColor() {
    if (palette == "rg") return "#2ca02c";  // green
    else return "#5233FF";  // blue
  }

  function getBothLinkColor() { return "#A02CA0"; } // purple
  function getUnselectedLinkColor() { return "#999"; } // gray

  force.on("tick", function() {
    // Don't allow nodes to fall off the screen
    d3.selectAll("circle")
      .attr("cx", function(d) {
        return d.x = Math.max(d.radius, Math.min(width - d.radius, d.x));
      })
      .attr("cy", function(d) {
        return d.y = Math.max(d.radius, Math.min(height - d.radius, d.y));
      });

    d3.selectAll("text")
      .attr("x", function(d) { return d.x; })
      .attr("y", function(d) { return d.y; });

    // Draw links/arrows to the edge of node
    link.attr("d", function(d) {
      // Total difference in x and y from source to target
      diffX = d.target.x - d.source.x;
      diffY = d.target.y - d.source.y;

      // Length of path from center of source node to center of target node
      pathLength = Math.sqrt((diffX * diffX) + (diffY * diffY));

      // x and y distances from center to outside edge of target node
      offsetX = (diffX * d.target.radius) / pathLength;
      offsetY = (diffY * d.target.radius) / pathLength;

      return "M" + d.source.x + "," + d.source.y + "L" + (d.target.x - offsetX) + "," + (d.target.y - offsetY);
    });

    // Make sure nodes don't overlap
    node.each(collide(0.5));
  });

  var choices;
  function filterGroups() {
    // Get selected groups
    choices = [];
    d3.selectAll(".group_checkbox").each(function(d) {
      cb = d3.select(this);
      if (cb.property("checked")) {
        choices.push(cb.property("value"));
      }
    });

    newData = nodes.filter(function(d) { return choices.includes(d.group);});
    newLinks = links.filter(function(d) {
      return choices.includes(d.target.group) && choices.includes(d.source.group);
    });

    force.nodes(newData);
    force.links(newLinks);
    force.start();

    // Create an array logging what is connected to what
    linkedByIndex = {};
    for (i = 0; i < newData.length; i++) {
      linkedByIndex[i + "," + i] = 1;
    };
    newLinks.forEach(function(d) {
      linkedByIndex[d.source.index + "," + d.target.index] = 1;
    });

    // Remove all links
    link = link.data([]);
    link.exit().remove();

    // Add selected links
    link = link.data(newLinks);
    link = link.enter().append("path")
      .attr("class", "link")
      // Assign a unique marker to each link object
      .attr("marker-end", function(d) {
        return "url(#marker" + d.link_index + ")";
      });

    // Remove all of the nodes
    node = node.data([]);
    node.exit().remove();

    // Add selected nodes
    node = node.data(newData);
    node.enter().append("g");
    node.attr("class", "node")
      .call(force.drag)
      .on('click', toggleConnectedNodes);

    // Node radius is determined by weight (number of links)
    var minWeight = newData.length * 2,
        maxWeight = 0;
    node.each(function(d) {
      minWeight = Math.min(minWeight, d.weight);
      maxWeight = Math.max(maxWeight, d.weight);
    });

    var minRadius = 8,
        maxRadius = 24;
    var scale = d3.scale.linear()
      .domain([minWeight, maxWeight])
      .range([minRadius,maxRadius]);

    node.append("circle")
      .attr("r", function(d) {
        d.radius = scale(d.weight);
        return d.radius;
      })
      .style("fill", function(d) { return getNodeColor(d) });

    // Set-up labels
    node.append("text")
      .attr("dx", 12)
      .attr("dy", ".35em")
      .attr("class", function(d) { return 'node-label'; })
      .text(function(d) { return d.name })
      .style("stroke", "black")
      .style("opacity", 0);

    var selected_packages = packages.filter(function(d) {
      var group_index = group_map[d.name];
      if (group_index === undefined) { group_index = 0; }
      var group_name = groups[group_index];
      return choices.includes(group_name);
    });

    var selected_package_names = [];
    sorted_package_names.length = 0;  // clear array
    selected_packages.forEach(function(pkg) {
      selected_package_names.push(pkg.name);
      sorted_package_names.push(pkg.name);
    });
    sorted_package_names.sort();

    var package_map = {};
    selected_packages.forEach(function(pkg, num) {
      //if (pkg.name === d.name) {
      var depends = [];
      if (pkg.depends) {
        depends = pkg.depends.filter(function(d) {
          return selected_package_names.includes(d);
        });
      }
      var dependents = [];
      if (pkg.dependents) {
        dependents = pkg.dependents.filter(function(d){
          return selected_package_names.includes(d);
        });
      }
      var both = [];  // Get a list of links that are dependencies AND dependents
      if (depends && dependents) {
        both = depends.filter(function(n) {
          return dependents.indexOf(n) != -1
        });
      }
      var both_count = both.length,
          dependencies_count = 0,
          dependents_count = 0;
      if (depends) dependencies_count = depends.length - both_count;
      if (dependents) dependents_count = dependents.length - both_count;

      var p = {
        both_count: both_count,
        dependencies_count: dependencies_count,
        dependents_count: dependents_count
      };
      package_map[pkg.name] = p;
    });

    // Set-up tooltips
    node.append("title")
      .text(function(d) {
        var text = "Name: " + d.name;
        if (d.group) {
          text += "\nGroup: " + d.group;
        }
        if (package_map[d.name].dependencies_count > 0) {
          text += "\nDependencies: " + package_map[d.name].dependencies_count;
        }
        if (package_map[d.name].both_count> 0) {
          text += "\nBoth: " + package_map[d.name].both_count;
        }
        if (package_map[d.name].dependents_count> 0) {
          text += "\nDependents: " + package_map[d.name].dependents_count;
        }
        return text;
      });

    clearSearch();
  }

  var padding = 1;  // separation between circles;

  function collide(alpha) {
    var quadtree = d3.geom.quadtree(nodes);
    return function(d) {
      var rb = 2*d.radius + padding,
      nx1 = d.x - rb,
      nx2 = d.x + rb,
      ny1 = d.y - rb,
      ny2 = d.y + rb;
      quadtree.visit(function(quad, x1, y1, x2, y2) {
        if (quad.point && (quad.point !== d)) {
          var x = d.x - quad.point.x,
          y = d.y - quad.point.y,
          l = Math.sqrt(x * x + y * y);
          if (l < rb) {
            l = (l - rb) / l * alpha;
            d.x -= x *= l;
            d.y -= y *= l;
            quad.point.x += x;
            quad.point.y += y;
          }
        }
        return x1 > nx2 || x2 < nx1 || y1 > ny2 || y2 < ny1;
      });
    };
  }

  var linkedByIndex = {};
  function neighboring(a, b) {
    return linkedByIndex[a.index + "," + b.index];
  }

  function toggleConnectedNodes() {
    if (d3.event.defaultPrevented) return;  // ignore drag
    d = d3.select(this).node().__data__;
    toggleNode(d);
  }

  var selected_node;
  function toggleNode(d) {
    if (selected_node != d) {
      // Clear search box
      document.getElementById("search").value = '';
      // Update selected node
      selected_node = d;
      updateDisplay();
    } else {
      clearSearch();
    }
  }

  function updateDisplay() {
    // Update node and link opacity and colors
    d3.selectAll("circle")
      // Reduce the opacity of all but the neighboring nodes
      .style("opacity", function(d) {
        return neighboring(selected_node, d) | neighboring(d, selected_node) ? 1 : 0.1;
      })
      // Toggle the color of the selected node
      .style("fill", function(d) {
        if (selected_node === d) return "#fff";
        else return getNodeColor(d);
      })
      .style("stroke", function(d) {
        if (selected_node === d) return getNodeColor(d)
        else return "#fff"; // white
      });

    // Show label for selected node
    d3.selectAll(".node-label")
      .style("opacity", function(d) {
        return selected_node === d ? 1 : 0;
      });

    link
      .style("opacity", function(d) {
        return isADependency(d) | isADependent(d) ? 1 : 0.1;
      })
      .style("stroke", function(d) {
        var is_a_dependency = isADependency(d);
        var is_a_dependent = isADependent(d);
        if (is_a_dependency) {
          var dependents = nodes[selected_node.package_num].dependents;
          is_a_dependent = (dependents && dependents.indexOf(d.target.name) >= 0);
        } else if (is_a_dependent) {
          var dependencies = nodes[selected_node.package_num].dependencies;
          is_a_dependency = (dependencies && dependencies.indexOf(d.source.name) >= 0);
        }

        // Update link and arrow (marker) color
        if (is_a_dependency && is_a_dependent) {
          d3.selectAll("#marker" + d.link_index +" path")
            .style("fill", getBothLinkColor());
          return getBothLinkColor();
        } else if (is_a_dependency) {
          d3.selectAll("#marker" + d.link_index +" path")
            .style("fill", getDependencyLinkColor())
          return getDependencyLinkColor();
        } else if (is_a_dependent) {
          d3.selectAll("#marker" + d.link_index +" path")
            .style("fill", getDependentLinkColor());
          return getDependentLinkColor();
        } else { // Not connected to selected node, link is gray
          d3.selectAll("#marker" + d.link_index +" path")
            .style("fill", getUnselectedLinkColor())
          return getUnselectedLinkColor();
        }
      })
      .style("stroke-width", function(d) {
        return isADependency(d) | isADependent(d) ? 2 : 1;
      });
  }

  // Return true if node is a dependency of the selected node
  // i.e. [selected node] --> [node]
  function isADependency(node){
    return selected_node.index == node.source.index;
  }

  // Return true if node is a dependent of the selected node
  // i.e. [selected node] <-- [node]
  function isADependent(node) {
    return selected_node.index == node.target.index;
  }

  function searchNode(eve, ui) {
    var selected_value = ui.item.value;
    var selected_node;
    node.each(function (d, i) {
      // Assumes nodes have unique names
      if (d.name == selected_value) selected_node = d;
    });
    toggleNode(selected_node);
  }

  function clearSearch() {
    // Clear search box
    document.getElementById("search").value = '';
    // Update selected node
    selected_node = null;
    // Return nodes and links to original opacity and colors
    d3.selectAll("circle")
      .style("opacity", 1)
      .style("fill", function(d) { return getNodeColor(d); })
      .style("stroke", function(d) { return "#fff"; });

   d3.selectAll(".node-label")
     .style("opacity", 0);
    link
      .style("opacity", 0.6)
      .style("stroke", function(d) {
        d3.selectAll("#marker" + d.link_index +" path")
          .style("fill", getUnselectedLinkColor())
        return getUnselectedLinkColor(); })
      .style("stroke-width", 1);
  }

  function selectAll() {
    d3.selectAll(".group_checkbox").property('checked', true);
    filterGroups();
  }

  function reset(){
    d3.selectAll(".group_checkbox").property('checked', false);
    filterGroups();
  }

  var palette = "rg";
  function swapColorScheme() {
    palette = (palette == "rg") ? "cb" : "rg";
    if (selected_node) updateDisplay();
  }

  $(function () {
    $("#search").autocomplete({
        source: sorted_package_names,
        select: searchNode
    });
  });

  document.getElementById("search").onfocus = function(){ clearSearch(); };
  document.getElementById("colorMode").onclick = function(){ swapColorScheme(); };
  document.getElementById("selectAll").onclick = function() { selectAll(); };
  document.getElementById("reset").onclick = function() { reset(); };
}

</script>
</body>
</html>
