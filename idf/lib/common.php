<?php
/**
 * Classe utility `common`
 *
 * Contiene metodi statici di supporto per debugging (via console browser)
 * e generazione dinamica di menu HTML a partire da strutture array o oggetti JSON.
 *
 */
 
class common {
	
	/**FUNCTION: console
	 * Stampa un messaggio nella console del browser tramite JavaScript.
	 *
	 * @param mixed  $msg  Messaggio da mostrare (stringa o array).
	 * @param string $mode Tipo di log (debug, info, log, warn, error). Default: info.
	 *
	 * @example
	 * common::console("Errore DB", "error");
	 //funzione usata in abbianta con idfErrorHandler che mostra gli errori php nella console
	 */
	public static function console($msg,$mode="info") {
		//$mode debug-info-log-error-warn
		$jsonMsg = json_encode($msg); // Codifica il messaggio in JSON per gestire correttamente eventuali caratteri speciali
		print("<script>console.$mode($jsonMsg);</script>");

	}
	/**FUNCTION: console_tpl
	 * Inietta un messaggio console come codice JavaScript in fondo alla pagina,
	 * destinato all'array globale `$GLOBALS['css_js_inclusion']['js_code_foot']`.
	 *
	 * @param mixed  $msg  Messaggio da loggare (stringa o array).
	 * @param object $tpl  Oggetto tpl (non utilizzato direttamente nel codice attuale).
	 * @param string $mode Tipo di log (debug, info, log, warn, error). Default: info.
	* common::console_tpl("I.D.F. Console - DEBUG ACTIVE IP ".$_SERVER["REMOTE_ADDR"],$tpl,"debug");
	 */
	public static function console_tpl($msg,$tpl,$mode="info") {
		//$mode debug-info-log-error-warn
		$jsonMsg = json_encode($msg); // Codifica il messaggio in JSON per gestire correttamente eventuali caratteri speciali
		$GLOBALS['css_js_inclusion']['js_code_foot'][] = "console.$mode($jsonMsg);";
//		$tpl -> set_var_to_value("CONSOLE_SCRIPT", "<script>console.$mode($jsonMsg);</script>",true); //File principale per template, sarà quello printato alla fine
	}
	
	/*public static function side_menu_html($array_menu,$array_menu_info,&$level=0,$back_level=""){
		global $color;
		global $text;
		$side_menu_html = '';
		$active = "";
		$init_level = $level;
		if($init_level == 0) $active = "active";
		$side_menu_html .= '<div class="menu-level '.$active.'" id="menu-level-'.$level.'">
		<ul>';
		$num = 1;
		$sub_menu = "";
		$txt_back = $text['sideMenu']['back'];
		$txt_back_symbol = $text['sideMenu']['back_symbol'];
		$color_submenu = $color['sideMenu']['submenu'];
		$color_voice =  $color['sideMenu']['voice'];
		$color_back =  $color['sideMenu']['back'];
		
		foreach($array_menu AS $km => $vm ){
			if(is_array($vm)){
				$level++;
				$backgroundcolor = $color_submenu;
				if(!empty($array_menu_info[$km]['color'])) $backgroundcolor = $array_menu_info[$km]['color'];
				$symbol = substr($km,0,1);
				if(!empty($array_menu_info[$km]['symbol'])) $symbol = $array_menu_info[$km]['symbol'];
				$side_menu_html .= '<li class="menu-item" onclick="showSubmenu('.$level.')">
					<span id="toggle-icon" class="circle" title="'.$km.'" style="background-color:'.$backgroundcolor.'; ">'.$symbol.'</span>
					<span ><nobr>&nbsp;&nbsp;&nbsp;&nbsp;'.$km.'</nobr></span>
				</li>';
				$sub_menu .= common::side_menu_html($vm,$array_menu_info[$km],$level,$init_level);
			}else{
				$backgroundcolor = $color_voice;
				if(!empty($array_menu_info[$km]['color'])) $backgroundcolor = $array_menu_info[$km]['color'];
				$symbol = substr($km,0,1);
				if(!empty($array_menu_info[$km]['symbol'])) $symbol = $array_menu_info[$km]['symbol'];
				$side_menu_html .= '<li class="menu-item" onclick="location.href=\''.$vm.'\'">
					<span id="toggle-icon" class="circle" title="'.$km.'" style="background-color:'.$backgroundcolor.'; ">'.$symbol.'</span>
					<span ><nobr>&nbsp;&nbsp;&nbsp;&nbsp;'.$km.'</nobr></span>
				</li>';
			//class="hidden-text"
			}
	
			$num++;
		}
		if($init_level > 0 ){
			$backgroundcolor = $color_back;
			$side_menu_html .= '<li class="menu-item" onclick="showSubmenu('.$back_level.')"> 
			<span id="toggle-icon" class="circle" title="'.$txt_back.'" style="background-color:'.$backgroundcolor.'; ">'.$txt_back_symbol.'</span>
			<span ><nobr>&nbsp;&nbsp;&nbsp;&nbsp;'.$txt_back.'</nobr></span>
			</li>';
		}

		$side_menu_html .= '</ul>
		</div>';
		$side_menu_html .= $sub_menu;
	
		return $side_menu_html;
		
	}*/
	
