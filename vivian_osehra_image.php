<div style="position:relative; left:0px; top:10px;">

<div style="position:relative; left:20px; top:2px;">
<a href="http://www.osehra.org">
<img src="http://www.osehra.org/profiles/drupal_commons/themes/commons_osehra_earth/logo.png"
style="border-width:0" height="35" width="150" alt="OSEHRA Logo" /></a>
<a href="http://www.vxvista.org">
<img src="images/vxVistA15.png"
style="border-width:0" height="35" width="150" alt="vxVistA Logo" /></a>

  <div id="title" style="position:absolute; left:350px; top:10px; font-size:1.0em;">
    ViViaN<sup style="font-size:0.59em;">TM</sup>(<u>Vi</u>sualizing <u>Vi</u>stA <u>a</u>nd <u>N</u>amespace)
  </div>

  <div id="navigation_buttons" style="font-size:1.0em; position:relative; top:50px">
    <nav class="navbar navbar-default navbar-static-top">
      <div class="container-fluid">
        <ul class="nav navbar-nav">
          <li>
            <a class="brand" href="index.php">
              <img src="http://osehra.org/sites/default/files/vivian.png" width="137" height="50" alt="ViViaN Logo"/>
            </a>
          </li>
          <li><a href="vista_menus.php">vxVistA Menus</a></li>
          <li><a href="vista_pkg_dep.php">vxVistA Package Dependency</a></li>
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">vxVistA Install<span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="installScale.php">Install Timeline</a></li>
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
