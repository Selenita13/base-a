<?php
/*
feed.php
Version: 1
Desarrollado por: Upgrade Diseño Interactivo
Objetivo - Extraer la información de los eventos, noticias y torneos para mostrarlos en la página principal de la app.
Consulta: Varias tablas según parametros POST
	[A] DOUBLE FEED 				PRINCIPAL    ( Double A, Double B, Double C )
	[B] DOUBLE DETAIL    			DETALLE DE NOTICIA
	[C] Data Feed   				FEED ACTIVIDADES  unit_id y valor de la unidad (oz,gal,litres.)
	[D] DETALLE DE ACTIVIDAD
	[E] DETALLE DE UNIDADES SELECT  Tipo de unidad: 	 1: Data, 2: Catalog, 3: subprocess

Desarrollador: Selene Herrera 
Fecha: 17 agosto 2021


*/

// config
$rootPath = 'https://antiguapp.com.mx/_develop/mobile_app/';


require_once("../_sudiEngine/_engine/config.php");
require_once("../../../".$root_folder."/connectMySql.php");
include_once('../../../'.$root_folder.'/sudi_functions.php');


$debug = false;

  	


  	$m = $_POST['mail'];


// [A] DOUBLE FEED
	// #Genera la lista de dobles
	// #Se activa: Si existe "POST - double_list"

