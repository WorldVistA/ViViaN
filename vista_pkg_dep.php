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
<div class="btn-group" data-toggle="buttons" style="font-size:.8em; position:relative; left:750px; top:10px">
  <label class="btn btn-primary btn-sm active">
    <input type="radio" name="options" id="option1" value="0" checked> Circular Layout
  </label>
  <label class="btn btn-primary btn-sm">
    <input type="radio" name="options" value="1" id="option2"> Bar Chart
  </label>
</div>

<div id="bar-chart" style="display:none; font-size:0.8em">
  <div class="btn-group" data-toggle="buttons" style="position:relative; left:20px; top:10px">
    <label class="btn btn-primary btn-xs active">
      <input type="radio" name="chart-option" id="option3" value="0" checked>Dependency Chart
    </label>
    <label class="btn btn-primary btn-xs">
      <input type="radio" name="chart-option" value="1" id="option4">Stats Chart
    </label>
  </div>
  <div style="position:relative; left:20px; top:20px">
    <form id="frm-dep">
      <label for="list-dep">Sorted By:</label>
      <SELECT id="list-dep">
          <OPTION VALUE="0">Name</OPTION>
          <OPTION VALUE="1" selected="selected">Dependencies</OPTION>
          <OPTION VALUE="2">Dependents</OPTION>
      </SELECT>
    </form>
    <form id="frm-stats" style="display:none">
      <label for="list-stats">Sorted By:</label>
      <SELECT id="list-stats">
          <OPTION VALUE="3">Name</OPTION>
          <OPTION VALUE="4"  selected="selected">Routines</OPTION>
          <OPTION VALUE="5">Files</OPTION>
      </SELECT>
    </form>
  </div>
  <div id="container" style="position:relative; top:20px; width: 90%; height: 3800px; margin: 0 auto"></div>
</div>
<div id="circular-chart">
    <!-- <select id="category"></select> -->
  <div class='hint' style="position:absolute; top:100px; left:30px; font-size:0.9em; width:400px">
  <p>
This circle plot captures the interrelationships among VistA packages. Mouse over any of the packages in this graph to see incoming links (dependents) in green and the outgoing links (dependencies) in red. Click on any of the packages to view package dependency details.
  </p>
  </div>
  <div id="chart_placeholder" style="position:relative;"/>
</div>
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

		<style type="text/css">
${demo.css}
		</style>
		<script type="text/javascript">
