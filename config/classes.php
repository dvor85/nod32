<?php
class Xmail
{	
	private	$attachments=array();
	public 	$count_attachments=0;
	public	$text_content_type="text/plain";	
	public	$text_encoding="Windows-1251";
	public 	$text_transfer_encoding="base64";
	public	$eol="\r\n";
	private	$headers=array();
			
	public function __construct()
	{		
		$this->count_attachments=0;
	}

	public function array_implode($glue,$array,$glue2=": ")
	{
		$res="";
		foreach ($array as $key=>$value)
		{
			$res.=$key.$glue2.$value.$glue;
		}
		return $res;
	}
	
	public function __get($name)
	{
		if (!property_exists($this,($property = ucwords($name))))
		{
			return $this->headers[$property];
		}
		if (method_exists($this, ($method = 'get_'.$name)))
		{
			return $this->$method();
		}
		else return;
	}
  
	public function __isset($name)
	{
		if (!property_exists($this,($property = ucwords($name))))
		{
			return array_key_exists($property,$this->headers);
		}
		if (method_exists($this, ($method = 'isset_'.$name)))
		{
			return $this->$method();
		}
		else return;
	}
  
	public function __set($name, $value)
	{
		if (method_exists($this, ($method = 'set_'.$name)))
		{
			$this->$method($value);
		}
		if (!property_exists($this,($property = ucwords($name))))
		{
			$this->headers[$property]=$value;
		}
	}
  
	public function __unset($name)
	{
		if (method_exists($this, ($method = 'unset_'.$name)))
		{
			$this->$method();
		}
		if (!property_exists($this,($property = ucwords($name))))
		{
			unset($this->headers[$property]);
		}
	}
	
	public function add_attachment($filetext,$attach_info)//$attachment_content_type="application/x-zip-compressed",$encode_type=null,$charset=null)
	{	
		if ($attach_info["attach_type"]=="file")
		{
			$f = @fopen($filetext,"rb");	
			if ((!$f)||(filesize($filetext)==0))
				return false;	
			if (isset($attach_info["encode_type"])&&($attach_info["encode_type"]=="base64"))
				$this->attachments[$this->count_attachments]["text"]    = chunk_split(base64_encode(fread($f,filesize($filetext))));
			else
				$this->attachments[$this->count_attachments]["text"]    = fread($f,filesize($filetext));							
			fclose($f);		
			
			if (isset($attach_info["attachment_name"]))
				$this->attachments[$this->count_attachments]["attachment_name"]=$attach_info["attachment_name"];
			else	
				$this->attachments[$this->count_attachments]["attachment_name"]=basename($filetext);
			
		}
		elseif($attach_info["attach_type"]=="text")
		{
			if (isset($attach_info["encode_type"])&&($attach_info["encode_type"]=="base64"))
				$this->attachments[$this->count_attachments]["text"]    = chunk_split(base64_encode($filetext));
			else
				$this->attachments[$this->count_attachments]["text"]    = $filetext;
				
			if (isset($attach_info["attachment_name"]))
				$this->attachments[$this->count_attachments]["attachment_name"]=$attach_info["attachment_name"];
			else	
				$this->attachments[$this->count_attachments]["attachment_name"]=(string)mt_rand();	
		}
		
		if (isset($attach_info["encode_type"]))			
			$this->attachments[$this->count_attachments]["encode_type"]=$attach_info["encode_type"];
		else
			$this->attachments[$this->count_attachments]["encode_type"]="base64";	
		
    	if (isset($attach_info["charset"]))
			$this->attachments[$this->count_attachments]["charset"]=$attach_info["charset"];	

		if (isset($attach_info["attachment_content_type"]))
			$this->attachments[$this->count_attachments]["attachment_content_type"]=$attach_info["attachment_content_type"];			
			
		if (isset($attach_info["content_id"]))			
			$this->attachments[$this->count_attachments]["content_id"]=$attach_info["content_id"];					
			
		$this->count_attachments++;
		return true;
	}
	
	public function encode_str($str)
	{
		$res="=?".$this->text_encoding."?B?".base64_encode($str)."?=";
		return $res;
	} 
	
	public function add_headers($headers)
	{				
		$this->headers=array_merge($this->headers,$headers);
	}
	
	public function clear_headers()
	{
		unset($this->headers);
		$this->headers=array();
	}
		
	public function clear_attachment()
	{
		unset($this->attachments);
		$this->attachments=array();			
		$this->count_attachments=0;		
	}
	
