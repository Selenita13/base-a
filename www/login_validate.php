<?php
/*
user.php
Version: 1
Desarrollado por: Upgrade Diseño Interactivo
MODIFICADO EL 17 agosto 2021 por Selene

*/

$rootPath = 'https://antiguapp.com.mx/_develop/mobile_app/';


require_once("../_sudiEngine/_engine/config.php");
require_once("../../../".$root_folder."/connectMySql.php");
include_once('../../../'.$root_folder.'/sudi_functions.php');

// var_dump($connectMySql);

// tabla donde se va a conectar el usuario
$GLOBALS['users_tb'] = 'app_users_tb';
	

	// SESSION_START
	if (!isset($_SESSION)) {
		session_name('_upgrade');
		session_start();
	}else{
		//header("Location: index.php "); exit;
	}


	// A) ---> VALIDATE SESSION
	if(isset($_GET['method']) && $_GET['method'] == 'validate_session'){ 
		if(isset($_SESSION['MM_Username'])){
			validate_userPass($_SESSION['MM_Username'],$_SESSION['MM_Password']);
		}elseif(isset($_COOKIE['APP_user_Cookie'])){
			validate_userPass($_COOKIE['APP_user_Cookie'],$_COOKIE['APP_pass_Cookie']);
		}else{
			echo "unsuccessful";	
		}
	}// <-- FIN DE "VALIDATE SESSION"
	
	
	// B) ---> C E R R A R   S E S I O N
	if(isset($_POST['method']) && $_POST['method'] == 'do_logout'){
		// destruimos todas las variables de sesión
		
		$_SESSION['MM_Username'] = NULL;
		unset($_SESSION['MM_Username']);
		$_SESSION['MM_Password'] = NULL;
		unset($_SESSION['MM_Password']);
		
		if(isset($_COOKIE['APP_user_Cookie'])){
			unset($_COOKIE['APP_userid_Cookie']);
			unset($_COOKIE['APP_user_Cookie']);
			unset($_COOKIE['APP_pass_Cookie']);
			setcookie('APP_userid_Cookie', null, -1, '/');
			setcookie('APP_user_Cookie', null, -1, '/');
			setcookie('APP_pass_Cookie', null, -1, '/');
		}
		
		session_destroy();
		
		echo "successful";
		exit;
	}
	
	// C) ---> E S T A B L E C E R  P A S S W O R D

	if (isset($_GET['method']) && ($_GET['method'] == 'set_new_pass')){
		
		//Consulta del código
		
		
		$requestPs_query=sprintf("SELECT * FROM sudi_passrequest_tb WHERE pass_code = '%s'", $_POST['input_passrequest']); 
		echo $requestPs_query;
			
		$request_rs = mysqli_query($connectMySql,$requestPs_query);
		$row_requestPs = mysqli_fetch_assoc($request_rs);
		$requestFoundUser = mysqli_num_rows($request_rs);
		
		$hourdiff = round((strtotime($fecha_actual) - strtotime($row_requestPs['pass_fecha']))/3600, 1);
		
		//Si la fecha es válida
		if($hourdiff <= 24 && !$row_requestPs['pass_used']){
			
			
			$theNewPassword = sha1($_POST['login_recover_pass']);
			
			// Actualizamos el password del usuario
			
			$requestPs_query=sprintf(
				"UPDATE users_tb
				SET user_password = %s
				WHERE user_id = %s",
		GetSQLValueString($theNewPassword, "text"),GetSQLValueString($row_requestPs['pass_user'], "int")); 
			
				$request_rs = mysqli_query($connectMySql,$requestPs_query);
			
			
			$requestPs_query=sprintf(
				"UPDATE sudi_passrequest_tb
				SET pass_used = '1'
				WHERE pass_code = %s",
		GetSQLValueString($_POST['input_passrequest'], "text")); 
			
				$request_rs = mysqli_query($connectMySql,$requestPs_query);
						
			
			echo "success";
			exit;
			
		}else{
			
			echo "unsuccessful";
			exit;
		}
	
		
	}


	// D) ---> R E C U P E R A R   P A S S W O R D
	if (isset($_GET['method']) && ($_GET['method'] == 'request_pass')){
		
		
		
		// $requestPs_query=sprintf("SELECT user_id, user_username, user_fname, user_mname, user_fav_name FROM sn_users_tb WHERE user_username=%s",
		// GetSQLValueString($_POST['login_recover_pass'], "text")); 

    	$requestPs_query = sprintf("SELECT * FROM %s WHERE app_user_mail = %s and hideField = 0;", $GLOBALS['users_tb'], GetSQLValueString($_POST['login_recover_pass'], "text"));


		echo $requestPs_query;
			
		
		
		$request_rs = mysqli_query($connectMySql,$requestPs_query);
		$row_requestPs = mysqli_fetch_assoc($request_rs);
		$requestFoundUser = mysqli_num_rows($request_rs);
		
		if($requestFoundUser){
			
			// switch ($row_requestPs['user_fav_name']){
			// 	case 'f':
			// 		$userName = $row_requestPs['user_fname'];
			// 	break;
			// 	case 'm':
			// 		$userName = $row_requestPs['user_mname'];
			// 	break;			
			// }
				$userName = $row_requestPs['pat_name'];
				$userMail = $_POST['login_recover_pass'];
				
				
				$pass_user = $row_requestPs['pat_id'];
				$pass_code = randomCode(10);
				
				
				$requestPs_query=sprintf(
				"INSERT INTO sudi_passrequest_tb (pass_user, pass_code) VALUES (%s, %s)",
				GetSQLValueString($pass_user, "int"),GetSQLValueString($pass_code, "text")); 
				echo $requestPs_query;
			
				$request_rs = mysqli_query($connectMySql,$requestPs_query);
				
				
				
				
				include('request_mail.php');
				
				mail($userMail, $asunto, utf8_decode($mailContent), $header);
				
			
			$requestData = array($userName,$userMail);
			print_r(json_encode($requestData));
			exit;
			
			
		}else{
			
			echo "unsuccessful";
			exit;
			
			
		}
		
		
		
	}
	
	
	// E) ---> V A L I D A R   L O G I N  F O R M
	if (($_GET['method'] == 'validate_login' && isset($_POST['login_user'])))
	{
		if (isset($_POST['login_user']))
		{		
			$loginUsername = $_POST['login_user'];
			$password = sha1($_POST['login_password']);
		}else
		{
			$loginUsername = $_COOKIE['APP_user_Cookie'];
			$password = $_COOKIE['APP_pass_Cookie'];
		}
		
		validate_userPass($loginUsername,$password);
		
		
	} // <-- FIN DE "VALIDAR LOGIN FORM"


	// F) ---> C O N S T R U I R   I N F O R M A C I O N   D E   U S U A R I O
	

	if(isset($_POST['method']) && $_POST['method'] == 'build_sudi'){
		
		$userInfo = getUserInfo($_POST['username'],$_POST['password']);
		
		$allData = array();
			$user_data = array();
			
			$allData['userData'] = array();
			$allData['menu_data'] = array();
			
			// Nombre de usuario
			$allData['userData']['user_username'] = $userInfo->user_username;
			// Nombre Favorito
			$allData['userData']['user_name'] = $userInfo->user_fav_name;
			// Apellido
			$lastName = explode(' ',trim($userInfo->user_lname));
			$allData['userData']['user_lname'] = $lastName[0];
			// Foto
			$allData['userData']['user_photo'] = $userInfo->user_photo;
			// Genero
			$allData['userData']['user_gender'] = $userInfo->user_gender;
			// Perfil
			$allData['userData']['user_profile'] = $userInfo->user_profile;
				
		print_r(json_encode($allData));
		exit;
		
	}



	// G) ---> REGISTER USER
	 if(isset($_GET['method']) && $_GET['method'] == 'register_user'){ 
	// echo "entro al metodo";
	//nombre
		$loginfname = $_POST['login_register_name']; 
	//telefono
		$loginPhone = ( $_POST['login_register_phone'] ) ? $_POST['login_register_phone'] : ''; 
	//sex
		$loginSex =  $_POST['login_register_gen'];
	//Paciente o Doctor
		//$loginCat =  $_POST['login_register_cat'];
	//Numero de tarjeta
		// $loginCard =  $_POST['login_register_card'];

		$loginUsername = $_POST['login_register_mail'];
		$password = sha1($_POST['login_register_password']);
		//$password_confirm = sha1($_POST['login_register_password_confirm']); 


/*DEBUG
		$loginfname = 'prueba'; 
	//telefono
		$loginPhone = '12345'; 
	//sex
		$loginSex = '0';
	//Paciente o Doctor
		$loginCat = '0';
	//Numero de tarjeta
		$loginCard = '1234567889';

		$loginUsername = 'mich@upgrade.com';

		$_POST['login_register_password'] = '7110eda4d09e062aa5e4a390b0a572ac0d2c0220';
		$_POST['login_register_password_confirm'] = '7110eda4d09e062aa5e4a390b0a572ac0d2c0220';

		$password = sha1($_POST['login_register_password']);
		$password_confirm = sha1($_POST['login_register_password_confirm']);
*/
		
		// echo("datos: "+$loginfname + $loginSex + $loginPhone   +$loginUsername +$password + $loginCard);

		
		$query_rsValidate = sprintf("SELECT * FROM %s WHERE pat_mail LIKE '%s' AND hideField = 0;", $GLOBALS['users_tb'], $loginUsername); 
		//
		  // var_dump($query_rsValidate);

		$rsValidate = mysqli_query($GLOBALS['connectMySql'],$query_rsValidate);
		$row_rsValidate = mysqli_num_rows($rsValidate);
		$totalRows_rsValidate = mysqli_fetch_assoc($rsValidate);
		
		// var_dump($totalRows_rsValidate);
		if( $totalRows_rsValidate ) {

			echo 'Ya existe esté correo';
			// echo '0';


		} else {
			$query_rsRegister = sprintf("
				INSERT INTO %s (pat_mail, 
								pat_name, 
								pat_phone,
								pat_password,
								pat_sex
								) 

				VALUES ('%s', '%s', '%s', '%s', %s);",

				$GLOBALS['users_tb'], 
				$loginUsername, 
				$loginfname, 
				$loginPhone, 
				$password,
				$loginSex
			); 

		
			$rsRegister = mysqli_query($GLOBALS['connectMySql'],$query_rsRegister);
			$main_id = mysqli_insert_id($GLOBALS['connectMySql']);
			
		
			
			if ( $rsRegister ) {
				echo 'ok';
				//include ('mailRegister.php');
				//validate_userPass($loginUsername,$password);
			} else {
				 // echo $query_rsRegister;
				echo 'unsuccessful';

			}
			
		}
			
		
		exit;
	 }


	
	// ---> F U N C I O N E S
	// SUDI_LOGIN_F.1 --> VALIDATE_USER_AND_PASSWORD
	function validate_userPass($loginUsername,$password){
		
		$LoginRS__query=sprintf("SELECT app_user_id, app_user_mail, app_user_pass FROM ". $GLOBALS['users_tb'] ." WHERE app_user_mail=%s AND app_user_pass=%s AND hideField = 0",
		GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 

		$LoginRS = mysqli_query($GLOBALS['connectMySql'],$LoginRS__query);
		$loginFoundUser = mysqli_num_rows($LoginRS);
		$loginData = mysqli_fetch_assoc($LoginRS);
		
		
		//Si el login es exitoso 
		if ($loginFoundUser)
		{

		$_COOKIE['APP_userid_Cookie'] = $loginData['pat_id'];
		$loginUserid = $_COOKIE['APP_userid_Cookie'];

			//Si nos pide que guardemos las cookies sesión
			//if(isset($_POST['saveSession']) && $_POST['saveSession'] == 'Yes')
			//{	
				setcookie("APP_userid_Cookie", $loginUserid, time() + 2419200,'/');
				setcookie("APP_user_Cookie", $loginUsername, time() + 2419200,'/');
				setcookie("APP_pass_Cookie", $password, time() + 2419200,'/');
				


			//}
			if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
			//Declaramos el nombre de usuario en la sesión
			// $_SESSION['MM_Usernid'] = $loginUserid;
			$_SESSION['MM_Username'] = $loginUsername;
			$_SESSION['MM_Password'] = $password;
			$sessionData = array($loginUserid,$loginUsername,$password);
			// $sessionData = array($loginUsername,$password);

			print_r(json_encode($sessionData));
			
			exit;
		//Si no coincide el login
		}else{
			echo 'unsuccessful';
		}
	} // <--- Fin de validate_userPass();


?>

