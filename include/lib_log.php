<?php
	#
	# $Id$
	#

	$GLOBALS[log_html_colors] = array(
		'db'		=> '#eef',
		'smarty'	=> '#efe',
		'http'		=> '#ffe',
	);

	$GLOBALS[log_handlers] = array(
		'notice'	=> array('html'),
		'error'		=> array('error_log'),
	);


	###################################################################################################################

	#
	# public api
	#

	function log_fatal($msg){
		die("FATAL ERROR: ".$msg);
	}

	function log_error($msg){
		_log_dispatch('error', $msg);
	}


	function log_notice($type, $msg, $time=-1){
		_log_dispatch('notice', $msg, array(type => $type, time => $time));
	}


	function _log_dispatch($level, $msg, $more = array()){

		if ($GLOBALS[log_handlers][$level]){

			foreach ($GLOBALS[log_handlers][$level] as $handler){

				call_user_func("_log_handler_$handler", $level, $msg, $more);
			}
		}
	}

	###################################################################################################################

	#
	# log handlers
	#

	function _log_handler_error_log($level, $msg, $more = array()){
		$page = $GLOBALS[HTTP_SERVER_VARS][REQUEST_URI];

		if ($more[type]){
			$msg = "[$more[type]] $msg";
		}

		$msg = str_replace("\n", ' ', $msg);

		error_log("[$level] $msg ($page)");
	}

	function _log_handler_html($level, $msg, $more = array()){
		if (!$_GET[debug]) return;

		$type = $more[type] ? $more[type] : '';

		$color = $GLOBALS[log_html_colors][$type] ? $GLOBALS[log_html_colors][$type] : '#eee';

		echo "<div style=\"background-color: $color; margin: 1px 1px 0 1px; border: 1px solid #000; padding: 4px; text-align: left\">";

		if ($type) echo "[$type] ";

		echo HtmlSpecialChars($msg);

		if ($more[time] > -1) echo " ($more[time] ms)";

		echo "</div>\n";

	}

	###################################################################################################################
?>