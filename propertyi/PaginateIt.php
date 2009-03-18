<?php
class PaginateIt {
    var $currentPage, $itemCount, $itemsPerPage, $linksHref, $linksToDisplay;
    var $pageJumpBack, $pageJumpNext, $pageSeparator;
    var $queryString, $queryStringVar;
    var $enableQueryString = true;

    function EnableQueryString(){
        $this->enableQueryString = true;
    }

    function SetCurrentPage($reqCurrentPage){
        $this->currentPage = (integer) abs($reqCurrentPage);
    }

    function SetItemCount($reqItemCount){
        $this->itemCount = (integer) abs($reqItemCount);
    }

    function SetItemsPerPage($reqItemsPerPage){
        $this->itemsPerPage = (integer) abs($reqItemsPerPage);
    }

    function SetLinksHref($reqLinksHref){
        $this->linksHref = $reqLinksHref;
    }

    function SetLinksFormat($reqPageJumpBack, $reqPageSeparator, $reqPageJumpNext){
        $this->pageJumpBack = $reqPageJumpBack;
        $this->pageSeparator = $reqPageSeparator;
        $this->pageJumpNext = $reqPageJumpNext;
    }

    function SetLinksToDisplay($reqLinksToDisplay){
        $this->linksToDisplay  = (integer) abs($reqLinksToDisplay);
    }

    function SetQueryStringVar($reqQueryStringVar){
        $this->queryStringVar = $reqQueryStringVar;
    }

    function SetQueryString($reqQueryString){
        $this->queryString = $reqQueryString;
    }
    
    function GetCurrentCollection($reqCollection){
        if($this->currentPage < 1){
            $start = 0;
        }
        elseif($this->currentPage > $this->GetPageCount()){
            $start = $this->GetPageCount() * $this->itemsPerPage - $this->itemsPerPage;
        }
        else {
            $start = $this->currentPage * $this->itemsPerPage - $this->itemsPerPage;
        }
        
        return array_slice($reqCollection, $start, $this->itemsPerPage);
    }

    function GetPageCount(){
        return (integer)ceil($this->itemCount/$this->itemsPerPage);
    }

    function GetPageLinks_Old(){
        $strLinks = '';
        $pageCount = $this->GetPageCount();
        $queryString = $this->GetQueryString_Old();
        $linksPad = floor($this->linksToDisplay/2);

        if($this->linksToDisplay == -1){
            $this->linksToDisplay = $pageCount;
        }


        if($pageCount == 0){
            $strLinks = '1';
        }
        elseif($this->currentPage - 1 <= $linksPad || ($pageCount - $this->linksToDisplay + 1 == 0) || $this->linksToDisplay > $pageCount){ 
            $start = 1;
        }
        elseif($pageCount - $this->currentPage <= $linksPad){
            $start = $pageCount - $this->linksToDisplay + 1;
        }
        else {
            $start = $this->currentPage - $linksPad;
        }


        if(isset($start)){
            if($start > 1){
                if(!empty($this->pageJumpBack)){
                    $pageNum = $start - $this->linksToDisplay + $linksPad;
                    if($pageNum < 1){
                        $pageNum = 1;
                    }

                    $strLinks .= '<a href="'.$this->linksHref.$queryString.$pageNum.'">';
                    $strLinks .= $this->pageJumpBack.'</a>'.$this->pageSeparator;
                }
				
				$strLinks .= "<a href='".$this->linksHref.$queryString.($this->currentPage-1)."'>Previous</a>".$this->pageSeparator; // added
				$tmpStart=1;// added
                $strLinks .= '<a href="'.$this->linksHref.$queryString.'1">1...</a>'.$this->pageSeparator;
            }

            if($start + $this->linksToDisplay > $pageCount){
                $end = $pageCount;
            }
            else {
                $end = $start + $this->linksToDisplay - 1;
            }
			
			if(($this->currentPage-1)!=0 && $tmpStart!=1) {// added
				$strLinks .= "<a href='".$this->linksHref.$queryString.($this->currentPage-1)."'>Previous</a>".$this->pageSeparator; // added
			} // added
			
            for($i = $start; $i <= $end; $i ++){
                if($i != $this->currentPage){
                    $strLinks .= '<a href="'.$this->linksHref.$queryString.($i).'">';
                    $strLinks .= ($i).'</a>'.$this->pageSeparator;
                }
                else {
                    $strLinks .= $i.$this->pageSeparator;
                }
            }
            $strLinks = substr($strLinks, 0, -strlen($this->pageSeparator));
			
			if(($this->currentPage)!=$pageCount && ($start + $this->linksToDisplay - 1 < $pageCount)!=1) {// added
				$strLinks .= $this->pageSeparator."<a href='".$this->linksHref.$queryString.($this->currentPage+1)."'>Next</a>"; // added
			} // added

            if($start + $this->linksToDisplay - 1 < $pageCount){
                $strLinks .= $this->pageSeparator.'<a href="'.$this->linksHref.$queryString.$pageCount.'">';
                $strLinks .= '...'.$pageCount.'</a>'.$this->pageSeparator;
                
				$strLinks .= "<a href='".$this->linksHref.$queryString.($this->currentPage+1)."'>Next</a>".$this->pageSeparator; // added
				
				
                if(!empty($this->pageJumpNext)){
                    $pageNum = $start + $this->linksToDisplay + $linksPad;
                    if($pageNum > $pageCount){
                        $pageNum = $pageCount;
                    }
                    									
                    $strLinks .= '<a href="'.$this->linksHref.$queryString.$pageNum.'">';
                    $strLinks .= $this->pageJumpNext.'</a>';
                }
            }
        }


        return $strLinks;
    }
	
