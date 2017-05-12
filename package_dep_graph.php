<!DOCTYPE html>
<html>
 <title>VistA Package Dependency Force-Directed Graph</title>
  <head>
  <?php
      include_once "vivian_common_header.php";
    ?>
    <script src="https://d3js.org/d3-queue.v3.min.js"></script>
    <?php include_once "vivian_google_analytics.php" ?>
    <script>
      $(function() {
        $('#navigation_buttons li').each(function (i) {
          if (i === 3) {
            $(this).removeClass().addClass("active");
          }
          else {
            $(this).removeClass();
          }
        });
      });
    </script>
  </head>

<body>
<?php include_once "vivian_osehra_image.php" ?>

<div class="ui-widget" style="position:relative; left:5px; top:50px;">
  <label for ="search" title="Select Package">Search:</label>
  <input id="search" style="width: 400px;"
  <div style="float: left; margin-left:15px; margin-right:35px;">
    <label class="btn-primary btn-sm">
      <input type="checkbox" id="colorMode"> Colorblind Mode </input>
    </label>
  </div>
</div>

<script>
///////////////////////////////////////////////////////////////////////////////
  var width = 1000,
      height = 550;

  //Set-up the force layout
  var force = d3.layout.force()
    .charge(-100)
    .linkDistance(400)
    .size([width, height]);

  var svg = d3.select("body")
    .append("svg")
    .attr("width", width)
    .attr("height", height)
    .attr("transform", "translate(" + 5 + "," + 60 + ")");

  // Read input files
  d3.queue()
    .defer(d3.json, 'pkgdep.json')
    .defer(d3.json, 'PackageCategories.json')
    .await(plot);

function plot(error, packages, categories) {
  // Organize input data into the expected format

  // First, get a mapping of package to group
  var groups = [""],  // First entry reserved from packages without a group
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

  // Then, find the nodes (packages)
  var nodes = [],
      package_names = [],
      sorted_package_names = [];  // For autocomplete
  packages.forEach(function(pkg, num) {
    var dependencies_count = 0,
        dependents_count = 0;
    if (pkg.depends) dependencies_count = pkg.depends.length
    if (pkg.dependents) dependents_count = pkg.dependents.length;
    // Only include packages that have at least one link
    if ((dependencies_count > 0) || (dependents_count > 0)) {
      package_names.push(pkg.name);
      sorted_package_names.push(pkg.name);
      var group_index = group_map[pkg.name];
      if (group_index === undefined) { group_index = 0; }
      var group_name = groups[group_index];
      var node = {
        name: pkg.name,
        package_num: package_names.indexOf(pkg.name),
        group_index: group_index,
        group: group_name,
        dependencies: pkg.depends,
        dependencies_count: dependencies_count,
        dependents: pkg.dependents,
        dependents_count: dependents_count
      };
      nodes.push(node);
    }
  });
  sorted_package_names.sort();

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

  var link = svg.selectAll(".link")
    .data(links)
    .enter().append("path")
    .attr("class", "link")
    // Assign a unique marker to each link object
    .attr("marker-end", function(d) {
      return "url(#marker" + d.link_index + ")";
    });

  var node = svg.selectAll(".node")
    .data(nodes)
    .enter().append("g")
    .attr("class", "node")
    .call(force.drag)
    .on('click', toggleConnectedNodes);

  // Node radius is determined by weight (number of links)
  var minWeight = nodes.length * 2,
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

  // Set-up the color scales
  var color1 = d3.scale.category20();
  var color2 = d3.scale.category20b();
  function getNodeColor(d) {
    if (d.group_index < 20) return color1(d.group_index);
    else return color2(d.group_index - 20);
  }

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

  node.append("circle")
    .attr("r", function(d) {
      d.radius = scale(d.weight);
      return d.radius;
    })
    .style("fill", function(d) { return getNodeColor(d); })

  // Set-up labels
  node.append("text")
    .attr("dx", 12)
    .attr("dy", ".35em")
    .text(function(d) { return d.name })
    .style("stroke", "black")
    .style("opacity", 0);

  // Set-up tooltips
  node.append("title")
    .text(function(d) {
      var text = "Name: " + d.name;
      if (d.group) text += "\nGroup: " + d.group;
      if (d.dependencies_count > 0) {
        text += "\nDependencies: " + d.dependencies_count;
      }
      if (d.dependents_count> 0) {
        text += "\nDependents: " + d.dependents_count;
      }
      return text;
    });

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

  // Create an array logging what is connected to what
  var linkedByIndex = {};
  for (i = 0; i < nodes.length; i++) {
    linkedByIndex[i + "," + i] = 1;
  };
  links.forEach(function(d) {
    linkedByIndex[d.source.index + "," + d.target.index] = 1;
  });

  function neighboring(a, b) {
    return linkedByIndex[a.index + "," + b.index];
  }

  function toggleConnectedNodes() {
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
    d3.selectAll("text")
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
    d3.selectAll("text")
      .style("opacity", 0);
    link
      .style("opacity", 0.6)
      .style("stroke", function(d) {
        d3.selectAll("#marker" + d.link_index +" path")
          .style("fill", getUnselectedLinkColor())
        return getUnselectedLinkColor(); })
      .style("stroke-width", 1);
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

  clearSearch();
}

</script>
</body>
</html>
