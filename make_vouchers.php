<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $servername = "localhost"; 
  $username = "radius";
  $password = "radpass";
  $dbname = "radius";
  
  $voucher_prefix = $_POST["voucher_prefix"];
  $num_vouchers = $_POST["num_vouchers"];
  $time_limit = (int) (((float) $_POST["time_limit"]) * 3600); #seconds
  $group_name = "group_" . $_POST["time_limit"] . "_hours";

  #file to download
  $voucher_file_name = "$group_name" . "_vouchers.txt";
  $vouchers_str = "";

  #set header
  header("Content-Type: text/plain");
  header("Content-disposition: attachment; filename=$voucher_file_name");
  header("Content-Transfer-Encoding: binary");
  header("Pragma: no-cache");
  header("Expires: 0");

  $conn = new mysqli($servername, $username, $password, $dbname);
  
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  #make vouchers
  for ($x = 0; $x < $num_vouchers; $x++) {
    $randu = substr(md5(microtime()), rand(0, 26), 5);
    $voucher_login = $voucher_prefix . "--" . $randu;
    $randpw = substr(md5(microtime()), rand(0, 26), 5);
    $voucher_pw = $randpw;

    $sql_1 = "insert into radcheck (username, attribute, op, value) values " .
            "(\"$voucher_login\", \"MD5-Password\", \":=\"," . 
            " MD5(\"$voucher_pw\"))";
    if ($conn->query($sql_1) == FALSE) {
      echo "Error :" . $sql_1 . "\n" .  $conn->error . "\n";
    }

    $vouchers_str =  $vouchers_str . 
            "Username: " . $voucher_login . "\nPassword: " 
            . $voucher_pw . "\n\n";

    $sql_2 = "insert into radusergroup (username, groupname, priority)" .
              " values (\"$voucher_login\", \"$group_name\", \"1\")";
    if ($conn->query($sql_2) == FALSE) {
      echo "Error :" . $sql_2 . "\n" . $conn->error . "\n";
    }

  }
  
  $sql_groupcheck = "select attribute from radgroupcheck " . 
                      "where groupname = \"$group_name\"";
  $sql_groupreply = "select attribute from radgroupreply " . 
                      "where groupname = \"$group_name\"";
  $result_1 = $conn->query($sql_groupcheck);
  $result_2 = $conn->query($sql_groupreply);
  
  if ($result_1->num_rows == 0 && $result_2->num_rows == 0) {
    #create group attributes
  
    #in radgroupcheck
    $sql_3 = "insert into radgroupcheck (groupname, attribute, op, value)" . 
              " values (\"$group_name\", \"Max-All-Session\"," . 
              " \":=\", \"$time_limit\")";
    if ($conn->query($sql_3) == FALSE) {
        echo "Error :" . $sql_3 . "\n" . $conn->error . "\n";
    }
  
    #in radgroupreply
    $sql_4 = "insert into radgroupreply (groupname, attribute, op, value)" .
              " values (\"$group_name\", \"Session-Timeout\"," . 
              " \":=\", \"$time_limit\"), " .
              "(\"$group_name\", \"Framed-Compression\"," .
              " \":=\", \"Van-Jacobsen-TCP-IP\"), " .
              "(\"$group_name\", \"Framed-Protocol\"," .
              " \":=\", \"PPP\"), " .
              "(\"$group_name\", \"Framed-MTU\"," .
              " \":=\", \"1500\"), " .
              "(\"$group_name\", \"Service-Type\"," .
              " \":=\", \"Login-User\")";
    if ($conn->query($sql_4) == FALSE) {
        echo "Error :" . $sql_4 . "\n" . $conn->error . "\n";
    }
  }
  
  $conn->close();
  
  #write file
  echo "$vouchers_str";
}

?>
