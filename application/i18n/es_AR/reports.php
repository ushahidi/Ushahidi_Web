<?php

$lang = array
(
	'upload' => array
	(
		'form_description'		=> 'Con este formulario se puede cargar nuevos eventos en la base de datos',
		'description_1'		=> 'Los eventos deben estar en formato CSV',
		'description_2'	=> 'Cuando el ID de un evento ya existe en la base de datos, el evento a importar será ignorado',
		'description_3'		=> 'Cada evento debe contar, por lo menos, con un título y una fecha',
		'example'		=> '#,INCIDENT TITLE,INCIDENT DATE,LOCATION,DESCRIPTION,CATEGORY,APPROVED,VERIFIED<br />
					"1","Homicidio en Parque Nacional","2009-05-15 01:06:00","Santa Tecla","Un cuerpo NN fue encontrado por personal policial en el día de la fecha","HOMICIDIO, ",YES,YES'
	),
    
    'submit' => array
	(
		'description_1'		=> 'Seleccione la ubicación precisa en el mapa', //If you can't find your location, please click on the map to pinpoint the correct location
		'thank_you'	=> 'El evento ha sido enviado y será revisado por nuestro personal. Nos contactaremos con Ud. de ser necesario', //Your Report has been submitted to our staff for review. We will get back to you shortly if necessary
		'feedback'		=> 'Déjenos sus comentarios presionand en el botón aqui debajo', //Please give us feedback about your experience by clicking on the button below
        'return'    => 'Regresar a la página de reportes', //Return to the reports page
		'example'		=> 'Ejemplo: Calle Poniente 3, Santa Tecla, El Salvador' //Examples: Johannesburg, Corner City Market, 5th Street & 4th Avenue
	),
    'view' => array
	(
		'incident_desciption'		=> 'Descripción del Evento', //Incident Report Description
		'add_information'		=> 'Agregar Información', //add information
		'original_report'	    => 'Evento Original', //Original Report
        'additional'            => 'Eventos y Discusiones Adicionales', //ADDITIONAL REPORTS AND DISCUSSION
        'related_news'          => 'Noticias Relacionadas con el Evento', //Related Mainstream News of Incident
        'reports'               => 'Reporte(s) sobre este Evento', //Incident Report(s)
	),
);
