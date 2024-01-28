<?php
	defined('_JEXEC') or die('Access deny');

	class plgContentLastDocsUpdates extends JPlugin 
	{
		function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}

		function onContentPrepare($content, $article, $params, $limit){				
			$document = JFactory::getDocument();
			$document->addStyleSheet('plugins/content/lastdocsupdates/style.css');	
			$T=array();
		
			$root = $this->params->get('dossier', '');

			$iter = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
				RecursiveIteratorIterator::SELF_FIRST,
				RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
			);

			$paths = array($root);
			foreach ($iter as $path => $dir) {
				$paths[] = $path;
				
			}

			$c = array();
			$i=0;
			foreach ($paths as $uneLigne)
			{
				
				$c[$i][0] = filemtime($uneLigne);
				$c[$i][1] = $uneLigne;
				
				$i++;
			}
			
			array_multisort(array_column($c, 0), SORT_ASC, $c);
		

			$ch='<div class="derniers-fichiers-modifies">';
			//j'affiche les éléments
			foreach ($c as $unfichier){
				$ch .= '<div class="un-fichier"><div class="un-fichier-nom">'.$unfichier[1].'</div><div class="un-fichier-date">'.date('d-M-Y',$unfichier[0]).'</div></div>';
			}
			$ch .= '</div>';
			
			$article->text  = str_replace('{LastDocUpdates}',$ch, $article->text);
			
	}
	}