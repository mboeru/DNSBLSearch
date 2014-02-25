<html>
<head><title></title>
<link rel="icon" href="favicon.ico" type="image/x-icon" />
</head>
<body>
<a href="/bllookup">Home</a>&nbsp&nbsp&nbsp<a href="?list=y">RBLs</a>&nbsp&nbsp&nbsp<a href="?ip=127.0.0.2">Test</a></br></br>
<form action="" method="GET">
IP/Domain: <input type="string" name="ip" autofocus="autofocus" value=<? echo $_GET['ip']; ?>>
<input type="submit" value="GO">
</form>

<?

//ini_set( 'display_errors', 0 );
include("bls.php");

//print_r($bls);


$ip = $_GET['ip'];

if ( isset($ip)) {

	if ( filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) || false === filter_var($ip, FILTER_VALIDATE_URL)) {
		echo "<table border=1>";
		echo "<tr><td>ID</td><td>DNSBL</td><td>STATUS</td><td>INFO</td></tr>";
		if (false === filter_var($ip, FILTER_VALIDATE_URL  )) {	
			$iptosplit = gethostbyname($ip);
			// var_dump($lookup);
		}
		else {
			$iptosplit = $ip;
		}
		$splitip = explode (".", $iptosplit);
		$iptolookup = "$splitip[3].$splitip[2].$splitip[1].$splitip[0]";
		//print_r(array_reverse($splitip));
		$counter=1;
		flush();
		foreach ( $bls as $rbl ) {
			echo "<tr>";
			//echo "Checking $rbl ...\n";
			$rbllookup = $iptolookup.".".$rbl;
			//echo $rbllookup."\n";
			$lookup = gethostbyname($rbllookup);
			if ( $lookup != $rbllookup || $lookup == $ip) {
				//echo $rbllookup." ".$lookup."\n";
				$qtxtresult = dns_get_record("$rbllookup", DNS_TXT);
				if ( ! isset($qtxtresult[0]['txt']) ) {
					$qtxtresult[0]['txt'] = "";
				}
				//echo $counter.". ".$rbl."\t LISTED ".$qtxtresult[0]['txt']."\n";
				echo "<td>".$counter."</td><td>".$rbl."</td><td><span style=color:red><b>LISTED</b></span></td><td>".$qtxtresult[0]['txt']."</td></tr>"; flush();
			}
			else {
				echo "<td>".$counter."</td><td>".$rbl."</td><td><span style=color:green>CLEAN</span></td><td></td></tr>"; flush();
				//echo $counter.". ".$rbl."\t CLEAN\n";
			}
			$counter++;

		}
		echo "</table>";
	}

	else {
		echo "Invalid, private of reserved IP Address\n";
	}
	//echo $ip."\t".$lookup."\n";
}

if ( $_GET['list'] == "y" ) {
	echo "<pre>";
	print_r($bls);
}

?>
</body></html>
