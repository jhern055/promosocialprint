<?php 

include_once (dirname(__FILE__)."../../db.php");

$obj= new DB();



$path_file="/opt/lampp/htdocs/mercadosale/image/";
# 175px 175px
$sql="SELECT product_id,image,model,(select description from product_description where product_id=p.product_id) as  description
	FROM mercadosale.product as p";

$products=$obj->get_results($sql);
foreach ($products as $k => $v) {
	if(!file_exists($path_file.$v["image"])){
		$products_not_image[$v["model"]]["model"]=$v["model"];
		$products_not_image[$v["model"]]["product_id"]=$v["product_id"];
		$products_not_image[$v["model"]]["description"]=$v["description"];
	}
	if(empty($v["image"])){
		$products_not_image[$v["model"]]["model"]=$v["model"];
		$products_not_image[$v["model"]]["product_id"]=$v["product_id"];	
		$products_not_image[$v["model"]]["description"]=$v["description"];
	}
}

#	Hacer el dir 
if ( ! function_exists('scandir_folders'))
{
function scandir_folders($d) {
	return glob($d . '/*' , GLOB_ONLYDIR);
}
}


// if(!empty($products_not_image))
// foreach ($products_not_image as $key => $value) {
// 	$cmd="sudo mkdir -p ".dirname(__FILE__)."/productsFolder/[".$value["model"]."]_".real_dir($value['product_id'])."";
// 	// pr($cmd);s
// 	shell_exec($cmd);
// }

$directories=scandir_folders(dirname(__FILE__)."/productsFolder");
$c=0;
foreach ($directories as $key => $value) {
	$tmp=explode("/", $value);
	$tmp_2=end($tmp);
	$tmp_3=explode("_", $tmp_2);
	$name_file=str_replace(array("[","]"), "", $tmp_3[0]);
	$file_tmp=str_replace(array("[","]"), "", $tmp_3[0]).".jpg";

	if(!file_exists($value."/".$file_tmp)){
		$c++;
		if(empty($_POST["ajax"])):
		echo "<div>";
		echo "<pre>";
		// echo "<a href='https://www.google.com.mx/search?q=".$name_file."&source=lnms&tbm=isch&sa=X&ved=0ahUKEwjQw5-Y3rjTAhXH7YMKHbFiCn8Q_AUICigD&biw=832&bih=533' target='_blank'>
		// 	 ".$file_tmp."</a>";
		// echo "<br>";
		// echo $value;
		// echo "<br>";
		echo "<a href='https://www.google.com.mx/search?q=".$products_not_image[$name_file]['description']."&source=lnms&tbm=isch&sa=X&ved=0ahUKEwjQw5-Y3rjTAhXH7YMKHbFiCn8Q_AUICigD&biw=832&bih=533' target='_blank'>
			".$products_not_image[$name_file]['description']."
			 </a>";

		echo "<br>";
		echo "<input type='text' class='url_image'>";
		echo "<button 
				data-file_name=".$file_tmp."
				data-path='".$value."/"."' id='submit'>Enviar</button>";
		
		echo "</pre>";
		echo "</div>";
		endif;
	}else{
		// pr($value."/".$file_tmp);

	}

}

#######################
// pr($directories);
foreach ($directories as $key => $value) {
	$tmp=explode("/", $value);
	$tmp_2=end($tmp);
	$tmp_3=explode("_", $tmp_2);
	$name_file=str_replace(array("[","]"), "", $tmp_3[0]);
	$file_tmp=str_replace(array("[","]"), "", $tmp_3[0]).".jpg";

	if(file_exists($value."/".$file_tmp)){
		if(!file_exists($path_file."articles/".$file_tmp)){
		
		shell_exec("cp ".$value."/".$file_tmp." ".$path_file."articles/");

		}
	}
		if(!empty($products_not_image)){
		$data=array("image"=>"articles/".$file_tmp);
        $obj->update("mercadosale.product",$data,array("product_id"=>$products_not_image[$name_file]["product_id"]),1);
        }
}
 ?>
 <?php 
		if(empty($_POST["ajax"])):

  ?>
<script type="text/javascript" src="../catalog/view/javascript/jquery/jquery-2.1.1.min.js?j2v=2.7.1"></script>
<script type="text/javascript">
$(document).ready(function(){

	$("button#submit").click(function(){
		var this_it=$(this);
		var path=$(this).data("path");
		var file_name=$(this).data("file_name");
		var url_image=$(this).parent().find("input.url_image").val();

			$.ajax({
				    url: "https://<?php echo $_SERVER['SERVER_NAME'].'/mercadosale/tools/createProductsDir.php'; ?>",
				    type: 'POST',
				    dataType: 'json',
				    data: {
				    	ajax:1,
				    	path:path,
				    	file_name:file_name,
				    	url_image:url_image,
				    },
				    beforeSend: function(response){


				    },
				    success: function(response){
				    	if(response.status)
				    	$(this_it).parent().parent().remove();
				    }
			 });

	});
});
</script>
<?php 
endif;
 ?>
<?php 
function wget($address,$filename)
{
  file_put_contents($filename,file_get_contents($address));
}
if(!empty($_POST["ajax"])):
	wget($_POST["url_image"],$_POST["file_name"]);
	$cmd=" mv ".$_POST["file_name"]." ".real_dir($_POST["path"]);
	shell_exec($cmd);

	return print_r(json_encode(array("status"=>1,"msg"=>"con exito")));
endif;
 ?>
 
