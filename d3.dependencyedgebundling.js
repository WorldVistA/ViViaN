d3.chart = d3.chart || {};

/**
 * Dependency edge bundling chart for d3.js
 *
 * Usage:
 * var chart = d3.chart.dependencyedgebundling();
 * d3.select('#chart_placeholder')
 *   .datum(data)
 *   .call(chart);
 */
d3.chart.dependencyedgebundling = function(options) {

  var _radius;
  var _diameter = 600;
  var _textRadius = 160;
  var _innerRadius;
  var _nodeTextHyperLink;
  var _txtLinkGap = 5;
  var _minTextWidth = 7.0;
  var _radialTextHeight = 12;
  var _mouseOvered, _mouseOuted;

  function resetDimension() {
    _radius = _diameter / 2;
    _innerRadius = _radius - _textRadius;
  }

  function autoDimension(data) {
    // automatically resize the dimension based on total number of nodes
    var item=0, maxLength=0, length=0, maxItem;
    for (item in data){
      length = data[item].name.length;
      if (maxLength < length)
        {
          maxLength = length;
          maxItem = data[item].name;
        }
    }
    var minTextRadius = Math.ceil(maxLength * _minTextWidth);
    if (_textRadius < minTextRadius) {
      _textRadius = minTextRadius;
    }
    var minInnerRadius = Math.ceil((_radialTextHeight * data.length)/2/Math.PI);
    if (minInnerRadius < 140)
      {
        minInnerRadius = 140;
      }
    var minDiameter = 2 * (_textRadius + minInnerRadius + _txtLinkGap + 2);
    if (_diameter < minDiameter) {
      _diameter = minDiameter;
    }
  }
  // Lazily construct the package hierarchy
  var _packageHierarchy = function (classes) {
    var map = {};

    function setparent(name, data) {
      var node = map[name];
      if (!node) {
        node = map[name] = data || {name: name, children: []};
        if (name.length) {
          node.parent = map[""];
          node.parent.children.push(node);
          node.key = name;
        }
      }
    }

    setparent("", null);
    classes.forEach(function(d) {
      setparent(d.name, d);
    });

    return map[""];
  }

  // Return a list of depends for the given array of nodes.
  var packageDepends = function (nodes) {
    var map = {},
        depends = [];

    // Compute a map from name to node.
    nodes.forEach(function(d) {
      map[d.name] = d;
    });

    // For each dependency, construct a link from the source to target node.
    nodes.forEach(function(d) {
      if (d.depends) d.depends.forEach(function(i) {
        depends.push({source: map[d.name], target: map[i]});
      });
    });

    return depends;
  }
  
  function chart(selection) {
    selection.each(function(data) {
      // logic to set the size of the svg graph based on input
      autoDimension(data);
      resetDimension();
      var root = data;
      // create the layout
      var cluster =  d3.layout.cluster()
        .size([360, _innerRadius])
        .sort(null)
        .value(function(d) {return d.size; });

      var bundle = d3.layout.bundle();

      var line = d3.svg.line.radial()
          .interpolate("bundle")
          .tension(.95)
          .radius(function(d) { return d.y; })
          .angle(function(d) { return d.x / 180 * Math.PI; });

      var svg = selection.insert("svg")
          .attr("width", _diameter)
          .attr("height", _diameter)
        .append("g")
          .attr("transform", "translate(" + _radius + "," + _radius + ")");

      // get all the link and node
      var link = svg.append("g").selectAll(".link"),
          node = svg.append("g").selectAll(".node");
      
      var pkgNodes  = _packageHierarchy(root);
      var nodes = cluster.nodes(pkgNodes),
          links = packageDepends(nodes);

      link = link
          .data(bundle(links))
        .enter().append("path")
          .each(function(d) { d.source = d[0], d.target = d[d.length - 1]; })
          .attr("class", "link")
          .attr("d", line);

      node = node
          .data(nodes.filter(function(n) { return !n.children; }))
        .enter();
      if (_nodeTextHyperLink) {
        node = node.append("a")
          .attr("xlink:href", _nodeTextHyperLink)
          .attr("target", "_blank")
          .style("text-decoration", "none")
          .insert("text");
      }
      else {
        node = node.append("text");
      }
      node = node.attr("class", "node")
          .attr("dy", ".31em")
          .attr("transform", function(d) { return "rotate(" + (d.x - 90) + ")translate(" + (d.y + _txtLinkGap) + ",0)" + (d.x < 180 ? "" : "rotate(180)"); })
          .style("text-anchor", function(d) { return d.x < 180 ? "start" : "end"; })
          .text(function(d) { return d.name; })
          .on("mouseover", mouseovered)
          .on("mouseout", mouseouted);
          //.on("click", _onNodeClick);

      link.forEach(function(link) {
          link.source = node[link.source] ||
              (node[link.source] = {name: link.source});
          link.target = node[link.target] ||
              (node[link.target] = {name: link.target});
      });

      function mouseovered(d) {
        // the following two variables are used to find and class paths that are dependant
        // and depend upon the mouseovered package.
        //
        // targetNames keeps the opposite end of the paths value and index
        // duplicateIndexes holds the index again when the opposite path is found
        var targetNames = {};
        var duplicateIndexes=[]

        node
            .each(function(n) { n.target = n.source = false; });

        var links = link.filter(function(l) { return l.target === d || l.source === d; })
             .classed(palette+"-link--target link--target", function(l,i) {
              if (l.target === d)  {
                targetNames[l.source.name] = i;
                return l.source.source = true;
              }
            })
            .classed(palette+"-link--source link--source", function(l,i) {
              if(l.target.name in targetNames) {
                  duplicateIndexes.push(targetNames[l.target.name])
                  d3.select(this).classed('link--target', true);
               }
              if (l.source === d) {
                return l.target.target = true;
              }
            });

        duplicateIndexes.forEach(function(d) {
           d3.select(links[0][d]).classed('link--source', true);
           links[0][d]= links[0][d];
        });
        links.each(function() {this.parentNode.appendChild(this); });

        node
            .classed(palette+"-node--target node--target", function(n) { return n.target; })
            .classed(palette+"-node--source node--source", function(n) { return n.source; });
        
        if (_mouseOvered) {
          _mouseOvered(d);
        }
      }

      function mouseouted(d) {

        link
            .classed(palette+"-link--target link--target", false)
            .classed(palette+"-link--source link--source", false);

        node
            .classed(palette+"-node--target node--target", false)
            .classed(palette+"-node--source node--source", false);

        if (_mouseOuted) {
          _mouseOuted(d);
        }
      }
    });
  }

  chart.nodeTextHyperLink = function(n) {
    if (!arguments.length) return n;
    _nodeTextHyperLink = n;
    return chart;
  };
  
  chart.packageHierarchy = function (p) {
    if (!arguments.length) return p;
    _packageHierarchy = p;
    return chart;
  };

  chart.diameter = function (d) {
    if (!arguments.length) return d;
    _diameter = d;
    return chart;
  };

  chart.textRadius = function (t) {
    if (!arguments.length) return t;
    _textRadius = t;
    return chart;
  };

  chart.txtLinkGap = function (t) {
    if (!arguments.length) return t;
    _txtLinkGap = t;
    return chart;
  };

  chart.txtWidth = function (t) {
    if (!arguments.length) return t;
    _minTextWidth = t;
    return chart;
  };

  chart.nodeWidth = function (n) {
    if (!arguments.length) return n;
    _radialTextHeight = n;
    return chart;
  };
  
  chart.mouseOvered = function (d) {
    if (!arguments.length) return d;
    _mouseOvered = d;
    return chart;
  };

  chart.mouseOuted = function (d) {
    if (!arguments.length) return d;
    _mouseOuted = d;
    return chart;
  };

  return chart;

};
