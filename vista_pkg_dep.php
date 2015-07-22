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
          <OPTION VALUE="6">File Fields</OPTION>
      </SELECT>
    </form>
  </div>
  <div id="container" style="position:relative; top:20px; width: 90%; height: 4200px; margin: 0 auto"></div>
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
  <div id="toolTip" class="tooltip" style="opacity:0;">
      <div id="header1" class="header"></div>
      <div id="dependency" style="color:#d62728;"></div>
      <div id="dependents" style="color:#2ca02c;"></div>
      <div  class="tooltipTail"></div>
  </div>

		<style type="text/css">
${demo.css}
		</style>
<script src="lib/highcharts/highcharts.js"></script>
  <script type="text/javascript" src="vista_pkg_dep_content.js"></script>
    <script>
      pkg_dep_main()
    </script>
  </body>
</html>

