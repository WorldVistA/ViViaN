<!DOCTYPE html>
<html>
  <title>ViViaN</title>
  <head>
    <?php
      include_once "vivian_common_header.php";
      include_once "vivian_tree_layout.css";
    ?>
    <!-- JQuery Buttons -->
    <script>
      $(function(){
        $( "button" ).button().click(function(event){
          event.preventDefault();
        });
        fileName = window.location.href.substring(window.location.href.lastIndexOf('/')+1)
        $('a[href="'+fileName+'"]').parents("#navigation_buttons li").each(function (i) {
            $(this).removeClass().addClass("active");
        });
      });
    </script>
    <?php include_once "vivian_google_analytics.php" ?>
  </head>

<body >
  <div>
    <?php include_once "vivian_osehra_image.php" ?>
    <!-- <select id="category"></select> -->
  </div>
</br>
</br>
</br>
  <div id="toolTip" class="tooltip" style="opacity:0;">
    <div id="header1" class="header"></div>
    <div id="TotalCount" ></div>
    <div id="percentage" ></div>
    <div id="list"></div>
    <div class="tooltipTail"></div>
  </div>

<div class="hint" style="position:relative;">
  <p>
    This page will accept an JSON file and display the data in one of two forms: a pie chart or a table.
    For more information on the expected form of the uploaded JSON, see the following <a href="./Documentation/QueryVis_formatting.rst">file</a>
  </p>
</div>
<button id="toggleDisplay"> Switch Display </button>
<div><label for="file_selection">Upload a file:</label></div>
<div><input type="file" id="file_selection" size="40" ></div>
<p>Or select from the following files</p>
  <select id="vivSelect"></select>

  <script type="text/javascript">
    document.cookie = 'url=' + DOX_URL + "*.json" + "; path=/";
    var files = "<?php  foreach(glob($_COOKIE['url']) as $filename) { echo $filename.",";  };?>"
    filesArray = files.split(",")
    filesArray.pop()
    $("#vivSelect").append("<option selected disabled> -- select an local file -- </option>")
    for( var localFile in filesArray) {
      $("#vivSelect").append("<option val='"+filesArray[localFile]+"'>"+filesArray[localFile]+"</option>");
    }

  function clearFilters() {
    var table = $('#tables_placeholder').DataTable();
    table
      .search( '' )
      .columns().search( '' )
      .draw();
    $("select").prop('selectedIndex', 0);
    $('#tables_placeholder tfoot input').val('');
  }
  //Taken from https://stackoverflow.com/questions/3446170/escape-string-for-use-in-javascript-regex
  function escapeRegExp(str) {
    return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
  }
  </script>
<div>
  <h4 id="displayedName"/>
</div>
<div id="attSelectDiv">
  <p>The information can be classified with by the following fields</p>
  <select id="attributeSelect"><option selected disabled> -- select an attribute -- </option></select>
</div>
  <img id="loadingImg" style="display:none;" src="./images/loading-big.gif" alt="Loading Data"></img>
<div id="dialog-modal" style="display:none;">
        <div id='filteredObjs'></div>
    </div>
 </div>

