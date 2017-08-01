<?php 
include_once (dirname(__FILE__)."../../../db.php");
$obj= new DB();

$path_file="/opt/lampp/htdocs/mercadosale/image/";

$sql="SELECT category_id,image,(select name from category_description where category_id=c.category_id) as  name
	FROM mercadosale.category as c
	where 
	status=1
	and parent_id!=0
	";

$categories_sql=$obj->get_results($sql);
foreach ($categories_sql as $k => $v) {
	if(!file_exists($path_file.$v["image"])){
		$categories_not_image[$v["category_id"]]["category_id"]=$v["category_id"];
		$categories_not_image[$v["category_id"]]["name"]=$v["name"];
	}
	if(empty($v["image"])){
	// pr($path_file.$v["image"]);

		$categories_not_image[$v["category_id"]]["category_id"]=$v["category_id"];	
		$categories_not_image[$v["category_id"]]["name"]=$v["name"];
	}
}

$directories=scandir_folders("/opt/lampp/htdocs/mercadosale/tools/categoriesFolder");

foreach ($directories as $key => &$value) {
	$tmp=explode("/", $value);
	$tmp_2=end($tmp);
	$tmp_3=explode("_", $tmp_2);
	$category_id=str_replace(array("[","]"), "", $tmp_3[0]);
	
	$files=scandir($value);
	foreach ($files as $k => $v) {
		if($v != '.' && $v != '..' && !is_dir($v)){

		# esto copea la imagen de la categoria en su categories
		$cmd="cp ".real_dir($value)."/".$v." ".$path_file."categories/";
		shell_exec($cmd);
			
			if(!empty($v) and !empty($categories_not_image)){
			$data=array("image"=>"categories/".$v);
			$obj->update("mercadosale.category",$data,array("category_id"=>$category_id),1);
			}		
		}
	}

}

// esto se corre cuando la categoria no la tenias contemplada para imagen
// foreach ($categories_not_image as $key => $value) {
// 	$cmd="sudo mkdir -p ".dirname(__FILE__)."/[".$value["category_id"]."]_".real_dir($value['name'])."";
// }
// pr($categories_not_image);
// categories/accesoriosCelular.png

?>