<?php 
use Medoo\Medoo;
$biblioteca = new Biblioteca\Custom();
$tabbelas = new Biblioteca\Tabelas();

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
$lista = $database->query("SELECT * FROM procedimentos WHERE promotoria = '$pj'" )->fetchAll();
$localizador = 'procedimentos';
/**********************************************************************************/
function regularidade($classe,$posse,$inst,$pror){
 global $biblioteca, $pj, $id_user;
   switch(true){
      case ($posse < $inst)://posse mais antiga - novo procedimento
              if($inst<$pror){
                  $r = $pror;
                  $ja = 1;//ja prorrogado
                  $inicio = 'prorrogacao';
              }else{
                  $r = $inst;
                  $inicio = 'instauracao';
              }
          break;
      case ($posse > $inst)://posse mais recente - veio encaminhado
             if($posse<$pror){
                  $r = $pror;
                  $ja = 1;//ja prorrogado
                  $inicio = 'prorrogacao';
              }else{
                if(isset($pror)){
                 $ja = 1;//se ja houve prorrogacao, mas a posse é mais recente, renova o prazo da prorrogacao com a nova data da posse
                }
                  $r = $posse;
                  $inicio = 'posse';
              }
          break;
      case ($posse == $inst)://instaurado na pj
              if($inst<$pror){
                  $r = $pror;
                  $ja = 1;//ja prorrogado
               $inicio = 'prorrogacao';
              }else{
                  $r = $inst;
               $inicio = 'instauracao';
              }
          break;

  }
 
 $dias = $biblioteca->diffDate($r);

 switch($biblioteca->slugando($classe)){
   case 'noticiadefato':
   if($ja == 1){
       $prazo = 90;
       $return = (($prazo-$biblioteca->diffDate($r)) > 0)?'':'table-danger';
       $tempo = ($prazo-$biblioteca->diffDate($r));
       $vencido = (($prazo-$biblioteca->diffDate($r)) > 0)?'':'procedimento_vencido';
   }else{
       $prazo = 30;
       $return = (($prazo-$biblioteca->diffDate($r)) > 0)?'':'table-danger';
       $tempo = ($prazo-$biblioteca->diffDate($r));
       $vencido = (($prazo-$biblioteca->diffDate($r)) > 0)?'':'procedimento_vencido';
   }
  break;
   case 'procedimentopreparatorio':
       $prazo = 90;
       $return = ($prazo-$biblioteca->diffDate($r) > 0)?'':'table-danger';
       $tempo = ($prazo-$biblioteca->diffDate($r));
       $vencido = (($prazo-$biblioteca->diffDate($r)) > 0)?'':'procedimento_vencido';
  break;
    default:
       $prazo = 365;
       $return = (($prazo-$biblioteca->diffDate($r)) > 0)?'':'table-danger';
       $tempo = ($prazo-$biblioteca->diffDate($r));
       $vencido = (($prazo-$biblioteca->diffDate($r)) > 0)?'':'procedimento_vencido';
  break;
 }
 
 return array('css'=>$return,'dias'=>$dias, 'tempo'=>$tempo,'contagem'=>$inicio,'classe'=>$biblioteca->slugando($classe),'vencido'=>$vencido);
 //return array('css'=>$return,'dias'=>$dias);
}

