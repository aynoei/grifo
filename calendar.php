<?php
//error_reporting(0); 

class EventosGoogle{

 
private $calendarId;
private $service;
public $agenda;
public $biblioteca;

 
public function __construct() {
 // include your composer dependencies
include $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';
 
$this->biblioteca = new Biblioteca\Custom(); 

$credentials = $_SERVER['DOCUMENT_ROOT']. 'grifo/parts/chaves/credentials.json';
$tokenPath = $_SERVER['DOCUMENT_ROOT']. 'grifo/parts/chaves/token.json'; 
 
 if (!file_exists($credentials)) {
     //file_put_contents( $credentials, json_encode(array('')));
 }
 
    $client = new Google_Client();
    $client->setApplicationName('Promon');
    $client->setScopes(Google_Service_Calendar::CALENDAR);
    $client->setAuthConfig($credentials);
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.

    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            echo '<script type="text/javascript">window.open("'.$authUrl.'","_blank")</script>'; 
            //printf("Open the following link in your browser:\n%s\n", $authUrl);
            //print 'Digite o código de verificação: ';
            echo '<div class="mx-auto text-center w-100"><h3>Digite o código de verificação de conta</h3><br/><form method="get"><input type="text" name="code"><button type="submit">Enviar Código</button></form></div>';
            $authCode = $_GET['code'];//trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
 
    //$this->calendarId = '8pr207ntou7qp47d3k14l962s0@group.calendar.google.com';
    $this->service = new Google_Service_Calendar($client);
}

/********************************************************************************************/

public function lerEventos($agenda='',$dataMin='', $dateMax='',$tipo=''){ 

$optParams = array(
  'orderBy' => 'startTime',
  'singleEvents' => true,
   'timeMax' => $dateMax,
   'timeMin' => $dataMin
);
$results =  $this->service->events->listEvents($agenda, $optParams);
$events = $results->getItems();

if (empty($events)) {
    $eventos[] = "No upcoming events found.\n";
} else {    
 
 $fullcalendar = array();
 
    foreach ($events as $event) {
    
     $descricao = @$event->extendedProperties->shared;
 
     $d = new DateTime($event->start->dateTime);    
     
     $horario = ((in_array($descricao['tipo'],array('oficio')))?' ':"<br/><strong>Horário: </strong>".$d->format('H:i'))."<br/>";
     
       $detalhes = "<div data-color='".$event->colorId."' style='text-align: left; width: 300px;'>
									<strong>Assunto: </strong>".$descricao['assunto_descricao']."".$horario."									
									<strong>Promotoria: </strong>".$descricao['promotoria']."<br/>
									".self::se_existe($descricao['procedimento'],"<strong>Procedimento: </strong>".$descricao['procedimento']."<br/>")."
									".self::se_existe($descricao['destinatario'],"<strong>Destinatario: </strong>".$descricao['destinatario']."<br/>")."
									".self::se_existe($descricao['projudi'],"<strong>Judicial: </strong>".$descricao['projudi']."<br/>")."
									".self::se_existe($descricao['local'],"<strong>Local: </strong>".$descricao['local']."<br/>")."
									".self::se_existe($descricao['solicitante'],"<strong>Solicitante: </strong>".$descricao['solicitante']."<br/>")."
									".self::se_existe($descricao['envolvidos'],"<strong>Envolvidos: </strong>".$descricao['envolvidos']."<br/>")."
									".self::se_existe($descricao['recebido'],"<strong>Recebido em: </strong>".$this->biblioteca->cliente($descricao['recebido'])."<br/>")."  
									".self::se_existe($descricao['prazo'],"<strong>Prazo de resposta: </strong>".$descricao['prazo']." ".$descricao['prazo_tipo']."<br/>",1)."         
									".self::se_existe($descricao['reu_preso'],"Réu Preso!</br>")."
									".self::se_existe($descricao['estado'],"<hr/>O Réu está ".$descricao['estado']."<br/>")."
         ".self::se_existe($descricao['vitima'],"<strong>Envolvidos: </strong>".$descricao['vitima']."<br/>")."
         ".self::se_existe($descricao['testemuna_acusacao'],"<strong>Testemunha de Acusação: </strong>".$descricao['testemuna_acusacao']."<br/>")."
         ".self::se_existe($descricao['testemunha_defesa'],"<strong>Testemunha de Defesa: </strong>".$descricao['testemunha_defesa']."<br/>")."
         ".self::se_existe($descricao['obs'],"<strong>Observação: </strong>".$descricao['obs']."<br/>")."
							</div>";										
     

							$fullcalendar[] = array(
       "id" => $event->id,
							"tipo" => $descricao['tipo'],//notificacao, oficio, juri, compromisso,audiencia	
							"title" => $event->summary,
							"start" => $event->start->dateTime,
       "end" => $event->end->dateTime,
							"color" => self::colorsCalendar($event->colorId),
							"description" => $detalhes,
							"url_link" => urlencode($descricao['url_link']));
     
 /*************************************************************************************************************************************************************/
  $fullcalendar_array[] = array(
  "id" => $event->id,
  "tipo" => $descricao['tipo'],
  "start" => $event->start->dateTime,
  "end" => $event->end->dateTime,
  "color" => self::colorsCalendar($event->colorId),
  "url" => '/#',
  'assunto' => $event->summary,
  'assunto_descricao' => $descricao['assunto_descricao'],
  'dia' => $d->format('d/m/Y'),
		'hora' => $d->format('H:i'),
  'promotoria' => $descricao['promotoria'],
  'procedimento' => $descricao['procedimento'],
  'destinatario' => $descricao['destinatario'],
  'projudi' => $descricao['projudi'],
  'local' => $descricao['local'],
  'solicitante' => $descricao['solicitante'],
  'envolvidos' =>  $descricao['envolvidos'],
  'numero_expediente' => $descricao['numero_expediente'],
  'recebido' => $descricao['recebido'],
  'prazo' => $descricao['prazo'],
  'prazo_tipo' => $descricao['prazo_tipo'],
  'reu_preso' => $descricao['reu_preso'],
  'estado' => $descricao['estado'],
  'vitima' => $descricao['vitima'],
  'testemuna_acusacao' => $descricao['testemuna_acusacao'],
  'testemunha_defesa' => $descricao['testemunha_defesa'],
  'obs' => $descricao['obs']
 );
 
    }
 
  
} //fim do if
if(!empty($fullcalendar)){ 
 if($tipo == 'array'){
    return $fullcalendar_array;
 }else{
    return self::array2json($fullcalendar);
 }
}else{
   return self::array2json(array());
}
 
}
 
function colorsCalendar($colorId = '50'){

 
$cores =  array(
  "9" => array("name"=> "bold blue", "color"=> "#5484ed", "index"=> "0"),
  "1" => array("name"=> "roxo", "color"=> "#BC6EF5", "index"=> "1"),
  "7" => array("name"=> "azul", "color"=> "#0B8AE6", "index"=> "2"),
  "2" => array("name"=> "green", "color"=> "#7ae7bf", "index"=> "3"),
  "10" => array("name"=> "verde", "color"=> "#8AC007", "index"=> "4"),
  "5" => array("name"=> "yellow", "color"=> "#fbd75b", "index"=> "5"),
  "6" => array("name"=> "laranja", "color"=> "#ff9730", "index"=> "6"),
  "4" => array("name"=> "red", "color"=> "#ff887c", "index"=> "7"),
  "11" => array("name"=> "vermelho", "color"=> "#FF0000", "index"=> "8"),
  "3" => array("name"=> "roxo", "color"=> "#BC6EF5", "index"=> "9"),
  "8" => array("name"=> "preto", "color"=> "#000000", "index"=> "10"),
  null => array("name"=> "cinza", "color"=> "#444444", "index"=> "50"),
 );
 
 
 return @$cores[$colorId]['color'];

}

public function inserirEvento($agenda,$array){


 $EndDataHora = (strlen($array['datafim'])>4)?$array['data_fim'].' '.$array['hora_fim']:$array['data_inicio'].' '.$array['hora_inicio']."+1 hours";
 
 $event = new Google_Service_Calendar_Event(array(
  'status' => 'confirmed',//cancelled//obrigatorio
  'colorId' => $array['color'],//obrigatorio
  'summary' => $array['assunto'],//obrigatorio
  'location' => $array['local'],
  'description' => $array['assunto'],
  'start' => array('dateTime'=>date('c', strtotime($array['data_inicio'].' '.$array['hora_inicio'])),'timeZone'=>'America/Sao_Paulo'),//obrigatorio
  'end' => array('dateTime'=>date('c', strtotime($EndDataHora)),'timeZone'=>'America/Sao_Paulo'),//obrigatorio
  'attendees' => $array['participantes'],
  'reminders' => array(
    'useDefault' => FALSE,
    'overrides' => array(
      array('method' => 'sms', 'minutes' => 60),
    ),
  ),
  'extendedProperties'=> array( //formatar o array para recebr todos os tipos de agendamentos
     'shared' => $array['descricao'],//obrigatorio
   )
)); 

$event = $this->service->events->insert($agenda, $event);
return $event;

}

public function editarEventoRecebido($agenda,$id,$novaData){

$event = $this->service->events->get($agenda, $id);

$event->extendedProperties->shared['recebido'] = $novaData;
 
$event->start->dateTime = date('c', strtotime($novaData.' 00:00'));
$event->end->dateTime = date('c', strtotime($novaData.' 01:00'));

 
$updatedEvent = $this->service->events->update($agenda, $id, $event);

return $updatedEvent->getUpdated();
 
}
public function apagaEvento($agenda,$id){

return $this->service->events->delete($agenda, $id);
}

function array2json($arr) { 
    if(function_exists('json_encode')) return json_encode($arr); //Lastest versions of PHP already has this functionality. 
    $parts = array(); 
    $is_list = false; 

    //Find out if the given array is a numerical array 
    $keys = array_keys($arr); 
    $max_length = count($arr)-1; 
    if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1 
        $is_list = true; 
        for($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position 
            if($i != $keys[$i]) { //A key fails at position check. 
                $is_list = false; //It is an associative array. 
                break; 
            } 
        } 
    } 

    foreach($arr as $key=>$value) { 
        if(is_array($value)) { //Custom handling for arrays 
            if($is_list) $parts[] = array2json($value); /* :RECURSION: */ 
            else $parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */ 
        } else { 
            $str = ''; 
            if(!$is_list) $str = '"' . $key . '":'; 

            //Custom handling for multiple data types 
            if(is_numeric($value)) $str .= $value; //Numbers 
            elseif($value === false) $str .= 'false'; //The booleans 
            elseif($value === true) $str .= 'true'; 
            else $str .= '"' . addslashes($value) . '"'; //All other things 
            // :TODO: Is there any more datatype we should be in the lookout for? (Object?) 

            $parts[] = $str; 
        } 
    } 
    $json = implode(',',$parts); 
     
    if($is_list) return '[' . $json . ']';//Return numerical JSON 
    return '{' . $json . '}';//Return associative JSON 
} 
 
function se_existe($se,$q,$carMin = 3){
	if(strlen($se) > $carMin):
	    return $q;
	endif;	
}

function se($algo,$isso,$entao){
if($algo):
		return $isso;
	else:
		return $entao;
	endif;
}
}//fim class





