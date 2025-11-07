<?php
/**

 
 
# 📦 tpl – PHP Template Engine Leggero
 
 Un sistema di template minimale e flessibile per PHP, pensato per progetti legacy e moderni, che consente la sostituzione di segnaposto con valori statici o blocchi di template.
 
 ---
 
 ## ✅ Funzionalità principali
 
 - ✔️ Delimitatori personalizzabili (default: `[#VAR#]`)
 - ✔️ Supporto a variabili statiche (`set_var_to_value`)
 - ✔️ Supporto a template modulari (`set_var_to_template`)
 - ✔️ Sostituzione ricorsiva automatica
 - ✔️ Caricamento di file locali o remoti
 - ✔️ Parsing completo e output pronto all’uso
 
 ---
 
 ## 🚀 Come si usa
 
 ### 1. Inizializzazione
 
 ```php
 require_once 'tpl.php';
 
 $tpl = new tpl(); // Usa [# e #] come delimitatori 
 
 Puoi anche personalizzare i delimitatori: $tpl = new tpl('{{', '}}');
 
 2. Caricamento template
 $tpl->load_file('main', 'template/main.html');
 $tpl->load_file('header', 'template/partials/header.html');
 
 3. Assegnazione variabili
$tpl->set_var_to_value('TITLE', 'Benvenuto');
$tpl->set_var_to_template('HEADER', 'header');

4. Output
$tpl -> g_output("main");
 
 */

class tpl {
	var	$start_tpl_var;//def [#
	var	$end_tpl_var;//def #]
	var	$exp_ereg;
	var	$file_template;
	var	$template;
	var	$template_mum;
	var	$var_to_value;
	var	$var_to_template;
	var	$template_parsed;
	//var	$seq_template;
	//var $testi;

	/**FUNCTION: __construct
	 * Inizializza la classe tpl con delimitatori personalizzati.
	 *
	 * @param string $start_tpl_var Delimitatore iniziale del segnaposto (default: "[#").
	 * @param string $end_tpl_var   Delimitatore finale del segnaposto (default: "#]").
	 */
	function __construct($start_tpl_var = "[#", $end_tpl_var = "#]") {//,$start_tpl_seq = "[%", $end_tpl_seq = "%]"
	    $this -> start_tpl_var = &$start_tpl_var;
	    $this -> end_tpl_var = &$end_tpl_var;
		$this -> exp_ereg = $this -> create_exp_ereg($start_tpl_var, $end_tpl_var);
		//$this -> testi = $n2_testi;
		$this -> template_parsed = 0;
	}

	/**FUNCTION: create_exp_ereg
	 * Crea un'espressione regolare per trovare tutti i segnaposto. 
	 * Richiamata dal costruttore
	 *
	 * @param string $start_reg Delimitatore iniziale.
	 * @param string $end_reg   Delimitatore finale.
	 * @return string Espressione regolare risultante.
	 */

	function create_exp_ereg($start_reg, $end_reg) {
		$reg = "[A-Z_a-z0-9]+";
		$start_reg = str_replace(".","\.",$start_reg);
		$start_reg = str_replace("*","\*",$start_reg);
		$start_reg = str_replace("?","\?",$start_reg);
		$start_reg = str_replace("+","\+",$start_reg);
		$start_reg = str_replace("[","\[",$start_reg);
		$start_reg = str_replace("]","\]",$start_reg);
		$start_reg = str_replace("(","\(",$start_reg);
		$start_reg = str_replace(")","\)",$start_reg);
		$start_reg = str_replace("{","\{",$start_reg);
		$start_reg = str_replace("}","\}",$start_reg);
		$start_reg = str_replace("^","\^",$start_reg);
		$start_reg = str_replace("$","\$",$start_reg);
		$start_reg = str_replace("|","\|",$start_reg);

		$end_reg = str_replace(".","\.",$end_reg);
		$end_reg = str_replace("*","\*",$end_reg);
		$end_reg = str_replace("?","\?",$end_reg);
		$end_reg = str_replace("+","\+",$end_reg);
		$end_reg = str_replace("[","\[",$end_reg);
		$end_reg = str_replace("]","\]",$end_reg);
		$end_reg = str_replace("(","\(",$end_reg);
		$end_reg = str_replace(")","\)",$end_reg);
		$end_reg = str_replace("{","\{",$end_reg);
		$end_reg = str_replace("}","\}",$end_reg);
		$end_reg = str_replace("^","\^",$end_reg);
		$end_reg = str_replace("$","\$",$end_reg);
		$end_reg = str_replace("|","\|",$end_reg);

		$exp_ereg = "/".$start_reg.$reg.$end_reg."/"; 
		return($exp_ereg);
	}