$(function () {
    var options = {
        chart: {
            type: 'bar'
        },
        title: {
            text: 'VistA Packages Dependencies Chart'
        },
        xAxis: {
          categories: [],
          labels: {
            formatter: function (){
              var package_link_url = "http://code.osehra.org/dox/";
              var doxLinkName = this.value.replace(/ /g, '_').replace(/-/g, '_')
              var lnkUrl = package_link_url + "Package_" + doxLinkName + ".html";
              return "<a href=\"" + lnkUrl + "\">" + this.value + "</a>";
            },
            useHTML: true,
            enabled: true
          },
        },
        yAxis: {
            min: 0,
            title: {
                //text: 'Total Number Of Dependencies'
                text: null
            },
        },
        legend: {
            align: 'right',
            x: -70,
            verticalAlign: 'top',
            y: 20,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        tooltip: {
            formatter: function () {
                return '<b>' + this.x + '</b><br/>' +
                    this.series.name + ': ' + this.y + '<br/>' + '';
                    //'Total: ' + this.point.stackTotal;
            }
        },
        plotOptions: {
            bar: {
                //stacking: 'normal',
                dataLabels: {
                    enabled: true
                }
            }
        },
        credits: {
          enabled: false
        },
        series: []
    };

    function sortByNoRoutines(one, two) {
      return sortByProp(one, two, "routines");
    }

    function sortByNoFiles(one, two) {
      return sortByProp(one, two, "files");
    }

    function sortByNoDepends(one, two) {
      return sortByProp(one, two, "depends");
    }

    function sortByNoDependents(one, two) {
      return sortByProp(one, two, "dependents");
    }

    function sortByName(one, two) {
      if (one.name > two.name) {
        return 1;
      }
      if (one.name < two.name) {
        return -1;
      }
      return 0;
    }

    function sortByProp(one, two, property) {
      if (property in one && property in two) {
        return two[property].length - one[property].length;
      }
      if (property in one) {
        return -1;
      }
      return 1;
    }

    function getSeriesByJson(data, property) {
      var totLen = data.length;
      var outSeries = {"data":[], "name": property};
      for (var idx = 0; idx < totLen; idx++) {
        if (property in data[idx]) {
          outSeries.data.push(data[idx][property].length);
        }
        else {
          outSeries.data.push(0);
        }
      }
      return outSeries;
    }

    function getJsonCategoriesArray(data) {
      var totLen = data.length;
      var categories = [];
      for (var idx = 0; idx < totLen; idx++) {
        if (data[idx].name && data[idx].name !== undefined) {
          categories.push(data[idx].name);
        }
      }
      return categories;
    }

    function setCategoriesByJson(data) {
      options.xAxis.categories = getJsonCategoriesArray(data);
    }
    // read the chart data from json file
    var jsonData = [];
    $.getJSON('pkgdep.json', function(data){
      jsonData = data;
      jsonData.sort(sortByNoDepends);
      setCategoriesByJson(jsonData);
      //setSeriesByJson(jsonData, "routines");
      //setSeriesByJson(jsonData, "files");
      var depData = getSeriesByJson(jsonData,"depends");
      depData.color = "#d62728";
      options.series.push(depData);
      depData = getSeriesByJson(jsonData,"dependents");
      depData.color = "#2ca02c";
      options.series.push(depData);
      $('#container').highcharts(options);
    });
      
    // utility function for resetting chart data
    function resetChartData(val) {
      var chart = $("#container").highcharts();
      while( chart.series.length > 0 ) {
       chart.series[0].remove( false );
      }
      if (val == 0 || val == 3) {
        jsonData.sort(sortByName);
      }
      else if (val == 1) {
        jsonData.sort(sortByNoDepends);
      }
      else if (val == 2){
        jsonData.sort(sortByNoDependents);
      }
      else if (val == 4){
        jsonData.sort(sortByNoRoutines);
      }
      else if (val == 5){
        jsonData.sort(sortByNoFiles);
      }
      chart.xAxis[0].setCategories(getJsonCategoriesArray(jsonData), false);
      var depData;
      if (val < 3) {
        depData = getSeriesByJson(jsonData,"depends");
        depData.color = "#d62728";
        chart.addSeries(depData);
        depData = getSeriesByJson(jsonData,"dependents");
        depData.color = "#2ca02c";
        chart.addSeries(depData);
      }
      else {
        depData = getSeriesByJson(jsonData,"routines");
        depData.color = "#d62728";
        chart.addSeries(depData);
        depData = getSeriesByJson(jsonData,"files");
        depData.color = "#2ca02c";
        chart.addSeries(depData);
      }
      chart.redraw();
    }

    $(' #list-dep').change(function(e) {
      resetChartData($(this).val());
    });
    $(' #list-stats').change(function(e) {
      resetChartData($(this).val());
    });

      $("input:radio[name='chart-option']").change(function() {
          var chart = $("#container").highcharts();
          var value = $(this).val();
          console.log(value);
          if (value == 1) {
            $('#frm-dep').hide();
            $('#frm-stats').show();
            chart.setTitle({text: "VistA Package Statistics"});
            resetChartData($("#list-stats").val());
          }
          else {
            chart.setTitle({text: "VistA Package Dependencies"});
            $('#frm-stats').hide();
            $('#frm-dep').show();
            resetChartData($("#list-dep").val());
          }
      });

      $("input:radio[name='options']").change(function() {
          var value = $(this).val();
          console.log(value);
          if (value == 1) {
            $('#circular-chart').hide();
            $('#bar-chart').show();
          }
          else if (value == 0) {
            $('#bar-chart').hide();
            $('#circular-chart').show();
          }
      });

});
		</script>

<script src="highcharts/highcharts.js"></script>
  </body>
</html>

