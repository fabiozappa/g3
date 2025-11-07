<?php
/**
 * Classe `html`
 *
 * Generatore dinamico di codice HTML per layout a griglia Bootstrap e form.
 * Include supporto completo per la maggior parte degli input HTML5, textarea,
 * select, checkbox, radio, e altri elementi.
 *
 * Le funzioni sono statiche e permettono la generazione rapida di elementi
 * da strutture array associative.
 *
 * @package FormBuilder
 * @author  
 * @version 1.0
 */
 
class html {
	
/** FUNCTION "grid"
 * Genera una struttura HTML a griglia (Bootstrap-style) con riga e colonne.
 * Supporta anche l'inclusione opzionale di un tag `<form>`.
 *
 * Ogni colonna è definita come un array con le seguenti chiavi:
 * - `col`, `col-sm`, `col-md`, `col-lg`, `col-xl` → dimensioni responsive (opzionali)
 * - `class`, `style` → attributi CSS aggiuntivi
 * - `html` → contenuto HTML da visualizzare nella colonna
 *
 * Parametri `info['form']` per generare automaticamente il tag `<form>`:
 * - `action`, `method`, `name`, `id`, `class`, `style`, `title`
 *
 * @param array $div  Array di elementi da disporre nelle colonne.
 * @param array $info (Opzionale) Opzioni generali (es. attributi form, pred_col).
 *
 * @return string Codice HTML generato.
 *
 * @example
 * echo html::grid([
 *     ['col' => 6, 'html' => '<input ...>'],
 *     ['col' => 6, 'html' => '<button>Invia</button>']
 * ]);
 */
	public static function grid($div,$info=array()) {
		$num_col = 0;
		$html_grid = "";
		$pred_col = !empty($info['pred_col']) ? $info['pred_col'] : "3";//definisco il valore di default se non è passato nei singoli elementi

		/*FINIRE FORM PARAMS*/
		if(!empty($info['form'])){
			$form_attribute = "";
			if(!empty($info['form']['action'])) $form_attribute .= 'action="'.$info['form']['action'].'"';
			if(!empty($info['form']['method'])) $form_attribute .= 'method="'.$info['form']['method'].'"';
			if(!empty($info['form']['name'])) $form_attribute .= 'name="'.$info['form']['name'] .'"';
			if(!empty($info['form']['class'])) $form_attribute .= 'class="'.$info['form']['class'].'"';
			if(!empty($info['form']['enctype'])) $form_attribute .= 'enctype="'.$info['form']['enctype'].'"';
			if(!empty($info['form']['target'])) $form_attribute .= 'target="'.$info['form']['target'].'"';
			if(!empty($info['form']['title'])) $form_attribute .= 'title="'.$info['form']['title'].'"';
			if(!empty($info['form']['style'])) $form_attribute .= 'style="'.$info['form']['style'].'"';
			if(!empty($info['form']['id'])) $form_attribute .= 'id="'.$info['form']['id'].'"';




			
			$html_grid .= '<form '.$form_attribute.'>';			
		}
		$html_grid .= '<div class="row">';
		foreach($div AS $kd => $vd ){
			$vd_col = !empty($vd['col']) ? $vd['col'] : $pred_col;
			$num_col += $vd_col;
			$extra_class = "";
			if(!empty($vd['col-sm'])) $extra_class .= " col-sm-".$vd['col-sm']; 
			if(!empty($vd['col-md'])) $extra_class .= " col-md-".$vd['col-md']; 
			if(!empty($vd['col-lg'])) $extra_class .= " col-lg-".$vd['col-lg']; 
			if(!empty($vd['col-xl'])) $extra_class .= " col-xl-".$vd['col-xl']; 
			if(!empty($vd['class'])) $extra_class .=  $vd['class']; 
			
			$style = "";
			if(!empty($vd['style'])) $style = 'style="'.$vd['style'].'"'; 

			$html_grid .= '<div class="col-'.$vd_col.' '.$extra_class.'" '.$style.' >'.$vd['html'].'</div>';

			if($num_col >= 12){
				$html_grid .= '</div><div class="row">';
				$num_col = 0;
			}
		}
		$html_grid .= '</div>';
		if(!empty($info['form'])){
			$html_grid .= '</form>';			
		}

		return($html_grid);

	}

/** FUNCTION "element"
	 * Genera un elemento di form HTML basato sul tipo richiesto.
	 *
	 * Supporta i seguenti tipi: input (text, password, email...), select, textarea,
	 * checkbox, radio, button, progress, meter. Include anche datalist e optgroup.
	 *
	 * @param string $type   Tipo dell'elemento (es. text, select, radio, etc.).
	 * @param array  $option Array associativo delle opzioni e attributi dell'elemento.
	 *
	 * @return string Codice HTML dell'elemento form.
	 *
	 * @example
	 * echo html::element('text', [
	 *     'name' => 'email',
	 *     'label' => 'La tua email',
	 *     'placeholder' => 'esempio@email.com',
	 *     'required' => true
	 * ]);
	 
	 📘 Dettagli sugli input supportati
	 
	 🟢 Input <input type="..."> supportati:
		 •	text, password, email, url, tel, search
		 •	number, range, date, month, week, time, datetime-local, color
		 •	checkbox, radio, file, submit, reset, hidden, image, button
	 
	 ⚙️ Attributi generali supportati:
		 •	name, id, type, value, class, style, placeholder, required, readonly, disabled, autocomplete, autofocus, form, title, tabindex, spellcheck, aria-*, inputmode, pattern, min, max, step, size
	 
	 📝 Textarea:
		 •	rows, cols, wrap, maxlength, minlength, e altri compatibili
	 
	 🔽 Select:
		 •	Supporta options e options_selected + gestione optgroup
	 
	 🔘 Checkbox & Radio:
		 •	Supportano selezioni multiple, separator personalizzato, e array di options
	 
	 📊 Meter / Progress:
		 •	Attributi come value, max, min, optimum, ecc.
	 
	 
	 */
/**
* @param string $type indica la tipologia di campo.
di seguito i valori disponibili pet i campi di tipo "<intput type='' >" a fianco di ogni campo sono indicati gli attributi specifici utilizzabili nel parametro @option
text: maxlength, minlength, pattern, size
password: maxlength, minlength, pattern, size, inputmode
email: multiple, maxlength, minlength, pattern
url: maxlength, minlength, pattern
tel: pattern, maxlength, minlength, inputmode
search: maxlength, minlength, pattern, results
number: min, max, step
range: min, max, step
date: min, max, step
month: min, max, step
week: min, max, step
time: min, max, step
datetime-local: min, max, step
color (nessuno specifico oltre i comuni)
checkbox: checked
radio: checked
file: accept, multiple, capture
submit: formaction, formenctype, formmethod, formtarget, formnovalidate, value
reset: value
button: value, formaction, formmethod, formtarget, formnovalidate
hidden: value
image: src, alt, width, height, formaction, formenctype, formmethod, formtarget, formnovalidate
	
questi invece i parametri utilizzabile per ogni tipo di input
•	id
•	name
•	type
•	value
•	form
•	class
•	style
•	title
•	disabled
•	readonly
•	required
•	tabindex
•	autocomplete
•	autofocus
•	placeholder
•	aria-* (accessibilità)
•	spellcheck

TEXTAREA
Il parametro $option['value'] contiene il testo inserito nella textarea 
Di seguito gli altri parametri utilizzabili per textarea
•	name
•	id
•	rows
•	cols
•	maxlength
•	minlength
•	placeholder
•	required
•	readonly
•	disabled
•	wrap (soft, hard)
•	form
•	autocomplete
•	autofocus
•	spellcheck

SELECT
l'array annidato options contiene le varie OPTION della select
mentre l'array annidato options_selected contiene le varie OPTION della select selezionate (più di una in caso di multiselect)
•	name
•	id
•	required
•	disabled
•	multiple
•	size
•	form
•	autofocus


BUTTON
•	type (submit, reset, button)
•	name
•	id
•	value
•	form, formaction, formmethod, formtarget, formnovalidate, formenctype
•	disabled
•	autofocus

PROGRESS
•	value
•	max
•	form

METER
•	value
•	min
•	max
•	low
•	high
•	optimum

RADIO E CHECKBOX
•	separator indica un eventuale carattere di separazione (come <br> tra le varie opzioni)
l'array annidato options contiene le varie OPTION della select
mentre l'array annidato options_selected contiene le varie OPTION della select selezionate (più di una in caso di multiselect)


*/





