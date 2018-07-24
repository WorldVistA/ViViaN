<!DOCTYPE html>
<html>
  <title>VistA Package Dependency</title>
  <head>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700|Roboto:400,700' rel='stylesheet' type='text/css'>
    <?php
      include_once "vivian_common_header.php";
      include_once "d3.dependencyedgebundling.css";
    ?>
    <?php include_once "vivian_google_analytics.php" ?>
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

  <div class="hint" style="position:relative; left:20px;">
    <p>This circle plot captures the interrelationships among VistA packages.</p>
    <p>Hover over any of the packages in this graph to see incoming links (dependents)
    in one color and the outgoing links (dependencies) in a second.
    Click on any of the packages to view package dependency details.</p>
  </div>

  <div style="position:relative; left:20px">
    <div id="legend_placeholder" style="float: left;"></div>
    <div style="float: left; margin-left:15px; margin-right:35px;">
      <label class="btn-primary btn-sm">
        <input type="checkbox" id="colorMode" onclick="javascript:swapColorScheme()"> Colorblind Mode </input>
      </label>
    </div>
  </div>

  <div id="chart_placeholder"></div>

  <div id="toolTip" class="tooltip" style="opacity:0;">
    <div id="header1" class="header"></div>
    <div id="dependency" ></div>
    <div id="bothDeps"></div>
    <div id="dependents"></div>
    <div class="tooltipTail"></div>
  </div>

 <style type="text/css">
  ${demo.css}
</style>

<script type="text/javascript">
  var jsonData = [];
  var palette = "rg";
  var colorLegend = [{name: "Depends", colorClass: palette + "-link--source"},
  {name: "Dependents", colorClass: palette + "-link--target"},
  {name: "Both", colorClass: "link--target link--source"}];

  var legendColorChart = d3.chart.treeview()
                          .height(50)
                          .width(300)
                          .margins({top:42, left:10, right:0, bottom:0})
                          .textwidth(110);

  function swapColorScheme() {
    var shapeLegendText = legendColorChart.svg().selectAll("text")
    $("#colorMode").toggleClass("active");
    if(palette == "rg") {
       palette = "cb";
    }
    else {
      palette = "rg";
    }
    shapeLegendText
      .filter( function(l) { if(l) return l.name === "Dependents"})
      .attr("class", function(d) {return palette + "-link--target";});
    shapeLegendText
      .filter( function(l) { if(l) return l.name === "Depends"})
      .attr("class", function(d) {return palette + "-link--source";});
    shapeLegendText.transition();
  }

  d3.json("PackageCategories.json", function(error, data) {
    var categories = data;
    function getPackageDoxLink(node) {
      var package_link_url = "http://code.osehra.org/dox/Package_";
      var doxLinkName = node.name.replace(/ /g, '_').replace(/-/g, '_')
      return package_link_url + doxLinkName + ".html";
    }

    function packageHierarchyByGroups(classes) {
      var map = {};
      map[categories.name] = {name: name, children: []};
      function setdata(name, data) {
        var node = map[name];
        if (!node) {
          node = map[name] = data || {name: name, children: []};
        }
      }

      classes.forEach(function(d) {
        setdata(d.name, d);
      });

      function setCategory(data) {
        var child_node;
        var name = data.name;
        var node = map[name];
        if (!node) {
          // ignore package that are in categorized but
          // not in the data
          if (data.children !== undefined) {
            node = map[name] = {name: name, children: []};
          }
        }
        if (data.children !== undefined && data.children) {
          var length = data.children.length;
          for (var i=0; i<length; i++) {
            child_node = setCategory(data.children[i]);
            if (child_node) {
              child_node.parent = node;
              node.children.push(child_node);
            }
          }
        }
        return node;
      } // end setCategory()

      setCategory(categories);
      for (var node_name in map) {
        if (map[node_name].parent === undefined && node_name !== categories.name) {
          map[node_name].parent = map[categories.name];
          map[categories.name].children.push(map[node_name]);
        }
      }
      return map[categories.name];
    } // end packageHierarchyByGroups

    function mouseOvered(d) {
      var header1Text = "Name: " + d.name + "</br> Group: " + d.parent.name + "</br>";
      $('#header1').html(header1Text);
      var localDepends = [];
      if (d.depends && d.dependents) {
        localDepends = d.depends.filter(function(n) {
          return d.dependents.indexOf(n) != -1
        });
      }
      if (d.depends) {
        var depends = "Depends: " + ( d.depends.length - localDepends.length);
        $('#dependency').html(depends).addClass(palette+"-link--source");
      }
      if (localDepends.length > 0 ) {
       var both = "Both: " + localDepends.length;
       $('#bothDeps').html(both).addClass("link--source link--target");;
      }
      if (d.dependents) {
        var dependents = "Dependents: " + (d.dependents.length - localDepends.length);
        $('#dependents').html(dependents).addClass(palette+"-link--target");
      }
      d3.select("#toolTip").style("left", (d3.event.pageX + 40) + "px")
              .style("top", (d3.event.pageY + 5) + "px")
              .style("opacity", ".9");
    }

    function mouseOuted(d) {
      $('#header1').text("");
      $('#dependents').text("").removeClass(palette+"-link--target");
      $('#dependency').text("").removeClass(palette+"-link--source");
      $('#bothDeps').text("").removeClass(palette+"-link--both");
      d3.select("#toolTip").style("opacity", "0");
    }

    var chart = d3.chart.dependencyedgebundling()
                .packageHierarchy(packageHierarchyByGroups)
                .mouseOvered(mouseOvered)
                .mouseOuted(mouseOuted)
                .nodeTextHyperLink(getPackageDoxLink);
    var localpath = "files/pkgdep.json";
    d3.select("#legend_placeholder").datum(null).call(legendColorChart);

    d3.json(localpath, function(error, classes) {
      jsonData = classes;
      if (error){
        errormsg = "json error " + error + " data: " + classes;
        document.write(errormsg);
        return;
      }
      classes.sort();
      d3.select('#chart_placeholder')
        .datum(classes)
        .call(chart);
      createColorLegend(legendColorChart);
    });
  });

  function createColorLegend(legendColorChart) {
    var colorLegendDisplay = legendColorChart.svg().selectAll("g.shapeLegend")
        .data(colorLegend)
        .enter().append("svg:g")
        .attr("class", "shapeLegend")
        .attr("transform", function(d, i) { return "translate("+(i * 115) +", -10)"; });

    colorLegendDisplay.append("path")
        .attr("class", function(d) {return d.colorClass;})
        .attr("r", 3);

    colorLegendDisplay.append("svg:text")
        .attr("class", function(d) {return d.colorClass;})
        .attr("id", function(d) {return d.name;})
        .attr("x", 13)
        .attr("dy", ".31em")
        .text(function(d) {
          return  d.name;
        });
    var colorLegendDisplay = legendColorChart.svg();
    colorLegendDisplay.append("text")
            .attr("x", 0)
            .attr("y", -28 )
            .attr("text-anchor", "left")
            .style("font-size", "16px")
            .text("Color Legend");
  }
</script>
</body>
</html>

