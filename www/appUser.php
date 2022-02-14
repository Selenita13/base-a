<?php
/*
user.php
Version: 1
Desarrollado por: Upgrade Diseño Interactivo
Modificado por Selene
17 agosto 2021

*/

$rootPath = 'https://antiguapp.com.mx/_develop/mobile_app/';


require_once("../_sudiEngine/_engine/config.php");
require_once("../../../".$root_folder."/connectMySql.php");
include_once('../../../'.$root_folder.'/sudi_functions.php');

	// #Se activa: Si existe "POST - user      client : global_prefig, mail : global_mail

if($_POST['user']){
  $jsondata = array();
  /* C O D E */
  $jsondata['user']=array();

  /* Debug*/
 	$debug = false;


	if($debug == true){
	    $db = 'creando';
		$m = 'eduardo@upgrade.com.mx';
	}
  /* end Debug*/
  	

	// $db = $_COOKIE['db_client'];

	// $m = $_COOKIE['user_mail'];
  	$m = $_POST['mail'];


	$query_rsData = sprintf("SELECT app_user_id, app_user_name, app_user_mail, app_user_pass FROM app_users_tb WHERE app_user_mail like %s%s%s AND hideField = 0", "'%", $m , "%'");
	 // echo $query_rsData;
	// exit;

	$rsData = mysqli_query($connectMySql,$query_rsData);
	if($rsData){
		$row_rsData = mysqli_fetch_assoc($rsData);
		$totalRows_rsData = mysqli_num_rows($rsData);
	}
	if($totalRows_rsData){
		do{
			
			array_push($jsondata['user'],array(
			'id'=>$row_rsData['app_user_id'],
			'name'=>$row_rsData['app_user_name'],
			'mail'=>$m
		));
			
		}while($row_rsData = mysqli_fetch_assoc($rsData));
		mysqli_data_seek($rsData,0);
		$row_rsData=mysqli_fetch_assoc($rsData);
	}
  	// header('Content-type: application/json; charset=utf-8');
  	echo json_encode($jsondata);
	exit();
}




?>