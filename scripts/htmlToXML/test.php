<?php

	// this is a test page for HTMLtoOpenXML 
	
	require_once "HTMLtoOpenXML.php";
	
	$html = '<span style="font-family: Arial, sans-serif; text-align: justify;">Aos vinte e um seis do mês de fevereiro do ano de 2019, compareceu à 9ª Promotoria de Justiça de Anápolis/GO, a DECLARANTE e expôs o seguinte:&nbsp;</span><span style="font-weight: bolder; font-family: Arial, sans-serif; text-align: justify;">QUE</span><span style="font-family: Arial, sans-serif; text-align: justify;">&nbsp;estava recebendo a medicação TERIPARATIDA 250mg pela CEMAC Juarez Barbosa desde 2017;&nbsp;</span><span style="font-weight: bolder; font-family: Arial, sans-serif; text-align: justify;">QUE</span><span style="font-family: Arial, sans-serif; text-align: justify;">&nbsp;desde o mês de dezembro de 2018 não tem recebido mais;&nbsp;</span><span style="font-weight: bolder; font-family: Arial, sans-serif; text-align: justify;">QUE</span><span style="font-family: Arial, sans-serif; text-align: justify;">&nbsp;já foi na unidade de Goiânia por 3 vezes e não obteve a medicação;&nbsp;</span><span style="font-weight: bolder; font-family: Arial, sans-serif; text-align: justify;">QUE</span><span style="font-family: Arial, sans-serif; text-align: justify;">&nbsp;além da dificuldade em obter a continuidade da dispensação da medicação existe o fato do alto gasto que possui ao ir em Goiânia e não poder receber a medicação na unidade de Anápolis;&nbsp;</span><span style="font-weight: bolder; font-family: Arial, sans-serif; text-align: justify;">QUE</span><span style="font-family: Arial, sans-serif; text-align: justify;">&nbsp;necessita com urgência do restabelecimento do fornecimento da medicação;&nbsp;</span><span style="font-weight: bolder; font-family: Arial, sans-serif; text-align: justify;">QUE</span><span style="font-family: Arial, sans-serif; text-align: justify;">&nbsp;não sabe se a obtenção da medicação se deu através do convênio ou por ordem judicial;</span><span style="font-family: Arial, sans-serif; text-align: justify;">&nbsp;&nbsp;</span><span style="font-family: Arial, sans-serif; text-align: justify;">Nada mais havendo a declarar, vai, depois de lido e achado conforme, devidamente assinado por mim ____________________(Daniel Felix), que o digitei e pela declarante.</span>';
	
	echo htmlentities($html);
	echo "<br>";

	$toOpenXML = HTMLtoOpenXML::getInstance()->fromHTML($html);
	
	echo "<br>";
	echo htmlentities($toOpenXML);


?>