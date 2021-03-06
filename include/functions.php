<?php
/**
 * Utility functions class that can be used anywhere by including this file.
 * 
 * Handy for a lot of tasks that are repeated over time.
 * 
 * @author Fabio Crispino
 */


function enable_errors(){
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}

function get_parameter($key,$default=null){
	return isset($_GET[$key]) ? $_GET[$key] : $default;
}

function post_parameter($key,$default=null){
	return isset($_POST[$key]) ? $_POST[$key] : $default;
}

function execute_atomically($function_to_execute){
	if($lock=flock('./lockfile', LOCK_EX)){
		try{$function_to_execute();}
		catch(Exception $e){echo $e;}
		finally{flock($lock, LOCK_UN);}
	}
}

function debug_var($var){
	return var_export($var,true);
}

function json($json_object){
	header('Content-Type: application/json');
	return json_encode($json_object);	
}

function echo_json($json_object,$prettify=false){
	echo trim(json($json_object,$prettify), '"');
}

function rrmdir($dir) { 
	if(is_dir($dir)){ 
	  $objects = scandir($dir); 
	  foreach($objects as $object){ 
			if($object != "." && $object != ".."){ 
				$target=$dir."/".$object;
				if(is_dir($target)) rrmdir($target);
				else unlink($target); 
			} 
	  }
	  rmdir($dir); 
	} 
}

function custom_rmdir($dir,$del_subdirs=false){
		$di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
		$ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
		foreach ( $ri as $file ) {
			$file->isDir() ? ($del_subdirs==true ? rmdir($file) : NULL) : unlink($file);
		}
}

function unique_random_string($length=16){
	if(function_exists('random_bytes'))
		return bin2hex(random_bytes($length));
	else
		return bin2hex(openssl_random_pseudo_bytes($length));
}

function sql_datetime($precision=0){
	$timestamp_micro = microtime();
  list($msec, $sec) = explode(' ', $timestamp_micro);
  $msec = explode(".",$msec);
  $msec = $msec[1];
	$msec = substr($msec,0,$precision);
	$append='';
	if($precision>0) $append=".".$msec;
	return date("Y-m-d H:i:s".$append);
}

function array_equals(array $array1, array $array2){
	$are_equals=true;
	if(count($array1) != count($array2)) return false;
	else{
		foreach($array1 as $key=>$value){
			if(!isset($array2[$key]) || $array1[$key]!=$array2[$key]){
				$are_equals=false;
				break;
			}
		}
	}
	return $are_equals;
}

function set_secure_cookie($name, $content, $expire=0){
	setcookie($name, $content, $expire, '', isset($_SERVER["HTTPS"]), true);
}

function get_secure_cookie($name){
	if(isset($_COOKIE[$name])){
		return $_COOKIE[$name];
	}else{
		return null;
	}
}

// Enable errors
enable_errors();
?>