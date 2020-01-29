<?php 
use Medoo\Medoo;

$biblioteca = new Biblioteca\Custom();
$tabbelas = new Biblioteca\Tabelas();

global $authGrifo, $eventos;

$pj = $authGrifo['promotoria'];
$id_user = $authGrifo['user'];

$database = new Medoo([
         'database_type' => 'mysql',
         'database_name' => 'grifo',
         'server' => 'localhost',
         'username' => 'root',
         'password' => ''
     ]);

$localizador = 'oficio';
/**********************************************************************************/
function href($x){
 return '<a class="text-dark" href="https://root-aynoei177396.codeanyapp.com/grifo/index.php?p=tabela-grifo&buscar='.$x.'" target="_blank">'.$x."</a>";
}
/**********************************************************************************/
function recebidoForma($dataInicial,$prazo,$tipoPrazo,$retorno='-'){
	global $biblioteca;
if($dataInicial == null || $dataInicial == '0000-00-00'){
   $output = $retorno;
}else{
		switch($tipoPrazo):
			case 'dias':
					$output = $biblioteca->cliente(@$biblioteca->expira_dia($dataInicial,$prazo));

			break;
			case 'uteis':
					$output =  $biblioteca->cliente(@$biblioteca->banco($biblioteca::SomaDiasUteis($biblioteca::cliente($dataInicial),$prazo)));

			break;
			case 'horas':
					$output = $biblioteca->cliente(@$biblioteca->expira_hora($dataInicial,$prazo));

			break;													
		endswitch;
}
	
	return $output;
	
}

$lista = $database->query("SELECT *  FROM oficios WHERE promotoria = '$pj' AND baixa IS NULL")->fetchAll();  

