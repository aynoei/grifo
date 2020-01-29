<?php
/*
total de procedimentos - ok
vencidos por classe - ok
quantidade por classe - ok
quantidade por ano de posse/instauração
tempo médio de movimentacao
tmt por procedimento
tempo médio de tramitação
tmt por classe
quantidade por assunto
quantidade por taxonomia
Relatorio por movimento - GROUP BY - ok

*/

use Medoo\Medoo;
$biblioteca = new Biblioteca\Custom();
$tabbelas = new Biblioteca\Tabelas();

include '../functions.php'; 
$grifo = new Grifo();

global $authGrifo;

$pj = $authGrifo['promotoria'];
$id_user = $authGrifo['user'];

$database = new Medoo([
         'database_type' => 'mysql',
         'database_name' => 'grifo',
         'server' => 'localhost',
         'username' => 'root',
         'password' => ''
     ]);

 $lista_total = $database->query("SELECT * FROM procedimentos WHERE promotoria = '$pj'" )->fetchAll();
/***********************************************************************************************************/
function href($x){
 return '<a class="text-dark" href="https://root-aynoei177396.codeanyapp.com/grifo/index.php?p=tabela-grifo&buscar='.$x.'" target="_blank">'.$x."</a>";
}

function array_group(array $data, $by_column){
    $result = [];

    foreach ($data as $item) {
        $column = $item[$by_column];
        unset($item[$by_column]);
        if (isset($result[$column])) {
            $result[$column][] = $item;
        } else {
            $result[$column] = array($item);
        }
    }

    return $result;
}

/************************************************************************************************************/
function gerais(){
  global $tabbelas, $biblioteca, $database, $grifo, $pj, $id_user;
 $lista = $database->query("SELECT * FROM procedimentos WHERE promotoria = '$pj' GROUP BY classe ORDER BY classe asc" )->fetchAll();
 $lista_total = $database->query("SELECT * FROM procedimentos" )->fetchAll();
 $localizador = 'gerais';
/*********************************************Tabela Dados Gerais*****************************************************************/
foreach($lista as $listagem){
$classe = $listagem['classe']; 
$total_contagem = $database->query("SELECT * FROM procedimentos WHERE promotoria = '$pj' AND classe = '$classe'" )->fetchAll();

$vencidos = array();
$tempoTramitacao = array();
$tempoMovimento = array();
foreach($total_contagem as $contagem){ //calcula a quantidade de vencidos
   $regularidade = $grifo->regularidade($contagem['classe'],$contagem['data_posse'],$contagem['data_instauracao'],$contagem['data_prorrogacao']);
     if($regularidade['tempo'] < 0){
       $vencidos[] = 1;
     } 
 $tempoTramitacao[] = $biblioteca->diffDate($contagem['data_registro']);
 $tempoMovimento[] = $biblioteca->diffDate($contagem['data_movimento']);
}

$vencidos_css = (array_sum($vencidos) > 0)?'badge badge-danger':'';

/********************************************************CRIANDO A TABELA****************************************************************/	
$Blista = array(
['tipo' => 'body-td', 'css'=> 'text-center', 'html' => 'hidden','texto'=> $id],
['tipo' => 'body-td', 'css'=> 'text-left', 'html' => '','texto'=> href($listagem['classe'])], 
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'','texto'=> count($total_contagem)],
['tipo' => 'body-td', 'css'=> 'text-center ', 'html'=>'','texto'=> '<span class="'.$vencidos_css.'">'.array_sum($vencidos).'</span>'],
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'','texto'=> round(array_sum($tempoTramitacao)/count($tempoTramitacao))],
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'','texto'=> round(array_sum($tempoMovimento)/count($tempoMovimento))],
['tipo' => 'body-td', 'css' => '', 'html' => 'hidden', 'texto'=>'-']
);
$btr = array('css' => $regularidade_css.' pointer text-center  detalhes_tr tr_'.$id, 'id' => $id, 'alt'=> $localizador, 'name' => $localizador);	
$trlinhaB[] = $tabbelas->geraBody($Blista,$btr);	
}
//****************************************TR da tabela******************************************************************************/	
$table = array('css'=> 'tabela_relatorio border border-none rounded tabela_datatable table-hover tabela_'.$localizador.' table-hover table-striped table-condensed', 'html' =>'', 'id' => $localizador);
$th = array(
	['css' => '', 'html'=>'hidden','texto'=>''], 
	['css' => 'px-1', 'html'=>'','texto'=>'Classe'],
	['css' => 'px-1', 'html'=>'','texto'=>'Qtde'],
	['css' => 'px-1', 'html'=>'','texto'=>'Venc'],
	['css' => 'px-1', 'html'=>'title="Tempo Médio de Tramitação - Dias"','texto'=>'TMT'],
	['css' => 'px-1', 'html'=>'title="Tempo Médio dos Movimentos - Dias"','texto'=>'TMM'], 
 ['css' => '', 'html'=>'hidden','texto'=>'-']);	
