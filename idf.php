<?php
/** FILE PRINCIPALE 
Dovrebbe essere l'unico file richiamato direttamente via web, poi i parametri disponibile m ed f presenti nella GET determino quale file deve essere incluso ed eseguito come file principale per generare il contenuto della pagina

La logica di di base è questa, viene richimato l'url
http(s)://dominio/idf.php?m=MODULO&f=FILE&p=PARAMS

Il file idf.php inizializza il framework con tutte le librerie, le connessioni al database e crea l'ambiente di lavoro, poi vien incluso il FILE.php che si trova nel modulo MODULO (entrambi parametri passati via GET, assieme ad altri eventuali parametri specifici).
Il FILE.php eseguirà quando deve e stamperà il risultato nella [#MODULE_CONTENT#] tramite la funzione

$tpl -> set_var_to_value("MODULE_CONTENT", ""); Spiegata nella libreria TPL.

Se il file richiamato non è presente nella directory del modulo sarà cercato il file in una directory generica dove saranno contenuti alcuni file con funzioni predefinite dal framework, ad esempio il file crud.php
	
*/

//FARE UN HTACCESS CHE CONSENTE SOLO LA LETTURA DEL FILE PRINCIPALE idf.php e non i file interni
//NON MANDARE eMAIL FORMATTATE MA SOLO LINK CHE APRONO una notifica web?
//FARE UN CONTROLLO OGNI VOLTa CHE ARRIVANO POST e GET

//ATTENZIONE ALLA SICUREZZA NEI FILE JSON PERCHé PASSANDO NEL URL UN JSON ESTERNO IL SOFTWARE LEGGEREBBE QUELLO E POTREBBe ESSERe FACILMENTE BUCABILE

//VALUTARE IN FASE DI LOGIN DI INSERIRE 2FA e oltre a user/pass anche il nome del cliente/database e nel caso suddividere i contenuti del file config.php in generici e in specifici per il cliente

if(!ob_start("ob_gzhandler")) ob_start();


//DEFINIZIONE DIRECTORY DI SISTEMA
	//DIRECTORY DA USARE VIA HTTP
	$dir_idf_http = "idf/";
	$dir_tpl_http = $dir_idf_http."tpl/";
	$dir_theme_http = "theme/";
	
	//DIRECTORY DA USAE IN INCLUDE/REQUIRE PHP
	$dir_base = __DIR__."/";
	$dir_idf = $dir_base.$dir_idf_http;
	$dir_lib = $dir_idf."lib/";
	$dir_tpl = $dir_tpl_http;
	$dir_theme = $dir_base.$dir_theme_http;
	$dir_modules = $dir_base."modules/";

	//LIBRERIE INCLUDESE SEMPRE
	$def_lib_inclusion = array('tpl.php','common.php','pdo.php','html.php');

	//INIZIALIZZAZIONE ARRAY PER INCLUSIONI FILE&CODICE CSS E JS
	$GLOBALS['css_js_inclusion'] = array();

	require_once($dir_idf."config.php");
	$dir_active_theme = $dir_theme.$theme."/";
	$module_active = $module_default;//qui prendere in base a login/session/link - magari preimpostare un modulo di default per tutti (user??)


//INIZIALIZZAZIONE 
if(in_array($_SERVER["REMOTE_ADDR"],$ip_allow_idf_debug)){
	ini_set('display_errors', '1');
	error_reporting(E_ALL ^ E_STRICT);	
	define("debug_active", true);
	
	function idfErrorHandler($errno, $errstr, $errfile, $errline) {
		$errorMsg  = "PHP Error: [{$errno}] {$errstr} in {$errfile} on line {$errline}";
		//echo "<script>console.error($jsonErrorMsg);</script>";
		if (method_exists("common", "console")) common::console($errorMsg,"error");
		else print($errorMsg."<br>\n");
	}
	set_error_handler("idfErrorHandler");

	
}else{
	ini_set('display_errors', '0');
	error_reporting(0);		
	define("debug_active", false);
}

/*
$usessl = false;
if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) {
	$prohost = "https";
	$usessl = true;
}*/