	function GetPageLinks(){
        $strLinks = '';
        $pageCount = $this->GetPageCount();
        $queryString = $this->GetQueryString();
        $linksPad = floor($this->linksToDisplay/2);

        if($this->linksToDisplay == -1){
            $this->linksToDisplay = $pageCount;
        }


        if($pageCount == 0){
            $strLinks = '1';
        }
        elseif($this->currentPage - 1 <= $linksPad || ($pageCount - $this->linksToDisplay + 1 == 0) || $this->linksToDisplay > $pageCount){ 
            $start = 1;
        }
        elseif($pageCount - $this->currentPage <= $linksPad){
            $start = $pageCount - $this->linksToDisplay + 1;
        }
        else {
            $start = $this->currentPage - $linksPad;
        }


        if(isset($start)){
            if($start > 1){
                if(!empty($this->pageJumpBack)){
                    $pageNum = $start - $this->linksToDisplay + $linksPad;
                    if($pageNum < 1){
                        $pageNum = 1;
                    }

                    $strLinks .= '<a href="'.$this->linksHref.'?'.$queryString.$pageNum.'" onClick=\'doAjaxLoadingText("'.$this->linksHref.'","GET","'.$queryString.$pageNum.'", "", "center", "yes", "'.$this->linksHref.$queryString.$pageNum.'", "1"); return false;\'>';
                    $strLinks .= $this->pageJumpBack.'</a>'.$this->pageSeparator;
                }
				
				$strLinks .= "<a href='".$this->linksHref.'?'.$queryString.($this->currentPage-1)."' onClick=\"doAjaxLoadingText('".$this->linksHref."','GET','".$queryString.($this->currentPage-1)."', '', 'center', 'yes', '".$this->linksHref.$queryString.($this->currentPage-1)."', '1'); return false;\">Previous</a>".$this->pageSeparator; // added
				$tmpStart=1;// added
                $strLinks .= '<a href="'.$this->linksHref.'?'.$queryString.'1" onClick=\'doAjaxLoadingText("'.$this->linksHref.'","GET","'.$queryString.'1", "", "center", "yes", "'.$this->linksHref.$queryString.'1", "1"); return false;\'>1...</a>'.$this->pageSeparator;
            }

            if($start + $this->linksToDisplay > $pageCount){
                $end = $pageCount;
            }
            else {
                $end = $start + $this->linksToDisplay - 1;
            }
			
			if(($this->currentPage-1)!=0 && $tmpStart!=1) {// added
				$strLinks .= "<a href='".$this->linksHref.'?'.$queryString.($this->currentPage-1)."' onClick=\"doAjaxLoadingText('".$this->linksHref."','GET','".$queryString.($this->currentPage-1)."', '', 'centerDv', 'yes', '".$this->linksHref.$queryString.($this->currentPage-1)."', '1'); return false;\">Previous</a>".$this->pageSeparator; // added
			} // added
			
            for($i = $start; $i <= $end; $i ++){
                if($i != $this->currentPage){
                    $strLinks .= '<a href="'.$this->linksHref.'?'.$queryString.($i).'" onClick=\'doAjaxLoadingText("'.$this->linksHref.'","GET","'.$queryString.($i).'", "", "centerDv", "yes", "'.$this->linksHref.$queryString.($i).'", "1"); return false;\'>';
                    $strLinks .= ($i).'</a>'.$this->pageSeparator;
                }
                else {
                    $strLinks .= $i.$this->pageSeparator;
                }
            }
            $strLinks = substr($strLinks, 0, -strlen($this->pageSeparator));
			
			if(($this->currentPage)!=$pageCount && ($start + $this->linksToDisplay - 1 < $pageCount)!=1) {// added
				$strLinks .= $this->pageSeparator."<a href='".$this->linksHref.'?'.$queryString.($this->currentPage+1)."' onClick=\"doAjaxLoadingText('".$this->linksHref."','GET','".$queryString.($this->currentPage+1)."', '', 'centerDv', 'yes', '".$this->linksHref.$queryString.($this->currentPage+1)."', '1'); return false;\">Next</a>"; // added
			} // added

            if($start + $this->linksToDisplay - 1 < $pageCount){
                $strLinks .= $this->pageSeparator.'<a href="'.$this->linksHref.'?'.$queryString.$pageCount.'" onClick=\'doAjaxLoadingText("'.$this->linksHref.'","GET","'.$queryString.$pageCount.'", "", "centerDv", "yes", "'.$this->linksHref.$queryString.$pageCount.'", "1"); return false;\'>';
                $strLinks .= '...'.$pageCount.'</a>'.$this->pageSeparator;
                
				$strLinks .= "<a href='".$this->linksHref.'?'.$queryString.($this->currentPage+1)."' onClick=\"doAjaxLoadingText('".$this->linksHref."','GET','".$queryString.($this->currentPage+1)."', '', 'centerDv', 'yes', '".$this->linksHref.$queryString.($this->currentPage+1)."', '1'); return false;\">Next</a>".$this->pageSeparator; // added
				
				
                if(!empty($this->pageJumpNext)){
                    $pageNum = $start + $this->linksToDisplay + $linksPad;
                    if($pageNum > $pageCount){
                        $pageNum = $pageCount;
                    }
                    									
                    $strLinks .= '<a href="'.$this->linksHref.'?'.$queryString.$pageNum.'" onClick=\'doAjaxLoadingText("'.$this->linksHref.'","GET","'.$queryString.$pageNum.'", "", "centerDv", "yes", "'.$this->linksHref.$queryString.$pageNum.'", "1"); return false;\'>';
                    $strLinks .= $this->pageJumpNext.'</a>';
                }
            }
        }


        return $strLinks;
    }
	
