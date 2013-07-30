<?php
	header ('Content-type: text/html; charset=UTF-8');

	// http://simplehtmldom.sourceforge.net/
	include('simple_html_dom.php');

	//$html = file_get_html('http://www.marioricci.com.br/index.php?option=com_content&view=article&id=347:6d-x-fight-mma-part-5&catid=39:outros&Itemid=98');
	$html = file_get_html( base64_decode( $_POST['url-fotos2'] ) );
	//foreach( $html->find('table.sboxgallery a.option') as $u ){
	foreach( $html->find( $_POST['padrao-container'] ) as $u ){
		//$item[] = $u->href;
		$item[] = $u->$_POST['atributo'];

		// ***** USAR NO CASO DE SER O SITE www.agitoararaquara.com.br *****
		//$tst_ = $u->$_POST['atributo'];
		//$tst_ = str_replace("t.jpg", ".jpg", $u->$_POST['atributo']);
		//$tst_ = str_replace("t/nr_", "nr_", $tst_);
		//$item[] = $tst_;//$u->$_POST['atributo'];
	}
	sort($item);
	set_time_limit( count($item) * 5); //echo '<pre>'; print_r($item); echo '</pre>';/*

	//$nomeDiretorio = "./tst_" . date('Y-m-d_G-i-s');
	$nomeDiretorio = $_POST['diretorio'];
	if( mkdir( $nomeDiretorio ) ){
		echo "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">Diretório de destino criado <b>" . $nomeDiretorio . "</b> - <span style=\"color:green;font-weight:bold;\">[OK]</span></p>";
	}else{
		echo "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">Diretório de destino não pode ser criado <b>" . $nomeDiretorio . "</b> - <span style=\"color:red;font-weight:bold;\">[ERROR]</span></p>";
	}

	$y = 0;
	$ly = array();
	$n = 0;
	$ln = array();
	$arquivosZIP = array();

	foreach( $item as $url ){
		$tmp = split("/", $url);
		$imagem = end($tmp);

		if( $im = @imagecreatefromjpeg($url) ){ //criar uma amostra da imagem original 
			$largurao = @imagesx($im); // pegar a largura da amostra
			$alturao = @imagesy($im); // pegar a altura da amostra
			
			$alturad = 100; // definir a altura da miniatura em px
			$largurad = ($largurao*$alturad)/$alturao; // calcula a largura da imagem a partir da altura da miniatura
			
			$nova = @imagecreatetruecolor($largurao,$alturao); //criar uma imagem em branco
			@imagecopyresampled($nova,$im,0,0,0,0,$largurao,$alturao,$largurao,$alturao); //copiar sobre a imagem em branco a amostra diminuindo conforma as especificações da miniatura
			
			$arquivosZIP[] = $nomeDiretorio . "/" . $imagem;
			if( @imagejpeg( $nova, $nomeDiretorio . "/" . $imagem ) ){
				$y++;
				$ly[] = "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">" . $url . " - <span style=\"color:green;font-weight:bold;\">[OK]</span></p>";
			}
			else{
				$n++;
				$ln[] = "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">" . $url . " - <span style=\"color:red;font-weight:bold;\">[UNSAVED]</span></p>";
			}

			@imagedestroy($nova); //libera a memoria usada na miniatura
			@imagedestroy($im); //libera a memoria usada na amostra
		}else{
			echo "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">" . $url . " - <span style=\"color:darkorange;font-weight:bold;\">[UNOPENED]</span></p>";
		}
	}

	function create_zip( $files = array(), $destination = '', $overwrite = false){
		if(file_exists($destination) && !$overwrite){ return false; }
		$valid_files = array();
		if( is_array($files) ){
			foreach($files as $file){
				if( file_exists($file) ){
					$valid_files[] = $file;
				}
			}
		}
		if( count($valid_files) ){
			$zip = new ZipArchive();
			if( $zip->open( $destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE ) !== true ){
				return false;
			}
			foreach( $valid_files as $file ){
				$zip->addFile( $file, $file );
			}
			$zip->close();

			return file_exists($destination);
		}else{
			return false;
		}
	}

	if( create_zip( $arquivosZIP, "down.zip", true ) ){
		$server = $_SERVER['SERVER_NAME']; 
		$endereco = $_SERVER ['REQUEST_URI'];

		if (copy("./down.zip", $nomeDiretorio . "/down.zip")) {
			unlink("./down.zip");
		}

		echo "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\"><a href=\"http://" . $server . str_replace("salva.php", "", $endereco) . $nomeDiretorio . "/down.zip\" style=\"color:green;font-weight:bold;\">BAIXAR</a></p>";
	}else{
		echo "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">Arquivo ZIP não pode ser criado - <span style=\"color:red;font-weight:bold;\">[ERROR]</span></p>";
	}

	if( $n > 0 ){
		echo "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">Ocorreram alguns erros:</p>";
		foreach( $ln as $iln ){
			echo $iln;
		}
	}

	if( $y > 0 ){
		echo "<hr><p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">Essas imagens foram salvas:</p>";
		foreach( $ly as $ily ){
			echo $ily;
		}
	}/**/
?>