if($_POST['double_list']){
  $jsondata = array();

  	//hoy
	$today = date("Y-m-d", strtotime("today"));
  /* C O D E */
  $jsondata['doubles']=array();
	
  	$user_id = $_POST['user_id'];

//    $query_rsData = sprintf("SELECT * FROM app_multi_doubles_users_tb as multi 
// INNER JOIN app_users_tb ON app_users_tb.app_user_id = multi.app_mus_user_id
// INNER JOIN mh_doubles_tb ON mh_doubles_tb.dbl_id = multi.app_mus_dbl_id
// LEFT JOIN  app_phases_tb ON mh_doubles_tb.dbl_step = app_phases_tb.phase_id 
// WHERE app_mus_user_id = %s
// AND mh_doubles_tb.hideField = 0 ORDER BY dbl_label ASC", $user_id , '0');


	$query_rsData = sprintf("SELECT dbl_id, dbl_label, phase_icon, mh_doubles_tb.dbl_folio , mh_doubles_tb.dbl_step ,mh_doubles_tb.dbl_finish FROM mh_doubles_tb, app_phases_tb WHERE dbl_step = phase_id AND mh_doubles_tb.hideField = %s AND dbl_app_users LIKE %s
	ORDER BY dbl_label ASC", '0' , '"%['.$user_id .']%"');

// var_dump($query_rsData);
// exit;

	$rsData = mysqli_query($connectMySql,$query_rsData);
	if($rsData){
		$row_rsData = mysqli_fetch_assoc($rsData);
		$totalRows_rsData = mysqli_num_rows($rsData);
	}
	if($totalRows_rsData){

		do{

/*
			$query_rsData2 = sprintf("select * FROM app_values2_tb WHERE app_values2_tb.v_folio like '%s' 
									AND v_dbl_id = %s 
									AND
										(app_values2_tb.v_value LIKE 'RESTART PROCESS'
										OR 
									 	app_values2_tb.v_value LIKE 'START PROCESS') 
									ORDER BY date_add DESC", $row_rsData['dbl_folio'],$row_rsData['dbl_id'], $row_rsData['dbl_folio'], $row_rsData['dbl_folio']);

			$rsData2 = mysqli_query($connectMySqluser,$query_rsData2);
			$row_rsData2 = mysqli_fetch_assoc($rsData2);
			$totalRows_rsData2 = mysqli_num_rows($rsData2);
			 // echo $query_rsData2;

			$date_start = $row_rsData2['date_add'];
			$date_start_s = explode(" ", $date_start);


			$query_rsData3 = sprintf("select * FROM app_values2_tb WHERE app_values2_tb.v_folio like '%s' ORDER BY date_add DESC LIMIT 1", $row_rsData['dbl_folio']);

			$rsData3 = mysqli_query($connectMySqluser,$query_rsData3);
			$row_rsData3 = mysqli_fetch_assoc($rsData3);
			$totalRows_rsData3 = mysqli_num_rows($rsData3);

			 if ($totalRows_rsData3) {
			 	//si tiene almenos un registro insertado, ponle la fecha del último registro insertado
			 	$date_end = $row_rsData3['date_add'];
				$date_end_s = explode(" ", $date_end);
			 } else{
			 	//sino, pon el de START 
			 	$date_end_s = $date_start_s;
			 }
		

			if($row_rsData['dbl_step'] == 0 || $row_rsData['dbl_step'] == '0'){
				//si está iniciando manda 0 porque aún no empieza los days
				
				$diff = 'false';
			}else{
				// dif dates between start and last record insert
				$date1 = new DateTime($date_start_s[0]);
				$date2 = new DateTime($today);

				$diff = $date1->diff($date2);
				$diff = $diff->days;
				// will output 2 days
			
			}

		    $dif_date = date_diff($date_start_s[0], $date_end_s[0]);
*/
			array_push($jsondata['doubles'],array(
			'id'=>$row_rsData['dbl_id'],
			'name'=>$row_rsData['dbl_label'],
			'step_img'=>$row_rsData['phase_icon'],
			'step' => $row_rsData['dbl_step'],
			'btn' => $row_rsData['dbl_finish'],
			'folio' =>  $row_rsData['dbl_folio']

			));


			// ,
			// 'date_start' => $date_start_s[0],
			// 'date_end' => $date2,
			// 'date_dif' => $diff

			
		}while($row_rsData = mysqli_fetch_assoc($rsData));

	}
  	// header('Content-type: application/json; charset=utf-8');
  	echo json_encode($jsondata);
  	// echo $query_rsData;
	exit();
}


// [B] DOUBLE DETAIL
	// #Extrae el detalle uina vez que seleccionas el doble
	// #Se activa: Si existe "POST - double_detail"

if($_POST['double_detail']){
  $jsondata = array();
  /* C O D E */
  $jsondata['double']=array();
	
	$query_rsData = sprintf("SELECT * FROM mh_doubles_tb, app_phases_tb WHERE dbl_step = phase_id AND dbl_id = %s;", $_POST['current_d']);

	$rsData = mysqli_query($connectMySql,$query_rsData);
	if($rsData){
		$row_rsData = mysqli_fetch_assoc($rsData);
		$totalRows_rsData = mysqli_num_rows($rsData);
	}
	if($totalRows_rsData){

			array_push($jsondata['double'],array(
			'id'=>$row_rsData['dbl_id'],
			'number'=>$row_rsData['dbl_number'],
			'name'=>$row_rsData['dbl_label'],
			'step_img'=>$row_rsData['phase_icon'],
			'step_name'=>$row_rsData['phase_label'],
			'step_name_spanish'=>$row_rsData['phase_label_spanish'],
			'step'=>$row_rsData['dbl_step'],
			'statusstep'=>$row_rsData['dbl_finish']

			));
		}
	header('Content-type: application/json; charset=utf-8');
  	echo json_encode($jsondata);
	exit();
}
 

// [C] Data Feed
	// #Genera la lista de registros posibles segín el doble
	// #Se activa: Si existe "POST - data_list"

if($_POST['data_list']){
	// var_dump($_POST);
  $jsondata = array();
  /* C O D E */
  $jsondata['records']=array();

  	//hoy
	$today = date("Y-m-d", strtotime("today"));
	$today_now = date("Y-m-d H:i:s");
	
	$query_rsData = "SELECT * FROM app_datas_tb WHERE data_steps LIKE '%[".$_POST['current_step']."]%'  AND hideField = 0;";
	$rsData = mysqli_query($connectMySql,$query_rsData);
	if($rsData){
		$row_rsData = mysqli_fetch_assoc($rsData);
		$totalRows_rsData = mysqli_num_rows($rsData);
	}
	
	if($totalRows_rsData){

		
		do{

			$unites = explode('[',$row_rsData['unit_data_id']);
                    $arrayUnites = array();
                    $arrayUnit = array();


                for( $i = 0; $i < sizeof($unites); $i++ ) {
                    if( $unites[$i] > 0 ) {
                        $u = explode(']',$unites[$i]);
                        array_push($arrayUnites,$u[0]);
                    }
                }
   
                for( $i = 0; $i < sizeof($arrayUnites); $i++ ) {
                    $query_rsUnit = sprintf("SELECT unit_value, unit_id FROM app_units_tb WHERE unit_id = %s AND hideField = 0;", $arrayUnites[$i] );
                 
                    //debug echo $query_rsService; //exit;
                    $rsUnit = mysqli_query($connectMySql,$query_rsUnit);
                    $row_rsUnit = mysqli_fetch_assoc($rsUnit);
                    $totalRows_rsUnit = mysqli_num_rows($rsUnit);

                     if( $totalRows_rsUnit ) { 
				        do {
                  			array_push($arrayUnit,array(
                  			'u_id' => $row_rsUnit['unit_id'],
                  			'u_value' => $row_rsUnit['unit_value']));

                  			
				     	} while( $row_rsProduct = mysqli_fetch_assoc($rsUnit) ); }

                   

                  
                }
//   $date1.' 00:00:00', $date2.' 23:59:59'
//   ["data_list"]=> string(4) "true"
//   ["current_step"]=>  string(1) "5"
//   ["current_d"]=> string(2) "22"
//   ["client"]=> string(4) "demo"
//   ["mail"]=> string(24) "selene@foodsafetycts.com"

                //'query' => $query_rsData2

		$query_rsData3 = sprintf("SELECT * FROM mh_doubles_tb, app_phases_tb WHERE dbl_step = phase_id AND dbl_id = %s;", $_POST['current_d']);
		$rsData3 = mysqli_query($connectMySql,$query_rsData3);
		$row_rsData3 = mysqli_fetch_assoc($rsData3);
		$totalRows_rsData3 = mysqli_num_rows($rsData3);

		$query_rsData2 = sprintf("select * FROM app_values2_tb WHERE app_values2_tb.v_folio like '%s' AND v_dbl_id = %s AND v_data = %s AND v_phase = %s AND date_add between '%s' AND '%s' ORDER BY date_add DESC LIMIT 1",$row_rsData3['dbl_folio'], $_POST['current_d'], $row_rsData['data_id'] , $row_rsData3['dbl_step'] , $today.' 00:00:00', $today . ' 23:59:59');
		$rsData2 = mysqli_query($connectMySql,$query_rsData2);
		$row_rsData2 = mysqli_fetch_assoc($rsData2);
		$totalRows_rsData2 = mysqli_num_rows($rsData2);
	  //echo $query_rsData2;
		if($totalRows_rsData2){
			array_push($jsondata['records'],array(
			'id'=>$row_rsData['data_id'],
			'name'=>$row_rsData['data_label'],
			'name_spanish'=>$row_rsData['dbl_label_spanish'],
			'units'=> $arrayUnit,
			'done' => '1'
			));
		}else{

			array_push($jsondata['records'],array(
			'id'=>$row_rsData['data_id'],
			'name'=>$row_rsData['data_label'],
			'name_spanish'=>$row_rsData['dbl_label_spanish'],
			'units'=> $arrayUnit,
			'done' => '0'
			));


		}
			

			
		}while($row_rsData = mysqli_fetch_assoc($rsData));
		mysqli_data_seek($rsData,0);
		$row_rsData=mysqli_fetch_assoc($rsData);
	}
	
	
  	header('Content-type: application/json; charset=utf-8');
  	echo json_encode($jsondata);
	exit();
}

// [D] INSERT RECORD
	// #Inserta el registro
	// #Se activa: Si existe "POST - insert_record"

if($_POST['insert_record']){
  $jsondata = array();
  /* C O D E */
  $jsondata['response']=array();
  $arraySubprocess = array();  
	
	//insert_record : true, double : current_double, record : current_record, data : last_data
  	// el v_type_unit nos indica 1: Data, 2: Catalog, 3: subprocess
  	// el v_fav_unit indica el id de la tabla 
  	// DATA 	Ejemplo 	WATERING	 type : 1, v_unit_id = 12 (data_id),  v_fav_unit = 0 
  	// CATALOG  Ejemplo		SUPPLEMENT 	 type : 2, v_unit_id =  4 (data_id),  v_fav_unit = 0 
  	$double_id =  $_POST['double'];

	

	$insert_unitCat = ($_POST['unit_cat']) ? $_POST['unit_cat'] :'0';   //si es tipo Catalog recibe el post, sino es tipo Data 0

	$insert_bed = ($_POST['bed']) ? $_POST['bed'] :'0';   //si el data es id 16 or 20 recibe el post, sino es tipo Data 0
	// $data = ($_POST['data']) ? $_POST['data'] :'';
	$data = ($_POST['data']) ? $_POST['data'] :''; 

	//comentarios adicionales
	// $v_comments = ($_POST['v_comments']) ? $_POST['v_comments'] :''; 
	$v_comments = $_POST['v_comments']; 

	//subprocesos
	$insert_length = ($_POST['sub_length']) ? $_POST['sub_length'] :'';   //si recibimos el post, sino es 0 (Sólo el tipo subprocesso tiene valores: 1,2,3)
	$insert_color = ($_POST['sub_color']) ? $_POST['sub_color'] :'';   //si recibimos el post, sino es 0 (Sólo el tipo subprocesso tiene valores: 1,2,3)
	$insert_squeeze = ($_POST['sub_squeeze']) ? $_POST['sub_squeeze'] :'';   //si recibimos el post, sino es 0 (Sólo el tipo subprocesso tiene valores: 1,2,3)
	$insert_density = ($_POST['sub_density']) ? $_POST['sub_density'] :'';   //si recibimos el post, sino es 0 (Sólo el tipo subprocesso tiene valores: 1,2,3)
	
	//recibe el id   del valor del catálogo
	$insert_value_cat = $_POST['value_cat'];

	// recibe el id_unit
	if($_POST['type'] == 1){
		$insert_unit = ($_POST['unit']) ? $_POST['unit'] : 0;

	}

	//si el type es diferente a (2) catalogo entonces toma la unidad que se le manda
	if($_POST['type'] == 2){
		$insert_unit = ($_POST['unit']) ? $_POST['unit'] : 0;

	}


	if($_POST['type'] == 4){
	//si el type es (2) catalogo entonces haz el siguiente if

		//si es el record (35) Harvest agregale a la unidad que sea pounds (unit_id 31)
		if ($_POST['record'] == 35){
			$insert_unit = 31;
		}
		
		//si es el record (12) Watering agregale a la unidad que sea gallons (unit_id 7)
		if ($_POST['record'] == 12){
			$insert_unit = 7;
		}

		//si es el record (9) Fly Count agregale a la unidad que sea units (unit_id 4)
		if ($_POST['record'] == 9){
			$insert_unit = 4;
		}

		//si es el record (29) Pest incindence agregale a la unidad que sea units (unit_id 38)
		if ($_POST['record'] == 29){
			$insert_unit = 38;
		}

		//si es el record (33) Post steaming agregale a la unidad que sea units (unit_id 24)
		if ($_POST['record'] == 33){
			$insert_unit = 24;
		}

		//si es el record (71) Empty steaming agregale a la unidad que sea units (unit_id 24)
		if ($_POST['record'] == 71){
			$insert_unit = 24;
		}

	}
	
	if($_POST['type'] == 3){
		$insert_unit = ($_POST['unit']) ? $_POST['unit'] : 0;

	}

	//tiempo del dispositivo
  	$time =  $_POST['date'];

	if (($insert_length == '') && ($insert_color == '') && ($insert_squeeze == '') && ($insert_density == '')) {
		$ser_subp =  '';
	}else{
	 	array_push($arraySubprocess,array(
	            'length' =>  $insert_length,
	            'color' =>   $insert_color,
	            'squeeze' => $insert_squeeze,
	            'density' => $insert_density
	    ));

	 	$ser_subp = serialize($arraySubprocess);
	    
 	}



 	

			$query_rsData = sprintf("INSERT INTO app_values2_tb 
			(v_dbl_id, 
			v_unit_id, 
			v_value_cat,
			v_unit_cat_id,
			v_phase, 
			v_data,

			v_value,

			v_type_unit, 
			user_add,
			v_folio,
			v_serial_sub,
			date_add) 
			VALUES (%s, %s, %s, %s, '%s', %s,
			 
			 %s,

			  %s, '%s', '%s','%s', '%s')", 
			$double_id, 
			$insert_unit, 
			$insert_value_cat,
			$insert_unitCat,
			$_POST['step'], 
			$_POST['record'], 

			$data, 

			$_POST['type'], 
			$_POST['user_id'],
			$row_rsSelectCode['dbl_folio'],
			$ser_subp,

			$time
			);

		if(mysqli_query($connectMySql,$query_rsData)){
			 echo '1';
			  // echo $query_rsData;
			}else{
			  echo '0';
				// echo $query_rsData;
			}
		
		exit();

	

} //end insert_record



// [E] Data Units
	// #Genera la lista de las unidades del double selecionado
	// #Se activa: Si existe "POST - data_units"

if($_POST['data_units']){

  $jsondata = array();
  
  $jsondata['units']=array();

	$query_rsData = "SELECT * FROM app_datas_tb WHERE data_id = '".$_POST['current_units']."';";
	
	// echo $query_rsData;
	$rsData = mysqli_query($connectMySql,$query_rsData);
	if($rsData){
		$row_rsData = mysqli_fetch_assoc($rsData);
		$totalRows_rsData = mysqli_num_rows($rsData);
	}
	//var_dump($rsData);

	if($totalRows_rsData){
		do{

				switch ($row_rsData['type_data']) {

				case 1:
					//TIPO 1 si el dato es tipo Data
	                $fav_unit =  $row_rsData['fav_unit'];

	                if( $fav_unit  == ''){

					 	$jsondata['units'] =  0;
					 
					
					}else{

		                $arrayUnites = array();
	                    $arrayUnit = array();


	                    $query_rsUnit = sprintf("SELECT unit_value, unit_id FROM app_units_tb WHERE unit_id = %s AND hideField = 0;", $fav_unit );
	                    // echo $query_rsUnit;
	                    $rsUnit = mysqli_query($connectMySql,$query_rsUnit);
	                    $row_rsUnit = mysqli_fetch_assoc($rsUnit);
	                    $totalRows_rsUnit = mysqli_num_rows($rsUnit);

	                    if( $totalRows_rsUnit ) { 
					        do {
	                  			array_push($jsondata['units'],array(
	                  			'u_id' => $row_rsUnit['unit_id'],
	                  			'u_value' => $row_rsUnit['unit_value'],
	                  			'u_data' => $_POST['current_units'],
	                  			'u_type' => $row_rsData['type_data'],
	                  			'u_min' => $row_rsData['data_min'],
	                  			'u_max' => $row_rsData['data_max']


	                  		));

					     	} while( $row_rsProduct = mysqli_fetch_assoc($rsUnit) ); 
					    }
					}


					
					/*FIN TIPO 1 */
					break;

					case 2:
					/*TIPO 2 si el dato es tipo catálogo			todos los id de los catalogos van a ser a partir cat_id 	1000 */

					$query_rsIndexCatalogs = sprintf("SELECT * FROM mh_catalogs_tb WHERE cat_id = '".$row_rsData['data_unit_id']."' AND hideField = 0;" );
					

					$rsIndexCatalogs = mysqli_query($connectMySqluser,$query_rsIndexCatalogs);
	               	$row_rsIndexCatalogs = mysqli_fetch_assoc($rsIndexCatalogs);
	                $totalRows_rsIndexCatalogs = mysqli_num_rows($rsIndexCatalogs);


	                if( $totalRows_rsIndexCatalogs ) { 

	                	//se construye una consulta para traer el catálogo correspondiente Ej. Supplement, Compost, etc.
	            
	                	$query_rsCatalog = sprintf("SELECT * FROM %s WHERE 1 and hideField = 0;", $row_rsIndexCatalogs['cat_table']);
						//echo  $query_rsCatalog;

	                	$rsCatalog = mysqli_query($connectMySqluser,$query_rsCatalog);
	                    $row_rsCatalog = mysqli_fetch_assoc($rsCatalog);
	                    $totalRows_rsCatalog = mysqli_num_rows($rsCatalog);

	                    //var_dump($row_rsCatalog);

		                    if( $totalRows_rsCatalog ) { 
						        do {
		                  			array_push($jsondata['units'],array(
		                  			'u_id' => $row_rsCatalog['cat_id'],
		                  			'u_value' => $row_rsCatalog['cat_value'],
		                  			'u_type' => $row_rsData['type_data'],
		                  			'u_data' => $_POST['current_units'],
		                  			'u_catid' => $row_rsData['data_unit_id']) );

						    	} while( $row_rsCatalog = mysqli_fetch_assoc($rsCatalog) ); 
						    }
	                } //cierre if $totalRows_rsIndexCatalogs
	                break;
					/*FIN TIPO 2*/


					//TIPO 3 si el dato es tipo SUBPROCESO
					case 3:

			

						array_push($jsondata['units'],array(
		                  			'u_type' => '3',
		                  			'u_serializa' => $row_rsData['data_serialize'] ));
					//FIN TIPO 3

					break;   

 
					//TIPO 4 si el dato es tipo Híbrido   u_unit_id
					case 4:


					$query_rsIndexCatalogs = sprintf("SELECT * FROM mh_catalogs_tb WHERE cat_id = '".$row_rsData['data_unit_id']."' AND hideField = 0;" );
					

					$rsIndexCatalogs = mysqli_query($connectMySqluser,$query_rsIndexCatalogs);
	               	$row_rsIndexCatalogs = mysqli_fetch_assoc($rsIndexCatalogs);
	                $totalRows_rsIndexCatalogs = mysqli_num_rows($rsIndexCatalogs);


	                if( $totalRows_rsIndexCatalogs ) { 

	                	//se construye una consulta para traer el catálogo correspondiente Ej. Supplement, Compost, etc.
	                
	                	$query_rsCatalog = sprintf("SELECT * FROM %s WHERE hideField = 0;", $row_rsIndexCatalogs['cat_table']);
						//echo  $query_rsCatalog;

	                	$rsCatalog = mysqli_query($connectMySqluser,$query_rsCatalog);
	                    $row_rsCatalog = mysqli_fetch_assoc($rsCatalog);
	                    $totalRows_rsCatalog = mysqli_num_rows($rsCatalog);

	                    //var_dump($row_rsCatalog);

		                    if( $totalRows_rsCatalog ) { 

		                    		$valor_temp = $row_rsCatalog['cat_value'];

								    	
								        do {
				                  			array_push($jsondata['units'],array(
				                  			'u_id' => $row_rsCatalog['cat_id'],
				                  			'u_value' => $row_rsCatalog['cat_value'],
				                  			'u_type' => $row_rsData['type_data'],
				                  			'u_data' => $_POST['current_units'],
				                  			'u_catid' => $row_rsData['data_unit_id'],
				                  			'u_unit_id' => $row_rsData['fav_unit']) );

								    	} while( $row_rsCatalog = mysqli_fetch_assoc($rsCatalog) ); 
		                    	}


						    


						    
	                } //cierre if $totalRows_rsIndexCatalogs

					//FIN TIPO 4

					break;   
	        }
			
		}while($row_rsData = mysqli_fetch_assoc($rsData));
		mysqli_data_seek($rsData,0);
		$row_rsData=mysqli_fetch_assoc($rsData);
	}
	
  	header('Content-type: application/json; charset=utf-8');
  	echo json_encode($jsondata);
	exit();
}



?>