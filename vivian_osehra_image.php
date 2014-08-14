<div style="position:relative; left:20px; top:2px;">
<a href="http://www.osehra.org">
<img src="http://www.osehra.org/profiles/drupal_commons/themes/commons_osehra_earth/logo.png"
style="border-width:0" height="35" width="150" alt="OSEHRA Logo" /></a>
<a href="http://www.vxvista.org">
<img src="images/vxVistA13.png"
style="border-width:0" height="44" width="100" alt="vxVistA Logo" /></a>
  <div id="title" style="position:absolute; left:260px; top:10px; font-size:1.0em;">
    VIVIAN<sup style="font-size:0.59em;">TM</sup>(<u>Vi</u>sualizing <u>Vi</u>stA <u>A</u>nd <u>N</u>amespace)
  </div>
</div>
<div id="demoexamples" style="font-size:.9em; position:relative; left:20px; top:10px">
  <ul class="nav nav-pills" role="navigation">
    <li><a href="index.php">Home</a></li>
    <li><a href="vista_menus.php">vxVistA Menus</a></li>
    <li><a href="vista_pkg_dep.php">vxVistA Package Dependency</a></li>
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
      title: "About VIVIAN"
    };
    $('#dialog-modal-about').dialog(overlayDialogObj).show();
  }
</script>
<div id="dialog-modal-about" style="display:none">
  <div id='About'>
  <p>
  VIVIAN(TM) (Visualizing VistA And Namespace) is an OSEHRA developed, web based tool for viewing and browsing relationships among hierarchical and connected entities.
  </p>
  <p>Originally developed to allow browsing of the VistA code base via a tree-based functional decomposition of the code, VIVIAN has expanded to include tree-based visualizations of VistA menus and the VHA Business Function Framework categorization; as well as circle plots of the interaction network among VistA packages.
  </div>
</div>
