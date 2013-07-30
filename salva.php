<?php
	header ('Content-type: text/html; charset=UTF-8');

	// http://simplehtmldom.sourceforge.net/
	include('simple_html_dom.php');

	$html = file_get_html( base64_decode( $_POST['url-fotos2'] ) );
	foreach( $html->find( $_POST['padrao-container'] ) as $u ){
		$item[] = $u->$_POST['atributo'];

		// ***** USAR NO CASO DE SER O SITE www.agitoararaquara.com.br *****
		//$tst_ = $u->$_POST['atributo'];
		//$tst_ = str_replace("t.jpg", ".jpg", $u->$_POST['atributo']);
		//$tst_ = str_replace("t/nr_", "nr_", $tst_);
		//$item[] = $tst_;//$u->$_POST['atributo'];
	}
	sort($item);
	set_time_limit( count($item) * 5 );
	//echo '<pre>'; print_r($item); echo '</pre>';/*

	$nomeDiretorio = $_POST['diretorio'];
	if( !file_exists($nomeDiretorio) ){
		if( mkdir( $nomeDiretorio ) ){
			echo "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">Diretório de destino criado <b>" . $nomeDiretorio . "</b> - <span style=\"color:green;font-weight:bold;\">[OK]</span></p>";
		}else{
			echo "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">Diretório de destino não pode ser criado <b>" . $nomeDiretorio . "</b> - <span style=\"color:red;font-weight:bold;\">[ERROR]</span></p>";
		}
	}else{
		echo "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">O diretório já existe - <span style=\"color:darkorange;font-weight:bold;\">[NOT_CREATED]</span></p>";
	}
	

	$y = 0;
	$ly = array();
	$n = 0;
	$ln = array();
	$arquivosZIP = array();

	foreach( $item as $url ){
		$tmp = split("/", $url);
		$imagem = end($tmp);

		if( $im = @imagecreatefromjpeg($url) ){
			$largurao = @imagesx($im);
			$alturao = @imagesy($im);
			
			$alturad = 100;
			$largurad = ($largurao*$alturad)/$alturao;
			
			$nova = @imagecreatetruecolor($largurao,$alturao);
			@imagecopyresampled($nova,$im,0,0,0,0,$largurao,$alturao,$largurao,$alturao);
			
			$arquivosZIP[] = $nomeDiretorio . "/" . $imagem;
			if( @imagejpeg( $nova, $nomeDiretorio . "/" . $imagem ) ){
				$y++;
				$ly[] = "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">" . $url . " - <span style=\"color:green;font-weight:bold;\">[OK]</span></p>";
			}
			else{
				$n++;
				$ln[] = "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">" . $url . " - <span style=\"color:red;font-weight:bold;\">[UNSAVED]</span></p>";
			}

			@imagedestroy($nova);
			@imagedestroy($im);
		}else{
			echo "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">" . $url . " - <span style=\"color:darkorange;font-weight:bold;\">[UNOPENED]</span></p>";
		}
	}

	// GERAR ARQUIVO ZIP
	if( isset( $_POST['gerar-zip'] ) ){
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
	}
	
	// EXIBIR O RELATÓRIO GERAL
	if( isset( $_POST['relatorio'] ) ){
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
		}
	}
	/**/
?>