	public function send_message($text)
	{		
			$un= strtoupper(uniqid(time())); 
    			$mime_boundary="--".$un;    			
    			$text_mime_boundary="--".$mime_boundary;
    			$mime["Mime-Version"]="1.0";
    			$mime["Content-Type"]="multipart/mixed; boundary=\"".$mime_boundary."\"".$this->eol;
    			$this->add_headers($mime);    		
    			//$this->headers=array_merge($mime,$this->headers);
    		//print_r($this->headers);exit;
    			$msg= $text_mime_boundary.$this->eol;
    			$msg.="Content-Type: ".$this->text_content_type."; charset=".$this->text_encoding.$this->eol;
    			$msg.= "Content-Transfer-Encoding: ".$this->text_transfer_encoding.$this->eol.$this->eol;
    			//$msg.="Content-Disposition: inline".$this->eol;
    			if ($this->text_transfer_encoding=="base64")
	    			$text=chunk_split(base64_encode($text));
    			$msg.=$text.$this->eol;    			
       			for ($i=0;$i<$this->count_attachments;$i++)
       			{		
    				$msg.= $text_mime_boundary.$this->eol;	
    				$msg.= "Content-Type: \"".$this->attachments[$i]["attachment_content_type"]."\"; name=\"".$this->attachments[$i]["attachment_name"]."\"";
    				if (isset($this->attachments[$i]["charset"])) 
    			 		$msg.=" charset=\"".$this->attachments[$i]["charset"]."\"";
    			 	$msg.=$this->eol;	
    				$msg.= "Content-Transfer-Encoding: ".$this->attachments[$i]["encode_type"].$this->eol; 
    				$msg.= "Content-Disposition: attachment; filename=\"".$this->attachments[$i]["attachment_name"]."\"".$this->eol;
    				if (isset($this->attachments[$i]["content_id"]))
	    				$msg.= "Content-ID: <".$this->attachments[$i]["content_id"].">".$this->eol;
	    			$msg.=$this->eol;	
    				$msg.=$this->attachments[$i]["text"].$this->eol;					
       			}					
       			
       			if (array_key_exists("To",$this->headers))
       			{
       				$to=$this->headers["To"];
       				unset($this->headers["To"]);
       			}
       			else
	       			return false;       
		       			
			if (array_key_exists("Subject",$this->headers))
       			{
       				$subject=$this->headers["Subject"];
       				unset($this->headers["Subject"]);
       			}
       			else
	       			$subject="";

				
       			$headers=$this->array_implode($this->eol,$this->headers);
	       		//var_dump($headers);exit;	       		
	       		//echo "$headers\n\n";
	       		//echo "$msg\n";
	       		//exit;
    		return @mail($to, $subject, $msg, $headers);		
    					
	}
	
	public function __destruct()
	{
		$this->clear_attachment();
		$this->clear_headers();
	}
}




class datamysql
{
	public $link=0;	
	private $host='';
	private $user='';
	private $pass='';
	private $database='';
	private $charset;
	public $result=0;		
	public $debug=false;
	
	public function __get($name)
	{
		if (method_exists($this, ($method = 'get_'.$name)))
		{
			return $this->$method();
		}
		else return;
	}
  
	public function __isset($name)
	{
		if (method_exists($this, ($method = 'isset_'.$name)))
		{
			return $this->$method();
		}
		else return;
	}
  
	public function __set($name, $value)
	{
		if (method_exists($this, ($method = 'set_'.$name)))
		{
			$this->$method($value);
		}
	}
  
	public function __unset($name)
	{
		if (method_exists($this, ($method = 'unset_'.$name)))
		{
			$this->$method();
		}
	}
	
	public function __construct($host,$database,$user,$pass)
	{
		$this->host=$host;
		$this->user=$user;
		$this->pass=$pass;
		$this->link = mysql_connect($host,$user,$pass)	or	die("Mysql is not avalible now");
		$this->set_database($database);
		$this->set_charset("cp1251");
  	}
	
	public function __destruct()
	{
		if ($this->link)
		{
			if ($this->result)
				@mysql_free_result($this->result);
			@mysql_close($this->link);			
		}
	}
	
	public function set_charset($value)
	{
		//mysql_set_charset("$value",$this->link);
		mysql_unbuffered_query("SET CHARACTER SET '$value'", $this->link)	or	die("Character set is not set");
		$this->character_set=$value;
	}
	
	public function get_database()
	{
		return $this->database;
	}
	
	public function set_database($database)
	{
		$this->database=$database;
		mysql_select_db($database,$this->link)	or	die("database is not set");
	}
	
	public function &query($query)
	{
		//$query=mysql_escape_string($query);
		$this->result = mysql_query($query,$this->link);
		if (!$this->result)
		{
			if ($this->debug)
				die ("Query \"$query\" failed : " . mysql_error() . " Oops, it is seems bugs, please inform me about this.");	
			else
				die ("Query failed : " . mysql_error() . " Oops, it is seems bugs, please inform me about this.");	
		}		
		while ($row[]=mysql_fetch_array($this->result, MYSQL_ASSOC));
		{
		};		
		array_pop($row);		
		if (count($row)<1)
		{
			$ret=false;
			return $ret;
		}
		else
			return $row;
	}

	public function uquery($query)
	{
		//$query=mysql_escape_string($query);
		if ($this->result)		
			@mysql_free_result($this->result);
		$this->result=0;		
		if (!mysql_unbuffered_query($query,$this->link))
		{
			if ($this->debug)
				die ("Query failed: \"$query\"" . mysql_error() . " Oops, it is seems bugs, please inform me about this.");
			else
				die ("Query failed: " . mysql_error() . " Oops, it is seems bugs, please inform me about this.");
		}
		return mysql_affected_rows($this->link);
	}
	
	public function table_exists($table)
	{
		$test=(mysql_num_rows(mysql_query("SHOW TABLES LIKE '$table'"))>0);
		return $test;
	}	
} 


?>