//INCLUSIONI LIBRERIE E FILE ESTERNE
foreach($def_lib_inclusion AS $klib => $vlib ){
	require_once($dir_lib.$vlib);//include tutti i file di libreria definiti nelle variabili di sistema
}
require_once($dir_active_theme."themeconfig.php");


ini_set('memory_limit',$memory_limit);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if ( $usessl == true ) {
	ini_set('session.cookie_secure', 1);
}

//if(!empty($idf_session_name)) session_name($idf_session_name);
ini_set('session.gc_maxlifetime', '14400'); //4 ore = 14400 secondi
session_start();


$tpl =  new tpl("[#","#]"); 
		
$tpl -> set_var_to_value("CONSOLE_SCRIPT", ""); //?????? capire come non rendeere neccessario e non far uscire l'erorro nella tpl

$tpl -> load_file("main", $dir_tpl."main.html"); //File principale per template, sarà quello printato alla fine

if(debug_active) common::console_tpl("I.D.F. Console - DEBUG ACTIVE IP ".$_SERVER["REMOTE_ADDR"],$tpl,"debug");

$tpl -> set_var_to_value("LANG", $lang); 
$tpl -> set_var_to_value("TITLE", $title); 
$GLOBALS['css_js_inclusion']['css_file'][] = $dir_theme_http.'style.php?uid='.date("YmdHis").'&theme='.$theme;
$GLOBALS['css_js_inclusion']['css_file'][] = $dir_theme_http.'grid.php?uid='.date("YmdHis").'&theme='.$theme;

$GLOBALS['css_js_inclusion']['js_file'][] = $dir_tpl_http.'js/main.js';


$menu_json = file_get_contents($dir_modules.$module_active."/menu.json");
$menu_json = json_decode($menu_json);

$array_connessioni = array();
if ( !empty($DB) ) {
	foreach ( $DB AS $kdb=>$vdb ) {
		$dbcon[$kdb] = new pdo_dbcon($vdb);
	}
}



/*
$table = 'test';
$params = array();
$params['test_id'] = "";
$params['campo1'] = "A".rand();
$params['campo2'] = "B".rand();

$return_query = $dbcon['idf_db'] -> insert($table,$params);
if(!$return_query) exit();
//print_r($data_query);

$params = array();
$params['nome'] = "%";
$sql = "SELECT * FROM `test` WHERE campo1 LIKE :nome";

$return_query = $dbcon['idf_db'] -> select2array($sql,$params);
//print_r($return_query);
//$return_query = $dbcon['idf_db'] -> query($sql,$params);

$table = 'test';
$params = array();
$params['campo1'] = "E".rand();
$params['campo2'] = "F".rand();
$condition = "test_id = :test_id";
$condition_params = array();
$condition_params['test_id'] = "2";
//ATTENZIONE A NOMINARE I CONDITION PARAMS DIFFERNETI DAI PARAMS NORMALI
//$return_query = $dbcon['idf_db'] -> update($table, $params, $condition, $condition_params);
if(!$return_query) exit();


$table = 'test';
$condition = "test_id = :test_id";
$condition_params = array();
$condition_params['test_id'] = "2";
//ATTENZIONE A NOMINARE I CONDITION PARAMS DIFFERNETI DAI PARAMS NORMALI
$return_query = $dbcon['idf_db'] -> delete($table, $condition, $condition_params);
if(!$return_query) exit();

*/

