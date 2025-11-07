<?php
/** FILE DI CREAZIONE DI QUESTA DOCUMENTAZIONE*/

function scanAllFiles($dir) {
	$files = array();
	$dirs = array();
	$items = scandir($dir);

	foreach ($items as $item) {
		if ($item === '.' || $item === '..') continue;
		$path = $dir . '/' . $item;
		if (is_file($path)) {
			$files[$item] = extractDocs($path);
		} elseif (is_dir($path)) {
			$dirs[$item] = scanAllFiles($path);
			$dirs[$item]['__dir_doc'] = extractDirectoryDoc($path);
		}
	}

	ksort($files);
	ksort($dirs);

	return $files + $dirs;
}

function extractDocs($filepath) {
	$content = file_get_contents($filepath);
	$docs = array(
		'file_doc' => '',
		'all_blocks' => array()
	);

	// Primo blocco dopo apertura
	if (preg_match('/<\?(php)?\s*\/\*\*(.*?)\*\//s', $content, $match)) {
		$docs['file_doc'] = trim($match[2]);
	} elseif (preg_match('/\/\*\*(.*?)\*\//s', $content, $match)) {
		$docs['file_doc'] = trim($match[1]);
	}

	// Tutti i blocchi
	if (preg_match_all('/\/\*\*(.*?)\*\//s', $content, $matches)) {
		foreach ($matches[1] as $block) {
			$docs['all_blocks'][] = trim($block);
		}
	}

	return $docs;
}

function extractDirectoryDoc($dir) {
	//$infoFiles = array('__readme.txt', '__info.txt', 'info.md');
	$infoFiles = array( '__info_dir.txt');

	foreach ($infoFiles as $file) {
		$path = $dir . '/' . $file;
		if (file_exists($path)) {
			$content = file_get_contents($path);
			if (preg_match('/\/\*\*(.*?)\*\//s', $content, $match)) {
				return trim($match[1]);
			}
		}
	}
	return '';
}

$data = scanAllFiles(dirname(__FILE__));

function safe_json_encode($value) {
	if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
		return json_encode($value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
	} else {
		return json_encode($value);
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Documentazione File</title>
	<style>
		body { margin: 0; font-family: sans-serif; }
		.container { display: flex; height: 100vh; }
		.sidebar {
			width: 300px;
			overflow-y: auto;
			border-right: 1px solid #ccc;
			padding: 10px;
			background: #f4f4f4;
		}
		.content {
			flex-grow: 1;
			padding: 20px;
			overflow-y: auto;
		}
		.file, .folder { margin-left: 20px; cursor: pointer; color: blue; }
		.file:hover, .folder:hover { text-decoration: underline; }
		.doc-block {
			background: #f9f9f9;
			border-left: 4px solid #007BFF;
			padding: 10px;
			margin: 10px 0;
			white-space: pre-wrap;
		}
	</style>
</head>
<body>
<div class="container">
	<div class="sidebar">
		<h3>📁 GM3 - Indipendent Data Framework</h3>
		<?php
		function printTree($tree, $path = '') {
			foreach ($tree as $name => $value) {
				$fullPath = trim($path . '/' . $name, '/');
				if (is_array($value) && isset($value['all_blocks'])) {
					if($name != "__info_dir.txt") echo '<div class="file" onclick="showDocs(\'' . addslashes($fullPath) . '\')">📄 ' . htmlspecialchars($name) . '</div>';
				} elseif (is_array($value)) {
					echo '<div class="folder" onclick="showDocs(\'' . addslashes($fullPath) . '\')">📂 ' . htmlspecialchars($name) . '</div>';
					echo '<div style="margin-left:15px;">';
					printTree($value, $fullPath);
					echo '</div>';
				}
			}
		}
		printTree($data);
		?>
	</div>
	<div class="content">
		<h2>📌 Documentazione</h2>
		<div id="docContent">Seleziona un file o una cartella per vedere i commenti.</div>
	</div>
</div>

<script>
var fileData = <?php echo safe_json_encode($data); ?>;

function getItemByPath(path) {
	var parts = path.split('/');
	var current = fileData;
	for (var i = 0; i < parts.length; i++) {
		current = current[parts[i]];
	}
	return current;
}

function showDocs(path) {
	var item = getItemByPath(path);
	var container = document.getElementById('docContent');
	container.innerHTML = '<h3>' + path + '</h3>';

	if (item.all_blocks) {
		// È un file
		if (item.all_blocks.length > 0) {
			for (var i = 0; i < item.all_blocks.length; i++) {
				var block = document.createElement('div');
				block.className = 'doc-block';
				block.textContent = item.all_blocks[i];
				container.appendChild(block);
			}
		} else {
			container.innerHTML += '<p>Nessun commento trovato.</p>';
		}
	} else if (item.__dir_doc) {
		// È una cartella con documentazione
		var block = document.createElement('div');
		block.className = 'doc-block';
		block.textContent = item.__dir_doc;
		container.appendChild(block);
	} else {
		container.innerHTML += '<p>Nessuna descrizione disponibile per questa cartella.</p>';
	}
}
</script>
</body>
</html>