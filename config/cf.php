<?php

define("MYSQL_HOST","localhost");	
define("MYSQL_USER", "user");
define("MYSQL_PASS","pass");
define("MYSQL_BASE", "eset");	

if ($_SERVER["SERVER_PORT"]!=443)
	$port="http://";
else
	$port="https://";
@define("DOCUMENT_HOST",$port.$_SERVER["HTTP_HOST"]);
$DOC_ROOT=($_SERVER["DOCUMENT_ROOT"]!="")?$_SERVER["DOCUMENT_ROOT"]:realpath(realpath(dirname($argv[0]))."/../");
@define("DOCUMENT_ROOT",$DOC_ROOT);
@define("CURRENT_URL",$port.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
@define("HOSTNAME",$_SERVER["SERVER_NAME"]);

@define("PACKED_CONTENT",false);
@define("STRIPED_CONTENT",false);


?>