//QUI IN MEZZO DEVE AVVENIRE LA MAGIA GM3
/*
$div = array();
$info = array();
//Aggiungere def colum se non passatp nel singolo campo
$info['pred_col'] = "3";
$info['form']['action'] = "idf.php";
$info['form']['method'] = "POST";
$info['form']['name'] = "form1";
$info['form']['class'] = "";
$info['form']['id'] = "";
$info['form']['style'] = "form1";
$info['form']['title'] = "TITOLO DEL FORM";


$id = 0;

$div[$id]['html'] = "Messaggio di testo html";//codice html
$div[$id]['col'] = "6";//default mobile - 
$div[$id]['col-sm'] = "6";//576px (tablet verticale)
$div[$id]['col-md'] = "6";//768px (tablet orizzontale)
$div[$id]['col-lg'] = "6";//992px (laptop)
$div[$id]['col-xl'] = "6";//1200px (desktop grande)
$div[$id]['class'] = "";//Classi aggiuntive
$div[$id]['style'] = "background:#FF0000;";//Style aggiuntivi per il div
$id++;

$option = array();
$option['label'] = "Text  ";
$option['name'] = "Text1";
$option['value'] = "Text1";
$option['pattern'] = ".{8,}";
$option['placeholder'] = "Text1";
$option['title'] = "Minimo 8 caratteri";
$option['inputmode'] = "numeric";
$option['datalist'][]= 'Rosso';
$option['datalist'][]= 'Verde';
$option['datalist'][]= 'Blu';

$div[$id]['html'] = html::element("text",$option); //codice html
$div[$id]['col'] = "6";//default mobile
$div[$id]['col-sm'] = "6";//576px (tablet verticale)
$div[$id]['col-md'] = "6";//768px (tablet orizzontale)
$div[$id]['col-lg'] = "6";//992px (laptop)
$div[$id]['col-xl'] = "6";//1200px (desktop grande)
$div[$id]['class'] = "6";//Classi aggiuntive
$div[$id]['style'] = "background:#0000FF;";//Style aggiuntivi per il div
$id++;

$option = array();
$option['label'] = "TextArea1";
$option['name'] = "TextArea1";
$option['value'] = "lorem ipsum";
$option['rows'] = "10";
$option['cols'] = "40";
$div[$id]['html'] = html::element("textarea",$option); //codice html
$div[$id]['col'] = "6";//default mobile
$id++;

$option = array();
$option['label'] = "Select";
$option['name'] = "Select";
$option['options'] = array('1' => 'Italia','2' => 'Germania','3' => 'Francia','USA' => array('11' => 'Maine','12' => 'Maryland','13' => 'Kansas'),'Isole' => array('21' => 'Cuba','22' => 'Caraibi','23' => 'Hawai') );
$option['options_selected'] = array('2' => 'Germania','21' => 'Cuba') ;
$option['multiple'] = "multiple";
$div[$id]['html'] = html::element("select",$option); //codice html
$div[$id]['col'] = "6";//default mobile
$id++;


$option = array();
$option['label'] = " ";
$option['name'] = "Bottone";
$option['type'] = 'reset' ;
$option['value'] = "BOTTONE RESET";
$div[$id]['html'] = html::element("button",$option); //codice html
$div[$id]['col'] = "6";//default mobile
$id++;

$option = array();
$option['label'] = "Progress";
$option['name'] = "prg";
$option['title'] = "prg";
$option['max'] = '100' ;
$option['value'] = "50";
$div[$id]['html'] = html::element("progress",$option); //codice html
$div[$id]['col'] = "6";//default mobile
$id++;

$option = array();
$option['label'] = "Meter";
$option['name'] = "prg";
$option['title'] = "prg";
$option['max'] = '100' ;
$option['value'] = "50";
$div[$id]['html'] = html::element("meter",$option); //codice html
$div[$id]['col'] = "6";//default mobile
$id++;


$option = array();
$option['label'] = "Imposta il tuo interesse";
$option['name'] = "Radio";
$option['options'] = array('1' => 'Italia','2' => 'Germania','3' => 'Francia' );
$option['options_selected'] = array('2' => 'Germania') ;
$option['multiple'] = "multiple";
$div[$id]['html'] = html::element("radio",$option); //codice html
$div[$id]['col'] = "6";//default mobile
$id++;

$option = array();
$option['label'] = "Seleziona i tuoi interessi";
$option['name'] = "Checkbox";
$option['options'] = array('1' => 'Italia','2' => 'Germania','3' => 'Francia' );
$option['options_selected'] = array('1' => 'Italia','2' => 'Germania','3' => 'Francia') ;
$option['multiple'] = "multiple";
$div[$id]['html'] = html::element("checkbox",$option); //codice html
$div[$id]['col'] = "6";//default mobile
$id++;

$tpl -> set_var_to_value("MODULE_CONTENT", html::grid($div,$info)); */
//Inizio esempio
$div = array();
$info = array();

