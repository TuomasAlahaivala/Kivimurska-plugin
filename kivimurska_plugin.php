<?php
/*
Plugin Name: KiviMurska Plugin
Plugin URI: http.www.kivimurska.fi
Description: Lisäosa murskeiden hallintaan, sekä esittämiseen sivustolla
Author: Tuomas Alahäivälä
Version: 0.1
Author URI: tuomasalahaivala.com
*/
// Start the session
session_start();

add_shortcode( 'listaa_murskeet', 'listaa_murskeet_func' );
add_action('admin_menu','kivimurskaplugin_admin_actions');


   wp_enqueue_script('murske_js', plugin_dir_url(__FILE__) . 'js/murske_javascript.js');


wp_localize_script('murske_js', 'murskejs', array(
    'pluginsUrl' => plugins_url(),
	'adminUrl' => admin_url(),
));
function kivimurskaplugin_admin_actions(){
	add_menu_page('Kivimurska Lisäosa','Kivimurska Lisäosa','manage_options',__FILE__,'kivimurskaplugin_admin');
}
function kivimurskaplugin_admin(){

	if($_GET['action'] == "muokkaa"){
		
		admin_murske($_GET['id']);
		
	}elseif($_GET['action'] == "poista"){
		
		admin_poista_Murske($_GET['id'],$_GET['conf']);
	}elseif($_GET['action'] == "lisaa_uusi"){
		$id = "";
		admin_murske($id);
		
	}else{
		
		admin_listaa_Murskeet();
	}

}
function admin_listaa_Murskeet(){
	$tyypit = array("nimi" => "Nimi","vari" => "Väri","paino" => "Paino per/lava", "dimensio" => "Dimensio", "kuva" => "Kuva");
	
	$url = hae_Url();
	
	?>
	<div class="wrap">
		<h4>Murskalaatujen hallinta</h4>
		<a href="<?php echo $url;?>&action=lisaa_uusi" class="add-new-h2">Lisää uusi</a>
		<table class="widefat">
			<thead>
				<tr>
					<?php foreach($tyypit as $item){
						echo "<th>".$item."</th>";
					}?>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tfoot>
			</tfoot>
			<tbody>
			<?php
			global $wpdb;
			
			$murskeet = $wpdb->get_results(
				"
				SELECT * FROM wp_murskeet
				"
			);
			$odd= 1;
			foreach($murskeet as $murske){
				if($odd == 1){
					echo '<tr bgcolor="#D0D0D0">';
					$odd = 0;
				}else{
					echo '<tr>';
					$odd = 1;
				}
				foreach($tyypit as $key => $item){ 
				if($key == "kuva"){
					
					echo "<td><img src='".plugins_url()."/kivimurska_plugin/img_murskeet/";
					if($murske->$key != ""){
						echo $murske->$key;
					}else{
						echo "noimage_gray.png";
					}
					echo "' alt='img' style='width:150px;'></td>";
					
				}else{
					echo "<td>".$murske->$key."</td>"; 
					}
				}?>
					<td><a href="<?php echo $url;?>&action=muokkaa&id=<?php echo $murske->id;?>">Muokkaa</a></td>
					<td><a href="<?php echo $url;?>&action=poista&conf=null&id=<?php echo $murske->id;?>">Poista</a></td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	</div><?php
}
function admin_murske($id, $returns = null){
	
	if($id){
		global $wpdb;
		$murske = $wpdb->get_row("SELECT * FROM wp_murskeet WHERE id = $id");
		echo $title = "Muokkaa murselajia";
	}else{
		
		echo $title = "Lisää murselaji";
	}
	
		?>
			<h2><?php echo $title; ?></h2>
			<form id="post_murske" action="" method="post" enctype="multipart/form-data">

				<input type="hidden" name="id" value="<?php if($id): echo $id; endif;?>"><br>
				<label>Nimi <input type="text" name="nimi" value="<?php if($murske->nimi): echo $murske->nimi; endif;?>"><span id="required" <?php if(isset($_SESSION['nimi_empty'])){ echo 'style="color:red;"';}?>>*</span></label><br>
				<label>Väri<input type="text" name="vari" value="<?php if($murske->vari): echo $murske->vari; endif;?>"></label><br>
				<label>Paino pre/lava<input type="text" name="paino" value="<?php if($murske->paino): echo $murske->paino; endif;?>"></label><br>
				<label>Dimensio<input type="text" name="dimensio" value="<?php if($murske->dimensio): echo $murske->dimensio; endif;?>"></label><br>
				<label>Kuva
				<input type="file" name="file" id="file" value="<?php if($murske->kuva): echo $murske->kuva; endif;?>"></label><br>
				
				
				<div id="image_preview">
					<img id="previewing" src="<?php 
						if($murske->kuva){
							echo plugins_url()."/kivimurska_plugin/img_murskeet/".$murske->kuva;
						}else{
							echo plugins_url()."/kivimurska_plugin/img_murskeet/eikuvaa.png";
						}
						
					?>" style='width:150px;'/>
						
				</div>
				<label id="required">* Pakollinen</label><br/>
				<input type="submit" value="Paivita"/>
			</form>
		
		<?php
}
function listaa_murskeet_func( $atts ) {
	
	global $wpdb;
	$myrows = $wpdb->get_results( "SELECT * FROM wp_murskeet" );
	$str = "<table style='width:100%;'>
		<tr>
			<th>Nimi</th>
			<th>Väri</th>
			<th>Paino</th>
			<th>Dimensio</th>
			<th></th>
		</tr>";
	if($myrows){
		foreach($myrows as $key => $item){
			$str .= "<tr>";
			$str .= "<td>".$item->nimi."</td>";
			$str .= "<td>".$item->vari."</td>";
			$str .= "<td>".$item->paino."</td>";
			$str .= "<td>".$item->dimensio."</td>";
			$str .= '<td><img id="previewing" src="';
			if($item->kuva){
				$str .= plugins_url()."/kivimurska_plugin/img_murskeet/".$item->kuva;
			}else{
				$str .= plugins_url()."/kivimurska_plugin/img_murskeet/noimage_gray.png";
			}
			$str .= '" style="width:150px;"/></td></tr>';
		}
	}
	$str .="</table>";
	return $str;
}
function admin_poista_Murske($id, $conf){
	
	if(!empty($id)){
		$url = hae_url();
		global $wpdb;
		
		if($conf == "true"){
			//echo $conf;
			$ok = $wpdb->delete( 'wp_murskeet', array( 'id' => $id ) );
		}else if($conf == "null"){
			global $wpdb;
			$murske = $wpdb->get_row("SELECT id, nimi FROM wp_murskeet WHERE id = $id");
			
			?>
			<h2>Varmista kohteen <?php echo $murske->nimi;?> poisto.</h2>
			<button onclick="location.href='<?php echo $url;?>&action=poista&conf=true&id=<?php echo $murske->id;?>'">Poista</button>
			<button onclick="location.href='<?php echo $url;?>'">Palaa</button>
			
			<?php
		}
		//echo $wpdb->last_query;
		if($ok || $conf == "false"){
			
			admin_listaa_Murskeet();
		}
	}
}
function hae_Url(){
	
	$fixedget = str_replace("/", "%2F", $_GET['page']);
			
	$url = admin_url('admin.php')."?page=".$fixedget;
	
	return $url;
}
?>