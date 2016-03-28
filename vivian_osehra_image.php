<div style="position:relative; left:0px; top:10px;">

  <div style="position:absolute; left: 20px;">
    <a href="http://www.osehra.org">
      <img src="http://www.osehra.org/profiles/drupal_commons/themes/commons_osehra_earth/logo.png"
      style="border-width:10" height="48" width="200" alt="OSEHRA Logo"/>
    </a>
  </div>

  <div id="title" style="position:absolute; left:235px; top:10px; font-size:1.0em;">
    ViViaN<sup style="font-size:0.59em;">TM</sup>(<u>Vi</u>sualizing <u>Vi</u>stA <u>a</u>nd <u>N</u>amespace)
  </div>

  <div id="navigation_buttons" style="font-size:1.0em; position:relative; top:50px">
    <nav class="navbar navbar-default navbar-static-top">
      <div class="container-fluid">
        <ul class="nav navbar-nav">
          <li>
          <a class="brand" href="index.php"> <img width="137" height="50" src="http://osehra.org/sites/default/files/vivian.png"></img></a></li>
          <li><a href="vista_menus.php">VistA Menus</a></li>
          <li><a href="bff_demo.php">VHA BFF Demo</a></li>
          <li><a href="vista_pkg_dep.php">VistA Package Dependency</a></li>
          <li><a href="installScale.php">VistA Install Timeline</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="javascript:aboutClicked();">About</a></li>
          <li><a href="http://www.osehra.org/content/visualization-open-source-project-group">Join the Visualization Working Group</a></li>
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">FOIA VistA <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="vxvista">DSS vxVistA</a></li>
            </ul>
          </li>
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">VA Visualizations <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="http://bim.osehra.org">Business Information Model</a></li>
              <li><a href="http://him.osehra.org">Hybrid Information Model</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</div>

<script>
  function aboutClicked(){
    var overlayDialogObj = {
      autoOpen: true,
      height: 'auto',
      width: 500,
      modal: true,
      position: ["center",150],
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
  </div>
</div>
