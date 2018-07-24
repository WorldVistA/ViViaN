<div style="display:inline-block; margin-left: 50px; font-size: 0;">
  <a href="http://www.osehra.org" style="display:inline-block;">
    <img src="http://www.osehra.org/profiles/drupal_commons/themes/commons_osehra_earth/logo.png" height="48" width="200" alt="OSEHRA Logo"/>
  </a>
  <p id="title" style="display:inline-block; font-size: 16px;">
    ViViaN<sup style="font-size: 12px;">TM</sup>(<u>Vi</u>sualizing <u>Vi</u>stA <u>a</u>nd <u>N</u>amespace)
  </p>
  <button style="font-size: 12px; margin-top: 5px; margin-bottom: 5px; margin-left: 500px; padding: 10px;"
          onclick="window.location.href='http://www.osehra.org/content/visualization-open-source-project-group'">
    Join the Visualization Working Group
  </button>
</div>

<div id="navigation_buttons" style="font-size:1.0em;">
  <nav class="navbar navbar-default navbar-static-top">
    <div class="container-fluid">
      <ul class="nav navbar-nav">
        <li><a class="brand" href="index.php"  style="height:50px; padding: 0px;"><img width="137" height="50" src="http://osehra.org/sites/default/files/vivian.png"></img></a></li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Menus<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="vista_menus.php#19">VistA Option Menus</a></li>
            <li><a href="vista_menus.php#101">VistA Protocol Menus</a></li>
          </ul>
        </li>

        <li><a href="bff_demo.php">VHA BFF & Requirements</a></li>

        <li class="dropdown" id="package-dependency">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Package Dependency<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="vista_pkg_dep.php" id="circle-layout">Circular Layout</a></li>
            <li><a href="vista_pkg_dep_chart.php" id="bar-chart">Bar Chart</a></li>
            <li><a href="package_dep_graph.php" id="force-directed-graph">Force-Directed Graph</a></li>
          </ul>
        </li>

        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Install<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="installScale.php">Install Timeline</a></li>
            <li><a href="patchDependency.php">Install Dependency Tree</a></li>
          </ul>
        </li>

        <li class="divider-vertical"></li>

        <li class="dropdown" id="vista-interfaces">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">VistA Interfaces<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="files/101/All-HL7.html" id="all_hl7">HL7</a></li>
            <li><a href="files/779_2/All-HLO.html" id="all_hlo">HLO</a></li>
            <li><a href="files/ICR/All-ICR%20List.html" id="all_icr">ICR</a></li>
            <li><a href="files/101/All-Protocols.html" id="all_protocols">Protocols</a></li>
            <li><a href="files/8994/All-RPC.html" id="all_rpc">RPC</a></li>
          </ul>
        </li>

        <li class="dropdown" id="vista-information">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">Name and Number<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="files/Namespace/Namespace.html" id="all_name">Namespace Listing</a></li>
            <li><a href="files/Numberspace/Numberspace.html" id="all_number">Numberspace Listing</a></li>
          </ul>
        </li>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <li><a href="javascript:aboutClicked();">About</a></li>
        <li class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">FOIA VistA <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="vxvista">DSS vxVistA</a></li>
          </ul>
        </li>

        <li class="dropdown" id="va-visualizations">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#">VA Visualizations<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="http://bim.osehra.org" id="business-information-model">Business Information Model</a></li>
            <li><a href="http://him.osehra.org" id="hybrid-information-model">Hybrid Information Model</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</div>

<script>
  function aboutClicked(){
    d3.json("files/filesInfo.json", function(json) {
      console.log($('#dialog-modal-dataVersion'))
      $('#dialog-modal-dataVersion').text("The information in this instance of ViViaN was generated on:  "+json["date"] +
                                          " from the OSEHRA VistA-M repository with a Git hash of:  "+ json["sha1"]);
    });
      var overlayDialogObj = {
        autoOpen: true,
        height: 'auto',
        width: 500,
        modal: true,
        position: {my: "center 150", of: window},
        title: "About ViViaN(TM)"
      };
      $('#dialog-modal-about').dialog(overlayDialogObj).show();
  }
</script>

<div id="dialog-modal-about" style="display:none">
  <div id='About'>
    <p>
    ViViaN(TM) (Visualizing VistA and Namespace) is an OSEHRA developed, web based
    tool for viewing and browsing relationships among hierarchical and connected entities.
    </p>
    <p>
    Originally developed to allow browsing of the VistA code base via a tree-based
    functional decomposition of the code, ViViaN has expanded to include
    tree-based visualizations of VistA menus and the VHA Business Function Framework
    categorization; as well as circle plots of the interaction network among VistA packages.
    </p>
    <p id="dialog-modal-dataVersion"></p>
  </div>
</div>
