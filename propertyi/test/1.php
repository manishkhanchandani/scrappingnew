<?
$outFile = "1.zip";
// make zip archive

$zip = new ZipArchive();
$zip->open($outFile, ZIPARCHIVE::CREATE);
//add Files
for($i=0;$i<1000;$i++) {
	copy("content.html",$i.".html");
	$zip->addFile($i.".html");
}
$zip->close();
?> 