<?
	//error_reporting(E_ALL);

    
    $tvip = "192.168.1.29"; //IP Address of TV
    $myip = "192.168.1.8"; //Doesn't seem to be really used
    $mymac = "00-0c-29-3e-b1-4f"; //Used for the access control/validation, but not after that AFAIK
    $appstring = "iphone..iapp.samsung"; //What the iPhone app reports
    $tvappstring = "iphone.UD40D6310.iapp.samsung"; //Might need changing to match your TV type
    $remotename = "php Samsung Remote"; //What gets reported when it asks for permission/also shows in General->Wireless Remote Control menu
    echo "Content-type: text/html\n\n";
    flush();
    $sock = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
	$result = socket_connect($sock, $tvip, '55000');
    if( $result === false)
	   die ("Could not create socket: \n");

    //Normal remote keys
    //KEY_0
    //KEY_1
    //KEY_2
    //KEY_3
    //KEY_4
    //KEY_5
    //KEY_6
    //KEY_7
    //KEY_8
    //KEY_9
    //KEY_UP
    //KEY_DOWN
    //KEY_LEFT
    //KEY_RIGHT
    //KEY_MENU
    //KEY_PRECH
    //KEY_GUIDE
    //KEY_INFO
    //KEY_RETURN
    //KEY_CH_LIST
    //KEY_EXIT
    //KEY_ENTER
    //KEY_SOURCE
    //KEY_AD
    //KEY_PLAY
    //KEY_PAUSE
    //KEY_MUTE
    //KEY_PICTURE_SIZE
    //KEY_VOLUP
    //KEY_VOLDOWN
    //KEY_TOOLS
    //KEY_POWEROFF
    //KEY_CHUP
    //KEY_CHDOWN
    //KEY_CONTENTS
    //KEY_W_LINK //Media P
    //KEY_RSS //Internet
    //KEY_MTS //Dual
    //KEY_CAPTION //Subt
    //KEY_REWIND
    //KEY_FF
    //KEY_REC
    //KEY_STOP

    //Bonus buttons not on the normal remote:
    //KEY_TV

    //Don't work/wrong codes:
    //KEY_CONTENT
    //KEY_INTERNET
    //KEY_PC
    //KEY_HDMI1
    //KEY_OFF
    //KEY_POWER
    //KEY_STANDBY
    //KEY_DUAL
    //KEY_SUBT
    //KEY_CHANUP
    //KEY_CHAN_UP
    //KEY_PROGUP
    //KEY_PROG_UP

	if(!isset($_REQUEST["key"])){
		$_REQUEST["key"] = 'KEY_CHAN_UP';
	}
	
	
	$ipencoded = base64_encode($myip);
	$macencoded = base64_encode($mymac);
    $messagepart1 = chr(0x64) . chr(0x00) . chr(strlen($ipencoded)) . chr(0x00) . $ipencoded . chr(strlen($macencoded)) . chr(0x00) . $macencoded .
                     	chr(strlen(base64_encode($remotename))) . chr(0x00) . base64_encode($remotename);
						
    $part1 = chr(0x00) . chr(strlen($appstring)) . chr(0x00) . $appstring . chr(strlen($messagepart1)) . chr(0x00) . $messagepart1;

    socket_write($sock, $part1, strlen($part1));
    echo $part1;
    echo "\n";

    $messagepart2 = chr(0xc8) . chr(0x00);
    $part2 = chr(0x00) . chr(strlen($appstring)) . chr(0x00) . $appstring . chr(strlen($messagepart2)) . chr(0x00) . $messagepart2;
    socket_write($sock, $part2, strlen($part2));
    echo $part2;
    echo "\n";

    //Preceding sections all first time only

    if (isset($_REQUEST["key"])) {
       //Send remote key
       $key = "KEY_" . $_REQUEST["key"];
       $messagepart3 = chr(0x00) . chr(0x00) . chr(0x00) . chr(strlen(base64_encode($key))) . chr(0x00) . base64_encode($key);
       $part3 = chr(0x00) . chr(strlen($tvappstring)) . chr(0x00) . $tvappstring . chr(strlen($messagepart3)) . chr(0x00) . $messagepart3;
       socket_write($sock,$part3,strlen($part3));
       //echo $part3;
       echo "\n";
    } else if (isset($_REQUEST["text"])) {
       //Send text, e.g. in YouTube app's search, N.B. NOT BBC iPlayer app.
       $text = $_REQUEST["text"];
       $messagepart3 = chr(0x01) . chr(0x00) . chr(strlen(base64_encode($text))) . chr(0x00) . base64_encode($text);
       $part3 = chr(0x01) . chr(strlen($appstring)) . chr(0x00) . $appstring . chr(strlen($messagepart3)) . chr(0x00) . $messagepart3;
       socket_write($sock,$part3,strlen($part3));
	//echo $part3;
       echo "\n";   
    }

    socket_close($sock);

    echo "\n\n";
?>