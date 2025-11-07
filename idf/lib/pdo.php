<?php
/**
 * Classe pdo_dbcon
 *
 * Questa classe fornisce un'interfaccia semplificata per l'accesso a un database
 * tramite PDO, includendo metodi per eseguire query parametrizzate, inserimenti, aggiornamenti e cancellazioni.
 * Supporta anche il logging di errori tramite una funzione esterna `common::console`.
 *
 * @author  
 * @version 1.0
 
SNIPPET:
$conn_data = array();
$conn_data['driver'] = 'mysql'; // Driver
$conn_data['host'] = 'localhost'; // Indirizzo del server
$conn_data['username'] = 'root'; // Nome utente del database
$conn_data['password'] = '******'; // Password dell'utente del database
$conn_data['database'] = 'idf_dev'; // Nome del database
$nome_connessione = new pdo_dbcon($conn_data);
*/
class pdo_dbcon{
	/**
	 * @var PDO|null Connessione PDO attiva.
	 */
	private $id_conn;
	/**
	 * @var string Host del database.
	 */
	private $host;
	/**
	 * @var string Nome del database.
	 */
	private $database;
	/**
	 * @var string Username per l'accesso al database.
	 */
	private $username;
	/**
	 * @var string Password per l'accesso al database.
	 */
	private $password;
	/**
	 * Costruttore
	 *
	 * Inizializza la connessione PDO al database utilizzando i dati forniti.
	 * In caso di errore di connessione, stampa o logga il messaggio di errore.
	 *
	 * @param array $dbinfo Array associativo con chiavi: driver, host, database, username, password
	 */
	function __construct($dbinfo) {
		$this -> driver = $dbinfo['driver'];
		$this -> host = $dbinfo['host'];
		$this -> database = $dbinfo['database'];
		$this -> username = $dbinfo['username'];
		$this -> password = $dbinfo['password'];
		try {
			$this -> id_conn = new PDO($this -> driver.":host=".$this -> host.";dbname=".$this -> database, $this -> username, $this -> password);
			$this -> id_conn -> setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			// Imposta il fetch di default su FETCH_ASSOC
			$this -> id_conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			// Imposta anche la modalità di gestione errori

		} catch(PDOException $e) {
			$error = "Error connection DB : " . $e->getMessage();
			if (method_exists("common", "console")) common::console($error,"error");
			else print($error."<br>\n");
		}
	}
/**
* Distruttore
*
* Chiude automaticamente la connessione al database.
*/
	function __destruct(){
		$this -> id_conn = null;
	}
/** FUNCTION "get_conn_data"
* Restituisce i parametri di connessione correnti.
*
* @return array Array associativo con driver, host, database, username, password
*/
	function get_conn_data(){
		$conn_data = array();
		$conn_data['driver'] = $this -> driver;
		$conn_data['host'] = $this -> host;
		$conn_data['database'] = $this -> database;
		$conn_data['username'] = $this -> username;
		$conn_data['password'] = $this -> password;
		return $conn_data;
		
	}
/** FUNZIONE "query"
* Esegue una query SQL utilizzando PDO in modalità preparata con parametri associativi.
*
* La funzione tenta di preparare ed eseguire la query specificata. In caso di errore,
* stampa un messaggio contenente la query con i parametri sostituiti per facilitare il debug.
* Può utilizzare un metodo di logging "common::console()" se disponibile, altrimenti stampa l'errore a schermo.
*
* @param string $query   La query SQL da eseguire, con parametri nel formato :nome.
* @param array  $params  (Opzionale) Array associativo dei parametri da associare alla query.
*
* @return bool Ritorna true se la query viene eseguita con successo, false in caso di errore.


SNIPPET:
$params = array();
$params['nome'] = "%";
$sql = "SELECT * FROM `test` WHERE campo1 LIKE :nome";
$return_query = $dbcon['idf_db'] -> query($sql,$params);
*/
	function query($query, $params = array()) {
		$array_return = array();

		try {
			$stmt = $this -> id_conn -> prepare($query);
			$stmt -> execute($params);
			return true;
		} catch(PDOException $e) {
			$sql_width_params = $query;
			if(!empty($params)){
				foreach ($params AS $kdata => $vdata ) {
					$sql_width_params = str_replace(":".$kdata, "'".$vdata."'", $sql_width_params);
				}
			}
			$error = "Error query DB : " . $e->getMessage();
			if (method_exists("common", "console")) common::console($error."\n".$sql_width_params,"error");
			else print($error."<br><b>".$sql_width_params."</b>\n");
			return false;
		}
	}
/** FUNZIONE "select2array"
 * Esegue una SELECT e restituisce un array di risultati associativi.
 *
 * @param string $select  Query SELECT con eventuali parametri.
 * @param array  $params  (Opzionale) Parametri da associare.
 *
 * @return array|false Array dei risultati oppure false in caso di errore.

SNIPPET:
$params = array();
$params['nome'] = "%";
$sql = "SELECT * FROM `test` WHERE campo1 LIKE :nome";
$return_query = $dbcon['idf_db'] -> select2array($sql,$params);
 */
	function select2array($select, $params = array()) {
		$array_return = array();
		try {
			$stmt = $this -> id_conn -> prepare($select);
			$stmt -> execute($params);
			$array_return = $stmt->fetchAll();
		} catch(PDOException $e) {
			$sql_width_params = $select;
			if(!empty($params)){
				foreach ($params AS $kdata => $vdata ) {
					$sql_width_params = str_replace(":".$kdata, "'".$vdata."'", $sql_width_params);
				}
			}
			$error = "Error query DB : " . $e->getMessage();
			if (method_exists("common", "console")) common::console($error."\n".$sql_width_params,"error");
			else print($error."<br><b>".$sql_width_params."</b>\n");
			return false;
		}
		return $array_return;
	}
/** FUNZIONE "insert"
 * Esegue un'INSERT nel database.
 *
 * @param string $table   Nome della tabella.
 * @param array  $params  Array associativo colonna => valore.
 *
 * @return int|false ID dell'ultima riga inserita, oppure false in caso di errore.
 *
 * @example
 * $db->insert("utenti", array("nome" => "Mario", "cognome" => "Rossi"));

SNIPPET:
$table = 'test';
$params = array();
$params['test_id'] = "";
$params['campo1'] = "A".rand();
$params['campo2'] = "B".rand();

$return_query = $dbcon['idf_db'] -> insert($table,$params);
  */

