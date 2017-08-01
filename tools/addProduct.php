<?php 
// TRUNCATE product;
// TRUNCATE product_attribute;
// TRUNCATE product_description;
// TRUNCATE product_discount;
// TRUNCATE product_filter;
// TRUNCATE product_image;
// TRUNCATE product_option;
// TRUNCATE product_option_value;
// TRUNCATE product_recurring;
// TRUNCATE product_related;
// TRUNCATE product_reward;
// TRUNCATE product_special;
// TRUNCATE product_to_category;
// TRUNCATE product_to_download;
// TRUNCATE product_to_layout;
// TRUNCATE product_to_store;
include_once (dirname(__FILE__)."../../db.php");

$obj= new DB();

// articulos ya del CSV
$productSocialTmp=csvstring_to_array(file_get_contents("productSocial.csv"),0,",");
    		      // csvstring_to_array($string, $skip_rows = 0, $separatorChar = ';', $enclosureChar = '"', $newlineChar = "\n") {
// pr($productSocialTmp);

# remueve los headers
array_shift($productSocialTmp);

$productSocial=array();
foreach ($productSocialTmp as $key => $value) {
            $productSocial[$key]["name"]=$value[0];
            $productSocial[$key]["description"]=$value[1];
            $productSocial[$key]["image"]=$value[2];
            $productSocial[$key]["category_text"]=$value[3];
            $productSocial[$key]["price"]=str_replace("$", "", $value[4]);
}
// pr($productSocial);	

// dir_files
#Producto
$c=0;
foreach ($productSocial as $key => &$value) {
$c++;

	// $value["image"]=str_replace(array(".camisetas","camisetas.","camisetas","tazas","Tazas"), "", $value["image"]);
	// $value["image"] = preg_replace('[\s+]',"", $value["image"]);
	// $value["image"]=str_replace("jpg.", ".jpg",$value["image"]);
	// $value["image"]=ucwords($value["image"]);

	$date_available = strtotime("-1 day");
	$date_available=date('Y-m-d',$date_available);

	// $date_available=date('Y-m-d', strtotime('+1 day', $stop_date));
	$value["model"]=substr($value["category_text"]. substr($value["description"], -3), 0,6).$c;
	$product=array(
	"model" =>$value["model"],
	"quantity"   =>1,
	"stock_status_id"=>7,
	"image"=>"articles/".$value["image"],
	"shipping"=>1,
	"price"=>(!empty($value["price"])?$value["price"]:120),
	"date_available"=>$date_available,
	"weight_class_id"=>1,
	"length_class_id"=>1,
	"minimum"=>1,
	"sort_order"=>1,
	"status"=>1,
	);

	$sql="SELECT * FROM promosoc_print.product where model='".$product["model"]."'";
	if($row=$obj->get_one_result($sql)){
	# UPDATE	
	$product=array_merge($product,array("date_modified"=>date("Y-m-d H:m:s")));
		$obj->update("promosoc_print.product",$product, array("product_id"=>$row["product_id"]),1 );

	}
	else{
	# INSERT	
	$product=array_merge($product,array("date_added"=>date("Y-m-d H:m:s")));
		$obj->insert("promosoc_print.product", $product );
	}

}


# PRODUCT DESCRIPTION 
$categories_note=array(
"Caballeros",
"Damas",
"Jovenes",
"Niños y niñas",
	);
