<?php

require "routeros_api.class.php";

/*function checkUsers($num_users) {
  $final_num_users = 0;
  if ($num_users > 251) {
    $final_num_users = 251;
  } else if ($num_users < 10) {
    $final_num_users = 10;
  } else {
    $final_num_users = $num_users;
  }
  return $final_num_users;
}*/

function checkOctet($octet) {
  $final_octet = 0;
  if ($octet > 255) {
    $final_octet = 255;
  } else if ($octet < 0) {
    $final_octet = 0;
  } else {
    $final_octet = $octet;
  }
  return $final_octet;
}

function multiexplode ($delimiters, $string) { 
  $ready = str_replace($delimiters, $delimiters[0], $string);
  $launch = explode($delimiters[0], $ready);
  return  $launch;
}

function genPool($oct1, $oct2, $oct3, $oct4, $mask) {
  $oct = 3;
  $du = 24;
  $dl = 16;
  $first_host = "";
  $last_host = "";

  $pool = "";
  #find the octet we are working in
  if ($mask >= 16 and $mask <= 24) {
    $oct = 3; 
    $du = 24; 
    $dl = 16;
  } else if ($mask >= 24 and $mask < 32) {
    $oct = 4;
    $du = 32;
    $dl = 24;
  }
  #get the subnet increment
  $inc = 2 ** ($du - $mask);

  #get the number of hosts
  $hosts = (2 ** (32 - $mask)) - 2;

  #validate network address and get the pool range
  if ($oct == 3 and ($oct3 % $inc == 0)) {
    $serv = "$oct1.$oct2.$oct3.1";
    $first_host = "$oct1.$oct2.$oct3.2";
    $last_host = "$oct1.$oct2." . ($oct3 + ($inc - 1)) . ".254";
  } else if ($oct == 4 and ($oct4 % $inc == 0)) {
    $serv = "$oct1.$oct2.$oct3." . ($oct4 + 1);
    $first_host = "$oct1.$oct2.$oct3." . ($oct4 + 2);
    $last_host = "$oct1.$oct2.$oct3." . (($oct4 + $inc) - 2);
  } 
#  else {
#    echo "bad network\n";
#    exit;
#  }

  #output results
  return array("pool" => "$first_host-$last_host", "server" => $serv);
}

function setup($router_addr, $username, $password) { 
	$API = new RouterosAPI();
	$API->debug = true;
	$poolName = randomString() . randomString() . randomString();
	$dhcpName = randomString() . randomString() . randomString(); 
	$profName = randomString() . randomString() . randomString();
	
  $net = $_POST["hs_net"];
  $octs = multiexplode(array(".", "/"), $net);
  $mask = $octs[4];
  
  $pool_array = genPool($octs[0], $octs[1], $octs[2], $octs[3], $mask);
  $serv = $pool_array["server"];
  $pool = $pool_array["pool"];
  
  if ($API->connect($router_addr, $username, $password)) {

		$API->write("/interface/wireless/set", false);
		$API->write("=.id=*6", false);
		$API->write("=ssid=" . $_POST["hs_ssid"], false);
    $API->write("=mode=ap-bridge", false);
    $API->write("=wireless-protocol=802.11", false);
    $API->write("=disabled=no");
		$READ = $API->read(false);
/*
		$API->write("/interface/bridge/add", false);
    $API->write("=name=bridge-hotspot", false);
    $API->write("=arp=enabled", false);
    $API->write("protocol-mode=stp", false);
  	$API->write("=disabled=no");
		$READ = $API->read(false);

		$API->write("/interface/bridge/port/add", false);
		$API->write("=interface=wlan1", false);
		$API->write("=bridge=bridge-hotspot");
		$READ = $API->read(false);
*/
		$API->write("/ip/address/add", false);
		$API->write("=address=$serv/$mask", false);
		$API->write("=interface=wlan1");
		$READ = $API->read(false);

		$API->write("/ip/pool/add", false);
		$API->write("=name=" . $poolName, false);
		$API->write("=ranges=$pool");
		$READ = $API->read(false);
				
		$API->write("/ip/dhcp-server/add", false);
		$API->write("=name=" . $dhcpName, false);
		$API->write("=interface=wlan1", false);
		$API->write("=address-pool=" . $poolName, false);
		$API->write("=lease-time=3d", false);
		$API->write("=disabled=no");
		$READ = $API->read(false);

		$API->write("/ip/dhcp-server/network/add", false);
		$API->write("=address=$net", false);
		$API->write("=gateway=$serv");
		$READ = $API->read(false);

    $API->write("/routing/ospf/network/add", false);
    $API->write("=network=$net", false);
    $API->write("=area=backbone");
    $READ = $API->read(false);

		$API->write("/ip/hotspot/profile/add", false);
		$API->write("=name=" . $profName, false);
		$API->write("=dns-name=" . $_POST["hs_dns"], false);
		$API->write("=hotspot-address=$serv", false);
		$API->write("=use-radius=yes", false);
		$API->write("=radius-accounting=yes", false);
		$API->write("=login-by=http-pap");
		$READ = $API->read(false);
		
		$API->write("/ip/hotspot/add", false);
		$API->write("=profile=" . $profName, false);
		$API->write("=name=" . $_POST["hs_ssid"], false);
		$API->write("=address-pool=" . $poolName, false);
		$API->write("=interface=wlan1", false);
		$API->write("=addresses-per-mac=1", false);
		$API->write("=disabled=no");
		$READ = $API->read(false);

//		$API->write("/ip/firewall/nat/set", false);
//		$API->write("=.id=*1", false);
//		$API->write("=src-address=$net");
//		$READ = $API->read(false);

    $conn = new mysqli("localhost", "mgt", "admin_mgt", "manager_db");
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    $sql = "select * from radlogin where id = 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

		$API->write("/radius/add", false);
		$API->write("=address=" . $row["radaddress"], false);
		$API->write("=secret=" . $row["radsecret"], false);
		$API->write("=service=hotspot", false);
		$API->write("=disabled=no");
		$READ = $API->read(false);

		$API->write("/radius/incoming/set", false);
		$API->write("=accept=yes");
		$READ = $API->read(false);

    $conn->close();
	}
}

function randomString() {
	$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randString = '';
	for ($i = 0; $i < 10; $i++) {
		$randString = $characters[rand(0, strlen($characters))];
	}
	return $randString;
}

function checkOctet_bool($octet) {
  $octet_ok = TRUE;
  if ($octet > 255 or $octet < 0) {
    $octet_ok = FALSE;
  } else {
    $octet = TRUE;
  }
  return $octet_ok;
}

?>