$info['pred_col'] = "3";
$info['form']['action'] = "idf.php";
$info['form']['method'] = "POST";
$info['form']['name'] = "form1";
$info['form']['class'] = "form-css";
$info['form']['id'] = "form_id";
$info['form']['style'] = "background:#eee;padding:10px;";
$info['form']['title'] = "Form completo con tutti i campi";

$id = 0;

// --- Messaggio HTML generico ---
$div[$id++] = array(
	'html' => "<h2>Compila tutti i campi</h2>",
	'col' => "12"
);

// --- Tipi di input standard ---
$input_types = array(
	'text' => array('pattern' => '[A-Za-z]{5,}', 'placeholder' => 'Min 5 lettere'),
	'password' => array('pattern' => '.{8,}', 'placeholder' => 'Min 8 caratteri'),
	'email' => array('multiple' => 'multiple', 'placeholder' => 'email@example.com'),
	'url' => array('placeholder' => 'https://example.com'),
	'tel' => array('pattern' => '[0-9]{10}', 'inputmode' => 'numeric'),
	'search' => array('results' => '5'),
	'number' => array('min' => '1', 'max' => '100', 'step' => '1'),
	'range' => array('min' => '0', 'max' => '100', 'step' => '5'),
	'date' => array('min' => '2024-01-01', 'max' => '2025-12-31'),
	'month' => array(),
	'week' => array(),
	'time' => array(),
	'datetime-local' => array(),
	'color' => array(),
	'file' => array('accept' => '.jpg,.png', 'multiple' => 'multiple'),
	'image' => array('src' => 'icon.png', 'alt' => 'Invia')
);

foreach ($input_types as $type => $attrs) {
	$option = array_merge(array(
		'label' => ucfirst($type),
		'name' => "field_$type",
		'value' => "Valore di test",
		'title' => "Campo di tipo $type",
		'required' => true,
		'class' => 'input-class',
		'style' => 'width:100%',
		'autocomplete' => 'on'
	), $attrs);

	if ($type === 'text') {
		$option['datalist'] = array('Rosso', 'Verde', 'Blu');
	}

	$div[$id++] = array(
		'html' => html::element($type, $option),
		'col' => "4"
	);
}

// --- Textarea ---
$option = array(
	'label' => 'Messaggio',
	'name' => 'textarea1',
	'value' => 'Testo iniziale',
	'rows' => 5,
	'cols' => 30,
	'maxlength' => 500,
	'placeholder' => 'Scrivi qui...',
	'required' => true
);
$div[$id++] = array('html' => html::element('textarea', $option), 'col' => "6");

// --- Select ---
$option = array(
	'label' => 'Paese preferito',
	'name' => 'select1',
	'multiple' => 'multiple',
	'size' => 5,
	'options' => array(
		'Europa' => array('it' => 'Italia', 'fr' => 'Francia'),
		'Asia' => array('jp' => 'Giappone', 'cn' => 'Cina')
	),
	'options_selected' => array('it' => 'Italia','jp' => 'Giappone')
);
$div[$id++] = array('html' => html::element('select', $option), 'col' => "6");

// --- Checkbox ---
$option = array(
	'label' => 'Seleziona interessi',
	'name' => 'check1',
	'options' => array('1' => 'Musica', '2' => 'Sport', '3' => 'Arte'),
	'options_selected' => array('1' => 'Musica', '2' => 'Sport'),
	'separator' => '<br>',
	'required' => true
);
$div[$id++] = array('html' => html::element('checkbox', $option), 'col' => "6");

// --- Radio ---
$option = array(
	'label' => 'Genere preferito',
	'name' => 'radio1',
	'options' => array('m' => 'Maschio', 'f' => 'Femmina', 'x' => 'Altro'),
	'options_selected' => array('x' => 'Altro'),
	'separator' => ' ',
	'required' => true
);
$div[$id++] = array('html' => html::element('radio', $option), 'col' => "6");

