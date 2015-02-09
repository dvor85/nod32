<?php

//callback функция для чистки упаковки текста html
function callback_pack_content($buffer)
{
	$ret=$buffer;
	
	if (@constant("STRIPED_CONTENT"))
	{
		$pattern=array("/(\r\n\t)/", "/\s{1,}/", "/<!--.*?-->/");
		$replace=array("", " ", "");
		$ret=preg_replace($pattern,$replace,$ret);
	}
	if (@constant("PACKED_CONTENT"))
	{
		$ret=gzencode($ret, 9); //Сжатие
	}	
	return $ret;
}
//Функция разбивки на предложения
function get_sentence($sentence,$count=1)
{
	$arr_match=preg_split("/([\.\?\!]\s)/",$sentence);
	$res="";
	for ($i=0;(($i<$count)&&($i<count($arr_match)));$i++)
		$res.=ucfirst($arr_match[$i]).". ";	
	return trim($res);
}

function get_default_mod_id()
{
	global $my;
	$ret=0;
	$rets=&$my->query("select id_module from modules where mod_default=1");
	if ($rets)
		$ret=$rets[0]["id_module"];
	return $ret;	
}

function get_default_mod_name()
{
	global $my;
	$ret="";
	$rets=&$my->query("select module from modules where mod_default=1");
	if ($rets)
		$ret=$rets[0]["module"];
	return $ret;	
}

function get_mod_name($mod_id)
{
	global $my;
	$ret="";
	$rets=&$my->query("select module from modules where id_module=$mod_id");
	if ($rets)
		$ret=$rets[0]["module"];
	return $ret;	
}

function set_default_mod($mod_id)
{
	global $my;
	$my->uquery("update modules set mod_default=0");
	$my->uquery("update modules set mod_default=1 where id_module=$mod_id");
}
	
//послать смс на мегафон номер
function send_sms($numega,$subject,$message,$from)
{
	$xmail=new Xmail();
	$xmail->text_transfer_encoding="8bit";
	$head["To"]="$numega@sms.mgsm.ru";
	$head["From"]=$from;
	$head["Reply-To"]=$from;
	$head["Subject"]=$xmail->encode_str("$subject");
	$head["Bcc"]="";
    $head["Cc"]="";
    $head["X-Mailer"]="PHPMail Tool";
    $xmail->add_headers($head);
    return $xmail->send_message($message);
}



?>