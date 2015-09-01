jQuery(document).ready(function (e) {
	jQuery("#post_murske").on('submit',(function(e) {
		e.preventDefault();
		jQuery("#message").empty();
		jQuery('#loading').show();
		jQuery.ajax({
			url: murskejs.pluginsUrl + "/kivimurska_plugin/ajax_php_file.php", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data)   // A function to be called if request succeeds
			{
				alert(data);
				jQuery('#loading').hide();
				jQuery("#message").html(data);
				if (data.indexOf("Tallennettu") >= 0){
				window.location.href = murskejs.adminUrl+"admin.php?page=kivimurska_plugin%2Fkivimurska_plugin.php";
				}
			}
		});
	}));

	// Function to preview image after validation
	jQuery(function() {
		jQuery("#file").change(function() {
			jQuery("#message").empty(); // To remove the previous error message
			var file = this.files[0];
			var imagefile = file.type;
			var match= ["image/jpeg","image/png","image/jpg"];
			if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
			{
				jQuery('#previewing').attr('src','noimage.png');
				jQuery("#message").html("<p id='error'>Please Select A valid Image File</p>"+"<h4>Note</h4>"+"<span id='error_message'>Only jpeg, jpg and png Images type allowed</span>");
				return false;
			}
			else
			{
				var reader = new FileReader();
				reader.onload = imageIsLoaded;
				reader.readAsDataURL(this.files[0]);
			}
		});
	});
	function imageIsLoaded(e) {
		jQuery("#file").css("color","green");
		jQuery('#image_preview').css("display", "block");
		jQuery('#previewing').attr('src', e.target.result);
		jQuery('#previewing').attr('width', '150px');
		//jQuery('#previewing').attr('height', '230px');
	};
});
