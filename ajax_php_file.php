<?php

$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/kivimurska/wp-config.php';
include_once $path . '/kivimurska/wp-load.php';
include_once $path . '/kivimurska/wp-includes/wp-db.php';
include_once $path . '/kivimurska/wp-includes/pluggable.php';

if(!empty($_POST['nimi'])){

	if ($_FILES['file']['size'] != 0 && $_FILES['file']['error'] == 0){
		$validextensions = array("jpeg", "jpg", "png");
		$temporary = explode(".", $_FILES["file"]["name"]);
		$file_extension = end($temporary);
		if (
			(($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")) 
				&& ($_FILES["file"]["size"] < 500000)//Approx. 100kb files can be uploaded.
		&& in_array($file_extension, $validextensions)) {
			if ($_FILES["file"]["error"] > 0){
				echo "Return Code: " . $_FILES["file"]["error"] . "<br/><br/>";
			}else{
			
				if (file_exists("upload/" . $_FILES["file"]["name"])) {
					
					echo $_FILES["file"]["name"] . " Saman niminen kuva on jo olemassa ";
				}else{
					$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
					$imagename = $_FILES['file']['name'];
					$targetPath = realpath(dirname(__FILE__))."/img_murskeet/".$_FILES['file']['name']; // Target path where file is to be stored
					move_uploaded_file($sourcePath,$targetPath) ; // Moving Uploaded file

				}
			}
		}else{
			
			echo "Kuvan tyyppi on väärä tai kuva on liian suuri";
		}
	}
	if(!empty($_POST['id'])){
		$ok = muokka_murske_sql($_POST,$imagename);
	}else{
		$ok = lisaa_murske_sql($_POST,$imagename);
	}
	if($ok == "ok"){
		echo "Tallennettu";

	}else{

		echo $ok;

	}
}else{

	echo "Nimi on tyhjä";
}

function muokka_murske_sql($data,$imagename){
		if($data){

			global $wpdb;

			$ok = $wpdb->update( 
				'wp_murskeet', 
				array( 
					'nimi' => $data['nimi'],
					'vari' => $data['vari'],
					'paino' => $data['paino'],
					'dimensio' => $data['dimensio'],
					
				), 
				array( 'id' => $data['id'] ) 
			);
			
			if($imagename != ""){
				$kuvaok = $wpdb->update( 
					'wp_murskeet', 
					array( 
						'kuva' => $imagename,
						
					), 
					array( 'id' => $data['id'] ) 
				);
			}
			
			
			if($ok || $wpdb->last_error == ""){
				return "ok";
			}else{
				echo $wpdb->last_error;
				//echo $wpdb->last_query;
				return "error2";
			}
		}
	}
	function lisaa_murske_sql($data,$imagename){
		if($data){
			global $wpdb;
			$ok = $wpdb->insert(
				'wp_murskeet', 
				array( 
					'nimi' => $data['nimi'],
					'vari' => $data['vari'],
					'paino' => $data['paino'],
					'dimensio' => $data['dimensio'],
					'kuva' => $imagename
				)
			);
			//echo $wpdb->last_query;
			if($ok){
				return "ok";
			}else{
				return "error2";
			}
		}
		
	}
?>