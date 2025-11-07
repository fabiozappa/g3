		document.addEventListener("DOMContentLoaded", () => {
	const sidebar = document.getElementById('sidebar');
	const isSidebarClosed = localStorage.getItem('sidebarClosed') === 'true';

	// Controlla lo stato memorizzato nel localStorage
	// Ripristina lo stato del menu laterale

	if (isSidebarClosed) {
		sidebar.classList.add('closed');
		content.classList.add('closed');
		document.getElementById("toggle-icon").innerHTML = "&#9654;";
	}else{
		sidebar.classList.remove('closed');
		content.classList.remove('closed');
	}


	// Funzione per gestire il toggle
	// Funzione per aprire/chiudere il menu laterale
	window.toggleSidebar = function() {
		sidebar.classList.toggle('closed');
		content.classList.toggle('closed');
		const isNowClosed = sidebar.classList.contains('closed');
		localStorage.setItem('sidebarClosed', isNowClosed);// Memorizza lo stato attuale
		document.getElementById("toggle-icon").innerHTML = isNowClosed ? "&#9654;" : "&#9776;";/*Alterna le due icone del menu*/
	};

	// Funzione per mostrare un sotto-menu
	window.showSubmenu = function (level) {
		// Nascondi tutti i menu
		document.querySelectorAll(".menu-level").forEach(menu => {
			menu.classList.remove("active");
		});
		// Mostra il menu selezionato
		document.getElementById(`menu-level-${level}`).classList.add("active");
	
		// Memorizza il livello attivo
		localStorage.setItem("activeMenuLevel", level.toString());
	};
});