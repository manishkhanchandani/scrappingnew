<?php
ini_set("memory_limit","500M");
ini_set("max_execution_time","-1");
include('../Connections/conn.php');

$continent = $_GET['c'];
if(!$continent) {
	echo 'enter continent';
	exit;
}
$dir = "files/db";
if(!is_dir($dir)) {
	mkdir($dir, 0777);
	chmod($dir, 0777);
}
$path = $dir."/".$continent;
if(!is_dir($path)) {
	mkdir($path, 0777);
	chmod($path, 0777);
}
$sql = "select * from country where continent_code = '".$continent."'";
echo $sql;
echo "<br>";
$rs = mysql_query($sql) or die(mysql_error());
if(mysql_num_rows($rs)) {
	while($rec = mysql_fetch_array($rs)) {
		$cns[] = $rec['iso2'];
	}
} else {
	echo 'no country found';
	exit;
}
$rsCnt = mysql_query('select count(*) as cnt from '.$key) or die('error');
$recCnt = mysql_fetch_array($rsCnt);
$cnt = $recCnt['cnt'];
			
			$fp = fopen($dir."/".$key."_dbstructure.sql","w");
			fwrite($fp, $tableStructure);
			fclose($fp);
			if($structure==1) {
				$complete = $tableStructure."\n\n";
			}
				
			$string = $rec[1].";";
			//$return .= $rec[1].";\n\n";
			//$return .= "\n\n";	
			$rsCnt = mysql_query('select count(*) as cnt from '.$key) or die('error');
			$recCnt = mysql_fetch_array($rsCnt);
			$cnt = $recCnt['cnt'];
			
			$max = 10000;
			if($m) $max = $m;
			if(!$s) $startCounter = 0; else $startCounter = $s;
			
			$totalPages = ceil($cnt/$max)-1;
			echo "Start: ".$startCounter." , max: $max , cnt: $cnt Total pages: $totalPages<hr>";
			for($counter=$startCounter;$counter<=$totalPages;$counter++) {
				echo $counter;
				echo "<br>";
				echo $start = $max*$counter;
				echo "<hr>";
				echo $sql='select * from `'.$key.'` LIMIT '.$start.', '.$max;
				echo "<br>";
				flush();
				$result = mysql_query($sql) or die('error');
				
				$data = '';
				$fulldata = "";
				while($rec = mysql_fetch_array($result)) {
					$query = "insert into ".$key." set ";
					$i = 0;
					$subquery = '';
					while ($i < mysql_num_fields($result)) {
						$meta = mysql_fetch_field($result, $i);
						$subquery .= "`".$meta->name."`='".addslashes(stripslashes(trim($rec[$meta->name])))."', ";
						$i++;
					}
					$query = $query.substr($subquery,0,-2);
					$data .= $query.";\n";
					$fulldata .= $query.";\n";
				}		
				//$return .= $data;
				//$return .= "\n\n";	
				$fp = fopen($dir."/".$key."_dbdata_".$counter.".sql","w");
				fwrite($fp, $fulldata);
				fclose($fp);
				echo $dir."/".$key."_dbdata_".$counter.".sql is done<br>";
				echo $counter;
				echo "<hr>";
				flush();
				//if($datas==1) {
					//$complete .= $fulldata;
				//}
					
			}
		}
	}
	//$fp = fopen($dir."/".$database."_db.sql","w");
	//fwrite($fp, $complete);
	//fclose($fp);
				
	//if($send_mail==1) {
		//mail_attachment ("system@".str_replace("www.","",$_SERVER['HTTP_HOST']) , $email, "Database Backup", "Attached is database backup", $complete, $date."_db.sql");
	//}	
	echo "Done";	
	return true;
}

$tblg = $_GET['t']; if(!$tblg) { echo 'choose table'; exit; }
$s = $_GET['s']; 
$m = $_GET['m'];
/*
$sql = "SHOW TABLES FROM ".$database_conn;
$result = mysql_query($sql);

while ($row = mysql_fetch_row($result)) {
	$tbls[] = $row[0];
}
*/
$tbls = array($tblg);
$email = "naveenkhanchandani@gmail.com";
$return = backup($s, $m, $tbls, $send_mail=1, $email, $database_conn, 1, 1);
//echo nl2br(htmlentities($return));
?>