$htr = array('css'=> 'text-center text-uppercase ');
$thlinhaH = $tabbelas->geraHead($th,$htr);
return $tabbelas->tabela(array('table'=> $table,'head-tr'=>$thlinhaH,'body-tr'=>$trlinhaB));
}
/*||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||*/
function atuacao(){
 global $tabbelas, $biblioteca, $database, $grifo, $pj, $id_user;
 $lista_total = $database->query("SELECT * FROM procedimentos WHERE promotoria = '$pj'" )->fetchAll();
 $localizador = 'atuacao';
/*********************************************Tabela Outras Informações*****************************************************************/
$lista_total_atuacao = $database->query("SELECT * FROM procedimentos WHERE promotoria = '$pj' GROUP BY atuacao ORDER BY atuacao asc" )->fetchAll();
foreach($lista_total_atuacao as $listagem){
$atuacao = $listagem['atuacao']; 

$total_contagem_atuacao = $database->query("SELECT * FROM procedimentos WHERE promotoria = '$pj' AND atuacao = '$atuacao'" )->fetchAll();
$id = '';
/********************************************************CRIANDO A TABELA****************************************************************/	
$Blista = array(
['tipo' => 'body-td', 'css'=> 'text-center', 'html' => 'hidden','texto'=> $id],
['tipo' => 'body-td', 'css'=> 'text-left text-uppercase', 'html' => '','texto'=> href($listagem['atuacao'])], 
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'','texto'=> count($total_contagem_atuacao)],
['tipo' => 'body-td', 'css' => '', 'html' => 'hidden', 'texto'=>'-']
);
$btr = array('css' => $regularidade_css.' pointer text-center  detalhes_tr tr_'.$id, 'id' => $id, 'alt'=> $localizador, 'name' => $localizador);	
$trlinhaB[] = $tabbelas->geraBody($Blista,$btr);	
}
//****************************************TR da tabela******************************************************************************/	
$table = array('css'=> 'tabela_relatorio border border-none rounded tabela_'.$localizador.' table-hover table-striped table-condensed', 'html' =>'', 'id' => $localizador);
$th = array(
	['css' => '', 'html'=>'hidden','texto'=>''], 
	['css' => 'px-2', 'html'=>'','texto'=>''],
	['css' => 'px-2', 'html'=>'','texto'=>'Total'],
 ['css' => '', 'html'=>'hidden','texto'=>'-']);	
$htr = array('css'=> 'text-center text-uppercase ');
$thlinhaH = $tabbelas->geraHead($th,$htr);
return $tabbelas->tabela(array('table'=> $table,'head-tr'=>$thlinhaH,'body-tr'=>$trlinhaB));
}
/*||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||*/
function assunto(){
 global $tabbelas, $biblioteca, $database, $grifo, $pj, $id_user;
 $lista_total = $database->query("SELECT * FROM procedimentos WHERE promotoria = '$pj'" )->fetchAll();
 $localizador = 'assunto';
/*********************************************Tabela Outras Informações*****************************************************************/
$lista_total_assuntos = $database->query("SELECT * FROM procedimentos WHERE promotoria = '$pj'" )->fetchAll();
foreach($lista_total_assuntos as $assunto){
    $ass = explode(',',$assunto['assunto']);
     foreach($ass as $a){
       $lista_total_assunto[] = $a;
     } 
}
$nassuntos = @array_unique($biblioteca->multi_uma($lista_total_assunto)); 

 
foreach($nassuntos as $listagem){
$assunto = $listagem; 
 
$total_assunto = array();
foreach($lista_total_assuntos as $lta){
 $ass = explode(',',$lta['assunto']);
 if(in_array($assunto,$ass)){
  $total_assunto[] = 1;
 }
}

$id = '';
/********************************************************CRIANDO A TABELA****************************************************************/	
$Blista = array(
['tipo' => 'body-td', 'css'=> 'text-center', 'html' => 'hidden','texto'=> $id],
['tipo' => 'body-td', 'css'=> 'text-left text-uppercase', 'html' => '','texto'=> href($assunto)], 
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'','texto'=> array_sum($total_assunto)],
['tipo' => 'body-td', 'css' => '', 'html' => 'hidden', 'texto'=>'-']
);
$btr = array('css' => $regularidade_css.' pointer text-center  detalhes_tr tr_'.$id, 'id' => $id, 'alt'=> $localizador, 'name' => $localizador);	
$trlinhaB[] = $tabbelas->geraBody($Blista,$btr);	
}
//****************************************TR da tabela******************************************************************************/	
$table = array('css'=> 'tabela_relatorio border border-none rounded tabela_'.$localizador.' table-hover table-striped table-condensed', 'html' =>'', 'id' => $localizador);
$th = array(
	['css' => '', 'html'=>'hidden','texto'=>''], 
	['css' => 'px-2', 'html'=>'','texto'=>''],
	['css' => 'px-2', 'html'=>'','texto'=>'Total'],
 ['css' => '', 'html'=>'hidden','texto'=>'-']);	
