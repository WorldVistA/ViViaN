<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>

<!-- Latest compiled and minified JavaScript -->
<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

<script type="text/javascript"> 
  $.widget.bridge('uitooltip', $.ui.tooltip);
  $( document ).uitooltip({
    classes: {
        "ui-tooltip": "tooltip"
    },
    items: ".node,.bar",
    track: "true",
    show: false,
    hide: false
  })
</script>

<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="d3/d3.v3.min.js" charset="utf-8"></script>
<script src="d3.treeview.js" charset="utf-8"></script>
<script src="d3.dependencyedgebundling.js" charset="utf-8"></script>

<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

<link type="text/css" rel="stylesheet" href="style.css"/>
<link rel="stylesheet" type="text/css" href="./css/vivian.css"/>
<link rel="stylesheet" type="text/css" href="./datatable/css/jquery.dataTables.css"/>
<link rel="stylesheet" type="text/css" href="./datatable/css/buttons.dataTables.css"/>
<link rel="stylesheet" type="text/css" href="./datatable/css/dataTables.searchHighlight.css"/>

<script type="text/javascript" src="./datatable/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="./datatable/js/buttons.colVis.min.js"></script>
<script type="text/javascript" src="./datatable/js/jquery.highlight.js"></script>
<script type="text/javascript" src="./datatable/js/dataTables.searchHighlight.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
