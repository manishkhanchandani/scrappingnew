<?php 
require_once('../Connections/conn2.php'); 

$table_name = "hotel_detail_wct_int_gd_1";

$sql = "SELECT distinct hotel_name, hotel_id FROM $table_name WHERE hotel_id>0";
$rsViewHids = mysql_query($sql, $conn) or die(mysql_error());
$row_rsViewHids = mysql_fetch_assoc($rsViewHids);

do {

	$query_rsView = "SELECT * FROM $table_name WHERE hotel_id='".$row_rsViewHids['hotel_id']."'";
	$rsView = mysql_query($query_rsView, $conn) or die(mysql_error());
	$row_rsView = mysql_fetch_assoc($rsView);
	$totalRows_rsView = mysql_num_rows($rsView);
	
	$filename = $row_rsView['filename'];
	$source = '<source name="WCT" avg-rating="'.$row_rsView['avgrating'].'">';
	
	do {

		$data['rating'] = $row_rsView['rating'];
		$data['reviewer'] = $row_rsView['reviewer'];
		$data['reviewdate'] = $row_rsView['reviewdate'];
		$data['review_title'] = $row_rsView['review_title'];
		$data['review_detail'] = $row_rsView['review_detail'];
		$data['source'] = $row_rsView['source'];
		
		$source .= createXML($data);
	
	} while ($row_rsView = mysql_fetch_assoc($rsView));
	
	
	$source .= '</source>';
	
	
	$xmlpath = $filename;
	$xmlpath = str_replace("C:/travelmuseXMLdata/property/","",$xmlpath);
	
	$explode = explode("/", $xmlpath);
	
	if($explode) {
	
		$newDir = "wct/";
		foreach($explode as $direc) {
			if(eregi(".xml", $direc)) {
				$newDir .= $direc;                                                          
				file_put_contents($newDir, $source);
			} else {
				$newDir .= $direc."/";
				if(!is_dir($newDir)) {
					mkdir($newDir, 0777);
					chmod($newDir, 0777);
				}
	
			}
	
		}
	
	}


} while ($row_rsViewHids = mysql_fetch_assoc($rsViewHids));

function createXML($data) {

	$review ='
	<review>
				<rating>'.$data['rating'].'</rating>
				<author><![CDATA['.convertLatin1ToHtml2($data['reviewer']).']]></author>
				<date>'.date('Y-m-d',strtotime($data['reviewdate'])).'</date>
				<title><![CDATA['.convertLatin1ToHtml2($data['review_title']).']]></title>
				<excerpt><![CDATA['.convertLatin1ToHtml2($data['review_detail']).']]></excerpt>
				<link><![CDATA['.convertLatin1ToHtml2($data['source']).']]></link>
	</review>'; 

    return $review;

}


function convertLatin1ToHtml2($msg) {

	$chars = str_split($msg, 1);
	$length = count($chars);
	for ($i=0; $i<$length; $i++) {
		if(ord($chars[$i])> 127) {
			$str.= ''; //$str.= '&#'. ord($chars[$i]).';';
		} else{
			$str.= $chars[$i];
		}
	}

	return $str;
			
}


?>