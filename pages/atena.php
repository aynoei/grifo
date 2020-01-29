<?php
include '../vendor/autoload.php';
global $authGrifo;
$user = $authGrifo['user'];
$output = shell_exec('php pages/atena_process.php -u'.$user.' > /dev/null & echo $!');
//echo "<pre>$output</pre>";
?>
<style type="text/css">
    #progress {
      width: 500px;
      border: 1px solid #aaa;
      height: 20px;
    }
    #progress .bar {
      background-color: #ccc;
      height: 20px;
    }
  </style>
<script type="text/javascript">
 
var source = new EventSource('pages/atena_checker.php?file=<?php echo date('Ymd').'_'.$user; ?>');
source.addEventListener("upd", function(event){

       var d = JSON.parse(event.data);
       //console.log(event.data);//get data
       //console.log(d.content);
 
      document.getElementById("progress").style.width =  d.content+'%';
      document.getElementById("conteudo").innerHTML = 'Atualizando processos: '+d.id+' de '+d.total; 
 
 
        if (d.content == 100) {
             document.getElementById("message").innerHTML = '<h4 class="alert-heading">Atualização completa!</h4><div id="conteudo">'+d.total+' procedimentos</div>';
             document.getElementById("message").className = 'alert alert-success';
             document.getElementById("progress").className = 'progress-bar bg-success progress';
          }

    
}, false);
  

</script>
 <div id="message" class="alert alert-warning" style="height: 86px;"><h4 class="alert-heading">Aguarde</h4><div id="conteudo"></div></div>
 <div class="progress">
  <div id="progress" class="progress-bar progress-bar-striped bg-success progress progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
 </div>
 





