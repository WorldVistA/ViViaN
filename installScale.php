<!DOCTYPE html>
<html>
  <title>VistA Install Timeline</title>
  <head>
    <?php
      include_once "vivian_common_header.php";
      include_once "install_scale.css";
    ?>
    <!-- JQuery Buttons -->
    <script>
      var vivianDataPath = "../vivian-data/";

      $(function() {
        $( "button" ).button().click(function(event){
          event.preventDefault();
        });
        fileName = window.location.href.substring(window.location.href.lastIndexOf('/')+1)
        $('a[href="'+fileName+'"]').parents("#navigation_buttons li").each(function (i) {
            $(this).removeClass().addClass("active");
        });

        d3.json(vivianDataPath + 'install_autocomplete.json', function(json) {
          var sortedjson = json.sort(function(a,b) { return a.localeCompare(b); });
          $("#package_autocomplete").autocomplete({
            source: sortedjson,
            select: packageAutocompleteChanged
          }).data('autocomplete')/*._trigger('select')*/;
        });


      });

    </script>
    <?php include_once "vivian_google_analytics.php" ?>
  </head>

<body>
  <script src="jquery-ui.min.js"></script>
  <div>
    <?php include_once "vivian_osehra_image.php" ?>
    <!-- <select id="category"></select> -->
  </div>
  <!-- Tooltip -->
     <div id="toolTip" class="tooltip" style="opacity:0;">
      <div id="header1" class="header"></div>
      <div id="installDate" ></div>
      <div id="filesTip" ></div>
      <div id="routinesTip" ></div>
      <div class="tooltipTail"></div>
    </div>
    <div id="ctrlToolTip" class="tooltip" style="opacity:0;">
      <div id="header1" class="header"></div>
    </div>
  </div>

  <div style="position:relative; left:20px; top:-10px;">
    <label title="Search for an option by entering the name of the option that you wish to find."> Install information for package:</label>
    <input id="package_autocomplete" size="40"></br>
    <div id='timeCtl'></div>
    <button id="timeline_date_reset">Reset</button>
  </div>
  <div id='timeline'>
    <svg></svg>
  </div>

<div id="treeview_placeholder"/>

<script type="text/javascript">
var margin = {top: 40, right: 40, bottom: 40, left:40},
        width = 1500,
        height =750;

var chartHeight = height - margin.top - margin.bottom;
var chartWidth = width - margin.left - margin.right
var y = d3.scale.linear()
    .range([chartHeight, 0]);
var shownPackage;
var index = 0
var dateArray = [];
colors = d3.scale.category20b()
backgroundColors = d3.scale.category20()
var currentDate = new Date();
var endDate = "12/31/"+ currentDate.getFullYear()
var pkgStart = ""
var pkgStop = endDate
var currentJSON = {};

/*
*  Function to handle the resetting of the time scale.
*  Clears the values of the two date boxes and redraws the
*  graph, keeping the same package, and uses the default values
*  for date selection
*/

$("#timeline_date_reset").click( function() {
  resetMenuFile(currentJSON,shownPackage,"","")
  createControl();
})

/*
*  Function to handle the graph when selecting a new package
*  from the package autocomplete.  Redraw the graph with the values
*  from the date selectors and the value of the new package
*/
function packageAutocompleteChanged(eve, ui) {

  //Read in the INSTALL JSON file
  d3.json(vivianDataPath + "install_information.json", function(json) {
    resetMenuFile(currentJSON,ui.item.label,"","");//$("#timeline_date_start")[0].value,$("#timeline_date_stop")[0].value)
    createControl();
  });
}

/*
*  Function which is called when each box in the chart has the mouse
*  hovering over it.  It generates the tooltip and positions it at
*  the location of the mouse event.
*/
function rect_onMouseOver(d) {
  var header1Text = "Name: " + d.name + "</br>";
  $('#header1').html(header1Text);
  if (d.installDate) {
    var depends = "Installed on: " + d.installDate;
    $('#installDate').html(depends);
  }
  if (d.numFiles) {
    var depends = "Number of installed files:" + d.numFiles;
    $('#filesTip').html(depends);
  }
  if (d.numRoutines) {
    var depends = "Number of installed routines: " + d.numRoutines;
    $('#routinesTip').html(depends);
  }
  d3.select("#toolTip").style("left", (d3.event.pageX + 0) + "px")
          .style("top", (d3.event.pageY - 0) + "px")
          .style("opacity", ".9");
}

/*
*   Clears the tooltip information and hides the tooltip from view
*/
function rect_onMouseOut(d) {
  $('#header1').text("");
  $('#installDate').text("");
  $('#routinesTip').text("");
  $('#filesTip').text("");
  d3.select("#toolTip").style("opacity", "0");
}