<svg style="display: block;" height=1000 width=1500 id="pie_placeholder"/>
<table class="display" style="display: none;" id="tables_placeholder"/>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script type="text/javascript">

 //file upload code taken from https://bl.ocks.org/cjrd/6863459
  radius =450;
  var parentJSONObj = []
  var pie = d3.layout.pie()
      .value(function(d) {return d.value});
  var height=1000;
  var width=1500;
  var path = d3.arc()
      .outerRadius(radius - 10)
      .innerRadius(0);
  var color = d3.scale.category20();
  var label = d3.arc()
      .outerRadius(radius - 40)
      .innerRadius(radius - 40);
  var x = d3.scaleBand()
      .rangeRound([0, width/2])
      .padding(0.1)
      .align(0.1);
  $("#filteredObjs").accordion({heightStyle: 'content', collapsible: true}).hide();
  var y = d3.scale.linear()
        .rangeRound([height/4,0]);
  var keys = [];
  var data={"colors":[]};
  var dataSummary={}
  var dataNameIENDict={}
  var totalJSON = {}
  var curJSON={};
  var curKeys=[];
  var curData={};
  var tableObj;

  function wedge_mouseover(d) {
    $("#header1").text(d.data.label);
    $("#TotalCount").text("Found Count: " + d.data.value);
    $("#percentage").text("Percentage of total: " +((d.endAngle - d.startAngle)/.0628).toFixed(4) +"%");
    d3.select("#toolTip").style("left", (d3.event.pageX + 40) + "px")
             .style("top", (d3.event.pageY + 5) + "px")
             .style("opacity", ".9");
  }

  function wedge_mouseout(d) {
    $("#header1").text("")
    $("#TotalCount").text("")
    $("#percentage").text("")
    d3.select("#toolTip").style("opacity", "0");
  }

