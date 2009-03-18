<?php
  // This is how you start a class.  You can say class x extends y, but we're not doing that yet.
  class RecursiveSearch {
  
    // Now, you can declare your variables here, but it is not good form to initialize them here.
    // The reason for this is that functions outside a child function (for initialization) aren't
    // called.  That's right.  So if I wrote:
    //  var $abc = dirname(__FILE__);
    // It would not get initialized properly.  Let us begin in earnest.

    // This is the root directory of our search, it should always be an absolute path, and not a relative path.
    var $basedir = '';
    // This will be an array, when our object is created, of all results in a path relative to the base dir.
    var $files;
    // This is an array of our directories, just in case someone happens to want those sometime. ;-)
    var $folders;
    var $count; // Just for kicks.
	//var $xmlfiles;//to store file list;

    // Now, this is different.  I use a double underscore for internal variables and this is a callback
    // function for any action the user may want to execute on a per-file basis.
    // The function will be called like this: call_user_func_array($__filefunc, $file)
    // A function prototype should be: find_file($file){}
    var $__filefunc;

    // This is our framework constructor.  All we need passed to it is the root directory to search, and an optional
    // per-file callback.  Because we don't want to supply the latter every time we'll make it default to ''.
    function RecursiveSearch($root,$callback = '')
    {
      $this->__filefunc = $callback; // We want this assigned even if blank.  More later!
      $this->basedir    = $root;
      $this->files      = array();
      $this->folders    = array();
      $this->xmlfiles    = array();
      $this->__search(); // This is how hard it is to initialize the object.  Wow huh?  In fact, more on this later.
      // The following line is not executed until after the entire search is finished.
      $this->count = count($this->files)+count($this->folders);
    }
    
    // This is our prototype internal search function.  You shouldn't call this on your own.  The dir string that gets
    // passed is what lets us go recursive in our search.  Only there are two problems which I will outline later.
    function __search($dir = '')
    {
      // This is the same as if($dir == '') do ? ... : else do : ... ;  This tutorial is to explain
      // classes in a basic sense, and how to do a recursive search in a stable one. ;) Not elementary
      // PHP coding.
      $path = $dir == '' ? $this->basedir : "{$this->basedir}/$dir";
      
      foreach(scandir($path) as $found)
      {
        // Now, this is extremely critical, as the __isdot call must be before everything else, or it *will*
        // register as a valid directory to be searched!
        if(!$this->__isdot($found))
        {
          $absolute = "$path/$found";
          $relative = $dir == '' ? $found : "$dir/$found";
          // We prioritize folders first, as this script dives to the deepest depth and then works outwards.  It's an
          // effective mechanism to ensure that you do end up getting the results in a rather efficient manner.
          if(is_dir($absolute))
          {
            $this->folders[] = $relative; // Store the result... again, with relative pathing.

            // And this is how you search recursively. :D Just call it with the relative path, and you're good to go!
            $this->__search($relative);
          }elseif(is_file($absolute)){
            //$this->files[] = $relative;
			$this->files[] = $absolute;
            // And this is how we add a callback hook, so that if there is a function to call whenever a file is found
            // this is it!  Pretty effective and very easy to handle I must say.
            if($this->__filefunc != '')
              call_user_func_array($this->__filefunc, $relative);
          }
        }
      }
    }
    
    function __isdot($s)
    {
      return ($s == '.' || $s == '..');
    }
  }

  /*
  
    The following is a test script; be careful with how you mangle it. :P

  */


function filelist($dirname){
	echo "<b>Files:</b>\n<ul>\n";
	//echo dirname("Z:\ROOT\property\us\ca\borrego-springs-resort-and-country-club.xml");//exit;
	 // $search = new RecursiveSearch(dirname(__FILE__),create_function('$found','echo "<li>$found</li>\n";'));
	// 	http://10.21.1.165/alf-static/ROOT/property/us/ca/borrego-springs-resort-and-country-club.xml
	//	$search = new RecursiveSearch(dirname("Z:\ROOT\property\us\ca\borrego-springs-resort-and-country-club.xml"),create_function('$found','echo "<li>$found</li>\n";'));	
		$search = new RecursiveSearch(dirname($dirname));	
	echo "<pre>\n";
	print_r($search->files);
	echo "<\pre>\n";

	  echo "</ul>\n",
		   "<table border='0'>\n",
		   "<tr><td><i>Number of Files:</i></td><td>".count($search->files)."</td></tr>\n",
		   "<tr><td><i>Total Results:</i></td><td>{$search->count}</td></tr>\n",
		   "</table>";

	  return($search->files);
}

function getNextFile($dirname,$currentFile){
	//global $search;
	$search = new RecursiveSearch(dirname($dirname));
	$file_array = $search->files; 
	
	foreach ($file_array as $key => $value) {
		if ($currentFile == $value) {
				return($file_array[$key+1]);
		}
		continue;
	}
	return(false);
}

function getFirstFile($dirname){
	//global $search;
	$search = new RecursiveSearch(dirname($dirname));
	$file_array = $search->files; 
	if(!empty($file_array)){
		return($file_array[0]);
	}
	echo "No file found in directory";
	return(false);
}
?>