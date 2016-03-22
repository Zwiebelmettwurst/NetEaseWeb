<?php
error_reporting(0);

header('content-type:text/html;charset=utf-8');
 $mp3query =$_GET['query'];
 $mp3type =$_GET['type'];

 
echo '<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<style>
div.textContainer {
    width: 70em;
    margin: 0 auto 1em auto;
    border: 1px solid #000;
    padding: 2em;
}
div.fancy:hover {
    width: 40em;
    margin: 0 auto 1em auto;
    border: 1px solid #000;
    padding: 2em;
}
span.welcome {
 font-weight:bold;
  font-size: 2em;
}

div.textContainer p {
    ;
}
a.music:hover {
   font-weight:bold;
}
a.music {
	text-decoration: none;
}

ul {
    list-style-type: none;
}

ul li:hover:before {
    content: ">>  ";
	color: red;
	font-weight:bold;
	visibility: visible;
}
ul li:hover:after {
    content: "  <<";
	color: red;
	font-weight:bold;
	visibility: visible;
}
ul li:before {
    content: ">>  ";
	color: red;
	font-weight:bold;
	visibility: hidden;
}
ul li:after {
    content: "  <<";
	color: red;
	font-weight:bold;
	visibility: hidden;
}
/* unvisited link */
a:link {
    color: blue;
}

/* visited link */
a:visited {
    color: blue;
}

/* mouse over link */
a:hover {
    color: red;
}

/* selected link */
a:active {
    color: red;
} 

</style>

<script src="jquery.js"></script>


<script type="text/javascript">
$(document).ready(function(){
  $("query").keypress(function(e){ 	//Wähle Inputfeld
      if (e.which == 13)			//Wenn taste 13 (Enter)
      {
		
		$("#ladeanimation").fadeIn("slow");
		$("#results").fadeOut("slow");
       
      }
  });
});
</script>

<script type="text/javascript">
function toggle(control)
{

var elem=document.getElementById(control);

var v = document.getElementsByName(control)[0];


if(elem.style.display=="none")
{
elem.style.display="inline";
v.play();

}
else
{
elem.style.display="none";
v.pause();
}

}
</script>

</head>
<div id="loader"><div id="loaderInner"></div></div>
<center><span class="welcome"><a href="/"><img style="width: 250px; padding: 20px 0 20px 0;" alt="Logo" src="tingtinglogo.png"></a></span></center>

<div class="textContainer">
    <center><p><form action="index.php" method="GET">