/**********************************************************************************/
foreach($lista as $listagem){
$vencimento = recebidoForma($listagem['recebido'],$listagem['prazo'],$listagem['tipo']);
		$diasVencidos = $biblioteca->diffDate($biblioteca->banco($vencimento));
		$diasDia = ($diasVencidos > 1)?' dias':' dia';
		if($diasVencidos > 0):
			if($vencimento != '-'):
				$trVencida = 'table-danger ';
				$classVenc = '2';
				$diasVenc = 'Vencido há '.$diasVencidos.$diasDia;
				$classHidden ='vencido';
				$iconVenc = '<span class="" title="Vencido há '.$diasVencidos.$diasDia.'"><i class="fa fa-bell text-danger" aria-hidden="true" ></i></span>';
			else:
				$trVencida = ' table-warning ';
				$classVenc = '1';
				$diasVenc = 'Não foi entregue ainda!';
				$classHidden ='';
				$iconVenc = '<span class="" title="Não foi entregue ainda!"><i class="fa fa-exclamation text-warning" aria-hidden="true" ></i</span>';
			endif;
		else:
  $trVencida = '';
			$vencida = '';
			$classVenc = '0';
			$diasVenc = '';
			$classHidden = '';
			$iconVenc ='';
		endif;

$id = $listagem['Id'];
 
$data_recebido = '<a href="#" id="'.$id.'_oficio" class=" border-0 autocomplete_input text-dark" style="display: inline;" data-type="text" data-pk="recebido" data-value="'.$biblioteca->cliente($listagem['recebido']).'" data-title="Insira">'.$biblioteca->cliente($listagem['recebido']).'</a>';

$interessado = '<a href="#" id="'.$id.'" class=" border-0 autocomplete_input_interessado text-dark" style="display: inline;" data-type="text" data-pk="interessado" data-value="'.$listagem['interessado'].'" data-title="Insira">'.$listagem['interessado'].'</a>';

$destinatario = '<a href="#" id="'.$id.'" class=" border-0 autocomplete_input_destinatario text-dark" style="display: inline;" data-type="text" data-pk="destinatario" data-value="'.$listagem['destinatario'].'" data-title="Insira">'.strtoupper($listagem['destinatario']).'</a>';

 
$btn = '<button class="btn btn-sm btn-success receber_oficio mr-2 click" title="Receber oficio" name="receber" id="'.$id.'"><i class="fa fa-check"></i></button>'; 
//$btn .= '<button class="btn btn-sm btn-info editar_registro mr-2 click" title="Editar registro" name="editar" id="'.$id.'"><i class="fa fa-pencil-square-o"></i></button>'; 
$btn .= '<button class="btn btn-sm btn-danger apagar_registro mr-2 click" title="Apagar registro" name="apagar" id="'.$id.'"><i class="fa fa-trash"></i></button>';
$option = '<div class="row">'.$btn.'</div>';
/********************************************************CRIANDO A TABELA****************************************************************/	
$Blista = array(
['tipo' => 'body-td', 'css'=> 'text-center', 'html' => 'hidden','texto'=> $classVenc],
['tipo' => 'body-td', 'css'=> 'text-center', 'html' => 'hidden','texto'=> $classHidden], 
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'', 'texto'=> $iconVenc],  
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'', 'texto'=> $listagem['numero']], 
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'', 'texto'=> href($listagem['procedimento'])],
['tipo' => 'body-td', 'css'=> 'text-center destinatario', 'html'=>'id="destinatario"', 'texto'=> $destinatario],  
['tipo' => 'body-td', 'css'=> 'text-center interessado', 'html'=>'id="interessado"', 'texto'=> $interessado], 
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'id="assunto"', 'texto'=> strtoupper($listagem['assunto'])],
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'', 'texto'=> $listagem['prazo']], 
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'', 'texto'=> $listagem['tipo']],  
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'', 'texto'=> $data_recebido], 
['tipo' => 'body-td', 'css'=> 'text-center ven_calendar', 'html'=>'', 'texto'=> $vencimento], 
['tipo' => 'body-td', 'css'=> 'text-center', 'html'=>'', 'texto'=> $option],  
['tipo' => 'body-td', 'css' => '', 'html' => 'hidden', 'texto'=>'-']
);
$btr = array('css' => $regularidade_css.' pointer text-center  detalhes_tr tr_'.$id.' '.$trVencida, 'id' => $id, 'alt'=> $localizador, 'name' => $localizador);	
$trlinhaB[] = $tabbelas->geraBody($Blista,$btr);	
}
//****************************************TR da tabela******************************************************************************/	
$table = array('css'=> 'border border-secondary rounded tabela_datatable table-hover tabela_'.$localizador.' table-hover table-striped', 'html' =>'', 'id' => $localizador);
$th = array(
	['css' => '', 'html'=>'hidden','texto'=>''],
	['css' => '', 'html'=>'hidden','texto'=>''], 
 ['css' => '', 'texto'=>'', 'html'=>'style="width: 30px;"'], 
 ['css' => '', 'texto'=>'Número', 'html'=>'style="width: 50px;"'],
	['css' => '', 'texto'=>'Atena', 'html'=>'style="width: 50px;"'],
	['css' => '', 'texto'=>'Destinatario', 'html'=>'style="200px"'],
	['css' => '', 'texto'=>'Interessado', 'html'=>'style="200px"'],
	['css' => '', 'texto'=>'Assunto', 'html'=>'style="100px"'], 
	['css' => '', 'texto'=>'Prazo', 'html'=>'style="width: 100px;"'],   
	['css' => '', 'texto'=>'Tipo de Prazo', 'html'=>'style="width: 100px;"'],
 ['css' => '', 'texto'=>'Recebido em', 'html'=>'style="width: 100px;"'],
 ['css' => '', 'texto'=>'Vencimento', 'html'=>'style="width: 100px;"'],
 ['css' => '', 'texto'=>'', 'html'=>'style="width: 100px;"'],
 ['css' => '', 'html'=>'hidden','texto'=>'-']);	
$htr = array('css'=> 'text-center text-uppercase ');
$thlinhaH = $tabbelas->geraHead($th,$htr);
$tabela_pronta = $tabbelas->tabela(array('table'=> $table,'head-tr'=>$thlinhaH,'body-tr'=>$trlinhaB));
/******************************************************************************************************************/

?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> 


<script  src="https://code.jquery.com/jquery-1.9.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.js"/></script>

<link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" id="datatable-css"> 
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>
 <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>

 <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>

 
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/jqueryui-editable/css/jqueryui-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/jqueryui-editable/js/jqueryui-editable.min.js"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"/></script>
<script src="https://root-aynoei177396.codeanyapp.com/grifo/scripts/default_scripts.js"/></script>

<style type="">

