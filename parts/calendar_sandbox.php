<?php  
error_reporting(0); 
include($_SERVER['DOCUMENT_ROOT'] . '/grifo/parts/includes.php'); 

$array = array(  
  "color" => 9,
  "url" => '/#',//url para acessar na lista https://root-aynoei177396.codeanyapp.com/grifo/index.php?p=lista_expedientes&
  'datafim' => true,
  'data_inicio' => '',
  'hora_inicio' => '',
  'data_fim' => '2019-10-30',
  'hora_fim' => '',
  'assunto' => 'Ferias Daniel',//assunto do evento
  'participantes' => array(array('displayName' => 'Santa Casa de Misericoria de Anapolis','email'=>'agendamento@sistemando.com')),
  'obs' => 'Para arquivamento',
  'descricao' => array( //formatar o array para recebr todos os tipos de agendamentos
               "tipo" => 'ferias',//oficio, notificacao, compromisso, juri, audiencia
               'assunto_descricao' => 'Ofício',//assunto que vai no header da descricao
               'promotoria' => '9ª Promotoria de Justiça',
               'procedimento' => '201900568221',
               'destinatario' => 'SEMUSA',
               'projudi' => '',
               'local' => '',
               'solicitante' => array(),//array('bletrano de tal','sicrano de tal')
               'envolvidos' =>  array(),//array('bletrano de tal','sicrano de tal')
               'numero_expediente' => '125/2019',//numero do expedientes ex: oficio, notificacao
               'recebido' => '2019-10-05',//data do recebimento do oficio
               'prazo' => '10',//numero
               'prazo_tipo' => 'dias',//dias ou horas
               'reu_preso' => '',
               'estado' => '',
               'vitima' => array(),//array('bletrano de tal','sicrano de tal')
               'testemuna_acusacao' => array(),//array('bletrano de tal','sicrano de tal')
               'testemunha_defesa' => array(),//array('bletrano de tal','sicrano de tal')     
                  
            )
 );

//$evento =  $eventos->inserirEvento('promotoriadasaude@gmail.com',$array);

echo '<pre>';
var_dump($evento);
echo '</pre>';








//echo $eventos->inserirEvento('promotoriadasaude@gmail.com',$array);



