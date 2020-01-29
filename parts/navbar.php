<?php  
if($_GET['a']=='admin'){
 $a = 'a=admin&';
 $navAdmin = '<li class="nav-item">
        <a class="nav-link" href="https://root-aynoei177396.codeanyapp.com/grifo/index.php?'.$a.'p=enviar">Enviar Planilha</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="https://root-aynoei177396.codeanyapp.com/grifo/index.php?'.$a.'p=atena">Atualizar Movimentos</a>
      </li>';

}

?>  <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
  
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
      <li class="nav-item active">
        <a class="nav-link" href="https://root-aynoei177396.codeanyapp.com/grifo/index.php?<?php echo $a; ?>">Início <span class="sr-only">(current)</span></a>
      </li>
     <li class="nav-item">
        <a class="nav-link" href="https://root-aynoei177396.codeanyapp.com/grifo/index.php?<?php echo $a; ?>p=relatorio">Relatórios</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="https://root-aynoei177396.codeanyapp.com/grifo/index.php?<?php echo $a; ?>p=tabela-grifo">Extrajudiciais</a>
      </li>
     <li class="nav-item">
        <a class="nav-link" href="https://root-aynoei177396.codeanyapp.com/grifo/index.php?<?php echo $a; ?>p=tabela-oficio">Ofício</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="https://root-aynoei177396.codeanyapp.com/grifo/index.php?<?php echo $a; ?>p=atendimento">Atendimento</a>
      </li>
     <?php echo $navAdmin; ?>
    </ul>

</div>
            <div class="text-center text-dark">
              <h1 class="text-dark">Grifo - Atena 4</h1>
            </div>
</nav>