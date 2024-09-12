<div style="display:inline-block; margin-left: 50px; font-size: 0;">
  <a href="https://www.worldvista.org" style="display:inline-block;">
    <img src="https://opensourcevista.net/NancysVistAServer/WorldVistALogoSmall.jpg" height="48" width="200" alt="WorldVistA Logo"/>
  </a>
  <p id="title" style="display:inline-block; font-size: 16px;">
    ViViaN<sup style="font-size: 12px;">TM</sup>(<u>Vi</u>sualizing <u>Vi</u>stA <u>a</u>nd <u>N</u>amespace)
  </p>
</div>

<div id="navigation_buttons" style="font-size:1.0em;">
  <nav class="navbar navbar-default navbar-static-top">
    <div class="container-fluid">
      <ul class="nav navbar-nav">

        <li><a class="brand" href="index.php" id="vivian"  style="height:50px; padding: 0px;">
            <img width="137" height="50" src="/vivian/vivian.png"></img></a></li>

        <li class="dropdown" id="vista-menus">
          <a class="dropdown-toggle qindex" data-toggle="dropdown" href="#">Menus<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a class="qindex-dropdown" href="vista_menus.php#19" id="option_menu">VistA Option Menus</a></li>
            <li><a class="qindex-dropdown" href="vista_menus.php#101" id="protocol_menu">VistA Protocol Menus</a></li>
          </ul>
        </li>

        <li><a href="bff_demo.php" id="bff-demo" class="qindex">VHA BFF & Requirements</a></li>

        <li class="dropdown" id="package-dependency">
          <a class="dropdown-toggle qindex" data-toggle="dropdown" href="#">Package Dependency<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a class="qindex-dropdown" href="vista_pkg_dep.php" id="circle-layout">Circular Layout</a></li>
            <li><a class="qindex-dropdown" href="vista_pkg_dep_chart.php" id="bar-chart">Bar Chart</a></li>
            <li><a class="qindex-dropdown" href="package_dep_graph.php" id="force-directed-graph">Force-Directed Graph</a></li>
          </ul>
        </li>

        <li class="dropdown" id="vista-install">
          <a class="dropdown-toggle qindex" data-toggle="dropdown" href="#">Install<span class="caret"></span></a>
          <ul class="dropdown-menu" id="vista-install">
            <li><a class="qindex-dropdown" href="installScale.php" id="install-timeline">Install Timeline</a></li>
            <li><a class="qindex-dropdown" href="patchDependency.php" id="install-tree">Install Dependency Tree</a></li>
          </ul>
        </li>

        <li class="divider-vertical"></li>

        <li class="dropdown" id="vista-interfaces">
          <a class="dropdown-toggle qindex" data-toggle="dropdown" href="#">VistA Interfaces<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a class="qindex-dropdown" onclick="window.location.href=FILES_URL+'101/All-HL7.html'" id="all_hl7">HL7</a></li>
            <li><a class="qindex-dropdown" onclick="window.location.href=FILES_URL+'779_2/All-HLO.html'" id="all_hlo">HLO</a></li>
            <li><a class="qindex-dropdown" onclick="window.location.href=FILES_URL+'870/870.html'" id="all_hl_logical">HL Logical Link</a></li>
            <li><a class="qindex-dropdown" onclick="window.location.href=FILES_URL+'ICR/All-ICR%20List.html'" id="all_icr">ICR</a></li>
            <li><a class="qindex-dropdown" onclick="window.location.href=FILES_URL+'101/All-Protocols.html'" id="all_protocols">Protocols</a></li>
            <li><a class="qindex-dropdown" onclick="window.location.href=FILES_URL+'8994/All-RPC.html'" id="all_rpc">RPC</a></li>
            <li><a class="qindex-dropdown" onclick="window.location.href=FILES_URL+'18_12/18.12.html'" id="all_web_server">Web Server</a></li>
            <li><a class="qindex-dropdown" onclick="window.location.href=FILES_URL+'18_02/18.02.html'" id="all_web_service">Web Service</a></li>
          </ul>
        </li>

        <li class="dropdown" id="vista-information">
          <a class="dropdown-toggle qindex" data-toggle="dropdown" href="#">Name and Number<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a class="qindex-dropdown" onclick="window.location.href=FILES_URL+'Namespace/Namespace.html'" id="all_name">Namespace Listing</a></li>
            <li><a class="qindex-dropdown" onclick="window.location.href=FILES_URL+'Numberspace/Numberspace.html'" id="all_number">Numberspace Listing</a></li>
          </ul>
        </li>

        <li><a href="queryVis_stats.php" id="queryVis_stats" class="qindex">Classify Data</a></li>
      </ul>

      <ul class="nav navbar-nav navbar-right">
        <li class="divider-vertical"></li>

        <li><a href="javascript:aboutClicked();" class="qindex">About</a></li>

        <li><a onclick="window.location.href=DOX_URL" class="qindex" style="cursor: pointer">DOX</a></li>

        <li><a class="qindex">FOIA VistA</a></li>
      </ul>
    </div>
  </nav>
</div>

<script>
  function aboutClicked(){
    d3.json(FILES_URL+"/filesInfo.json", function(json) {
      console.log($('#dialog-modal-dataVersion'))
      $('#dialog-modal-dataVersion').text("The information in this instance of ViViaN was generated on:  " + json["date"])
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
    ViViaN(TM) (Visualizing VistA and Namespace) was an OSEHRA developed, web based
    tool for viewing and browsing relationships among hierarchical and connected entities.
    Today <a href="https://www.worldvista.org">WorldVistA</a> maintains it.
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