    function GetQueryString_Old(){
		$pattern = array('/'.$this->queryStringVar.'=[^&]*&?/', '/&$/');
        $replace = array('', '');
        $queryString = preg_replace($pattern, $replace, $this->queryString);
        $queryString = str_replace('&', '&amp;', $queryString);
        
        if(!empty($queryString)){
            $queryString.= '&amp;';
        }

        return '?'.$queryString.$this->queryStringVar.'=';
    }
	
	function GetQueryString(){
		$pattern = array('/'.$this->queryStringVar.'=[^&]*&?/', '/&$/');
        $replace = array('', '');
        $queryString = preg_replace($pattern, $replace, $this->queryString);
        $queryString = str_replace('&', '&amp;', $queryString);
        
        if(!empty($queryString)){
            $queryString.= '&amp;';
        }

        return $queryString.$this->queryStringVar.'=';
    }

    function GetSqlLimit(){
        return ' LIMIT '.($this->currentPage * $this->itemsPerPage - $this->itemsPerPage).', '.$this->itemsPerPage;
    }


    function PaginateIt(){
        $this->SetCurrentPage(1);
        $this->SetItemsPerPage(10);
        $this->SetItemCount(0);
        $this->SetLinksFormat('&laquo; <<',' &nbsp;&nbsp; ','>> &raquo;');
        $this->SetLinksHref($_SERVER['PHP_SELF']);
        $this->SetLinksToDisplay(10);
        $this->SetQueryStringVar('page');
        if($this->enableQueryString) {
          $this->SetQueryString($_SERVER['QUERY_STRING']);
        }

        if(isset($_GET[$this->queryStringVar]) && is_numeric($_GET[$this->queryStringVar])){
            $this->SetCurrentPage($_GET[$this->queryStringVar]);
        }
    }
}
/*
$PaginateIt = new PaginateIt();

$PaginateIt->SetItemCount($_GET['t']);
echo $PaginateIt->GetPageLinks();


include('PaginateIt.php');
$PaginateIt = new PaginateIt();

$PaginateIt->SetItemCount($totalRows_rsView);
$PaginateIt->SetCurrentPage($page);
$PaginateIt->SetItemsPerPage($maxRows_rsView);
echo $PaginateIt->GetPageLinks_Old();
echo "<br>";echo "<br>";

*/
?>