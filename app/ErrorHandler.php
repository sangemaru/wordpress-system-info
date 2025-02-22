<?php 

class SI_ErrorHandler{
	//Custom Error Handling
	public static function enable_error_handling(){
		set_error_handler( array(__CLASS__,'error_handler'), E_ALL );		
		#set_exception_handler( array(__CLASS__,'error_handler'));				
		register_shutdown_function( array(__CLASS__,'shutdown_function') );	
	}
	
	public static function error_handler($errno,$str="",$file=null,$line=null,$context=null){ 
		global $SI_Errors;	
		
		$trace = debug_backtrace(10); 
		$SI_Errors[] = array(
			$errno,
			$str,
			$file,
			$line,
			$context,
			$trace
		);
		//Fatal Error?
		if($errno == E_USER_ERROR){
			echo "<h1>Fatal Error</h1>";
			//Dump the output and die
			$out = ob_get_clean();
			echo $out;
			exit;
		}				
		return false; #Just record the error, don't catch or do anything
	}		
	public static function fatal_error($error){
		global $wpdb;
		//Handle fatal error
		if( ob_get_contents() ){
			ob_clean();
		}
		echo "<h1>WP Total Details: Fatal Error Caught:</h1>";
		var_dump($error);
		echo "<h2>Backtrace</h2>";
		echo "<pre>".print_r(debug_backtrace(),true)."</pre>";
		echo "<h2>Last Query</h2>";
		echo "<pre>".print_r($wpdb->last_query,true)."</pre>";
	}
	public static function shutdown_function(){
		//Nothing in here right now
		$error = error_get_last();
		if($error !== NULL && $error['type'] === E_ERROR) {
			self::fatal_error($error);			
		}
	}

}