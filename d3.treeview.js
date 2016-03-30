d3.chart = d3.chart || {};

/**
 * d3 tree layout view for d3.js
 *
 * Usage:
 * var chart = d3.chart.treeview();
 * d3.select('#treelayout_placeholder')
 *   .datum(data)
 *   .call(chart);
 */

d3.chart.treeview = function(option) {

  var _width = 1600, _height = 800,
      _margins = {top: 0, left: 150, right: 0, bottom: 0},
      _textwidth = 220,
      _svg,
      _nodes,
      _i = 0,
      _tree,
      _diagonal,
      _nodeTextHyperLink,
      _cust = {
        node: {
          event:{
            click: chart.onNodeClick
          },
          attr: {},
          style: {}
        },
        text: {
          event:{},
          attr:{},
          style:{
          }
        },
        path: {
          attr: {
            r: function(d) {return 4.5;}
          },
          style:{
            fill: fillNodeCircle
          },
          event:{}
        }
      };

  function chart(selection) {
    selection.each(function(data) {
      _nodes = data;
      renderTree(selection);
    });
  }

  function renderTree(selection) {
    if (!_svg) {
        _svg = selection.insert("svg")
                .attr("height", _height)
                .attr("width", _width)
                .append("svg:g")
                .attr("transform", function(d){
                  return "translate(" + _margins.left + "," + _margins.top + ")";
                });
    }

    renderBody(_svg);
  };

  function renderBody(svg) {
    _tree = d3.layout.tree()
            .size([
      (_height - _margins.top - _margins.bottom),
      (_width - _margins.left - _margins.right)
    ]);

    _diagonal = d3.svg.diagonal()
            .projection(function (d) {
                return [d.y, d.x];
            });
    if (_nodes != null) {
      _nodes.x0 = (_height - _margins.top - _margins.bottom) / 2;
      _nodes.y0 = 0;

      render(_nodes);
    }
  }

  function render(source) {
      var nodes = _tree.nodes(_nodes).reverse();
      renderNodes(nodes, source);
      renderLinks(nodes, source);
  }

  function _customization(src, target, type, prop) {
    var custinfos =  _cust[target];
    var custs = {};
    if (custinfos !== undefined && custinfos) {
      if (type !== undefined) {
        if (custinfos[type] !== undefined) {
          if (prop !== undefined &&
              custinfos[type][prop] !== undefined &&
              custinfos[type][prop]) {
            custs[type] = {};
            custs[type][prop] = custinfos[type][prop];
          } else {
            custs = {};
            custs[type] = custinfos[type];
          }
        }
      }
      for (var tp in custs) {
        if (custs[tp]) {
          var pt;
          switch (tp) {
            case 'event':
              for (pt in custs[tp]) {
                //console.log("on " + pt + " " + target);
                src.on(pt, custs[tp][pt]);
              }
              break;
            case 'attr':
              for (pt in custs[tp]) {
                //console.log("attr " + pt + " " + target);
                src.attr(pt, custs[tp][pt]);
              }
              break;
            case 'style':
              for (pt in custs[tp]) {
                //console.log("style " + pt + " " + target);
                src.style(pt, custs[tp][pt]);
              }
              break;
          }
        }
      }
    }
    return src;
  }

  function renderNodes(nodes, source) {
    nodes.forEach(function (d) {
        d.y = d.depth * _textwidth;
    });

    var node = _svg.selectAll("g.node")
            .data(nodes, function (d) {
                return d.id || (d.id = ++_i);
            });

    var nodeEnter = node.enter().append("svg:g")
            .attr("class", "node")
            .attr("transform", function (d) {
                return "translate(" + source.y0
        + "," + source.x0 + ")";
            });
    _customization(nodeEnter, 'node', 'event');

    var circleEnter = nodeEnter.append("path")
            .style("stroke","steelblue")
            .attr("d",d3.svg.symbol().type(find_node_shape))
            .attr("r", 1e-6);

    _customization(circleEnter, 'path', 'event');
    _customization(circleEnter, 'path', 'style');

    var nodeUpdate = node.transition()
            .attr("transform", function (d) {
                return "translate(" + d.y + "," + d.x + ")";
            });
    var circleUpdate = nodeUpdate.select("path");
    _customization(circleUpdate, 'path', 'attr');
    _customization(circleUpdate, 'path', 'style');

    var nodeExit = node.exit().transition()
            .attr("transform", function (d) {
                return "translate(" + source.y
        + "," + source.x + ")";
            })
            .remove();

    nodeExit.select("g")
            .attr("r", 1e-6);

    renderLabels(nodeEnter, nodeUpdate, nodeExit);

    nodes.forEach(function (d) {
        d.x0 = d.x;
        d.y0 = d.y;
    });
  }

  function find_node_shape(d) {
    var shape = "circle";
    if (d.children || d._children) {
      shape = "triangle-up";
    } else if (d.hasSubpackage) {
      shape = "diamond";
    } else if (d.isSubpackage) {
      shape = "square";
    }
    return shape;
  }

  function renderLabels(nodeEnter, nodeUpdate, nodeExit) {
    var textEnter;
    if (_nodeTextHyperLink) {
      textEnter = nodeEnter.append("a")
        .attr("xlink:href", _nodeTextHyperLink)
        .attr("target", "_blank")
        .style("text-decoration", "none")
        .insert("svg:text");
    } else {
      textEnter = nodeEnter.append("svg:text");
    }
    textEnter = textEnter.attr("x", function (d) {
                return d.children || d._children ? -10 : 10;
            })
            .attr("dy", ".35em")
            .attr("text-anchor", function (d) {
                return d.children || d._children ? "end" : "start";
            })
            .text(function (d) {
                return d.name;
            })
            .style("fill-opacity", 1e-6);
    _customization(textEnter, "text", "attr");
    _customization(textEnter, "text", "event");
    nodeUpdate.select("text")
            .style("fill-opacity", 1);

    nodeExit.select("text")
            .style("fill-opacity", 1e-6);
  }

  function renderLinks(nodes, source) {
    var link = _svg.selectAll("path.link")
            .data(_tree.links(nodes), function (d) {
                return d.target.id;
            });

    link.enter().insert("svg:path", "g")
            .attr("class", "link")
            .attr("d", function (d) {
                var o = {x: source.x0, y: source.y0};
                return _diagonal({source: o, target: o});
            });

    link.transition()
            .attr("d", _diagonal);

    link.exit().transition()
            .attr("d", function (d) {
                var o = {x: source.x, y: source.y};
                return _diagonal({source: o, target: o});
            })
            .remove();
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

  function fillNodeCircle(d) {
    return d._children ? "lightsteelblue" : "#FFF";
  }

  chart.width = function (w) {
    if (!arguments.length) {
      return _width;
    } else {
      _width = w;
      return chart;
    }
  };

  chart.height = function (h) {
    if (!arguments.length) {
      return _height;
    } else {
      _height = h;
      return chart;
    }
  };

  chart.margins = function (m) {
    if (!arguments.length) {
      return _margins;
    } else {
      _margins = m;
      return chart;
    }
  };

  chart.textwidth = function (m) {
    if (!arguments.length) {
      return _textwidth;
    } else {
      _textwidth = m;
      return chart;
    }
  };

  chart.nodes = function (n) {
    if (!arguments.length) {
      return _nodes;
    } else {
      _nodes = n;
      return chart;
    }
  };

  chart.svg = function (n) {
    return _svg;
  };

  chart.on = function(target, type, prop, value) {
    if (arguments.length == 3) {
      return _cust[target][type][prop];
    }
    if (arguments.length > 3) {
      _cust[target][type][prop] = value;
    }
    return chart;
  };

  chart.onNodeClick = function (d) {
    toggle(d);
    render(d);
  };

  chart.update = function (d) {
    render(d);
  };

  chart.nodeTextHyperLink = function(n) {
    if (!arguments.length) {
      return n;
    } else {
      _nodeTextHyperLink = n;
      return chart;
    }
  };
  return chart;
}