function tempoMovimento($dias,$classe,$array = array('noticiadefato'=>10,'procedimentopreparatorio'=>10)){
  global $biblioteca;
 $prazo = $array[$biblioteca->slugando($classe)];
 $prazo_padrao = 30;//dias

 if($prazo > 0){
   if($dias > $prazo){
       $css = '<span class="badge badge-warning" title="Tempo mínimo entre os movimentos desta classe são de '.$prazo.' dias">'.$dias.'</span>';
      $alert = 'movimento_vencido';
   }else{
       $css = '<span class="1 '.$prazo.'">'.$dias.'</span>';
       $alert = '';
   }
 }else{
   if($dias > $prazo_padrao){
      $css = '<span class="badge badge-warning" title="Tempo mínimo padrão entre os movimentos desta classe são de '.$prazo_padrao.' dias">'.$dias.'</span>';
      $alert = 'movimento_vencido';
   }else{
     $css = '<span class="2 '.$prazo.'">'.$dias.'</span>';
     $alert = '';
   }
 }
 
 return array('css' => $css, 'alert'=>$alert);
 
}
/**********************************************************************************/
foreach($lista as $listagem){
 
 if($listagem['ultimo_movimento'] != $listagem['data_movimento']){
  $ultimo_movimento = $listagem['ultimo_movimento'];
 }else{
  $ultimo_movimento = $listagem['data_movimento'];
 }

$tempoMinimo = tempoMovimento($biblioteca->diffDate($ultimo_movimento),$listagem['classe']);
$regularidade = regularidade($listagem['classe'],$listagem['data_posse'],$listagem['data_instauracao'],$listagem['data_prorrogacao']);
$regularidade_css = $regularidade['css'];
$regularidade_dias = $regularidade['dias'];
$completo = $listagem['classe'].' '.$listagem['taxonomia'].' '.$regularidade['vencido'].' '.$tempoMinimo['alert'];
$id = $listagem['Id'];
 
 $atuacao_in = '<a href="#" id="'.$id.'" class="'.$regularidade_css.' border-0 autocomplete_select text-dark" style="display: inline;" data-type="select" data-pk="atuacao" data-value="'.$listagem['atuacao'].'" data-title="Selecione">'.$listagem['atuacao'].'</a>';
 $assunto_in = '<a href="#" id="'.$id.'" class="'.$regularidade_css.' border-0 autocomplete_input text-dark" style="display: inline;" data-type="select2" data-pk="assunto" data-value="'.$listagem['assunto'].'" data-title="Selecione">'.$listagem['assunto'].'</a>';
 $checkbox = '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input checkboxes" id="check_'.$id.'" data-id="'.$id.'"><label class="custom-control-label" for="check_'.$id.'"></label></div>';

 

/********************************************************CRIANDO A TABELA****************************************************************/	
$Blista = array(
['tipo' => 'body-td', 'css'=> 'text-center', 'html' => 'hidden','texto'=> $regularidade['tempo']], 
['tipo' => 'body-td', 'css'=> 'text-center', 'html' => 'hidden','texto'=> $completo],
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'style="width: 20px;"','texto'=> $checkbox],
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=> '', 'texto'=> '<a class="'.$regularidade_css.' border-0" href="http://www.mpgo.mp.br/atena_cidadao/exibir/'.$listagem['numero_dos_autos'].'" target="_blank" title="Ver no Atena">'.$listagem['numero_dos_autos'].'</a>'],
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=> 'title="'.$listagem['classe'].'"', 'texto'=> $biblioteca->_limi($listagem['classe'],27)],
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=> 'title="'.$listagem['taxonomia'].'"', 'texto'=> $biblioteca->_limi($listagem['taxonomia'],27)],
['tipo' => 'body-td', 'css'=> 'text-center text-capitalize edit_select', 'html'=>'id="'.$id.'|atuacao"', 'texto'=> $atuacao_in],
['tipo' => 'body-td', 'css'=> 'text-center text-capitalize edit_input', 'html'=>'id="'.$id.'|assunto"', 'texto'=> $assunto_in], 
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'title="'.$listagem['envolvidos'].'"', 'texto'=> $biblioteca->_limi($listagem['envolvidos'],27)], 
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'', 'texto'=> $listagem['tipo']],
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'', 'texto'=> $biblioteca->cliente($ultimo_movimento)],
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'', 'texto'=> $tempoMinimo['css']],
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'', 'texto'=> $biblioteca->cliente($listagem['data_instauracao'])],
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'', 'texto'=> $regularidade_dias],
['tipo' => 'body-td', 'css' => '', 'html' => 'hidden', 'texto'=>'-']
);
$btr = array('css' => $regularidade_css.' pointer text-center  detalhes_tr tr_'.$id, 'id' => $id, 'alt'=> $localizador, 'name' => $localizador);	
$trlinhaB[] = $tabbelas->geraBody($Blista,$btr);	
}
//****************************************TR da tabela******************************************************************************/	
$table = array('css'=> 'table-responsive border border-secondary rounded tabela_datatable table-hover tabela_'.$localizador.' table-hover table-striped', 'html' =>'', 'id' => $localizador);
$th = array(
	['css' => '', 'html'=>'hidden','texto'=>''],
	['css' => '', 'html'=>'hidden','texto'=>''],
	['css' => '', 'html'=>'','texto'=>''],
 ['css' => '', 'texto'=>'Autos', 'html'=>'style="width: 50px;"'],
	['css' => '', 'texto'=>'Classe', 'html'=>'style="width: 50px;"'],
	['css' => '', 'texto'=>'Taxonomia', 'html'=>'style="min-width: 200px;"'],
	['css' => '', 'texto'=>'Atuacao', 'html'=>'style="width: 100px;"'], 
	['css' => '', 'texto'=>'Assunto', 'html'=>'style="width: 200px;"'],
	['css' => '', 'texto'=>'Envolvidos', 'html'=>''],
 ['css' => '', 'texto'=>'Último Movimento', 'html'=>'style="width: 50px;"'],
 ['css' => '', 'texto'=>'Data do Último Movimento', 'html'=>'style="width: 50px;"'],
 ['css' => '', 'texto'=>'Dias', 'html'=>'style="width: 50px;"'], 
 ['css' => '', 'texto'=>'Desde', 'html'=>'style="width: 50px;"'],
 ['css' => '', 'texto'=>'Dias', 'html'=>'style="width: 50px;"'], 
 ['css' => '', 'html'=>'hidden','texto'=>'-']);	