$htr = array('css'=> 'text-center text-uppercase ');
$thlinhaH = $tabbelas->geraHead($th,$htr);
return $tabbelas->tabela(array('table'=> $table,'head-tr'=>$thlinhaH,'body-tr'=>$trlinhaB));
}
/*||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||*/
function taxonomias(){
 global $tabbelas, $biblioteca, $database, $grifo, $pj, $id_user;
 $lista_total = $database->query("SELECT * FROM procedimentos WHERE promotoria = '$pj'" )->fetchAll();
 $localizador = 'taxonomia';
/*********************************************Tabela Outras Informações*****************************************************************/
$lista_total_taxonomia = $database->query("SELECT * FROM procedimentos WHERE promotoria = '$pj' GROUP BY taxonomia ORDER BY taxonomia asc" )->fetchAll();
foreach($lista_total_taxonomia as $listagem){
$taxonomia = $listagem['taxonomia']; 

$total_contagem_taxonomia = $database->query("SELECT * FROM procedimentos WHERE taxonomia = '$taxonomia'" )->fetchAll();
$id = '';
/********************************************************CRIANDO A TABELA****************************************************************/	
$Blista = array(
['tipo' => 'body-td', 'css'=> 'text-center', 'html' => 'hidden','texto'=> $id],
['tipo' => 'body-td', 'css'=> 'text-left text-uppercase', 'html' => '','texto'=> href($listagem['taxonomia'])], 
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'','texto'=> count($total_contagem_taxonomia)],
['tipo' => 'body-td', 'css' => '', 'html' => 'hidden', 'texto'=>'-']
);
$btr = array('css' => $regularidade_css.' pointer text-center  detalhes_tr tr_'.$id, 'id' => $id, 'alt'=> $localizador, 'name' => $localizador);	
$trlinhaB[] = $tabbelas->geraBody($Blista,$btr);	
}
//****************************************TR da tabela******************************************************************************/	
$table = array('css'=> 'tabela_relatorio border border-none rounded tabela_'.$localizador.' table-hover table-striped table-condensed', 'html' =>'', 'id' => $localizador);
$th = array(
	['css' => '', 'html'=>'hidden','texto'=>''], 
	['css' => 'px-2', 'html'=>'','texto'=>''],
	['css' => 'px-2', 'html'=>'','texto'=>'Total'],
 ['css' => '', 'html'=>'hidden','texto'=>'-']);	
