<?php defined('SYSPATH') OR die('No direct access allowed.');
// DO NOT EDIT
// This file is automatically generated from the matching PO file
// Updates should be made through Transifex
// I18n generated at: 2014-09-27 06:25+0000
// PO revision date:  2014-09-27 05:31+0000
$lang = array(
	'actions' => array(
		'add_to_category' => 'Esto inclúe informes de categorías adicionáis. Se selecciona Categoría 1 eiquíe o Informe xa ten a Categoría 2 vencellada, o Informe terá asociadas ambalasdúas.',
		'approve' => 'Aproba o informe ou non. Se o aproba, aparecerá publicado.',
		'assign_badge' => 'Poderá asignar un distintivo a un/ha usuario/a disparador/a',
		'between_times' => 'Trátase dun rango horario entre dous espazos de tempo en formato de 24hs. Se se introduce un tempo anterior no segundo campo, trocarase cara ao primeiro. Ambos tempos deben estar no mesmo día. Amais, este tempo é comparado co que se estableceu no site nas configuracións e pode <strong>non estar definido</strong>necesariamente no mesmo fuso horario da/o usuaria/o que participa no Ushahidi.Deixe os valores a 00:00 e 00:00 para ignorar este habilitador.',
		'category' => 'Se quere activar un disparador só cando certa categoría estea sendo usada pode facelo eiquí. Esto permite cun disparador se active únicamente candose empregue determiñada categoría. Por exemplo, se se seleccionan as Categorías 1 e 2 eiquí e un informe é enviado empregando as Categorías  2 e 3, o informe pasará o test.',
		'days_of_the_week' => 'Se esas accións acontecen en certos días da semana, defínao eiquí. Teña en conta que o día determíñase segundo o fuso horario configurado na súa instancia do Ushahidi. Empregue Shift, Command ou Control para seleccionar múltiples días.',
		'email_body' => 'Corpo do correo a enviar.',
		'email_subject' => 'Asunto do correo a enviar.',
		'feed_id' => 'O feed pode ser de todolos feeds ou dun feed específico. Se se quere que só certos feeds activen un disparador, débense escoller eiquí. En caso contrario deixe esto co valor predetermiñado: "todos"',
		'from' => 'Nome da/o usuario/a no Twitter (ou múltiples nomes separados por comas). Se ten intención de activar disparadores só para twits dun/ha usuario/a concreto/a introduza o seu nome eiquí (sen incluir a @)',
		'keywords' => 'Pódese deixar en branco se non se queren verificar palabras-chave. Se se engaden palabras eiquí, hainas que separar con comas (,). Por exemplo, se se queren activar disparadores cando alguén mencione "amor" ou "paz"na súa mensaxe, haberá que engadir: "paz, amor" na caixa de palabras-chave.',
		'location' => 'Pódese seleccionar calquera lugar ou unha localización específica. Neste último caso, solicitarase que debuxe unha caixa delimitando a área que require unha acción. Por exemplo, se se quere cun disparador se active cando alguén presente un informe sobre Ourense, hai que seleccionar "área específica" e deseguido debuxar unha "caixa" en torno a Ourense. Pódense facer éstas tan grandes ou pequenas como se queira. Tamén é posible debuxar múltiples caixas.',
		'on_specific_count' => 'Este habilitador activará o disparador na conta nº X contrastándoa contra o conxunto de usuarias/os ou para cada usuaria/o individualmente. Deixeo en branco se prefire ignoralo.',
		'report_title' => 'Este é o título predetermiñado para engadir a un informe.',
		'response' => 'Se tódolos habilitadores enriba estivesen activos, o disparador iniciaría unha resposta. Esto pode ser dende aprobar un Informe a Enviar un Email a un/ha usuario/a. Seleccione eiquí a resposta para activar opcións adicionáis para respostas específicas.',
		'send_to' => 'Se selecciona "Usuario/a Disparador/a", o correo será enviado a/ao usuaria/o que executou a acción concreta. Se se selecciona o botón de radio ao carón da caixa de entrada, poderá incluir un enderezo de correo persoalizado. Ésto é útil se se están configurando disparadores para notificar á xente cando certas zonas no mapa reciben informes, chekins ou calqueira outra actividade.',
		'specific_days' => 'Pode seleccionar múltiples días eiquí. As datas veñen determiñadas polo fuso horario definido na configuración do site. Se non selecciona nengunha data, o sistema amosará predetermiñadamente tódalas datas.',
		'trigger' => 'O disparador é o compoñente central na configuración das Accións Disparadoras.Eiquí é onde se determiña qué acontecerá cando alguén envíe un informe, faga un checkin, etc. Eiquí pode filtrar as respostas a esas accións despois de seleccionar un disparador.',
		'user' => 'A/o usuaria/o pode ser un/ha calquera ou un/ha específico/a. Se se quere traballar con un/ha concreto/a, hai que seleccionalo/a. En caso contrario, hai que deixar o valor a "calquera" xa que a maioría dos disparadores establécense para tódalas/os usuarias/os que interactúan co sistema.',
		'verify' => 'Marca o informe como verificado ou non.',
	) ,
	'change_picture' => 'As páxinas de perfil no site empregan Gravatar. Ao premer na súa imaxe, será redirixida/o ao site Gravatar onde poderá trocar a súa foto de perfil.',
	'default_value' => 'Separe cada valor cunha coma, p.ex. valor1, valor2.',
	'radio_choices' => 'Separe cada valor cunha coma, p.ex. valor1, valor2. No caso de querer definir un valor predetermiñado, remate a súa lista de opcións con: "::" P.ex. se quere que o valor3 sexa o predetermiñado, a cousa sería: valor1, valor2, valor3::valor3',
	'dropdown_choices' => 'Separe cada elemento seleccionado cunha coma, p.ex.: Elemento1, Elemento2, etc.',
	'private_to' => 'Comence a escreber a lista de membros.',
	'private_subject' => 'Asunto da mensaxe privada',
	'private_message' => 'Mensaxe privada',
	'profile_color' => 'Pode seleccionar unha cor que será a que apareza baixo a súa imaxe no perfil público. Esta cor será tamén asociada aos puntos dos seus checkins no mapa.',
	'profile_email' => 'O seu enderezo de correo',
	'profile_name' => 'Este é un dos xeitos nos que será identificado no site. Téñao en conta, pois é un dato público!',
	'profile_new_password' => 'Se se define, éste ha ser o seu novo contrasinal. Deixe o campo en branco se prefire manter o actual.',
	'profile_new_users_password' => 'Esto é un requisito cando se crea un/ha novo/a usuario/a e establece os seus respectivos contrasináis. Debe avisar ás/aos novas/os usuarias/os de que troquen o seu contrasinal ao acceder por vez primeira á conta.',
	'profile_notify' => 'Ao seleccionar SÍ, recibirá alertas via mail en canto un novo informe ou un comentario sexa publicado no site.',
	'profile_password' => 'O seu contrasinal actual. É preciso que introduza o seu contrasinal para impedir que alguén sen autorización poda trocar cousas na súa conta.',
	'profile_public' => 'O seu perfil será visible para calqueira en Internet se selecciona esto. Ao mesmo tempo é tamén o xeito máis sinxelo de amosar nunha soa páxina os informes que enviou, os seus checkins, identificativos, etc.',
	'profile_public_url' => 'Este é o enderezo no que o seu perfil público pode ser topado.',
	'profile_username' => 'O seu nome de usuaria/o non pode ser cambiado.',
	'settings_access_level' => 'Os nivéis de acceso son empregados para restrinxir o acceso aos datos dos campos persoalizados dos formularios. Maiores nivéis de acceso permiten manexar nivéis mais baixos. A/o superadmin ten o nivel máis alto de acceso (100). Os datos públicos son accesibles no máis baixo nivel (0). Os membros teñen o nivel de acceso 10. A/o admin ten predetermiñado o nivel 90.',
	'settings_alert_email' => 'Este é o enderezo de correo que será empregado para enviar alertas por email.',
	'settings_allow_alerts' => 'Permite ás/aos usuarias/os suscribirse a alertas via web.',
	'settings_allow_clustering' => 'Permite agrupar informes similares nun só punto do mapa.',
	'settings_allow_comments' => 'Permite ás/aos usuarias/os comentar nos informes do site principal. ',
	'settings_allow_feed' => 'Permite amosar os RSS\'s de novas no site principal.',
	'settings_allow_feed_category' => 'Isto permite crear unha nova Categoría dende as fontes de Noticias RSS',
	'settings_allow_reports' => 'Permite ás/aos usuarias/ps enviar información via formulario web.',
	'settings_api_default_record_limit' => 'Número predetermiñado de rexistros solicitados en cada chamada da API.',
	'settings_api_max_record_limit' => 'Número máximo de rexistros solicitados en cada chamada á API.',
	'settings_api_max_requests_per_ip' => 'Número máximo de chamadas á API por enderezo IP.',
	'settings_banner' => 'O banner do site aparece na cima frontal do seu site se o tema que emprega o permite. O tamaño recomendado para esta faixa ha depender do tema. Teña en conta que o mesmo substitúe o título e o lema do site na mesma posición do frontal.',
	'settings_blocks_per_row' => 'Número de columnas do bloque que serán amosadas en cada liña.',
	'settings_cache_pages' => 'Activar ou desactivar a caché das páxinas. Esto permite que as páxinas se amosen mais axiña pois reduce o tempo de resposta. Recoméndase empregalo en sites con moito tráfico. Lembre que os informes aparecerán na páxina principal conforme o planexamento de tarefas que se definira (tempo de vida da caché).',
	'settings_cache_pages_lifetime' => 'Definir o tempo de vida da caché.',
	'settings_checkins' => 'Esta configuración permite empregar checkins no seu Ushahidi. Trátase dunha clase de informe simplificado que non pasa por moderación antes de ser publicado e requiere que o site sexa configurado de certo xeito. Ao habilitar esta opción, comprobe que a súa configuración do fuso horario está en formato UTC e que o seu tema soporta os checkins. Cando active esta opción, os temas baseados en checkin aparecerán habilitados na páxina de configuración de addons/temas.',
	'settings_configure_map' => 'Configurar o mapa para cubrir unha localización concreta.',
	'settings_default_category_colors' => 'Seleccionar un código de cor para tódalas categorías no site.',
	'settings_default_category_icons' => 'Seleccionar unha icona para tódalas categorías no site.',
	'settings_default_location' => 'Este é o país sobre o que o site traballa.',
	'settings_display_contact' => 'Activa ou desactiva a lapela de Contacto no site principal.',
	'settings_display_howtohelp' => 'Activa ou desactiva a lapela de Axuda no site principal.',
	'settings_display_items_per_page' => 'Este é o número de informes que se amosan por páxina no site principal.',
	'settings_display_items_per_page_admin' => 'Este é o número de informes amosados por páxina no Panel de Control.',
	'settings_flsms_download' => 'Este é o hub para as mensaxes de entrada.',
	'settings_flsms_synchronize' => 'Esto sincroniza as mensaxes no hub coa plataforma Ushahidi.',
	'settings_flsms_text_1' => 'Números de teléfono atraverso dos que chegan mensaxes de texto.',
	'settings_google_analytics' => 'Rastrexa os visitantes do seu site. Amosa estatísticas polo miúdo.',
	'settings_locale' => 'Selecciona a a lingua que será usada no site.',
	'settings_manually_approve_users' => 'Se fixa esta opción a "sí", deberá aprobar a cada usuaria/o individual que cree unha conta no site asignándolle os correspondentes roles (p.ex. Membro, Admin, SuperAdmin).',
	'settings_map_provider' => 'Esto define qué mapa será empregado no sitio.',
	'settings_map_timeline' => 'Esto amosa unha liña de tempo baseada na data e hora dos informes que se envíen.',
	'settings_private_deployment' => 'Fixando este valor a "verdade" ou "sí" o Ushahidi será privado e só as/os usuarias/os con conta que se especifiquen poderán acceder ao mesmo.',
	'settings_require_email_confirmation' => 'As/os usuarias/os recibirán un email cunha ligazón de confirmación antes de poder acceder ao Ushahidi se isto é fixado a "sí". Se activa isto despois de que o site xa teña creado usuarias/os, solicitaráselles confirmar a súa conta antes de permitírselles continuar o acceso.',
	'settings_server_host' => 'Este é o lugar onde os emails serán arquivados.',
	'settings_server_password' => 'Este é o contrasinal do enderezo de correo que recibirá os informes.',
	'settings_server_port' => 'Esto é preciso para aceptar as conexións entrantes dende este enderezo de correo.',
	'settings_server_ssl_support' => 'Esto é preciso para aumentar a seguridade da conexión.',
	'settings_server_type' => 'Esto é preciso para recibir emails dende o servidor de correo.',
	'settings_server_username' => 'Este é o enderezo de correo que recibirá os informes.',
	'settings_share_site_stats' => 'As estatísticas de visitas son gardadas no server controlado polo Ushahidi. Activando esta opción, poderá visualizar os datos de acceso directamente dende o panel de administración. Deshabilitandoo déixanse de recabar estatísticas e non haberá acceso aos datos gardados mentras a función siga deshabilitada.',
	'settings_site_copyright_statement' => 'Quere permitir a outras persoas republicar o texto, as imaxes, os videos ou mesmo o deseño do site que vostede ou as/os usuarios teñen creado eiquí? Vaia a https://creativecommons.org/choose/ se ten interese en especificar cunha licenza o que outras/os poden facer co seu traballo. E lembre especificar claramente qué elementos do site está licenciando.',
	'settings_site_email' => 'Este é o enderezo de correo que recibirá informes e mensaxes dende o formulario de contacto.',
	'settings_site_message' => 'Este é o texto que aparecerá na cima do mapa ma páxina principal. É útil para ofrecer información importante as/aos visitantes do site. Para eliminar a caixa simplemente borre esta mensaxe.',
	'settings_site_name' => 'Este é o nome do site que aparecerá na cima da páxina principal.',
	'settings_site_submit_report_message' => 'Esta é a mensaxe que aparecerá na páxina de envío dos informes. É un bó lugar para avisos ou instruccións adicionáis para as/os informantes.',
	'settings_site_tagline' => 'En poucas palabras, explique en qué consiste o site.',
	'settings_site_timezone' => 'Este é o fuso horario no que o seu site traballa, cousa que terá repercusión en cada acción que se teña configurado que empregue datas ou horas, tanto no horario predetermiñado dos informes como na interacción externa ou nos mecanismos internos do site.',
	'settings_twitter_configuration' => 'Defina o hashtag asociado ao proxecto no twitter',
);
