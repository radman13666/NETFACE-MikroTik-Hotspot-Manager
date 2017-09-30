<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $servername = "localhost";
  $username = "radius";
  $password = "radpass";
  $dbname = "radius";
  
  $addr = $_POST["router_addr"];

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql_1 = "delete from nas where nasname = \"$addr\"";
  if ($conn->query($sql_1) == FALSE) {
    echo "Error: " . $sql_1 . "\h" . $conn->error . "\n";
  } else {
    header("Location: routers.php");
    //echo "Router removed!";
  }

  $conn->close();
}
?>


