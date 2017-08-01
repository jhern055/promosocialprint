<?php 

include_once (dirname(__FILE__)."../../db.php");
$obj= new DB();

// exit();
// TRUNCATE category;
// TRUNCATE category_path;
// TRUNCATE category_description;
// TRUNCATE category_to_layout;
// TRUNCATE category_to_store;

$array_list=array(
"Invitaciones",
"tarjetas de presentación y regalo",
"Recuerdos",
);

$parent_id=5; # -> con categoria relacion
$level=1; # -> con categoria relacion

$cat=$obj->get_row("SELECT category_id FROM category order by category_id desc");

if(!empty($cat[0]))
$category_id=$cat[0]+1;

$parentMenu=5;

// foreach ($array_list as $key => $value):

// if($obj->get_row("SELECT category_id FROM category_description where name like '%".ucfirst($value)."%'")){
// pr(ucfirst($value));
// die("Ya existe esta categoria");
// }


// endforeach;
if(empty($category_id))
$category_id=1;

foreach ($array_list as $key => $value) {
// pr("SELECT category_id FROM category_multiparent where category_id = '".$category_id."' and parent_id = '".$parentMenu."'");
$tmp=$obj->get_row("SELECT category_id FROM category_description where name = '".ucfirst($value)."'");	

# cuando es multicategoria
// $sql="SELECT category_id FROM category_multiparent where category_id = '".$tmp[0]."' and parent_id = '".$parent_id."'";

$sql="SELECT category_id FROM category_path where category_id = '".$tmp[0]."' and path_id = '".$parent_id."'";
// pr($tmp[0]);
// exit();
if(!$tmp[0] and !$obj->get_row($sql) ){

	if(!$obj->get_row("SELECT * FROM category WHERE category_id='".$category_id."'")){

	# cuando es multi categoria 
	$sql="INSERT INTO `category` (`category_id`, `image`, `parent_id`, `top`, `column`, `sort_order`, `status`, `date_added`, `date_modified`) VALUES ('".$category_id."', '', '".$parent_id."', '0', '1', '".$parentMenu."', '1', '".date("Y-m-d H:m:s")."', '".date("Y-m-d H:m:s")."');";
	$obj->query( $sql );
	}

	if(!$obj->get_row("SELECT * FROM category_description WHERE category_id='".$category_id."'")){
	$sql="INSERT INTO `category_description` (`category_id`, `language_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`) VALUES ('".$category_id."', '2', '".ucfirst($value)."', '', '".strtolower(str_replace(" ", "-",$value))."', '".strtolower(str_replace(" ", "-",$value))."', '".str_replace(" ", "-",$value)."');";
	$obj->query( $sql );
	}

	# reparar zapateria juarez este query
	if(!$obj->get_row("SELECT * FROM category_path WHERE category_id='".$category_id."' AND path_id='".$parent_id."'")){
	$sql="INSERT INTO `category_path` (`category_id`, `path_id`, `level`) VALUES ('".$category_id."','".$parent_id."', '0');";
	$obj->query( $sql );
	}

	if(!$obj->get_row("SELECT * FROM category_path WHERE category_id='".$category_id."' AND path_id='".$category_id."'")){
	$sql="INSERT INTO `category_path` (`category_id`, `path_id`, `level`) VALUES ('".$category_id."','".$category_id."', '".$level."');";
	$obj->query( $sql );
	}

	if(!$obj->get_row("SELECT * FROM category_to_layout WHERE category_id='".$category_id."'")){
	$sql="INSERT INTO `category_to_layout` (`category_id`, `store_id`, `layout_id`) VALUES ('".$category_id."', '0', '0');";
	$obj->query( $sql );
	}


	if(!$obj->get_row("SELECT * FROM category_to_store WHERE category_id='".$category_id."'")){
	$sql="INSERT INTO `category_to_store` (`category_id`, `store_id`) VALUES ('".$category_id."', '0');";
	$obj->query( $sql );
	}

	$category_id++;
}

}
pr("Se insertaron correctamente");
 ?>