function findObjects(d,attrOnly) {
    $("#filteredObjs").empty()
    var selectName = d3.select('#attributeSelect').property('value')
    selectValue = dataNameIENDict[selectName.split("(")[0].trim()]
    // two filters needed to ensure that only object with the subfield are accessed
    // before trying to take a substring of them
    filteredJSON = Object.entries(totalJSON).filter(x => selectValue in x[1])
    if(attrOnly) {
      return filteredJSON;
    }
    filteredJSON = filteredJSON.filter(x => RegExp(escapeRegExp(d.data.label)+'($|[,}])',"g").test(x[1][selectValue]) )
    for (i in filteredJSON) {
      $("#filteredObjs").append("<h2>"+i+"</h2>")
      objDiv = "<div><ul>"
      for (entry in filteredJSON[i][1]) {
        objDiv += "<li>"+filteredJSON[i][1][entry]+"</li>"
      }
      objDiv += ("</ul></div>")
      $("#filteredObjs").append(objDiv)
    }
$('#filteredObjs').accordion("option", "active", 0);
$('#filteredObjs').accordion("refresh");
$('#filteredObjs').accordion({heightStyle: 'content'}).show();
}


  function wedge_mouseclick(d) {
    var overlayDialogObj = {
          autoOpen: true,
          height: ($(window).height() - 200),
          position : {my: "top center", at: "top center", of: $("#pie_placeholder")},
          width: 700,
          modal: true,
          title: "Filtered Object Information",
          open: findObjects(d,false),
       };
       $('#dialog-modal').dialog(overlayDialogObj);
       $('#dialog-modal').show();
  }

  function resetSelectors() {
      // Empty Pie Chart information
      $("#attributeSelect").empty()
      $("#attributeSelect").append("<option selected disabled> -- select an attribute -- </option>")
      $("#displayedName").empty()
      $("#vivSelect option:first").prop("selected", true);
      $("#file_selection").val('')
      var svg = d3.select('#pie_placeholder')
      svg.selectAll("*").remove();
      $("#loadingImg").hide()

        // Empty table information
      d3.select('#tables_placeholder_wrapper').remove()
      tableObj = null;
      $('<table class="display" id="tables_placeholder"/>').appendTo("body")
      return;
  }
  function parseJSON(parentJSONObj) {
    dataSummary = {}
    dataNameIENDict={}
    $("#attributeSelect").empty()
    var svg = d3.select('#pie_placeholder')
    svg.selectAll("*").remove();
    if(tableObj) {
        d3.select('#tables_placeholder_wrapper').remove()
        tableObj.destroy();
        tableObj = null;
        $('<table class="display" id="tables_placeholder"/>').appendTo("body")
    }
    tableHeader = "<thead>\n<tr>"
    if (Object.keys(parentJSONObj).length) {
        Object.keys(parentJSONObj).forEach(function(d,i) {
          // Assume all entries will have the same keys
          // gather stats by key
          for (entryVal in parentJSONObj[d]) {
            fieldName = parentJSONObj[d][entryVal].split(":")[0];
            foundEntries = [parentJSONObj[d][entryVal]]
            if (Object.keys(dataSummary).indexOf(fieldName) == -1) {
               dataSummary[fieldName]= {}
               dataNameIENDict[fieldName] = entryVal
               tableHeader += "<th>"+fieldName+"</th>\n"
            }
            if (parentJSONObj[d][entryVal].indexOf("{") != -1) {   //Checks to see if a multiple exists
                //matchReg ensures that field name is found beyond the start of the string
                var regexName = fieldName
                if (fieldName.substr(fieldName.length -1) === "S") {
                    regexName = fieldName.substr(0, fieldName.length -1) + "[S]?"
                }
                var matchReg = new RegExp("(?<!^)"+regexName + ":[ ]+?.+?[},]","gi")
                var replaceReg = new RegExp("^ |[}{]*","gi")
                foundEntries = parentJSONObj[d][entryVal].match(matchReg)
                for (object in foundEntries) {
                    var tmp = foundEntries[object].replace(replaceReg,'')
                    foundEntries[object] = tmp
                }
            }
            for (object in foundEntries) {
              if (Object.keys(dataSummary[fieldName]).indexOf(foundEntries[object])== -1){
                dataSummary[fieldName][foundEntries[object]] = 1
              }
              else {
                dataSummary[fieldName][foundEntries[object]]++
              }
            }
          }
        })
        $('#tables_placeholder').append(tableHeader += "</tr>\n</thead>\n<tbody>\n</tbody>")
        $("#attributeSelect").append("<option selected disabled> -- select an attribute -- </option>")
        for (key in dataSummary) {
          $("#attributeSelect").append("<option>"+key+" (#"+dataNameIENDict[key]+")</option>")
        }
        renderTable(parentJSONObj);
    } else {
        alert("No data in JSON file.  Please try again!")
        resetSelectors()
    }
    $("#loadingImg").hide()
    if ($("#pie_placeholder").attr("style") == "display: block;" )  {
        $("#tables_placeholder_wrapper").hide();
        $("#tables_placeholder").show();
    }
  };

  function renderWindow(jsonObj,keys,data) {
    //  OSEHRA Modification to access different type of information
    var svg = d3.select('#pie_placeholder')
    svg.selectAll("*").remove();
    //add Pie chart
    var vis = svg.append("g").data([jsonObj]).attr("transform", "translate(" +width /2 + "," +height /2 + ")");
    var arcs = vis.selectAll("g.slice").data(pie).enter().append("svg:g").attr("class", "slice");
    arcs.append("svg:path")
        .attr("d", function (d) {return path(d);})  //d.length for lists of objects?
        .attr("fill", function(d){return d.data.color})
        .on("mouseover", wedge_mouseover)
        .on("mouseout", wedge_mouseout)
        .on("click", wedge_mouseclick);
    arcs.append("svg:text")
        .attr('text-anchor','middle')
        .attr("transform", function(d) { return "translate(" + label.centroid(d) + ")"; })
        .style("fill", '#ffffff');
  }
  function renderTable(jsonObj) {
    //  OSEHRA Modification to access different type of information
    selectValue = d3.select('#displayedName').text().split(":")[1];
    for (object in jsonObj) {
      var tableEntry = "<tr>"
      for (val in dataNameIENDict) {
        var tableEntryVal = " "
        if (dataNameIENDict[val] in jsonObj[object]) {
          tableEntryVal = jsonObj[object][dataNameIENDict[val]];
        }
        tableEntry += "<td>"+ tableEntryVal +"</td>"
      }
      $('#tables_placeholder tbody').append(tableEntry += "</tr>\n")
    }
    if(!tableObj) {
      tableObj =  $("#tables_placeholder").DataTable({
              bInfo: true,
              dom: '<Bfr<t>ilp>',
              iDisplayLength: 25,
              pagingType: "full_numbers",
              bStateSave: true,
              bAutoWidth: false,
              searchHighlight: true,
              buttons: [
                {
                  text: 'Toggle Columns',
                  extend: 'colvis',
                },
                {
                  text: 'Reset Columns',
                  extend: 'colvisRestore',
                },
                {
                  text: 'Clear Search',
                  action: function ( e, dt, node, conf ) {
                    clearFilters();
                  }
                },
                {
                    extend: 'csv',
                    title: selectValue,
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function(html, indx, node) {
                                var entryList = html.split("</li>");
                                return $("<div/>").html(entryList.join("|")).text();
                            }
                        }
                    }
                },
                {
                    extend: 'pdf',
                    title: selectValue,
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                           body: function(html, indx, node) {
                              var entryList = html.split("</li>");
                              var parsedList = []
                              entryList.forEach(function(d) {
                                  if (d.indexOf("<li>") != -1) {
                                      parsedList.push("* " + d)
                                  } else {
                                      parsedList.push(d)
                                  }
                              });
                              return $("<div/>").html(parsedList.join("  ")).text();
                           }
                        }
                    },
                    customize: function (doc) {
                        // Taken from https://stackoverflow.com/questions/35642802/datatables-export-pdf-with-100-width
                        var colCount = new Array();
                        var length = $('#tables_placeholder tbody tr:first-child td').length;
                        console.log('length / number of td in report one record = '+length);
                        $('#tables_placeholder').find('tbody tr:first-child td').each(function(){
                            colCount.push(parseFloat(100 / length)+'%');
                        });
                        doc.content[1].table.widths = colCount;
                    }
                }

              ]
            });
    }
  }
  d3.select("#attributeSelect").on("change", function(event){
    selectValue = d3.select('#attributeSelect').property('value').split("(")[0].trim()
    Keys = [];
    Data={"colors":[]};
    json = []
    for (i in dataSummary[selectValue]) {
      Data[i] = dataSummary[selectValue][i]
      Keys.push(selectValue)
      json.push({"label":i, "value":dataSummary[selectValue][i],"color":color(Math.random())})
    }
    curData=Data
    curKeys=Keys
    curJSON = json
    renderWindow(json,curKeys,curData);
  });
  d3.select("#vivSelect").on("change", function(){
    $("#loadingImg").show()
    $("#file_selection").val('')
    selectValue = d3.select('#vivSelect').property('value')
    $("#displayedName").text("Displaying information from: "+ selectValue);
    d3.json(selectValue, function(error, data) {
      totalJSON = data
      parseJSON(data);
    })
  });

  d3.select("#toggleDisplay").on("click", function(){
    if ($("#pie_placeholder").attr("style") == "display: block;" )  {
      $("#pie_placeholder").hide()
      $("#attSelectDiv").hide()
      $("#tables_placeholder_wrapper").show()
      $("#tables_placeholder").show()
    } else {
      $("#pie_placeholder").show()
      $("#attSelectDiv").show()
      $("#tables_placeholder_wrapper").hide()
    }
  });

  d3.select("#file_selection").on("change", function(){
    $("#loadingImg").show()
    if (window.File && window.FileReader && window.FileList && window.Blob) {
      var uploadFile = this.files[0];
      var filereader = new window.FileReader();
      keys = [];
      data={"colors":[]};
      filereader.onload = function(){
      var txtRes = filereader.result;
      // TODO better error handling
        try{
          parentJSONObj = JSON.parse(txtRes);
          totalJSON = parentJSONObj
          parseJSON(parentJSONObj)
        }catch(err){
          window.alert("Error parsing uploaded file\nerror message: " + err.message);
          resetSelectors();
        }
      };
      if (this.files.length) {
          $("#vivSelect option:first").prop("selected", true);
          $("#displayedName").text("Displaying information from: "+ uploadFile.name);
          filereader.readAsText(uploadFile);
      } else { resetSelectors()}

    }
  });
</script>