	/*function set_seq_to_array($template, $sequence, $array_seq){
		//$this->seq_template[$template]['sequence'] = $sequence;
		$this->seq_template[$template][$sequence] = $array_seq;
	}

	function replace_sequence_to_array($text_to_replace, $sequence, $array_seq, $var_start = "[#", $var_end = "#]"){
		preg_match_all('/(\[%S\_'.$sequence.'\_S%\])([.|\e|\s|\n|\r|\S|\t]+)(\[%E\_'.$sequence.'\_E%\])/', $text_to_replace, $result);
		$stringa_completa = @$result[0][0];
		$inizio = @$result[1][0];
		$contenuto = @$result[2][0];
		$fine = @$result[3][0];
		$nuovo_contenuto = "";

		if(!empty($array_seq)) foreach ( $array_seq AS $k_v=>$v_v ) {
			$valori = $array_seq[$k_v];
			$i = 0;
			foreach ( $valori AS $k=>$v ) {
				$contenutox = $contenuto;
				foreach ( $v AS $k1=>$v1 ) {
					if(is_array($v1)){
						$vx[$k1] = $v1;
						$contenutox = $this->replace_sequence_to_array($contenutox, $k1, $vx);
					}elseif($v1 == '__EMPTY__'){
						$contenutox = $this->replace_sequence_to_array($contenutox, $k1, "");
					}else{						
						$contenutox = str_replace($var_start.$k1.$var_end , $v1 , $contenutox);
					}
				}
				$nuovo_contenuto .= $contenutox;
				$i++;
			}
		}
		$nuovo_contenuto = str_replace($stringa_completa,$nuovo_contenuto,$text_to_replace);
		return ($nuovo_contenuto);
	}*/

/**FUNCTION: load_file
	 * Associa un file locale a un nome di template.
	 *
	 * @param string $nome           Nome del template da usare internamente.
	 * @param string $percorso_file  Percorso assoluto o relativo al file.
	 */
	function load_file ($nome, $percorso_file){
		if (file_exists($percorso_file)) { 
	   		$this->file_template[$nome] = $percorso_file;
		} else { 
			 print "Errore: il file <b>".$percorso_file."</b> non esiste"; //Spostare in testi
			 exit();
		}
	}
/**FUNCTION: load_external_file
	 * Associa un file remoto a un nome di template (verifica esistenza tramite file_get_contents).
	 *
	 * @param string $nome           Nome del template da usare internamente.
	 * @param string $percorso_file  Percorso al file remoto.
	 */
	function load_external_file ($nome, $percorso_file){
		if (@file_get_contents($percorso_file)) { 
	   		$this->file_template[$nome] = $percorso_file;
		} else { 
			 print "Errore: il file <b>".$percorso_file."</b> non è raggiungibile"; //Spostare in testi
			 exit();
		}
	}

	/**FUNCTION: set_var_to_value
	 * Associa una variabile statica a un valore.
	 *
	 * @param string  $name   Nome del segnaposto.
	 * @param string  $value  Valore da inserire.
	 * @param boolean $append Se true, concatena il nuovo valore a quello esistente.
	 */
	function set_var_to_value($name,$value,$append = false) {
		if($append){
			$this -> var_to_value[$name] .= $value;
		}else{
			$this -> var_to_value[$name] = $value;
		}
	}	

	/**FUNCTION: set_var_to_template
	 * Associa una variabile a un template (inclusione di file).
	 *
	 * @param string $name   Nome del segnaposto.
	 * @param string $value  Nome del template (precedentemente caricato).
	 */
	function set_var_to_template($name,$value) {
		$this -> var_to_template[$name] = $value;

	}	
	