$htr = array('css'=> 'text-center text-uppercase ');
$thlinhaH = $tabbelas->geraHead($th,$htr);
$tabela_pronta = $tabbelas->tabela(array('table'=> $table,'head-tr'=>$thlinhaH,'body-tr'=>$trlinhaB));
/******************************************************************************************************************/

$lista_assunto = $database->query("SELECT * FROM assuntos" )->fetchAll();
foreach($lista_assunto as $assunto){
  $assun[] = "{value: '".strtolower($assunto['assunto'])."', text: '".ucfirst($assunto['assunto'])."'}";
  $tags[] = "'".ucfirst($assunto['assunto'])."'";
 // $assun[] = ucfirst($assunto['assunto']);
}

$assuntos = $biblioteca->opcao($assun,',');
$assuntos_tags = $biblioteca->opcao($tags,',');
?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> 

<script  src="https://code.jquery.com/jquery-1.9.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" id="datatable-css"> 
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.0/bootbox.min.js"></script>


 <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>

 
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/jqueryui-editable/css/jqueryui-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/jqueryui-editable/js/jqueryui-editable.min.js"></script>
 
<link href="https://vitalets.github.io/x-editable/assets/select2/select2.css" rel="stylesheet" />
<script src="https://vitalets.github.io/x-editable/assets/select2/select2.js"></script>


<style type="">

