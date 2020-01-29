<?php  

include($_SERVER['DOCUMENT_ROOT'] . 'grifo/parts/includes.php'); 

use Medoo\Medoo;

$biblioteca = new Biblioteca\Custom();

   $database = new Medoo([
         'database_type' => 'mysql',
         'database_name' => 'grifo',
         'server' => 'localhost',
         'username' => 'root',
         'password' => ''
     ]);

$post = $_POST;

foreach ($post['value'] as $value) {
			$valores[$value["name"]] = $value["value"];
}
$recebido = $biblioteca->banco($valores['recebido']);

function recebidoForma($dataInicial,$prazo,$tipoPrazo,$retorno='-'){
	global $biblioteca;
	
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

	
	return $output;
	
}
switch($post['tipo']){
 case 'oficio':
    
/*********************************************************Rotinas para vencidos****************************************************************/
		$diasVencidos = $biblioteca->diffDate($biblioteca->banco(recebidoForma($recebido,$valores['prazo'],$valores['tipo'])));
		$diasDia = ($diasVencidos > 1)?' dias':' dia';
		if($diasVencidos > 0):
			if(recebidoForma($recebido,$valores['prazo'],$valores['tipo']) != '-'):
				$trVencida = '';// table-danger ';
				$classVenc = ' alert alert-danger ';
				$diasVenc = 'Vencido há '.$diasVencidos.$diasDia;
				$classHidden ='';
				$iconVenc = '<span class="" title="Vencido há '.$diasVencidos.$diasDia.'"><i class="fa fa-bell text-danger" aria-hidden="true" ></i></span>';
			else:
				$vencida = '';// table-warning ';
				$classVenc = ' alert alert-warning ';
				$diasVenc = 'Não foi entregue ainda!';
				$classHidden ='';
				$iconVenc = '<span class="" title="Não foi entregue ainda!"><i class="fa fa-exclamation text-warning" aria-hidden="true" ></i</span>';
			endif;
		else:
			$vencida = '';
			$classVenc = '';
			$diasVenc = '';
			$classHidden = 'hidden';
			$iconVenc ='';
		endif;

 if($biblioteca->validaData($recebido) != '-'){ 
/**********************************************************************/
$array = array(  
  "color" => 8,
  'datafim' => false,
  'data_inicio' => $biblioteca->banco(recebidoForma($recebido,$valores['prazo'],$valores['tipo'])),
  'assunto' => 'Ofício nº '.$valores['oficio_numero'],//assunto do evento
  'participantes' => '',//array(array('displayName' => 'Santa Casa de Misericoria de Anapolis','email'=>'agendamento@sistemando.com')),
  'obs' => $valores['observacao'],
  'descricao' => array( //formatar o array para recebr todos os tipos de agendamentos
               "tipo" => $post['tipo'],//oficio, notificacao, compromisso, juri, audiencia
               'assunto_descricao' => strtoupper($valores['assunto']),//assunto que vai no header da descricao
               'promotoria' => '9ª Promotoria de Justiça',
               'procedimento' => $valores['procedimento'],
               'destinatario' => strtoupper($valores['destinatario']),
               'interessado' => strtoupper($valores['interessado']),   
               'numero_expediente' => $valores['oficio_numero'],//numero do expedientes ex: oficio, notificacao
               'recebido' => $recebido,//data do recebimento do oficio
               'prazo' => $valores['prazo'],//numero
               'prazo_tipo' => $valores['tipo'],//dias ou horas
               "url_link" => urlencode($valores['oficio_numero']),//url para acessar na lista https://root-aynoei177396.codeanyapp.com/grifo/index.php?p=lista_expedientes&
            )
 );

$evento =  $eventos->inserirEvento($calendario_id,$array);
  }
/*********************************************************************/

               $array_insert = array(
                   'Id'=> '',
                   'id_usuario'=> $authGrifo['user'],
                   'promotoria'=> $authGrifo['promotoria'],
                   'procedimento'=> $valores['procedimento'],
                   'destinatario'=> strtoupper($valores['destinatario']),
                   'interessado'=> strtoupper($valores['interessado']),                
                   'assunto' => strtoupper($valores['assunto']),
                   'numero' => $valores['oficio_numero'],
                   'recebido' => $recebido,
                   'prazo' => $valores['prazo'],
                   'tipo' => $valores['tipo'],
                   'rastreamento' => $valores['rastreamento'],
                   'obs' => $valores['obs'],
                   'event_id' => $evento->id,
                   'data' => date('Y-m-d')
                   );
               $database->insert('oficios',$array_insert);
  
$res = $evento;
  
  break;
  /*********************************************************************************/
 case 'receber':
   $oficio = $database->get("oficios",'*',["Id" =>$post['id']]);
   $database->update("oficios", ['baixa' => $post['value']],["Id" =>$post['id']]);//atualiza a ultima atualizacao
   $res = $eventos->apagaEvento($calendario_id,$oficio['event_id']);
  break;
  /*********************************************************************************/
 case 'apagar':
   $oficio = $database->get("oficios",'*',["Id" =>$post['id']]);
   $database->delete("oficios", ["Id" =>$post['id']]);//atualiza a ultima atualizacao
   $res = $eventos->apagaEvento($calendario_id,$oficio['event_id']);
  break;
}
//echo json_encode(array_merge(array('calendar'=>$evento),array('insert'=>$array_insert)));
echo json_encode($res);
