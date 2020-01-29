<?php
//sleep(3);
include($_SERVER['DOCUMENT_ROOT'] . 'grifo/parts/includes.php'); 

use Medoo\Medoo;
$biblioteca = new Biblioteca\Custom();
$tabbelas = new Biblioteca\Tabelas();

$database = new Medoo([
         'database_type' => 'mysql',
         'database_name' => 'grifo',
         'server' => 'localhost',
         'username' => 'root',
         'password' => ''
     ]);

$input = filter_input_array(INPUT_POST);
/*****************************************************************************/
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
/************************Assunto Atuacao*******************************************/
/*{name: "123", value: Array(3), pk: "assunto", text: ''}*/
$id = @$input['name'];
$value = @$input['value'];
$tipo = @$input['pk'];
$text = @$input['text'];
switch($tipo){
 case 'atuacao':
  
     $database->update("procedimentos", array('atuacao'=> $value), ["Id" => $id]); 
     $res = json_encode('enviou '.$value.' para o id '.$id);
         
  break;
 case 'assunto':
        $nvalue = (!empty($value))?$value:array();    
        $database->update("procedimentos", array('assunto'=> strtolower(join(', ',$nvalue))), ["Id" => $id]); 
  if(!empty($nvalue)){
        foreach($nvalue as $val){
           $v = strtolower($val);
           $assunto = $database->query("SELECT *  FROM assuntos WHERE assunto = '$v'")->fetchAll(); 
           if(empty($assunto)){
               $database->insert("assuntos", array('Id'=>'','assunto'=>$v)); 
           }
        }
           $res = json_encode('enviou '.strtolower(join(', ',$value)).' para o id '.$id);
    }

    break;
 case 'recebido':

  $i = explode('_',$id);
  list($id_,$exp) = $i;
      $oficio = $database->get("oficios", '*', ["Id" => $id_]);
  
      $database->update("oficios", array('recebido'=>$biblioteca->banco($value)),["Id" => $id_]); 

   $vencimento = recebidoForma($biblioteca->banco($value),$oficio['prazo'],$oficio['tipo']);
switch($exp){
 case 'oficio':
  $color = 8;
  break;
}  
/*******************************************************************************************/
if(strlen($oficio['event_id']) > 5){
   $result = $eventos->editarEventoRecebido($calendario_id,$oficio['event_id'],$biblioteca->banco($vencimento));
}else{  
/*********************************************************************************************/
if($biblioteca->validaData($biblioteca->banco($value)) != '-'){ 
     $array = array(  
        "color" => $color,
        'datafim' => false,
        'data_inicio' => $biblioteca->banco($vencimento),
        'assunto' => 'Ofício nº '.$oficio['numero'],//assunto do evento
        'participantes' => '',//array(array('displayName' => 'Santa Casa de Misericoria de Anapolis','email'=>'agendamento@sistemando.com')),
        'obs' => $oficio['obs'],
        'descricao' => array( //formatar o array para recebr todos os tipos de agendamentos
                     "tipo" => $exp,//oficio, notificacao, compromisso, juri, audiencia
                     'assunto_descricao' => strtoupper($oficio['assunto']),//assunto que vai no header da descricao
                     'promotoria' => '9ª Promotoria de Justiça',
                     'procedimento' => $oficio['procedimento'],
                     'destinatario' => strtoupper($oficio['destinatario']),
                     'interessado' => strtoupper($oficio['interessado']),
                     'numero_expediente' => $oficio['numero'],//numero do expedientes ex: oficio, notificacao
                     'recebido' => date('Y-m-d'),//data do recebimento do oficio
                     'prazo' => $oficio['prazo'],//numero
                     'prazo_tipo' => $oficio['tipo'],//dias ou horas
                     "url_link" => urlencode($oficio['numero']),
                  )
       );
 $result =  $eventos->inserirEvento($calendario_id,$array);
 $database->update("oficios", ['event_id' => $result->id],["Id" =>$id_]);//atualiza a ultima atualizacao
}else{
 $result = 'data inválida';
}
}
  
     $res = $vencimento;
  break;
 case 'interessado':
     $res = strtoupper($value);  
     $database->update("oficios", ['interessado' => $res],["Id" =>$id]);//atualiza a ultima atualizacao
  break;
 case 'destinatario':
     $res = strtoupper($value);  
     $database->update("oficios", ['destinatario' => $res],["Id" =>$id]);//atualiza a ultima atualizacao
  break;
}
  
echo json_encode($res);//$biblioteca->opcao($value,',');