// --- Progress ---
$option = array(
	'label' => 'Completamento',
	'name' => 'progress1',
	'value' => '75',
	'max' => '100',
	'title' => 'Avanzamento'
);
$div[$id++] = array('html' => html::element('progress', $option), 'col' => "6");

// --- Meter ---
$option = array(
	'label' => 'Valutazione',
	'name' => 'meter1',
	'value' => '0.7',
	'min' => '0',
	'max' => '1',
	'low' => '0.2',
	'high' => '0.8',
	'optimum' => '0.5'
);
$div[$id++] = array('html' => html::element('meter', $option), 'col' => "6");

// --- Bottoni: submit e reset ---
$div[$id++] = array('html' => html::element('button', array(
	'label' => '',
	'name' => 'btnSubmit',
	'type' => 'submit',
	'value' => 'INVIA',
	'autofocus' => true
)), 'col' => "3");

$div[$id++] = array('html' => html::element('button', array(
	'label' => '',
	'name' => 'btnReset',
	'type' => 'reset',
	'value' => 'RESET'
)), 'col' => "3");

// --- Output finale ---
$tpl->set_var_to_value("MODULE_CONTENT", html::grid($div, $info));

//Fine Esempio
$sideMenuHtml = common::side_menu_json_html($menu_json->menu);
$tpl -> set_var_to_value("SIDE_MENU", $sideMenuHtml); 


//CSS-JS inclusion file & code
$tpl -> set_var_to_value('CSS_FILE_INCLUSION',"");
$tpl -> set_var_to_value('CSS_CODE_INCLUSION',"");
$tpl -> set_var_to_value('JS_FILE_INCLUSION',"");
$tpl -> set_var_to_value('JS_HEAD_CODE_INCLUSION',"");
$tpl -> set_var_to_value('JS_FOOT_CODE_INCLUSION',"");
foreach($GLOBALS['css_js_inclusion'] AS $kjc => $vjc ){
	switch($kjc){
		case "css_file":
			foreach($vjc AS $vjc_key => $vjc_value )$tpl -> set_var_to_value("CSS_FILE_INCLUSION", '<link rel="stylesheet" type="text/css" href="'.$vjc_value.'">', true);
			break;
		case "css_code":
			foreach($vjc AS $vjc_key => $vjc_value )$tpl -> set_var_to_value("CSS_CODE_INCLUSION", $vjc_value, true);
			break;
			
		case "js_file":
			foreach($vjc AS $vjc_key => $vjc_value )$tpl -> set_var_to_value("JS_FILE_INCLUSION", '<script type="text/javascript" src="'.$vjc_value.'"></script>', true);
			break;
		case "js_code_head":
			foreach($vjc AS $vjc_key => $vjc_value )$tpl -> set_var_to_value("JS_HEAD_CODE_INCLUSION", $vjc_value, true);
			break;
		case "js_code_foot":
			foreach($vjc AS $vjc_key => $vjc_value )$tpl -> set_var_to_value("JS_FOOT_CODE_INCLUSION", $vjc_value, true);
			break;
	}
}




common::console_tpl("I.D.F. Console - END EXECUTION ".date("Y-m-d H:i:s"),$tpl,"info");
$tpl -> g_output("main");


//DEFINIRE COLORI TEMA IN CSS - CSS in PHP?
//COMMENTARE E PULIRE DA FUNZIONI NON USATE LA TPL LIB
//FARE UN OVERRIDE DEI TESTI PER LE LINGUE

//Da qui in poi appunti
if(!empty($_GET['logout']) && $_GET['logout'] == 'y'){
	foreach($_SESSION AS $k=>$v) {
		unset($_SESSION[$k]);
	}
	setcookie($cookie_user_name, "", time());
	setcookie($cookie_password_name, "", time());	
}

//Mettere if che genera la funzione se è partitolo lo start, valutare compressione della pagina

ob_end_flush();

?>