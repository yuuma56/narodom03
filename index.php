<?php include('inc/define.php');?>
<?php
require 'inc/mysqli.inc.php';
if(isset($_GET['url'])){
  $PAGE_TEMPLATE = $_GET['url'].".php";
}else {
  // code...
    $PAGE_TEMPLATE = 'show.php';
}

include 'template.php';

?>