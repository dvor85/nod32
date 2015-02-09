<?php





//отобразить сообщение
function show_message($msg,$class=0)
{	
	echo get_message($msg,$class);
}

//получить текст сообщения
function get_message($msg,$class=0) 
{	
	$ret="";
	$msg=substr(stripslashes(trim($msg)),0,512);
	switch ($class)
	{
		case 0:
			$ret="<div class='warning'><p style=\"margin:0 16px 0 64px\">$msg</p></div>\r\n";
			break;
		case 1:
			$ret="<div class='information'><p style=\"margin:0 16px 0 64px\">$msg</p></div>\r\n";
			break;
	}
	return $ret;
}



//ip адрес
function getremoteaddr()
{
    return $_SERVER["REMOTE_ADDR"];
}

function getxforwardedaddr()
{
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
    		$res=$_SERVER["HTTP_X_FORWARDED_FOR"];
    	else $res="";	
    	return $res;
}

//форматированная дата
function form_date ($time=null)
{
	if (is_null($time))
		$time=time();
	$months=array(1=>"Января",
				"Февраля",
				"Марта",
				"Апреля",
				"Мая",
				"Июня",
				"Июля",
				"Августа",
				"Сентября",
				"Октября",
				"Ноября",
				"Декабря");	
				
	$res=sprintf("%d %s %d года",date("j",$time),$months[date("n",$time)],date("Y",$time));	
	return($res);				
}



//сменить страницу
function location($url,$target="_self")
{
	header("Location: $url");
	exit;
}

//обединение для функции send_post
function array_implode($glue,$array,$glue2=": ")
{
	$res="";
	foreach ($array as $key=>$value)
	{
		$res.=$key.$glue2.$value.$glue;
	}
	return $res;
}



//Посылка запроса POST или GET и получение результата
function send_post($url,$DATA,$method="post")
{
	$curl=curl_init();	
	
	//curl_setopt($curl, CURLOPT_HEADER, 1);
	if ($method=="post")
	{	
	    curl_setopt($curl,CURLOPT_URL,$url);
	    curl_setopt($curl,CURLOPT_POST,TRUE);		
	    curl_setopt($curl,CURLOPT_POSTFIELDS,$DATA);
	}
	elseif($method=="get")
	{
	    $url.="?".array_implode("&",$DATA,"=");
	    //$f=fopen("/home/httpd/www.infatum.ru-80/html/test","wb");
	    //fwrite($f,$url);
	    //fclose($f);
	    
	    curl_setopt($curl,CURLOPT_URL,$url);
	}
	//curl_setopt ( $curl , CURLOPT_HTTPHEADER, array("Location: $url") );
	//curl_setopt($curl,CURLOPT_MUTE,1);
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
	curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, 1);		
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);	
	
		
	$res=curl_exec($curl);	
	$err=curl_error($curl);
	curl_close($curl);
	if ($err)
		return $err;	
	return $res;
	
}
function array_filter_key( $input, $callback ) {
    if ( !is_array( $input ) ) {
        trigger_error( 'array_filter_key() expects parameter 1 to be array, ' . gettype( $input ) . ' given', E_USER_WARNING );
        return null;
    }
    
    if ( empty( $input ) ) {
        return $input;
    }
    
    $filteredKeys = array_filter( array_keys( $input ), $callback );
    if ( empty( $filteredKeys ) ) {
        return array();
    }
    
    $input = array_intersect_key( array_flip( $filteredKeys ), $input );
    
    return $input;
}


?>