	/**FUNCTION: replace_variables_to_template
	 * Sostituisce tutti i segnaposto con il contenuto dei template associati.
	 *
	 * @param string $str Contenuto da elaborare.
	 * @return string Contenuto elaborato con inclusioni effettuate.
	 */
	function replace_variables_to_template($str) {
		$var_to_template = $this->var_to_template;
		if($var_to_template){
			$j=0;
			foreach ( $var_to_template AS $key=>$name_template ) {
				$file_name = $this->file_template[$name_template];
				$value = file_get_contents($file_name);
				if(strpos($str, $this->start_tpl_var.$key.$this->end_tpl_var)){
					$this -> template_parsed++;
					$j++;
				}
				$str = str_replace($this->start_tpl_var.$key.$this->end_tpl_var,$value,$str);
	
				//INIZIO CICLO PER SOSTITUZIONI SEQUENZA
				if(!empty($this->seq_template[$name_template])){
					foreach ( $this->seq_template[$name_template] AS $kseq=>$vseq ) {
						$str = $this->replace_sequence_to_array($str, $kseq, $vseq);				
						
					}
				}
				//FINE CICLO PER SOSTITUZIONI SEQUENZA

			}

			if($this -> template_parsed < count($this -> var_to_template) && $j != 0){
				$str = $this->replace_variables_to_template($str);
			}
		}
		return $str;
	}	

	/**FUNCTION: replace_variables_to_value
	 * Sostituisce tutti i segnaposto con i valori statici.
	 *
	 * @param string $str Contenuto da elaborare.
	 * @return string Contenuto con valori statici sostituiti.
	 */
	function replace_variables_to_value($str) {
		$var_to_value = $this->var_to_value;
		if($var_to_value){
			foreach ( $var_to_value AS $key=>$value ) {
				$str = str_replace($this->start_tpl_var.$key.$this->end_tpl_var,$value,$str);
			}
		}
		return $str;
	}	
		
	/**FUNCTION: g_output
	 * Esegue il rendering e la stampa del template principale.
	 *
	 * @param string $page_one (Opzionale) Nome del template da usare come principale.
	 */
	function g_output($page_one = "") {
		if($page_one == ""){
			$page_one = $this -> template_mum;
		}
		$filename = $this->file_template[$page_one];
		$str_to_print = file_get_contents($filename);
		$str_to_print = $this -> replace_variables_to_template($str_to_print);
		$str_to_print = $this -> replace_variables_to_value($str_to_print);
		$str_to_print = preg_replace($this -> exp_ereg, "",$str_to_print);
		$str_to_print = $str_to_print;
		print $str_to_print;
	}
	
	
	
	//! Gestione Testi
/*	
	function get_testo($chiave_testo,$modulo_testo='gm',$trasforma_testo='') {
		$stringa_testo = '';
		$testi = $this -> testi;
		if ( isset($testi[$modulo_testo][$chiave_testo]) ) {
			$stringa_testo = $testi[$modulo_testo][$chiave_testo];
			if ( !empty($trasforma_testo) ) {
				if ( in_array($trasforma_testo, array('upper','upper-trim')) ) {
					$stringa_testo = strtoupper($stringa_testo);
				}
				if ( in_array($trasforma_testo, array('lower','lower-trim')) ) {
					$stringa_testo = strtolower($stringa_testo);
				}
				if ( in_array($trasforma_testo, array('trim','upper-trim','lower-trim')) ) {
					$stringa_testo = trim($stringa_testo);
				}
			}
		}
		return $stringa_testo;
	}
	
	function get_testo_array($chiave_testo,$modulo_testo='gm',$trasforma_testo='') {
		$array_testi = '';
		$testi = $this -> testi;
		if ( isset($testi[$modulo_testo][$chiave_testo]) && is_array($testi[$modulo_testo][$chiave_testo]) ) {
			$tmp_array_testi = $testi[$modulo_testo][$chiave_testo];
			foreach ( $tmp_array_testi AS $k_tmp_at=>$v_tmp_at ) {
				$stringa_testo = $v_tmp_at;
				if ( !empty($trasforma_testo) ) {
					if ( in_array($trasforma_testo, array('upper','upper-trim')) ) {
						$stringa_testo = strtoupper($stringa_testo);
					}
					if ( in_array($trasforma_testo, array('lower','lower-trim')) ) {
						$stringa_testo = strtolower($stringa_testo);
					}
					if ( in_array($trasforma_testo, array('trim','upper-trim','lower-trim')) ) {
						$stringa_testo = trim($stringa_testo);
					}
				}
				$array_testi[$k_tmp_at] = $stringa_testo;
			}
		}
		return $array_testi;
	}

	function set_var_to_testo($name,$chiave_testo,$modulo_testo='gm',$trasforma_testo='',$accoda = "no") {
		
		$value = $this->get_testo($chiave_testo,$modulo_testo,$trasforma_testo);
		
		if($accoda == "acc"){
			$this -> var_to_value[$name] .= $value;
		}else{
			$this -> var_to_value[$name] = $value;
		}
	}
	
	function reload_testi($n2_testi) {
		$this -> testi = $n2_testi;
	}*/
		
}
?>