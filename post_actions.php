<?php
//post_actions.php
require_once "kivimurska_plugin.php";
if($_POST){
	
	global $wpdb;
	$checked['form_fail'] = false;
	$raports = "";

	if($_POST['id'] != ""){
		
		$murske = $wpdb->get_row("SELECT * FROM wp_murskeet WHERE id = ".$_POST['id']);
		if($murske){
			
			$checked = checkValues($_POST);
			
			if($checked['form_fail'] != true){
				
				muokka_murske_sql($_POST);
				if($murske->kuva != $_POST['kuva']){

					unlink(plugins_url()."/kivimurska_plugin/img_murskeet/".$murske->kuva);
					$imagefile=$_FILES["image"]["kuva"]; //kuvan nimi lomakkeelta
					insert_pic($imagefile);
				}
			}else{
				admin_murske($_POST['id'],$tyhjat[$key]);
			}
		}else{
		
		$raports .= "tapahtui virhe 113";
		}
	}else{
		
		$lisaa_ok = lisaa_murske_sql($_POST);
		$imagefile=$_FILES["image"]["kuva"]; //kuvan nimi lomakkeelta
		$lisaa_kuva = insert_pic($imagefile);
	}
}
	function checkValues($data){
		if($data){
			foreach($data as $key => $item){
				
				if($item == ""){
					
					$returns['tyhjat'][$key] = "1";
					$returns['form_fail'] = true;
				}
			}
		}
		return $returns;
	}
	function insert_pic($imagefile){
		
		if(!empty($imagefile)){
			
			$imagefile=$_FILES["image"]["name"]; //kuvan nimi lomakkeelta

			//TARKISTA, ETTÄ POLKU ON OIKEIN!!!
			$path = plugins_url()."/kivimurska_plugin/img_murskeet/";

			//tarkistetaan kuvatyyppi
			// grab the path to the temporary file (image) that the user uploaded
			$photo = $_FILES['image']['tmp_name'];
			// tarkistetaan, että kuva on väliaikaisena olemassa - muuten ei voi tallentaa
			if(is_uploaded_file($photo)){
				// kuvan tyyppi tutkitaan:
				if(strstr($imagefile,".png")){
					$image_type = "png";
				}else if(strstr($imagefile,".jpg")){
					$image_type = "jpg";
				}else if(strstr($imagefile,".gif")){
					$image_type = "gif";
				}
				else if(strstr($imagefile,".JPG")){
					$image_type = "JPG";
				}
				else if(strstr($imagefile,".JPEG")){
					$image_type = "JPEG";
				}
				else if(strstr($imagefile,".jpeg")){
					$image_type = "jpeg";
				}
				
				else{
					die("Tuntematon kuvatyyppi!");
				}
			
				if(move_uploaded_file($_FILES['image']['tmp_name'],$path.$imagefile)){
					echo "Kuva on tallennettu.";
					
				}
				else{
					echo "Ei onnistunut!";
				}


			}
	}
}
?>