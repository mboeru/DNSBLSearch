<html>
<head><title>DNSBL Search <mboeru@gmail.com></title></head>
<body>
<form action="" method="GET">
IP/Domain: <input type="string" name="ip" value=<?php echo $_GET['ip']; ?>>
<input type="submit" value="GO">
</form>

<?php

//ini_set( 'display_errors', 0 );
include("bls.php");


$ip = $_GET['ip'];

if ( isset($ip)) {

	if ( filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) || false === filter_var($ip, FILTER_VALIDATE_URL)) {

		echo "<table border=1>";
		echo "<tr><td>ID</td><td>DNSBL</td><td>STATUS</td><td>INFO</td></tr>";

		if (false === filter_var($ip, FILTER_VALIDATE_URL  )) {	
			$iptosplit = gethostbyname($ip);
		}
		else {
			$iptosplit = $ip;
		}
		$splitip = explode (".", $iptosplit);
		$iptolookup = "$splitip[3].$splitip[2].$splitip[1].$splitip[0]";
		$counter=1;
		foreach ( $bls as $rbl ) {
			echo "<tr>";
			$rbllookup = $iptolookup.".".$rbl;
			$lookup = gethostbyname($rbllookup);
			if ( $lookup != $rbllookup || $lookup == $ip) {
				$qtxtresult = dns_get_record("$rbllookup", DNS_TXT);
				if ( ! isset($qtxtresult[0]['txt']) ) {
					$qtxtresult[0]['txt'] = "";
				}
				echo "<td>".$counter."</td><td>".$rbl."</td><td><span style=color:red><b>LISTED</b></span></td><td>".$qtxtresult[0]['txt']."</td></tr>";
			}
			else {
				echo "<td>".$counter."</td><td>".$rbl."</td><td><span style=color:green>CLEAN</span></td><td></td></tr>";
			}
			$counter++;

		}
		echo "</table>";
	}

	else {
		echo "Invalid, private of reserved IP Address\n";
	}

}
echo "</body></html>"
?>
