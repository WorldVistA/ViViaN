<div style="position:relative; left:20px; top:2px;">
<a href="http://www.osehra.org">
<img src="http://www.osehra.org/profiles/drupal_commons/themes/commons_osehra_earth/logo.png"
style="border-width:0" height="35" width="150" alt="OSEHRA Logo" /></a>
  <div id="title" style="position:absolute; left:160px; top:10px; font-size:1.0em;">
    ViViaN<sup style="font-size:0.59em;">TM</sup>(<u>Vi</u>sualizing <u>Vi</u>stA <u>a</u>nd <u>N</u>amespace)
  </div>
</div>
<div id="demoexamples" style="font-size:.9em; position:relative; left:20px; top:10px">
  <ul class="nav nav-pills" role="navigation">
    <li><a href="index.php">Home</a></li>
    <li><a href="vista_menus.php">VistA Menus</a></li>
    <li><a href="bff_demo.php">VHA BFF Demo</a></li>
    <li><a href="vista_pkg_dep.php">VistA Package Dependency</a></li>
    <li><a href="javascript:aboutClicked();">About</a></li>
    <li><a href="http://www.osehra.org/content/visualization-open-source-project-group">Join the Visualization Working Group</a></li>
    <li class="dropdown">
      <a class="dropdown-toggle" data-toggle="dropdown" href="#">
        FOIA VistA <span class="caret"></span>
      </a>
      <ul class="dropdown-menu" role="menu">
        <li role="presentation"><a role="menuitem" tabindex="-1" href="vxvista">DSS vxVistA</a></li>
      </ul>    <li class="dropdown">
      <a class="dropdown-toggle" data-toggle="dropdown" href="#">
        VA Visualizations <span class="caret"></span>
      </a>
      <ul class="dropdown-menu" role="menu">
        <li role="presentation"><a role="menuitem" tabindex="-1" href="http://bim.osehra.org">Business Information Model</a></li>
        <li role="presentation"><a role="menuitem" tabindex="-1" href="http://him.osehra.org">Hybrid Information Model</a></li>
      </ul>
    </li>
  </ul>
</div>
<script>
  function aboutClicked(){
    var overlayDialogObj = {
      autoOpen: true,
      height: 'auto',
      width: 500,
      modal: true,
      position: ["center","center-50"],
      title: "About ViViaN(TM)"
    };
    $('#dialog-modal-about').dialog(overlayDialogObj).show();
  }
</script>
<div id="dialog-modal-about" style="display:none">
  <div id='About'>
  <p>
  ViViaN (Visualizing VistA and Namespace) is an OSEHRA developed, web based tool for viewing and browsing relationships among hierarchical and connected entities.
  </p>
  <p>Originally developed to allow browsing of the VistA code base via a tree-based functional decomposition of the code, ViViaN has expanded to include tree-based visualizations of VistA menus and the VHA Business Function Framework categorization; as well as circle plots of the interaction network among VistA packages.
  </div>
</div>