	/**FUNCTION: side_menu_json_html
	 * Genera un menu HTML multilivello da un oggetto JSON (convertito in PHP).
	 *
	 * Ogni voce può contenere attributi:
	 * - `name`: etichetta testuale
	 * - `link`: URL da aprire (se voce finale)
	 * - `symbol`: lettera/simbolo iniziale (facoltativo)
	 * - `color`: colore personalizzato
	 * - `submenu`: array di sottovoci
	 *
	 * @param object $array_menu  Oggetto JSON decodificato rappresentante il menu.
	 * @param int    $level       (Riferimento) Livello corrente del menu (default: 0).
	 * @param int    $back_level  (Opzionale) Livello da tornare con il pulsante "indietro".
	 *
	 * @return string HTML del menu multilivello.
	 *
	 * @example
	 * $menu = json_decode(file_get_contents("menu.json"));
	 * echo common::side_menu_json_html($menu);
	 */
	public static function side_menu_json_html($array_menu,&$level=0,$back_level=""){
		global $color;
		global $text;
		$side_menu_html = '';
		$active = "";
		$init_level = $level;
		if($init_level == 0) $active = "active";
		$side_menu_html .= '<div class="menu-level '.$active.'" id="menu-level-'.$level.'">
		<ul>';
		$num = 1;
		$sub_menu = "";
		$txt_back = $text['sideMenu']['back'];
		$txt_back_symbol = $text['sideMenu']['back_symbol'];
		$color_submenu = $color['sideMenu']['submenu'];
		$color_voice =  $color['sideMenu']['voice'];
		$color_back =  $color['sideMenu']['back'];
		
		foreach($array_menu AS $km => $vm ){
			if(isset($vm->submenu)){
				$level++;
				$backgroundcolor = $color_submenu;
				if(!empty($vm->color)) $backgroundcolor = $vm->color;
				$symbol = substr($km,0,1);
				if(!empty($vm->symbol)) $symbol = $vm->symbol;
				$side_menu_html .= '<li class="menu-item" onclick="showSubmenu('.$level.')">
					<span id="toggle-icon" class="circle" title="'.$vm->name.'" style="background-color:'.$backgroundcolor.'; ">'.$symbol.'</span>
					<span ><nobr>&nbsp;&nbsp;&nbsp;&nbsp;'.$vm->name.'</nobr></span>
				</li>';
				$sub_menu .= common::side_menu_json_html($vm->submenu,$level,$init_level);
			}else{
				$backgroundcolor = $color_voice;
				if(!empty($vm->color)) $backgroundcolor = $vm->color;
				$symbol = substr($km,0,1);
				if(!empty($vm->symbol)) $symbol = $vm->symbol;
				$side_menu_html .= '<li class="menu-item" onclick="location.href=\''.$vm->link.'\'">
					<span id="toggle-icon" class="circle" title="'.$vm->name.'" style="background-color:'.$backgroundcolor.'; ">'.$symbol.'</span>
					<span ><nobr>&nbsp;&nbsp;&nbsp;&nbsp;'.$vm->name.'</nobr></span>
				</li>';
			//class="hidden-text"
			}
	
			$num++;
		}
		if($init_level > 0 ){
			$backgroundcolor = $color_back;
			$side_menu_html .= '<li class="menu-item" onclick="showSubmenu('.$back_level.')"> 
			<span id="toggle-icon" class="circle" title="'.$txt_back.'" style="background-color:'.$backgroundcolor.'; ">'.$txt_back_symbol.'</span>
			<span ><nobr>&nbsp;&nbsp;&nbsp;&nbsp;'.$txt_back.'</nobr></span>
			</li>';
		}
	
		$side_menu_html .= '</ul>
		</div>';
		$side_menu_html .= $sub_menu;
	
		return $side_menu_html;
		
	}
		
}
?>