</style>
<script type="text/javascript">
function getCheckedBoxes(chkboxName) {
  var checkboxes = document.getElementsByClassName(chkboxName);
  var checkboxesChecked = [];
  // loop over them all
  for (var i=0; i<checkboxes.length; i++) {
     // And stick the checked ones onto an array...
     if (checkboxes[i].checked) {
        checkboxesChecked.push(checkboxes[i].dataset.id);
     }
  }
	 	
  // Return the array if it is non-empty, or null
  return checkboxesChecked.length > 0 ? checkboxesChecked : null;
}
	$(document).ready(function() {
  //turn to inline mode

     $.fn.editable.defaults.mode ='inline';// popup default

     $('.autocomplete_input').editable({              
             url: 'scripts/inline_edit_editable.php',
             mode: 'inline',
             type: 'text',
             emptytext: '...',
             inputclass: 'text-capitalize',
             showbuttons: false,
             select2: {
                tags:[<?php echo $assuntos_tags; ?>],
                multiple: true,
                allowClear: true,
                minimumInputLength: 0,
                placeholder: 'Selecione',
                width: '230px',
             },
             ajaxOptions: {
                 dataType: 'json'
             },
             success: function(response, newValue) {
              console.log(response);
             }
         });
  

       
			
      $('.autocomplete_select').editable({              
             source: [
              {value:"", text:"Selecione"}, 
              {value:"coletivo", text: "Coletivo"}, 
              {value: "individual", text:"Individual"}, 
              {value: "fundações", text: "Fundações"}, 
              {value: "tac", text: "TAC"}
             ],
             url: 'scripts/inline_edit_editable.php',
             mode: 'inline',
             type: 'select',
             showbuttons: false,
             emptytext: '...',
             inputclass: 'text-capitalize',
             ajaxOptions: {
                 dataType: 'json'
             },
             success: function(response, newValue) {
              console.log(response);
             }
         }); 
  
  
//****************************DataTable*************************************************//
    /*datatable*/
  $('.tabela_<?php echo $localizador; ?>').dataTable({
       "search": {
           "search": "<?php echo $_GET['buscar']; ?>"
         },
     /*"info": false,*/
     "emptyTable": "Sem dados para mostrar",
     "language": {
      "url": "https://cdn.datatables.net/plug-ins/1.10.15/i18n/Portuguese-Brasil.json"
     },
     "dom": 'Blfrtip',
					"order": [[ 0, "asc" ]],
     "buttons": [
                  {
                      extend: 'print',
                      text: 'IMPRIMIR',
                      exportOptions: {
                          columns: ':visible'
                      },						
             className: 'btn-sm btn-danger click_btn float-left mr-2'
                  },
					            	{
                      text: 'ATUAÇÃO',
                      action: function ( e, dt, node, config ) {
																							
																							
																							
																							var ids = getCheckedBoxes("checkboxes");
																							var checkboxes = document.getElementsByClassName("checkboxes");
																							
																			     	bootbox.prompt({
																															title: "Quais assuntos serão inseridos no procedimentos selecionados?", 
																															centerVertical: true,
																															inputType: 'select',
																															inputOptions: [
																																	{value:"", text:"Selecione"}, 
																																	{value:"coletivo", text: "Coletivo"}, 
																																	{value: "individual", text:"Individual"}, 
																																	{value: "fundações", text: "Fundações"}, 
																																	{value: "tac", text: "TAC"}
																															],
																															callback: function(result){
																																
																							
																																																							if(ids !== null && result !== null){
																																																									bootbox.dialog({ message: '<div class="wait-loading alert text-center"><i class="fa fa-spin fa-spinner"></i>Salvando</div>',closeButton: false });
																																																																	
																																																								ids.forEach(function(element, index, array) {
																																																														$.ajax({
																																																																	url: 'scripts/inline_edit_editable.php', 
																																																																	data: {name: element, value: result, pk: "atuacao", text: ''},
																																																																	type: 'POST',
																																																																	success: function(r){console.log('success',r);},
																																																																	beforeSend: function(r){
																																																																		console.log('bsend',r);
																																																																		},
																																																																	complete: function(r){
																																																																		console.log('complete',r);																																																																		
																																																																		bootbox.hideAll();
																																																																	},
																																																																	error: function (xhr, ajaxOptions, thrownError) {
																																																																		alert("ERROR:" + xhr.responseText+" - "+thrownError);
																																																																		bootbox.hideAll();
																																																																	}	 
																																																															});
																																																									$('td[id="'+element+'|atuacao"]').find('a').html(result);
																																																									$('td[id="'+element+'|atuacao"]').find('a').attr('data-value',result);
																																																									console.log("a[" + index + "] = " + element);
																																																									
																																																									});
																																																										for (var i = 0; i < checkboxes.length; i++) {
																																																														if (checkboxes[i].type == 'checkbox')
																																																																		checkboxes[i].checked = false;
																																																										}

																																																									}

																														
																															}
																											});//fim bootbox
                						},						
             									className: 'btn-sm btn-warning float-left mr-2'
                  },
					            	{
                      text: 'ASSUNTOS',
                      action: function ( e, dt, node, config ) {
																							
																							
																							
																							var ids = getCheckedBoxes("checkboxes");
																							var checkboxes = document.getElementsByClassName("checkboxes");
																							
																			     	bootbox.prompt({
																															title: "Quais assuntos serão inseridos no procedimentos selecionados?", 
																															centerVertical: true,
																										     onShown: function(e) {			
																																
																														     $('.bootbox-input').removeClass('form-control');//remove o form-control              		
																																                    
																																																				$('.bootbox-input-text').select2({																												
																																																													width: '100%',
																																																													tags:[<?php echo $assuntos_tags; ?>],
																																																													minimumInputLength: 0,
																																																									}); 
																																		
																															},
																															callback: function(result){ 			
																							
																																
																							
																																																							if(ids !== null && result !== null){
																																																								var tags = result.split(',');
														
																																																								ids.forEach(function(element, index, array) {
																																																														$.ajax({
																																																																	url: 'scripts/inline_edit_editable.php', 
																																																																	data: {name: element, value: tags, pk: "assunto", text: ''},
																																																																	type: 'POST',
																																																																	success: function(r){console.log('success',r);},
																																																																	beforeSend: function(r){
																																																																		console.log('bsend',r);
																																																																		bootbox.dialog({ message: '<div class="wait-loading alert text-center"><i class="fa fa-spin fa-spinner"></i>Salvando</div>',closeButton: false });
																																																																	},
																																																																	complete: function(r){
																																																																		console.log('complete',r);																																																																		
																																																																		bootbox.hideAll();
																																																																	},
																																																																	error: function (xhr, ajaxOptions, thrownError) {
																																																																		alert("ERROR:" + xhr.responseText+" - "+thrownError);
																																																																		bootbox.hideAll();
																																																																	}	 
																																																															});
																																																									$('td[id="'+element+'|assunto"]').find('a').html(result);
																																																									$('td[id="'+element+'|assunto"]').find('a').attr('data-value',result);
																																																									console.log("a[" + index + "] = " + element);
																																																									
																																																									});
																																																								console.log(tags);
																																																										for (var i = 0; i < checkboxes.length; i++) {
																																																														if (checkboxes[i].type == 'checkbox')
																																																																		checkboxes[i].checked = false;
																																																										}

																																																									}

																														
																															}
																											});//fim bootbox
                						},						
             									className: 'btn-sm btn-warning float-left mr-2'
                  }
              ],
     "responsive": true								
    }); 



});
 
</script>
 <?php 
if(!empty($lista)){
 echo $tabela_pronta;
}else{
 echo '<div class="alert alert-info" role="alert">
  <h4 class="alert-heading text-center">Seja bem vindo!</h4>
  <p>Parece que você ainda não tem registro no sistema para ser mostrado. Então, acesse o menu Enviar Planilha e siga os passos para atualizar seus dados.</p>
  <hr>
  <p class="mb-0">Após isso, você poderá acessa os dados simplificados do Grifo.</p>
</div>';
}

 ?>
  
