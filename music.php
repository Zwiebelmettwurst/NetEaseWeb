<?php
//error_reporting(0);

header('content-type:text/html;charset=utf-8');
 $mp3id =$_GET['id'];
  $mp3dl =$_GET['dl'];
  $mp3_json =file_get_contents('http://music.163.com/api/song/detail?ids=[' . $mp3id . ']');
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

if ($byte2 == NULL)
{
echo '<!DOCTYPE html>
<html>
<body>

<center>Error. MP3 not found. Try another ID.</center>

</body>
</html>';
exit();
}
$id_ = implode('',$byte2);
$id_ = md5($id_,1);
$m = base64_encode($id_);
$m = str_replace(array('/','+'),array('_','-'),$m);
return $m;
}
  $musiccode = $mp3_result->songs[0]->hMusic->dfsId;
  $extension = $mp3_result->songs[0]->hMusic->extension;
$filesize = $mp3_result->songs[0]->hMusic->size;
$encryptedmusiccode =  encrypted_id('' . $musiccode . '');
$songname = htmlentities($mp3_result->songs[0]->name, ENT_COMPAT,'ISO-8859-1', true);
$artist = $mp3_result->songs[0]->artists[0]->name;
$songfilename = str_replace(" ", "_", $artist) . '_-_' .  str_replace(" ", "_", $mp3_result->songs[0]->name);


	$mp3url = $mp3_result->songs[0]->hMusic->dfsId;
	//var_dump($mp3_result);
		//echo '<a href="http://m1.music.126.net/' . $encryptedmusiccode . '/' . $musiccode . '.' . $extension . '">' . $songname . '</a>';
/*  header("Content-type: application/x-download");
  header("Content-Length: ".$filesize);
    header('Content-Disposition: filename="' . $songfilename . '.mp3"');
    header('X-Pad: avoid browser bug');
    header('Cache-Control: no-cache');
header('Location: ' . 'http://m3.music.126.net/' . $encryptedmusiccode . '/' . $musiccode . '.' . $extension . '');
*/

if (($mp3dl) == '1')
{
$downloadfile = 'http://198.47.104.134/m1.music.126.net/' . $encryptedmusiccode . '/' . $musiccode . '.' . $extension . '';
$filename = $songfilename;
//$filesize = filesize($downloadfile);

header('Content-Type: audio/mpeg'); 
header('Content-Disposition: attachment; filename=' . $filename . '.mp3'); 
header('Content-Length: ' . $filesize . '');

readfile($downloadfile);
exit; 

} else {





		echo '<!DOCTYPE html>
<html>
<body>

<center><audio autoplay="autoplay" controls="controls">
  <source src="http://198.47.104.134/m1.music.126.net/' . $encryptedmusiccode . '/' . $musiccode . '.' . $extension . '" type="audio/mpeg">
 Your browser does not support the audio element.
</audio></center>

</body>
</html>';
}



		
?>