foreach ($productSocial as $key => &$value) {

	$sql="SELECT * FROM promosoc_print.product where model='".$value["model"]."'";
	$row=$obj->get_one_result($sql);


if(array_search($value["category_text"], $categories_note)){
$note="

</br>
</br>
</br>
<strong>Nota:</strong></br>
El precio es solo una referencia para ser cotizado dependiendo el diseño proporcionado.";
}
	$product_description=array(
	"language_id"      =>2,
	"name"             =>ucwords(substr($value["name"], 0,255)),
	"description"      =>ucwords($value["description"]).(!empty($note)?$note:""),
	"tag"              =>"",
	"meta_title"       =>substr($value["description"], 0,255),
	"meta_description" =>substr($value["description"], 0,255),
	"meta_keyword"     =>substr($value["description"], 0,255),
	);
	$sql="SELECT * FROM promosoc_print.product_description where product_id='".$row["product_id"]."'";
	if($row2=$obj->get_one_result($sql) ) {
		$obj->update("promosoc_print.product_description",$product_description, array("product_id"=>$row2["product_id"]),1 );

	}else{

		$product_description=array_merge($product_description,array("product_id"=>$row["product_id"]));
		$obj->insert("promosoc_print.product_description", $product_description );
	}

	if(!empty($row["product_id"]));
	$value["product_id"]=$row["product_id"];
}


# PRODUCT TO CATEGORY 

foreach ($productSocial as $key => &$value) {

	$sql="SELECT * FROM promosoc_print.product where model='".$value["model"]."'";
	$row=$obj->get_one_result($sql);

	$sql="SELECT * FROM promosoc_print.category_description where name='".$value["category_text"]."'";
	if($row2=$obj->get_one_result($sql) ) {
		
		$product_toCategory=array(
		"product_id"=>$value["product_id"],
		"category_id"=>$row2["category_id"],
		);

		$sql="SELECT * FROM promosoc_print.product_to_category where category_id='".$product_toCategory["category_id"]."' and  product_id='".$product_toCategory["product_id"]."'";

		if(!$obj->num_rows($sql))
		$obj->insert("promosoc_print.product_to_category", $product_toCategory );

	}

}

# PRODUCT TO store 
foreach ($productSocial as $key => &$value) {

		$product_toStore=array(
		"product_id"=>$value["product_id"],
		"store_id"=>0,
		);

		$sql="SELECT * FROM promosoc_print.product_to_store where store_id='".$product_toStore["store_id"]."' and  product_id='".$product_toStore["product_id"]."'";

		if(!$obj->num_rows($sql))
		$obj->insert("promosoc_print.product_to_store", $product_toStore );


}

############## 

# PRODUCT image
$path_image="/opt/lampp/htdocs/PromoandSocialPrint/tools/reinformacindesuempresa";
$path_file="/opt/lampp/htdocs/PromoandSocialPrint/image/";

$files_path_image=dir_files($path_image);
$folder_files=array();

foreach ($files_path_image as $key => &$value) {
	$tmp_expl=explode("/",$value);

	// $tmp_expl[7]=str_replace(array(".camisetas","camisetas","tazas","Tazas"), "", $tmp_expl[7]);
	// $tmp_expl[7] = preg_replace('[\s+]',"", $tmp_expl[7]);
	// $tmp_expl[7]=str_replace("jpg.", ".jpg",$tmp_expl[7]);
	// $tmp_expl[7]=ucwords($tmp_expl[7]);	
	// $value=implode("/", $tmp_expl);

// $folder_files[$key]["path"]=$value;
// $folder_files[$value]["image"]=$tmp_expl[7];
$folder_files[$value]=$tmp_expl[7];

}



foreach ($productSocial as $key => $value) {

if(array_search($value["image"], $folder_files)){

	if(file_exists($path_image."/".$value["image"])){

		if(!file_exists($path_file."articles/".$value["image"])){
		$cmd="cp ".$path_image."/".$value["image"]." ".$path_file."articles/";
		shell_exec($cmd);
		// pr($cmd);
		}
	}
}
else{
	pr($value["image"]);

}
		// $product_toStore=array(
		// "product_id"=>$value["product_id"],
		// "store_id"=>0,
		// );

		// $sql="SELECT * FROM promosoc_print.product_to_store where store_id='".$product_toStore["store_id"]."' and  product_id='".$product_toStore["product_id"]."'";

		// if(!$obj->num_rows($sql))
		// $obj->insert("promosoc_print.product_to_store", $product_toStore );
}

############## 
 ?>