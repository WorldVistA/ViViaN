<div style="position:relative; left:20px; top:2px;">
<script type="text/javascript" src="jscoverage.js"></script>
<a href="http://www.osehra.org">
<img src="http://www.osehra.org/profiles/drupal_commons/themes/commons_osehra_earth/logo.png"
style="border-width:0" height="35" width="150" alt="OSEHRA Logo" /></a>
  <div id="title" style="position:absolute; left:160px; top:10px; font-size:1.0em;">
    VIVIAN<sup style="font-size:0.59em;">TM</sup>(<u>Vi</u>sualizing <u>Vi</u>stA <u>A</u>nd <u>N</u>amespace)
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
      </ul>
    </li>
    <li><a id="run_cov" href="javascript:runCov();" style="font-size:.6em;"><img src='lib/images/document_save.png' title="Save Coverage Result"></a>
    </li><li><a id="sav_cov" download="coverage.json" style="visibility: hidden;"><img src='lib/images/download.png' title="Download Coverage Result"></a></li>
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
  
  function runCov() {
  var textFile = null;
  var data = new Blob([jscoverage_serializeCoverageToJSON()], {type: 'text/plain'});

    // If we are replacing a previously generated file we need to
    // manually revoke the object URL to avoid memory leaks.
    if (textFile !== null) {
      window.URL.revokeObjectURL(textFile);
    }

    textFile = window.URL.createObjectURL(data);

    // returns a URL you can use as a href
    var save = document.getElementById("sav_cov");
    save.href=textFile;
    save.style.visibility = 'visible';
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