</style>
<script type="text/javascript">

	$(document).ready(function() {
  
 $( ".data_mask" ).mask("00/00/0000", {placeholder: "__/__/____"});
  
  $.fn.editable.defaults.mode ='inline';// popup default

     $('.autocomplete_input').editable({              
             url: 'scripts/inline_edit_editable.php',
             mode: 'inline',
             type: 'text',
             emptytext: '...',
             inputclass: 'text-uppercase recebido',
             showbuttons: true,
             ajaxOptions: {
                 dataType: 'json'
             },
             success: function(response, newValue) {
              console.log(response);
              $('.ven_calendar').html(response);
             }
         });
 
  $('.autocomplete_input_interessado').editable({              
             url: 'scripts/inline_edit_editable.php',
             mode: 'inline',
             type: 'text',
             emptytext: '...',
             inputclass: 'text-uppercase',
             showbuttons: false,
             ajaxOptions: {
                 dataType: 'json'
             },
             success: function(response, newValue) {
                 console.log(response);
            },
              params: function(params) {
                     return params;
                 }
         });
   $('.autocomplete_input_destinatario').editable({              
             url: 'scripts/inline_edit_editable.php',
             mode: 'inline',
             type: 'text',
             emptytext: '...',
             inputclass: 'text-uppercase',
             showbuttons: false,
             ajaxOptions: {
                 dataType: 'json'
             },
             success: function(response, newValue) {
                 console.log(response);
            },
              params: function(params) {
                     return params;
                 }
         });
 /* 
  $('.autocomplete_input_geral').editable({              
             url: 'scripts/inline_edit_editable.php',
             mode: 'inline',
             type: 'text',
             emptytext: '...',
             inputclass: 'text-capitalize',
             showbuttons: false,
             ajaxOptions: {
                 dataType: 'json'
             },
             success: function(response, newValue) {
                 console.log(response);
             }
         });
  */
 $(document).on("focus",".recebido",function(){
  $(this).datepicker({
	dateFormat: 'dd/mm/yy',
	dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
	dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
	dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
	monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
	monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
	nextText: 'Próximo',
	prevText: 'Anterior',
  maxDate: 0
});
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
					"order": [[ 0, "desc" ]],
     "buttons": [
                  {
                      extend: 'print',
                      text: 'IMPRIMIR',
                      exportOptions: {
                          columns: ':visible'
                      },						
             className: 'btn-sm btn-danger click_btn float-left mr-2'
                  }

              ],
     "responsive": true								
    }); 



});
	
//***********************************************Botoes*************************************************/	
	$(document).on('click','.click',function(){		
  var url_inline  = 'assincs/expedientes.php';
		var element = $(this);//button
		var name = element.attr('name');//switch pelo name do elemento
		var id = element.attr('id');
		var dados = {id:id, value:'<?php echo date('Y-m-d'); ?>',tipo:name};
  console.log(dados);

  switch(name){		
			case 'receber':	
				ajaxCustom('confirm','Você deseja confirmar o recebimento da resposta deste movimento?',url_inline,dados,
							  function(resposta){
								if (resposta != false) {
									console.log('receber: '+resposta);									
								}
							  },
							  function(){
							   $(".tr_"+id+"").html('<td colspan="100%"><div class="alert alert-warning" role="alert"><i class="fa fa fa-spinner fa-spin" aria-hidden="true"></i> Baixando...</div></td>');
							    element.find('i').attr('class','fa fa-spinner fa-spin');//sppiner no button
							    console.log("indo");
							  },
							  function(){							  
							   element.find('i').attr('class','fa fa-check');	
							   $(".tr_"+id+"").hide( "slow" );
							   alert_boot('alert','Registro baixado com sucesso!','','','','',false);   
							   
							  });
				break;
			case 'apagar':
				ajaxCustom('confirm','Você deseja apagar este movimento?',url_inline,dados,
							  function(resposta){
								if (resposta != false) {
									console.log('receber: '+resposta);									
								}
							  },
							  function(){
							    $(".tr_"+id+"").html('<td colspan="100%"><div class="alert alert-warning" role="alert"><i class="fa fa fa-spinner fa-spin" aria-hidden="true"></i> Apagando...</div></td>');
							    element.find('i').attr('class','fa fa-spinner fa-spin');//sppiner no button
							    console.log("indo");
							  },
							  function(){							  
							   element.find('i').attr('class','fa fa-trash');	
							   $(".tr_"+id+"").hide( "slow" );
							   alert_boot('alert','Registro apagado com sucesso!','','','','',false);     
							   
							  });
				break;			
			}//endswitch*/
  
	}); 
</script>
 <div class="">
  
<?php 
echo '<div class="text-center"><a class="btn btn-sm btn-success" href="https://root-aynoei177396.codeanyapp.com/grifo/index.php?p=inserir-oficio">Inserir Ofício</a></div>';
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
</div>  

