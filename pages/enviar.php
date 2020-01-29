<?php
include '../functions.php'; 
$grifo = new Grifo();

?>
<html>
<head>
   <title>Enviar Grifo</title>
</head>
<body>
  <div>
  <h3>Instruções para importação</h3>
    <ol>
      <li>Salve a planilha como Planilha Excel</li>
    </ol>
 </div>
   <form action="#" method="POST" enctype="multipart/form-data">
      <input type="file" name="fileUpload">
      <input type="submit" value="Enviar">
   </form>
 <div>
  <?php

     echo $grifo->upload(@$_FILES['fileUpload']);

  ?>
 </div>
</body>
</html>
