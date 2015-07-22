function pkg_dep_main() {

    var jsonData = [];
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


      function mouseOvered(d) {
        var header1Text = "Name: " + d.name + "</br> Group: " + d.parent.name + "</br>";
        $('#header1').html(header1Text);
        if (d.depends) {
          var depends = "Depends: " + d.depends.length;
          $('#dependency').html(depends);
        }
        if (d.dependents) {
          var dependents = "Dependents: " + d.dependents.length;
          $('#dependents').html(dependents);
        }
        d3.select("#toolTip").style("left", (d3.event.pageX + 40) + "px")
                .style("top", (d3.event.pageY + 5) + "px")
                .style("opacity", ".9");
      }

      function mouseOuted(d) {
        $('#header1').text("");
        $('#dependents').text("");
        $('#dependency').text("");
        d3.select("#toolTip").style("opacity", "0");
      }

      var chart = d3.chart.dependencyedgebundling()
               .packageHierarchy(packageHierarchyByGroups)
               .mouseOvered(mouseOvered)
               .mouseOuted(mouseOuted)
               .nodeTextHyperLink(getPackageDoxLink);
      var localpath = "pkgdep.json";
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
        setupBarChart();
      });
    });

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
                return "<a href=\"" + lnkUrl + "\"" + " target=\"_blank\"" + ">" + this.value + "</a>";
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
                  var ttp = '<b>' + this.x + '</b><br/>';
                  $.each(this.points, function(){
                    ttp += this.series.name + ': ' + this.y + '<br/>' + '';
                  });
                  return ttp;
              },
              shared: true
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

      function sortByNoFileFields(one, two) {
        return sortByProp(one, two, "fields");
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
          if (two[property] instanceof Array) {
            return two[property].length - one[property].length;
          }
          else {
            return two[property] - one[property];
          }
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
            if (data[idx][property] instanceof Array) {
              outSeries.data.push(data[idx][property].length);
            }
            else {
              outSeries.data.push(data[idx][property]);
            }
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

      function setupBarChart() {
      // read the chart data from json file
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
      }

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
        else if (val == 6){
          jsonData.sort(sortByNoFileFields);
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
          depData = getSeriesByJson(jsonData,"fields");
          depData.color = "#3399FF";
          chart.addSeries(depData);
        }
        chart.redraw();
      }
  $(function () {

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
};