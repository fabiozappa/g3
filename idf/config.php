<?php
/**
 * File di configurazione generale.
 * Contiene principalmente i setting PHP e alcune informaizoni di base come il tema grafico, gli IP abilitati a vedere il debug
 */

$memory_limit = '1024M';
$ip_allow_idf_debug = array('178.19.149.219','77.32.106.79');

//DATABASE PRINCIPALE
$DB = array();
$DB['idf_db']['driver'] = 'mysql'; //Driver
$DB['idf_db']['host'] = 'localhost'; // Indirizzo del server
$DB['idf_db']['username'] = 'root'; // Nome utente del database
$DB['idf_db']['password'] = 'talp016sqlx!'; // Password dell'utente del database
$DB['idf_db']['database'] = 'idf_dev'; // Nome del database



$lang = "it";
$title = "Indipendent Data Framework";
$theme = "base";
$module_default = "start";//qui prendere in base a login/session/link - magari preimpostare un modulo di default per tutti (user??)

$super_admin_active = true;//mettere a false per disattivare, se è attivo permette di loggarsi nel sistema per gestire gli utenti
$super_admin_user = "";
$super_admin_pass = "";

?>