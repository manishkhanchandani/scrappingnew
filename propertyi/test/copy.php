<?php
for($i=1000;$i<50000;$i++) {
	copy("contents/content.html","contents/".$i.".html");
}
?>