	public static function element($type,$option=array()) {
		$param_and_value = array(  'form', 'class', 'style', 'title', 'tabindex', 'autocomplete',  'placeholder', 'aria-*', 'spellcheck', 'pattern', 'min', 'max', 'step', 'maxlength', 'minlength', 'size','inputmode', 'results', 'capture', 'accept', 'src', 'alt', 'width', 'height', 'formaction', 'formenctype', 'formmethod', 'formtarget', 'rows', 'cols', 'wrap', 'type', 'low', 'high', 'optimum');
		$param_only = array('disabled', 'readonly', 'required', 'autofocus', 'checked', 'multiple', 'formnovalidate', 'multiple');
		
		$name = $option['name'];//Definisce il nome del campo, che viene utilizzato per identificare il valore quando i dati del modulo vengono inviati.
		if(!empty($option['id'])) $id = $option['id'];//Definisce l'id del campo, che viene utilizzato per identificare il campo in caso di javascript
		else $id = $name;
		$label = "";//Definisce il testo visibile nell'etichetta del campo.
		if(!empty($option['label'])) $label = '<label for="'.$name.'">'.$option['label'].'</label><br>';
		
		$extra = "";//Serve per aggiungere eventuali onClick e altri parametri non previsti
		if(!empty($option['extra'])) $extra = $option['extra'];

		$value = "";//Serve per aggiungere eventuali onClick e altri parametri non previsti
		if(!empty($option['value'])) $value = $option['value'];

		$input_attributes = "";
		foreach($option AS $kopt => $vopt ){
			if(in_array($kopt, $param_and_value))$input_attributes .= ' '.$kopt.' = "'.$vopt.'" ';
			elseif(in_array($kopt, $param_only))$input_attributes .= ' '.$kopt;
		}
			
		switch($type){
			case "text":
			case "password":
			case "email": 
			case "url": 
			case "tel": 
			case "search": 
			case "number": 
			case "range": 
			case "date": 
			case "month": 
			case "week": 
			case "time": 
			case "datetime-local": 
			case "color": 
			case "file": 
			case "submit": 
			case "reset": 
			case "hidden": 
			case "image": 						
				$datalist = "";
				if(!empty($option['datalist'])){
					$input_attributes .= 'list = "list-'.$name.'" ';
					$datalist .= '<datalist id="list-'.$name.'">';
					foreach($option['datalist'] AS $kdl => $vdl ){
						$datalist .= ' <option value="'.$vdl.'">'.$vdl.'</option>';
					}
					$datalist .= '</datalist>';
				}
				$html_element = $label.'<input type="'.$type.'" name="'.$name.'" id="'.$id.'" value="'.$value.'" '.$input_attributes.' '.$extra.'>'.$datalist;
				break;
			case "checkbox":
				$html_element = "";
				$separator = "";
				if(!empty($option['separator'])) $separator = $option['separator'];
				if(!empty($option['label'])) $html_element .= '<legend>'.$option['label'].'</legend>';
				foreach($option['options'] AS $kops => $vops ){
					$opt_selected = "";
					if(in_array($vops, $option['options_selected'])) $opt_selected = "checked";
				
					$html_element .= '<input type="checkbox" id="'.$name.'_'.$kops.'" value="'.$kops.'[]" name="'.$name.'" id="'.$id.'" '.$input_attributes.' '.$extra.' '.$opt_selected.' >
				  	<label for="'.$name.'_'.$kops.'">'.$vops.'</label>'.$separator;
				}
				break;
			case "radio": 
				$html_element = "";
				$separator = "";
				if(!empty($option['separator'])) $separator = $option['separator'];
				if(!empty($option['label'])) $html_element .= '<legend>'.$option['label'].'</legend>';
				foreach($option['options'] AS $kops => $vops ){
					$opt_selected = "";
					if(in_array($vops, $option['options_selected'])) $opt_selected = "checked";

					$html_element .= '<input type="radio" id="'.$name.'_'.$kops.'" value="'.$kops.'" name="'.$name.'" id="'.$id.'" '.$input_attributes.' '.$extra.' '.$opt_selected.'>
					  <label for="'.$name.'_'.$kops.'">'.$vops.'</label>'.$separator;
				}

			
				break;
			case "select":// [optgroup]
				$options = "";
				foreach($option['options'] AS $kops => $vops ){
					if(is_array($vops)){
						$options .= '<optgroup label="'.$kops.'">';
						foreach($vops AS $kopsg => $vopsg ){
							$opt_selected = "";
							if(in_array($vopsg, $option['options_selected'])) $opt_selected = "selected";
							$options .= '<option value="'.$kopsg.'" '.$opt_selected.'>'.$vopsg.'</option>';
						}
						$options .= '</optgroup>';

					}else{
						$opt_selected = "";
						if(in_array($vops, $option['options_selected'])) $opt_selected = "selected";
						$options .= '<option value="'.$kops.'" '.$opt_selected.'>'.$vops.'</option>';
					}
				}
				$html_element = $label.'<select name="'.$name.'" id="'.$id.'" '.$input_attributes.' '.$extra.'>
				  '.$options.'
				</select>';
				break;
			case "textarea":
				$html_element = $label.'<textarea name="'.$name.'" id="'.$id.'" '.$input_attributes.' '.$extra.'>'.$value.'</textarea>';
				break;
			case "button":
				$html_element = $label.'<button name="'.$name.'" id="'.$id.'" '.$input_attributes.' '.$extra.'>'.$value.'</button>';
				break;
			case "progress":
				$html_element = $label.'<progress value="'.$value.'" name="'.$name.'" id="'.$id.'" '.$input_attributes.' '.$extra.'>'.$value.'</progress>';
				break;
			case "meter":
				$html_element = $label.'<meter value="'.$value.'" name="'.$name.'" id="'.$id.'">';
				break;
			default:
				$html_element = "Invalid Type!";
				break;

		}

		return($html_element);
	
	}
		
}
/**ESEMPIO COMPLETO
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
*/
?>