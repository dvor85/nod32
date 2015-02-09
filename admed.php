<?php
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
	require_once("config/cf.php");	
	require_once("config/classes.php");	
	require_once("config/functions.php");
	echo "<html>";	
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\" >\r\n";	
	echo "<head>";
	echo "<style>";
	echo "input[type='text'],input[type='password'] {width:100%;height:100%;background-color:#eee;border-width:0}";
	echo "th {background-color:#cccccc;}";
	echo "table {background-color:#eee;}";
	echo "td {font-size:14px;}";
	echo "</style>";	
	echo "</head>";
	echo "<body style='width:1024px;margin:auto'>";
	
	$my=new datamysql(MYSQL_HOST,MYSQL_BASE,MYSQL_USER,MYSQL_PASS);
	
	if (isset($_POST["edit"]))
	{
		$id_user=(int)substr(strip_tags(stripslashes(trim($_POST["id_user"]))),0,9);
		$user=substr(strip_tags(stripslashes(trim($_POST["user"]))),0,255);
		$pass=substr(strip_tags(stripslashes(trim($_POST["pass"]))),0,16);
		$login=substr(strip_tags(stripslashes(trim($_POST["login"]))),0,16);
		$email=substr(strip_tags(stripslashes(trim($_POST["email"]))),0,255);
		$user_start_date=($ust=substr(strip_tags(stripslashes(trim($_POST["user_start_date"]))),0,32))?$ust:date("Y-m-d H:i:s");
		$user_days_enable=(int)substr(strip_tags(stripslashes(trim($_POST["user_days_enable"]))),0,9);
		$logins_per_day=(int)substr(strip_tags(stripslashes(trim($_POST["logins_per_day"]))),0,9);
		$my->uquery("update users set name='$user',login='$login',email='$email',user_start_date='$user_start_date',user_days_enable='$user_days_enable',logins_per_day='$logins_per_day' where id_user=$id_user");
	}
	elseif (isset($_POST["editpass"]))
	{
		$id_user=(int)substr(strip_tags(stripslashes(trim($_POST["id_user"]))),0,9);
		$user=substr(strip_tags(stripslashes(trim($_POST["user"]))),0,255);
		$pass=substr(strip_tags(stripslashes(trim($_POST["pass"]))),0,16);
		$login=substr(strip_tags(stripslashes(trim($_POST["login"]))),0,16);
		$email=substr(strip_tags(stripslashes(trim($_POST["email"]))),0,255);
		$user_start_date=substr(strip_tags(stripslashes(trim($_POST["user_start_date"]))),0,32);
		$user_days_enable=(int)substr(strip_tags(stripslashes(trim($_POST["user_days_enable"]))),0,9);
		$logins_per_day=(int)substr(strip_tags(stripslashes(trim($_POST["logins_per_day"]))),0,9);
		$my->uquery("update users set pass=md5('$pass') where id_user=$id_user");
		echo "<table border='1' cellspacing='0' cellpadding='5'>";
		echo "<tr><th>Имя</th><td>$user</td></tr>";
		echo "<tr><th>Логин</th><td>$login</td></tr>";
		echo "<tr><th>Пароль</th><td>$pass</td></tr>";
		echo "</table><br>";
	}
	elseif(isset($_POST["del"]))
	{
		$id_user=(int)substr(strip_tags(stripslashes(trim($_POST["id_user"]))),0,9);
		$my->uquery("delete from users where id_user=$id_user");
	}
	elseif (isset($_POST["add"]))
	{
		$user=substr(strip_tags(stripslashes(trim($_POST["user"]))),0,255);
		$pass=substr(strip_tags(stripslashes(trim($_POST["pass"]))),0,16);
		$login=substr(strip_tags(stripslashes(trim($_POST["login"]))),0,16);
		$email=substr(strip_tags(stripslashes(trim($_POST["email"]))),0,255);
		$user_start_date=($ust=substr(strip_tags(stripslashes(trim($_POST["user_start_date"]))),0,32))?$ust:date("Y-m-d H:i:s");		
		$user_days_enable=(int)substr(strip_tags(stripslashes(trim($_POST["user_days_enable"]))),0,9);
		$logins_per_day=(int)substr(strip_tags(stripslashes(trim($_POST["logins_per_day"]))),0,9);
		$my->uquery("insert into users (name,pass,login,email,user_start_date,user_days_enable,logins_per_day) values('$user',md5('$pass'),'$login','$email','$user_start_date','$user_days_enable','$logins_per_day')");
		echo "<table border='1' cellspacing='0' cellpadding='5'>";
		echo "<tr><th>Имя</th><td>$user</td></tr>";
		echo "<tr><th>Логин</th><td>$login</td></tr>";
		echo "<tr><th>Пароль</th><td>$pass</td></tr>";
		echo "</table><br>";
		
	}
	
	
	$q="select *,DATEDIFF(NOW(),user_start_date) as adatediff from users";		
	$users=&$my->query($q);
	echo "<table border='1' cellspacing='0' cellpadding='5'>";		
	echo "<tr><th width='20px'>Id</th><th>Имя</th><th width='80px'>Логин</th><th width='80px'>Пароль</th><th>Email</th><th width='120px'>Дата создания</th><th width='120px'>Дата начала</th><th colspan='2' width='100px'>Количество дней</th><th colspan='2' width='100px'>Количество заходов в день</th><th width='160px'>Действия</th></tr>";			
	if($users)
	{			
		for ($i=0;$i<count($users);$i++)
		{			
			$user=$users[$i]["name"];
			$id_user=$users[$i]["id_user"];			
			$logins=&$my->query("select count(id_user)as logins_per_day from login_stat where (id_user=$id_user) & (DATEDIFF(CURRENT_DATE,DATE_FORMAT(date_time,'%Y-%m-%d'))=0) group by id_user");
			$logins_per_now=($logins)?$logins[0]["logins_per_day"]:'0';
			$logins_per_day=$users[$i]["logins_per_day"];
			$name=$users[$i]["name"];
			$datediff=$users[$i]["adatediff"];
			$pass=substr($users[$i]["pass"],0,6);
			$login=$users[$i]["login"];
			$email=$users[$i]["email"];
			$user_start_date=$users[$i]["user_start_date"];
			$user_create_date=$users[$i]["user_create_date"];
			$user_days_enable=$users[$i]["user_days_enable"];
			echo "<form method='post' action=''>";
			echo "<tr><td><input type='text' name='id_user' value='$id_user' readonly></td><td><input type='text' name='user' value='$user'></td><td><input type='text' name='login' value='$login'></td><td><input type='text' name='pass' value='$pass'></td><td><input type='text' name='email' value='$email'></td><td>$user_create_date</td><td><input type='text' name='user_start_date' value='$user_start_date'></td><td width='50px'><input type='text' name='user_days_enable' value='$user_days_enable'><td width='50px'>$datediff</td></td><td width='50px'><input type='text' name='logins_per_day' value='$logins_per_day'><td width='50px'>$logins_per_now</td></td><td align='center'><input type='submit' name='edit' value='Edit'><input type='submit' name='editpass' value='EditPass' onclick=\"javascript:return confirm('Edit password?')\"><input type='submit' name='del' value='Del' onclick=\"javascript:return confirm('Delete?')\"></td></tr>";			
			echo "</form>";
		}					
	}	
	echo "<form method='post' action=''>";
	echo "<tr><td>&nbsp;</td><td><input type='text' name='user' value=''></td><td><input type='text' name='login' value=''></td><td><input type='password' name='pass' value=''></td><td><input type='text' name='email' value=''></td><td></td><td><input type='text' name='user_start_date' value=''></td><td colspan='2'><input type='text' name='user_days_enable' value=''></td><td colspan='2'><input type='text' name='logins_per_day' value=''></td><td align='center'><input type='submit' name='add' value='Add'></td></tr>";				
	echo "</form>";
	echo "</table>";

echo "</body></html>";
?>

