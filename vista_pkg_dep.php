<!DOCTYPE html>
<html>
  <head>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700|Roboto:400,700' rel='stylesheet' type='text/css'>
    <?php
      include_once "vivian_common_header.php";
      include_once "d3.dependencyedgebundling.css";
    ?>
    <?php include_once "vivian_google_analytics.php" ?>
    <script>
    $(function() {
      $('#demoexamples li').each(function (i) {
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

<body >
  <div>
    <?php include_once "vivian_osehra_image.php" ?>
  </div>
    <!-- <select id="category"></select> -->
  <div class='hint' style="position:absolute; top:120px; left:30px; font-size:0.9em; width:400px">
  <p>
This circle plot captures the interrelationships among VistA packages. Mouse over any of the packages in this graph to see incoming links (dependents) in green and the outgoing links (dependencies) in red. Click on any of the packages to view package dependency details.
  </p>
  </div>
  <div id="chart_placeholder"/>
<script type="text/javascript">

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
      }

      setCategory(categories);
      for (var node_name in map) {
        if (map[node_name].parent === undefined && node_name !== categories.name) {
          map[node_name].parent = map[categories.name];
          map[categories.name].children.push(map[node_name]);
        }
      }
      return map[categories.name];
    }

    var chart = d3.chart.dependencyedgebundling()
             .packageHierarchy(packageHierarchyByGroups)
             .nodeTextHyperLink(getPackageDoxLink);
    var localpath = "pkgdep.json";
    d3.json(localpath, function(error, classes) {
      if (error){
        errormsg = "json error " + error + " data: " + classes;
        document.write(errormsg);
        return;
      }
      classes.sort();
      d3.select('#chart_placeholder')
        .datum(classes)
        .call(chart);
    });
  });
    </script>
  </body>
</html>

