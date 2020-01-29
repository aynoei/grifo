<?php  


function colorsCalendar($colorId = null){

 
$cores =  array(
  "7" => array("name"=> "azul", "color"=> "#0B8AE6", "index"=> "2"),
  "10" => array("name"=> "verde", "color"=> "#8AC007", "index"=> "4"),
  "6" => array("name"=> "laranja", "color"=> "#ff9730", "index"=> "6"),
  "11" => array("name"=> "vermelho", "color"=> "#FF0000", "index"=> "8"),
  "3" => array("name"=> "roxo", "color"=> "#BC6EF5", "index"=> "9"),
  "1" => array("name"=> "roxo", "color"=> "#BC6EF5", "index"=> "9"),
  "8" => array("name"=> "preto", "color"=> "#000000", "index"=> "10"),
  null => array("name"=> "cinza", "color"=> "#444444", "index"=> "50"),
 );
 
 
 return @$cores[$colorId]['color'];

}

?><!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
 <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
 <link href='scripts/popover.css' rel='stylesheet' />
<script src="https://code.jquery.com/jquery-3.4.1.min.js" ></script>
 <script src=" https://unpkg.com/popper.js@1.15.0/dist/umd/popper.min.js" ></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>


 <script src="https://unpkg.com/tooltip.js@1.3.2/dist/umd/tooltip.min.js" ></script>
<style type="text/css">
 
 .fc-title{
  color: #FFF;  
 }
 </style>
<link href='../assets/fullcalendar/packages/core/main.css' rel='stylesheet' />
<link href='../assets/fullcalendar/packages/daygrid/main.css' rel='stylesheet' />
<script src='../assets/fullcalendar/packages/core/main.js'></script>
<script src='../assets/fullcalendar/packages/core/locales-all.js'></script>
<script src='../assets/fullcalendar/packages/interaction/main.js'></script>
<script src='../assets/fullcalendar/packages/daygrid/main.js'></script>
<script>

  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      header: {
       left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek, dayGridWeek, dayGridDay'
      },
     displayEventTime: false,
      plugins: [ 'dayGrid', 'timeGrid', 'list'],
      defaultDate: '<?php echo date('Y-m-d'); ?>', 
      editable: true,
      navLinks: true,
      eventLimit: true, // allow "more" link when too many events
      eventRender: function(info) {
       console.log(info)
       var tooltip = new Tooltip(info.el, {
         title: info.event.extendedProps.description,
        placement: 'top',
        trigger: 'hover',
        container: 'body',
        html: true,
        template: '<div class="tooltip border border-dark rounded" role="tooltip" ><div class="tooltip-arrow"></div><div class="tooltip-title text-white text-center text-uppercase" style="background-color:'+info.event.backgroundColor+';border-color:'+info.event.borderColor+'">'+info.event.extendedProps.tipo+'</div><div class="tooltip-inner tooltip-inner bg-white text-dark text-left" style="max-width: 300px !important;"></div></div>'
        
       });
       
        if (info.event.extendedProps.url_link != null && info.event.extendedProps.url_link != "") {
          $(info.el).attr("href", 'https://root-aynoei177396.codeanyapp.com/grifo/index.php?p=tabela-oficio&buscar='+info.event.extendedProps.url_link);
          $(info.el).attr("target", "_blank");
       }

    }, 
     noEventsMessage: 'Sem eventos para mostrar',
     	events: {
        url: 'https://root-aynoei177396.codeanyapp.com/grifo/parts/calendar_source.php',
        error: function(e) {
         $('#script-warning').show();
         console.log(e);				}
       },
      eventSourceSuccess: function(content, xhr) {
         console.log(content);
       },
      error: function(e) {
         $('#script-warning').show();
         console.log(e);				
     },
      eventClick: function(event) {
			        if (event.url) {
			            window.open(event.url,"_blank");
			            return false;
			        }
			    },
      loading: function(bool) {
       $('#carregando').toggle(bool);
      },
      locale: 'pt-br',

   });

    calendar.render();
  });
 
</script>
<style>

  #calendar {
    max-width: 1200px;
    margin: 0 auto;
  }
 	.label{
		margin: 2px !important;
		position: relative !important;
    	float: left !important;
	}
 #script-warning {
		display: none !important;
		text-align: center;
	}

	#carregando {
		display: none;
		position: relative;
		float:right;
		margin-bottom: 0px;
   		padding: 5px;
		
	}
</style>
</head>
<body>
 <div class="row">
          <div class="dados_calendario col-sm-6 mb-3" style="height: 40px;">							
												<div id="script-warning" class="alert alert-danger alert-dismissible" role="alert" hidden>
													<button type="button" class="close" data-dismiss="danger" aria-label="Close"><span aria-hidden="true">&times;</span></button>
													<strong>Ocorreu um erro!</strong><br />Por favor, recarregue a página.
												</div>
												<div class="opcao_todas float-left">
													<a href="" target="_blank" class="mr-1 text-white"><span class="badge label-notificacao" style="background: <?php echo colorsCalendar(7); ?>">Notificação</span></a> 
													<a href="" target="_blank" class="mr-1 text-white"><span class="badge label-audiencia" style="background: <?php echo colorsCalendar(11); ?>">Audiência</span></a> 
													<a href="" target="_blank" class="mr-1 text-white"><span class="badge label-juri" style="background: <?php echo colorsCalendar(10); ?>">Juri</span></a> 
													<a href="" target="_blank" class="mr-1 text-white"><span class="badge label-compromisso" style="background: <?php echo colorsCalendar(1); ?>">Compromisso</span></a>
													<a href="" target="_blank" class="mr-1 text-white"><span class="badge label-oficio" style="background: <?php echo colorsCalendar(8); ?>">Ofício</span></a> 
													<a href="" target="_blank" class="mr-1 text-white"><span class="badge label-decurso" style="background: <?php echo colorsCalendar(6); ?>">Decurso de Prazo</span></a>
													<!---<span class="input-group-addon"><input type="checkbox" aria-label="Todas da Comarca">Toda a Comarca</span>-->
												</div>             
											</div>
          <div class="dados_carregando col-sm-6 mb-3" style="height: 40px;"><div id='carregando' class="alert alert-warning" role="alert">Aguarde, carregando eventos do calendário...</div></div>
 									
  </div>
 <div id='calendar'></div>


</body>
</html>