<label for="query">Find Music: </label><input type="text" id="query" name="query" />
<!-- <input type="radio" id="searchtype" name="type" value="1" checked >Song-Name<br />
<input type="radio" id="searchtype" name="type" value="100" >Interpret<br /> -->
<button type="submit">search</button><br />
</form>
Search for a title, an artist or an album!<br />
</p></center>
</div>
<div id="ladeanimation" style="display: none;"><center><img src="images/loading_animation.gif"></center></div>';

 if(isset($_GET['query']))
{

 // if(!isset($_GET['type']))
// exit;
$postdata = http_build_query(
		array(
			's' => '' . $mp3query . '',
			'type' => '1',
			'offset' => '0',
			'sub' => 'false',
			'limit'=> '50'
		)
	);
 
	// Set the POST options
	$opts = array('http' => 
		array (
			'method' => "POST",
			'header' => "Cookie: appver = 2.3.1\r\n" . 
						"Referer: http://music.163.com\r\n" . 
						"Content-Type: application/x-www-form-urlencoded",
			'content' => $postdata
		)
	);
 //"Cookie: appver = 2.0.2\r\n" . 
	// Create the POST context
	$context  = stream_context_create($opts);
 
	// POST the data to an api
	$url = 'http://music.163.com/api/search/get/';
	$result = file_get_contents($url, false, $context);
	$mp3_search = json_decode ($result);
 	$songsfound = $mp3_search->result->songCount;
	echo '<div class="textContainer" id="results"><ul>';
	$a = 1;
	$foundsongids = array();
	if ($songsfound < 1){
	echo '<center><li style="color: red;">Sorry, nothing found. Try another keyword.</li></center></ul></div>';
	exit();
	}
	foreach($mp3_search->result->songs as $mysongs)
							{	
					
									$foundsongids[] =  $mysongs->id;
						
							}
 $mp3_json =file_get_contents('http://music.163.com/api/song/detail?ids=[' . implode(",", $foundsongids) . ']');
$mp3_result = json_decode ($mp3_json);
 function encrypted_id($id)
{
    $byte1 = '3go8&$8*3*3h0k(2)2';
	for($i=0;$i<strlen($id);$i++)
	{
		$byte2[$i] = $id[$i];
	}
	$byte1_len = strlen($byte1);
	for($i=0;$i<strlen($id);$i++)
	{
		$byte2[$i] = $byte2[$i] ^ $byte1[$i % $byte1_len];
	}
$id_ = implode('',$byte2);
$id_ = md5($id_,1);
$m = base64_encode($id_);
$m = str_replace(array('/','+'),array('_','-'),$m);
return $m;
}

 $musiccode = array();
  $extension = array(); 
  $filesize = array();
  $encryptedmusiccode = array();
  $songname = array();
  $songfilename = array();
  $mp3url = array();
  $artist =array();
  $musicid = array();
  $bitrate = array();
  $length = array();
foreach($mp3_result->songs as $allsongs)
{
$musiccode[] = $allsongs->hMusic->dfsId;
$extension[] = $allsongs->hMusic->extension;
//$allsongs[] $allsongs->hMusic->size;
$encryptedmusiccode[] =  encrypted_id('' . $allsongs->hMusic->dfsId . '');
$songname[] = htmlentities($allsongs->name, ENT_COMPAT,'UTF-8', true);
$songfilename[] = str_replace(" ", "_", $allsongs->name);
$mp3url[] = $allsongs->hMusic->dfsId;
$bitrate[] = $allsongs->hMusic->bitrate/1000;
$length[] = sprintf("%02d:%02d", floor($allsongs->hMusic->playTime/1000/60), $allsongs->hMusic->playTime/1000 % 60);
$artist[] = $allsongs->artists[0]->name;
$musicid[] = $allsongs->id;

}
$max = sizeof($foundsongids);

if ($max > 0)
{
for($i = 0; $i < $max;$i++)
{
//echo   '<li class="cool">[' . $a++ . '.] ' . $artist[$i] . ' - ' . $songname[$i] . ' [' . $length[$i] . ' - ' . $bitrate[$i] . ' kbit/s] [<a class="music" target="_blank" href="http://185.72.176.118/music.php?id=' . //$musicid[$i] .  '&dl=1">download</a>] [<a class="music" href="javascript:toggle(\'' . $musiccode[$i]  . '\')">listen</a>] <span id="' . $musiccode[$i] . '" style="display:none"><audio name="' . $musiccode[$i] . '" //controls="controls" preload="none"><source src="http://m1.music.126.net/' . $encryptedmusiccode[$i] . '/' . $musiccode[$i] . '.' . $extension[$i] . '" type="audio/mpeg">Your browser does not support the audio element.//</audio></span></li>';
	echo   '<li class="cool">[' . $a++ . '.] ' . $artist[$i] . ' - ' . $songname[$i] . ' [' . $length[$i] . ' - ' . $bitrate[$i] . ' kbit/s] [<a class="music" target="_blank" href="http://dl.tingting.ru/music.php?id=' . $musicid[$i] .  '&dl=1">download</a>] [<a class="music" href="javascript:toggle(\'' . $musiccode[$i]  . '\')">listen</a>] <span id="' . $musiccode[$i] . '" style="display:none"><audio name="' . $musiccode[$i] . '" class="audiocontrol" controls="controls" preload="none"><source src="http://dl.tingting.ru/music.php?id=' . $musicid[$i] .  '&dl=1" type="audio/mpeg">Your browser does not support the audio element.</audio></span></li>';

}
}
else
{ 
echo '<center><li style="color: red;">Sorry, nothing found. Try another keyword.</li></center>';
}


							
						//	print_r($result);
	
	echo '</ul></div>';
	if (($_GET['debug']) == "1")
	{
	echo 'DEBUGGING:<br/>';
	echo count ( $foundsongids );
	echo count ( $musiccode );
	echo count ( $extension );
	echo count ( $encryptedmusiccode );
	echo count ( $songname );
	echo count ( $songfilename );
	echo count ( $mp3url );
	var_dump ( $foundsongids );
	var_dump( $musiccode );
	var_dump( $extension );
	var_dump( $encryptedmusiccode );
	var_dump( $songname );
	var_dump ($songfilename );
	var_dump( $mp3url );
	
var_dump($mp3_result);	
	print_r($result);
	}
	}
	echo '</body>
	</html>';
	?>