/*
* When each bar is clicked on, show the files page for each install
*/

function rect_onClick(d) {
  window.open(vivianDataPath + "9_7/9.7-" + d.ien + ".html","_blank");
}

function pkgVersionData_gen(pkgInfo) {
    var pkgVersions=[]
    var pkgVersionStart = pkgInfo[0].installDate
    pkgInfo.forEach(function(d) {
      if (d.packageSwitch) {
        pkgVersions.push({"stop" : d.installDate, "start": pkgVersionStart})
        pkgVersionStart = d.installDate
      }
    });
    pkgVersions.push({"stop" : endDate, "start": pkgVersionStart})// pkgInfo[pkgInfo.length-1].installDate
    return pkgVersions;
}

function createControl() {

        d3.select('#timeCtl').selectAll("*").remove();
        // Creating the legend control
        ctrlX = d3.time.scale()
          .domain([new Date(pkgStart), new Date(pkgStop)])
          .range([0, 750])
          .clamp(true);
        // Generate the xAxis for the above scale
        var brush = d3.svg.brush()
          .x(ctrlX)
          .extent(ctrlX.domain())
          .on("brush", ctlZoomFunc);
        function ctlZoomFunc() {
            var value = brush.extent()[0];
            var header1Text = "Date: " + ctrlX.invert(d3.event.sourceEvent.x);
            $('#ctrlToolTip div').html(header1Text);
            d3.select("#ctrlToolTip").style("left", (d3.event.sourceEvent.pageX + 0) + "px")
                    .style("top", (d3.event.sourceEvent.pageY - 0) + "px")
                    .style("opacity", ".9");
            d3.select(".extent").attr("height","7px").style("fill","steelblue")
            resetMenuFile(currentJSON, shownPackage,
                      brush.extent()[0],
                      brush.extent()[1])
        }
        var axisTimeControl = d3.select('#timeCtl').insert("svg")
                        .attr("height", 50)
                        .attr("width",1500)
                        .insert('g')
                        .attr("height", 50)
                        .attr("width",1500)
                        .attr('class', 'x axis chart')
                        .on('mousemove', function(d) {
                            var header1Text = "Date: " + ctrlX.invert(d3.event.x);
                            $('#ctrlToolTip div').html(header1Text);
                            d3.select("#ctrlToolTip").style("left", (d3.event.pageX + 0) + "px")
                                    .style("top", (d3.event.pageY - 0) + "px")
                                    .style("opacity", ".9");
                        }).on('mouseout', function(d) {
                            $('#ctrlToolTip div').html();
                            d3.select("#ctrlToolTip").style("opacity", "0");
                        })

        var ctrlXAxis = d3.svg.axis()
          .scale(ctrlX)
          .orient('middle')
          .tickSize(10)
          .tickPadding(8);
        var activityHisto = d3.layout.histogram()
                              .bins(ctrlX.ticks(d3.time.week,1))
                              .value(function(d) {return new Date(d)})
        d3.select('#timeCtl').select('g').selectAll('.histo').data(activityHisto(dateArray)).enter().append('rect')
                       .attr('fill',"firebrick")
                       .attr('x', 1)
                       .attr('width', 1)
                       .attr('height', function(d) { return d.y *3} )
                       .attr("transform", function(d) { return "translate("+ctrlX(d.x)+",-"+ d.y *3+")"})
        axisTimeControl.call(ctrlXAxis)
                       .attr("transform", "translate(0,25)");
        axisTimeControl.call(brush);
}
/*
*  Main function to set up the scales and objects necessary to show
*  the install information
*/
function resetMenuFile(json, packageName,start,stop) {
  $("#package_autocomplete").val(packageName)

    /*
    *  Capture the package specific information.  The start date
    *  of the scale should be the install date of the first patch
    *  The end date is set to be some time in the future.
    *  TODO: Add a more specific date to the end.
    */
    if (packageName in json) {
      var pkgInfo = json[packageName]

      /*
      *  Pushes the JSON information into an array so that it can be sorted.
      *  First sort by install date to acquire the earliest install date
      *  and use that as the start of the default display time frame
      */
      var pkgInfoArray = [];
      dateArray = []
      for (elem in pkgInfo) {
        pkgInfoArray.push(pkgInfo[elem])
        dateArray.push(pkgInfo[elem].installDate)
      }
      pkgInfoArray.sort(function(a,b) { return a.installDate.localeCompare(b.installDate); });
      if (start === "") { pkgStart = pkgInfoArray[0].installDate; start = pkgStart}
      if (stop === "") { pkgStop = endDate; stop = pkgStop}
      var svg = d3.select('#timeline').select('svg')
      svg.selectAll("*").remove();
      $("#timeline_date_start").datepicker("setDate",new Date(start))
      $("#timeline_date_stop").datepicker("setDate",new Date(stop))

      // Generate a time scale to position the dates along the axis
      // domain uses the dates above, rangeRound is set to keep it within
      // the visualization window
      var x = d3.time.scale()
        .domain([new Date(start), new Date(stop)])
        .range([0, width]);

      // Generate the xAxis for the above scale
      var xAxis = d3.svg.axis()
        .scale(x)
        .orient('bottom')
        .tickSize(10)
        .tickPadding(8);
      /*
      *  This function generates the background bars which represent
      *  different versions of each package.
      *
      *  ie Change from Dietetics 5.0 to Dietetics 5.5
      */
      var pkgVersions= pkgVersionData_gen(pkgInfoArray)

      /* Sorts the pkgInfo to put the tallest bars first, this should prevent the smaller bars from being
      *  hidden by a taller bar.
      */
      pkgInfoArray.sort(function(a,b) {
          return (((b.numFiles||1) + (b.numRoutines||1)) - ((a.numFiles||1) + (a.numRoutines||1)));
      });

      // Add the chart to the SVG object
      svg.attr('class', 'chart')
        .attr('width', width)
        .attr('height', height)
        .append('g')
        .attr('transform', 'translate(' + margin.left + ', ' + margin.top + ')');

      /*
      *  Adds the package version bars to the chart.  The height is the entire height
      *  of the chart, the width is from the scale value of the start date to the
      *  scale value of the stop date.  The color of the bar is determined by cycling
      *  through one of the available color palettes
      *
      *  Known issues:
      *    The generation of this data is done assuming that an install which has no asterisks
      *    in the name changes the package version.  "IFCAP" and "Accounts Receivable" are among
      *    the packages which have an oddly named package that introduces a "version change" when
      *    it should not.
      */
      svg.selectAll('.chart')
        .data(pkgVersions)
        .enter().append('rect')
        .attr('class', 'backgroundBar')
        .attr('x', function(d) {return x(new Date(d.start)); })
        .attr('width', function(d) {return (x(new Date(d.stop)) - x(new Date(d.start))); })
        .attr('y', 0)
        .attr('height', height - margin.bottom - margin.top)
        .attr("fill", function(d,i) {return backgroundColors(i); })
      /*
      *  Add the install information bars to the chart. This places these bars in front of
      *  the package version bars.  The width of the bar is fixed at 5, the height of the bar
      *  depends on the amount of routines and files sent with the package.  If the install came
      *  with neither, the values default to 1.
      *
      *
      */
      svg.selectAll('.chart')
        .data(pkgInfoArray)
        .enter().append('rect')
        .attr('class', 'bar')
        .attr('x', function(d) {return x(new Date(d.installDate)); })
        .attr('width',5)
        .attr('y', function(d) { return y(.01* ((d.numFiles||1) + (d.numRoutines||1)));})
        // This needs to be done to make all the bars touch the xAxis
        .attr('height', function(d) { return(chartHeight - y(.01* ((d.numFiles||1) + (d.numRoutines||1))))})
        .attr("fill", function(d,i) {return colors(i); }) ;  //*/;

      /*
      *  Set all of the ".bar" classed bars, all of the install information, to have the
      *  mouse events described above.
      */
      svg.selectAll('.bar')
        .on("mouseover", rect_onMouseOver)
        .on("mouseout", rect_onMouseOut)
        .on("click", rect_onClick);

      /*
      *  Append the xAxis to the SVG,
      */
      svg.append('g')
        .attr('class', 'x axis')
        .attr('transform', 'translate(0, ' + (chartHeight) + ')')
        .attr('width',chartWidth)
        .call(xAxis)
        // Selecting the text to to be able to modify the labels for the axis
        // 1. have the text run vertically
        // 2. have the anchor be at the start of the word, not the middle.
        .selectAll("text")
        .attr("y", 0)
        .attr("x", 9)
        .attr("dy", ".35em")
        .attr("transform", "rotate(90)")
        .style("text-anchor", "start");
        shownPackage=packageName;
    } else {
      alert("No information for that package")
      $("#package_autocomplete").val(shownPackage)
    }
    // Capture the package name that is being displayed
};

// Start the visualization at the

  //Read in the INSTALL JSON file
  d3.json(vivianDataPath + "install_information.json", function(json) {
    currentJSON = json
    resetMenuFile(currentJSON,'Accounts Receivable',"","")
    createControl();
  });
    </script>
  </body>
</html>