	function insert($table, $params) {
		$field = "";
		$value_prepare = "";
		$value_fixed = "";
		$value = array();
		$i = 0;
		foreach ($params AS $kdata => $vdata ) {
			$separator = ($i == 0) ? "" : ", ";
			$field .= $separator."`".$kdata."`";
			$value_prepare .= $separator." :".$kdata;
			$value_fixed .= $separator." '".$vdata."'";
			$value[$kdata] = $vdata;			
			$i++;
		}
		$sql = "INSERT INTO ".$table." (".$field.") VALUES (".$value_prepare.")";
		try {
			$stmt = $this -> id_conn -> prepare($sql);
			$stmt -> execute($value);
			return $this -> id_conn -> lastInsertId();
		} catch(PDOException $e) {
			$sql_width_params = "INSERT INTO ".$table." (".$field.") VALUES (".$value_fixed.")";
			$error = "Error query DB : " . $e->getMessage();
			if (method_exists("common", "console")) common::console($error."\n".$sql_width_params,"error");
			else print($error."<br><b>".$sql_width_params."</b>\n");
			return false;
		}
	}
/** FUNZIONE "update"
 * Esegue un'UPDATE su una tabella con condizione.
 *
 * @param string $table            Nome della tabella.
 * @param array  $params           Array associativo colonna => nuovo valore.
 * @param string $condition        Clausola WHERE (es. "id = :id").
 * @param array  $condition_params Parametri associati alla condizione WHERE.
 *
 * @return int|false Numero di righe modificate, oppure false in caso di errore.
 *
 * @example
 * $db->update("utenti", array("email" => "test@example.com"), "id = :id", array("id" => 1));

SNIPPET:
$table = 'test';
$params = array();
$params['campo1'] = "E".rand();
$params['campo2'] = "F".rand();
$condition = "test_id = :test_id";
$condition_params = array();
$condition_params['test_id'] = "2";
//ATTENZIONE A NOMINARE I CONDITION PARAMS DIFFERNETI DAI PARAMS NORMALI
$return_query = $dbcon['idf_db'] -> update($table, $params, $condition, $condition_params);
  */

