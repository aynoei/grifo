<?php
include($_SERVER['DOCUMENT_ROOT'] . '/grifo/parts/includes.php'); 


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="google-site-verification" content="9DO1e-XQgGfalUpq8JrEDIkW2CjbqJ9duFmZPXSPqEo" />
	<title>Gestão dos Procedimentos</title>
 
<style type="text/css">
.banner {
    /*background: url(https://bootstrapmade.com/demo/themes/Medilab/img/bg-banner.jpg) no-repeat fixed;*/
    background-size: cover;
}
.bg-color {
    background-color: RGBA(13, 70, 83, 0.78);

    padding:0;
    margin:0;

    top:0;
    left:0;

    width: 100vw;
    height: 100vh;
 }
.navbar {
    border-radius: 0px;
}
.navbar-default {
    background-color: transparent;
    border: 0px;
}
.navbar-default {
    padding: 20px 0;
    transition: all 0.3s;
}

 
@media screen and (max-width: 1150px) {
th { font-size: 12px; }
td { font-size: 11px; }
 }

</style>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script type="text/javascript">
if (location.protocol != 'https:'){
 location.href = 'https:' + window.location.href.substring(window.location.protocol.length);
}
</script>
</head>
<body class=" " >
	<div class="">

   <section >
    <div class="">
             <?php include('parts/navbar.php'); ?>
      <div class="container-fluid">
        <div class="row">
          <div class="">
          </div>
        </div>
       <div class="">
        <?php include('pages/'.((isset($_GET['p']))?$_GET['p']:'calendario').'.php'); ?>
        <!---<img src="https://root-aynoei177396.codeanyapp.com/cear/parts/logo_ueg.png" class="w-50">-->
       </div>
      </div>
    </div>
  </section>
 </div>
</body>
</html>

