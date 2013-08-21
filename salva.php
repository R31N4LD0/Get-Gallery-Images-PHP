<?php
	header('Content-type: text/html; charset=UTF-8');

	// DOWNLOAD: http://simplehtmldom.sourceforge.net/
	include('simple_html_dom.php');

	$printHTML = '<!DOCTYPE html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Relatório</title></head><body>';

	$html = file_get_html( base64_decode( $_POST['url-fotos2'] ) );//print_r($html);

	foreach( $html->find( $_POST['padrao-container'] ) as $u ){
		// ***** DEFAULT www.marioricci.com.br ***** Padrão do container: ".sboxgallery a" | Atributo: "href"
		$item[] = $u->$_POST['atributo'];

		// ***** USAR NO CASO DO SITE www.agitoararaquara.com.br ***** Padrão do container: ".thumbs img" | Atributo: "src"
		//$tst_ = $u->$_POST['atributo'];
		//$tst_ = str_replace("t.jpg", ".jpg", $u->$_POST['atributo']);
		//$tst_ = str_replace("t/nr_", "nr_", $tst_);
		//$item[] = $tst_;

		// ***** USAR NO CASO DO SITE www.saibaja.com.br ***** Padrão do container: "#eventos_slide img" | Atributo: "src"
		//$tst_ = $u->$_POST['atributo'];
		//$tst_ = str_replace("/0/", "/2/", $u->$_POST['atributo']);
		//$item[] = "http://www.saibaja.com.br" . $tst_;

		// ***** USAR NO CASO DO SITE www.mataourgente.com.br ***** Padrão do container: ".items a" | Atributo: "href"
		//$tst_ = $u->$_POST['atributo'];
		//$item[] = "http://www.mataourgente.com.br" . $tst_;//$u->$_POST['atributo'];
	}
	sort($item);
	set_time_limit( count($item) * 5 );
	//echo '<pre>'; print_r($item); echo '</pre>';/*

	// CRIA O DIRETÓRIO DE DESTINO
	$nomeDiretorio = $_POST['diretorio'];
	if( !file_exists($nomeDiretorio) ){
		if( mkdir( $nomeDiretorio ) ){
			$printHTML .= "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">Diretório de destino criado <b>" . $nomeDiretorio . "</b> - <span style=\"color:rgb(69, 169, 69);font-weight:bold;\">[OK]</span></p>";
		}else{
			$printHTML .= "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">Diretório de destino não pode ser criado <b>" . $nomeDiretorio . "</b> - <span style=\"color:red;font-weight:bold;\">[ERROR]</span></p>";
		}
	}else{
		$printHTML .= "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">O diretório já existe - <span style=\"color:darkorange;font-weight:bold;\">[NOT_CREATED]</span></p>";
	}
	

	$y = 0;
	$ly = array();
	$n = 0;
	$ln = array();
	$arquivosZIP = array();

	// SALVA AS IMAGENS
	foreach( $item as $url ){
		$tmp = split("/", $url);
		$imagem = end($tmp);

		if( $im = @imagecreatefromjpeg($url) ){
			$largurao = @imagesx($im);
			$alturao = @imagesy($im);
			
			$alturad = 100;
			$largurad =($largurao*$alturad)/$alturao;
			
			$nova = @imagecreatetruecolor($largurao,$alturao);
			@imagecopyresampled($nova,$im,0,0,0,0,$largurao,$alturao,$largurao,$alturao);
			
			$arquivosZIP[] = $nomeDiretorio . "/" . $imagem;
			if( @imagejpeg( $nova, $nomeDiretorio . "/" . $imagem ) ){
				$y++;
				$ly[] = "<p style=\"font-family:Arial, Tahoma, Verdana;\"><a href=\"" . $url . "\" style=\"color:#666;font-family:Arial, Tahoma, Verdana;\" target=\"_blank\">" . $url . "</a> - <span style=\"color:rgb(69, 169, 69);font-weight:bold;\">[OK]</span></p>";
			}
			else{
				$n++;
				$ln[] = "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">" . $url . " - <span style=\"color:red;font-weight:bold;\">[UNSAVED]</span></p>";
			}

			@imagedestroy($nova);
			@imagedestroy($im);
		}else{
			$printHTML .= "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">" . $url . " - <span style=\"color:darkorange;font-weight:bold;\">[UNOPENED]</span></p>";
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

			if( copy("./down.zip", $nomeDiretorio . "/down.zip") ){
				unlink("./down.zip");
			}

			$printHTML .= "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\"><a href=\"http://" . $server . str_replace("salva.php", "", $endereco) . $nomeDiretorio . "/down.zip\" style=\"color:rgb(69, 169, 69);font-weight:bold;\">BAIXAR</a></p>";
		}else{
			$printHTML .= "<p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">Arquivo ZIP não pode ser criado - <span style=\"color:red;font-weight:bold;\">[ERROR]</span></p>";
		}
	}
	
	// EXIBIR O RELATÓRIO GERAL
	if( isset( $_POST['relatorio'] ) ){
		if( $n > 0 ){
			$printHTML .= "<hr><p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">" . count( $ln ) . " erros:</p>";
			foreach( $ln as $iln ){
				$printHTML .= $iln;
			}
		}

		if( $y > 0 ){
			$printHTML .= "<hr><p style=\"color:#666;font-family:Arial, Tahoma, Verdana;\">" . count( $ly ) . " imagens salvas:</p>";
			foreach( $ly as $ily ){
				$printHTML .= $ily;
			}
		}
	}
	/**/

	echo $printHTML . "</body></html>";
?>