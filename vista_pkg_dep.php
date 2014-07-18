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
    <!-- <select id="category"></select> -->
  <div id="chart_placeholder"/>
<script type="text/javascript">

  var package_link_url = "http://code.osehra.org/dox/Package_";

  function getPackageDoxLink(node) {
    var doxLinkName = node.key.replace(/ /g, '_').replace(/-/g, '_')
    return package_link_url + doxLinkName + ".html";
  }

  var chart = d3.chart.dependencyedgebundling()
           .nodeTextHyperLink(getPackageDoxLink);
  var localpath = "pkgdep.json";
  d3.json(localpath, function(error, classes) {
  if (error){
    errormsg = "json error " + error + " data: " + classes;
    document.write(errormsg);
    return;
  }
  d3.select('#chart_placeholder')
    .datum(classes)
    .call(chart);
  });

    </script>
  </body>
</html>

