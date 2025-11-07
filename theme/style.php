<?php
header("Content-type: text/css");
$theme = "base";
if(!empty($_GET['theme'])) $theme = $_GET['theme'];
include("./".$theme."/themeconfig.php");
?>

body {
	margin: 0;
	font-family: Arial, sans-serif;
	display: flex;
	height: 100vh;
}

/*SIDEBAR START*/
#sidebar {
	width: 250px;
	background-color: <?php echo $color['css']['sideMenu_bgcolor']; ?>;
	color: <?php echo $color['css']['sideMenu_txtcolor']; ?>;;
	overflow: hidden;
	transition: width 0.3s;
}

#sidebar.closed {
	width: 60px;
}
#content.closed  {
	max-width: calc(100vw - 50px);
}
#sidebar ul {
	list-style: none;
	padding: 0;
	margin: 0;
}



#sidebar .toggle-btn {
	text-align: center;
	cursor: pointer;
	background-color: #34495e;
	padding: 10px 0;
}

#main-content {
	flex-grow: 1;
	background-color: #ecf0f1;
	padding: 0px;
	overflow: auto;
}

.hidden-text {
	display: inline-block;
	overflow: hidden;
	white-space: nowrap;
	width: 0;
	transition: width 0.3s;
}

#sidebar.closed .hidden-text {
	width: 0;
}

#sidebar:not(.closed) .hidden-text {
	width: auto;
}
	/*MENU START*/
	.menu-item {
		display: flex;
		align-items: baseline;
	}
	
	.menu-level {
		display: none; /* Nascondi i menu non attivi */
		padding: 0;
		margin: 0;
		list-style: none;
	}
	
	.menu-level.active {
		display: block; /* Mostra solo il menu attivo */
	}
	
	.menu-level ul li {
		padding: 15px 15px;
		cursor: pointer;
	}
	
	.menu-level ul li:hover {
		background-color: #34495e;
	}
	

	.circle {
		display: inline-block; /* Blocca l'elemento ma lo mantiene in linea */
		min-width: 25px; /* Larghezza fissa */
		height: 25px; /* Altezza fissa */
		line-height: 25px; /* Centra il testo verticalmente */
		border-radius: 50%; /* Rende l'elemento circolare */
		color: #fff; /* Colore del testo */
		font-size: 12px; /* Dimensione del testo */
		font-weight: bold; /* Testo in grassetto */
		text-align: center; /* Centra il testo orizzontalmente */
		cursor: pointer; /* Mostra la mano al passaggio del mouse */
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Ombreggiatura */
		transition: background-color 0.3s ease; /* Transizione fluida per il cambio colore */
	}
	

	/*MENU END*/
/*SIDEBAR END*/

#main-content {
	background-color: #fff;
}		
#content {
	margin-top: 10px; 
	padding-left: 10px;
	padding-right: 10px;
	max-width: calc(100vw - 250px);
}


/*TOP BAR START*/
#top-bar {
	position: sticky; /* Puoi usare fixed se vuoi che sia sempre visibile anche durante lo scorrimento */
	top: 0;
	left: 0;
	right: 0;
	background-color: #ffffff;
	padding: 9px 20px;
	border-bottom: 1px solid #ccc;
	z-index: 1000; /* Assicura che sia sopra altri elementi */    
	display: flex;\n    
	align-items: center;\n    j
	ustify-content: space-between;
}

#top-bar span {\n    font-size: 18px;\n    color: #2c3e50;\n}

#top-bar input {\n    padding: 5px 10px;\n    border: 1px solid #ccc;\n    border-radius: 4px;\n    font-size: 14px;\n}

#top-bar button {\n    background-color: #2c3e50;\n    color: #fff;\n    border: none;\n    padding: 7px 15px;\n    border-radius: 4px;\n    cursor: pointer;\n    font-size: 14px;\n}

#top-bar button:hover {\n    background-color: #34495e;\n}
/*TOP BAR END*/