	function update($table, $params, $condition, $condition_params = array() ) {		
		$field_prepare = "";
		$field_fixed = "";
		$value = array();
		$i = 0;
		foreach ($params AS $kdata => $vdata ) {
			$separator = ($i == 0) ? "" : ", ";
			$field_prepare .= $separator."`".$kdata."` = :".$kdata;
			$field_fixed .= $separator."`".$kdata."` = '".$vdata."'";
			$value[$kdata] = $vdata;			
			$i++;
		}
		foreach ($condition_params AS $kdatac => $vdatac ) {
			$value[$kdatac] = $vdatac;			
			$i++;
		}

		$sql = "UPDATE ".$table." SET ".$field_prepare." WHERE ".$condition;
		try {
			$stmt = $this -> id_conn -> prepare($sql);
			$stmt -> execute($value);
			return $stmt -> rowCount();
		} catch(PDOException $e) {
			$sql_width_params = "UPDATE ".$table." SET ".$field_fixed." WHERE ".$condition;
			if(!empty($condition_params)){
				foreach ($condition_params AS $kdatac => $vdatac ) {
					$sql_width_params = str_replace(":".$kdatac, "'".$vdatac."'", $sql_width_params);
				}
			}
			$error = "Error query DB : " . $e->getMessage();
			if (method_exists("common", "console")) common::console($error."\n".$sql_width_params,"error");
			else print($error."<br><b>".$sql_width_params."</b>\n");
			return false;

		}
	}
/** FUNZIONE "delete"
* Esegue una DELETE su una tabella con condizione.
*
* @param string $table            Nome della tabella.
* @param string $condition        Clausola WHERE (es. "id = :id").
* @param array  $condition_params Parametri per la clausola WHERE.
*
* @return bool True se la query ha avuto successo, false in caso di errore.
*
* @example
* $db->delete("utenti", "id = :id", array("id" => 1));

SNIPPET:
$table = 'test';
$condition = "test_id = :test_id";
$condition_params = array();
$condition_params['test_id'] = "2";
//ATTENZIONE A NOMINARE I CONDITION PARAMS DIFFERNETI DAI PARAMS NORMALI
$return_query = $dbcon['idf_db'] -> delete($table, $condition, $condition_params);
	  */

	function delete($table, $condition, $condition_params = array() ) {
		$i = 0;
		foreach ($condition_params AS $kdatac => $vdatac ) {
			$value[$kdatac] = $vdatac;			
			$i++;
		}
	
		$sql = "DELETE FROM ".$table." WHERE ".$condition;

		try {
			$stmt = $this -> id_conn -> prepare($sql);
			$stmt -> execute($value);
			return true;//$stmt -> rowCount();
		} catch(PDOException $e) {
			$sql_width_params = $sql;
			if(!empty($condition_params)){
				foreach ($condition_params AS $kdatac => $vdatac ) {
					$sql_width_params = str_replace(":".$kdatac, "'".$vdatac."'", $sql_width_params);
				}
			}
			$error = "Error query DB : " . $e->getMessage();
			if (method_exists("common", "console")) common::console($error."\n".$sql_width_params,"error");
			else print($error."<br><b>".$sql_width_params."</b>\n");
			return false;
	
		}
	}

}

?>