$htr = array('css'=> 'text-center text-uppercase ');
$thlinhaH = $tabbelas->geraHead($th,$htr);
return $tabbelas->tabela(array('table'=> $table,'head-tr'=>$thlinhaH,'body-tr'=>$trlinhaB));
}
/*||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||*/
function movimentos(){
 global $tabbelas, $biblioteca, $database, $grifo, $pj, $id_user;
 $lista_total = $database->query("SELECT * FROM procedimentos WHERE promotoria = '$pj'" )->fetchAll();
 $localizador = 'movimento';
/*********************************************Tabela Outras Informações*****************************************************************/
$lista_total_ = $database->query("SELECT * FROM procedimentos WHERE promotoria = '$pj' GROUP BY tipo ORDER BY tipo asc" )->fetchAll();
foreach($lista_total_ as $listagem){
$movimento = $listagem['tipo']; 

$total_contagem_ = $database->query("SELECT * FROM procedimentos WHERE promotoria = '$pj' AND tipo = '$movimento'" )->fetchAll();
$id = '';
/********************************************************CRIANDO A TABELA****************************************************************/	
$Blista = array(
['tipo' => 'body-td', 'css'=> 'text-center', 'html' => 'hidden','texto'=> $id],
['tipo' => 'body-td', 'css'=> 'text-left text-uppercase', 'html' => '','texto'=> href($listagem['tipo'])], 
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'','texto'=> count($total_contagem_)],
['tipo' => 'body-td', 'css' => '', 'html' => 'hidden', 'texto'=>'-']
);
$btr = array('css' => $regularidade_css.' pointer text-center  detalhes_tr tr_'.$id, 'id' => $id, 'alt'=> $localizador, 'name' => $localizador);	
$trlinhaB[] = $tabbelas->geraBody($Blista,$btr);	
}
//****************************************TR da tabela******************************************************************************/	
$table = array('css'=> 'tabela_relatorio border border-none rounded tabela_'.$localizador.' table-hover table-striped table-condensed', 'html' =>'', 'id' => $localizador);
$th = array(
	['css' => '', 'html'=>'hidden','texto'=>''], 
	['css' => 'px-2', 'html'=>'','texto'=>''],
	['css' => 'px-2', 'html'=>'','texto'=>'Total'],
 ['css' => '', 'html'=>'hidden','texto'=>'-']);	
$htr = array('css'=> 'text-center text-uppercase ');
$thlinhaH = $tabbelas->geraHead($th,$htr);
return $tabbelas->tabela(array('table'=> $table,'head-tr'=>$thlinhaH,'body-tr'=>$trlinhaB));
}

?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

<script src="https://code.jquery.com/jquery-3.4.1.min.js" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>


<link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" id="datatable-css"> 
 <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>

 <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
//****************************DataTable*************************************************//
    $('.tabela_relatorio').dataTable({
     "info": false,
      "lengthChange": false,
     "searching": false,
     "emptyTable": "Sem dados para mostrar",
     "language": {
      "url": "https://cdn.datatables.net/plug-ins/1.10.15/i18n/Portuguese-Brasil.json"
     },
					"order": [[ 2, "desc" ]],     
     "responsive": true								
    }); 
 });
</script>
<?php
if(!empty($lista_total)){ ?>
<div class="container-fluid px-0">
      <div class="row mb-5">
            <div class="col-sm-6 col-md-4 col-lg-4 mt-4">
                <div class="card card-primary">
                 <div class="card-header">
                  <div class="row">
                   <div class="col"><h5>Dados Gerais</h5></div>
                   <div class="col"><?php echo count($lista_total); ?> procedimentos em tramitação</div>
                  </div>
                 </div>
                        <div class="card-body p-0 m-0"> <?php echo gerais(); ?></div>
                        <div class="card-footer"></div>
                    </div>
             </div>
           <div class="col-sm-6 col-md-4 col-lg-4 mt-4">
                <div class="card card-primary">
                        <div class="card-header"><h5>Taxonomias</h5></div>
                        <div class="card-body p-0 m-0"> <?php echo taxonomias(); ?></div>                        
                    </div>
             </div>
           
         <div class="col-sm-6 col-md-4 col-lg-4 mt-4">
                <div class="card card-primary">
                        <div class="card-header"><h5>Assuntos</h5></div>
                        <div class="card-body p-0 m-0"> <?php echo assunto(); ?></div>
                </div>
         </div>
      </div> 
 
       <div class="row mb-5">
           
             <div class="col-sm-6 col-md-4 col-lg-4 mt-4">
                <div class="card card-primary">
                        <div class="card-header"><h5>Movimentos</h5></div>
                        <div class="card-body p-0 m-0"> <?php echo movimentos(); ?></div>                        
                    </div>
             </div>
        
             <div class="col-sm-6 col-md-4 col-lg-4 mt-4">
                <div class="card card-primary">
                        <div class="card-header"><h5>Atuação</h5></div>
                        <div class="card-body p-0 m-0"> <?php echo atuacao(); ?></div>
                </div>
            </div>
      </div>
</div>
<?php }else{
 echo '<div class="alert alert-info" role="alert">
  <h4 class="alert-heading text-center">Seja bem vindo!</h4>
  <p>Parece que você ainda não tem registro no sistema para ser mostrado. Então, acesse o menu Enviar Planilha e siga os passos para atualizar seus dados.</p>
  <hr>
  <p class="mb-0">Após isso, você poderá acessa os dados simplificados do Grifo.</p>
</div>';
}

?>

