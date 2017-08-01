<?php 

include_once (dirname(__FILE__)."../../db.php");

$obj= new DB();
# 175px 175px

$cat=$obj->get_results("SELECT c.category_id,
						c_des.name description
						FROM category c  

						LEFT join(SELECT category_id,name FROM category_description) as c_des on c_des.category_id=c.category_id
						WHERE c.parent_id > 0
						and status=1
						order by c.category_id desc");

// hacer el dir 
if(!empty($cat))
foreach ($cat as $key => $value) {
	// $cmd="sudo mkdir -p ".dirname(__FILE__)."/categoriesFolder/[".$value["category_id"]."]_".real_dir($value['description'])."";
	// pr($cmd);
	// shell_exec($cmd);
}

 ?>