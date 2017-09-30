<?php require "disabler.php";?>
<!DOCTYPE html>
<html>
<head>
<style>
h2{text-align:center;}
ul {
    list-style-type: none;
    margin: 0;
    margin-top: 120px;
    padding: 0;
    width: 250px;
    background-color: #f1f1f1;
    height:auto;
    position:fixed;
    overflow:auto;
    border:1px solid;
    font-weight:bold;
    text-transform:uppercase;
}

li a {
    display:block;
    color: #000;
    padding: 8px 0 8px 16px;
    text-decoration: none;
    text-align:left;
    border-bottom:1px solid;
}

/* Change the link color on hover */
li a:hover {
    background-color: #555;
    color: white;
}

li a:.active {
    background-color: #4CAF50;
    color: white;
}
li:last-child {
    border-bottom: none;
}
    
*{font-family:Arial;}
label{
width:180px;
clear:left;

padding-right:0px;
}

input,label {
float:left;
border-radius:10px;
}
body {
background-color:#DFE2DB; 
}
input[type=submit]{
color:white;
text-shadow: 0px 1px 1px #ffffff;
border-bottom: 2px solid #b2b2b2;
background-color: #333;
height:50px;
width:100px;
}

fieldset {
border: 1px solid #dcdcdc;
border-radius: 10px;
padding: 20px;
text-align: left;}
legend {
background-color: #efefef;
border: 1px solid #dcdcdc;
border-radius: 10px;
padding: 10px 20px;
text-align: left;
}

div.form
{
display:block;
text-align:center;
}
form
{
display:inline-block;
margin-left:auto;
margin-right:auto;
text-align:left;
}
</style>
</head>

<body>
<p></p>
<?php require 'menu.php';?>
<h2>VOUCHERS</h2>
<div class="form">
<form action="<?php echo htmlspecialchars('make_vouchers.php');?>" 
      method="post">
<fieldset>
<legend><b>MAKE NEW VOUCHERS<b></legend>
<label>Voucher prefix:</label>
<input type="text" name="voucher_prefix" required="required"><br><br>
<label>Number of Vouchers:</label>
<input type="text" name="num_vouchers" required="required"><br><br>
<label>Time Limit (in hours):</label>
<input type="text" name="time_limit" required="required"><br><br>
<input type="submit" value="SUBMIT">
</fieldset>
</form>

<br>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"
      method="post">
<fieldset>
<legend><b>FLUSH OLD VOUCHERS<b></legend>
<input type=submit value="FLUSH"><br><br>
</fieldset>
</form>
</div>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$servername = "localhost";
  $username = "radius";
  $password = "radpass";
  $dbname = "radius";
  
  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql_1 = "drop table radcheck";
  $sql_2 = "drop table radusergroup";

  if ($conn->query($sql_1) == FALSE & $conn->query($sql_2) == FALSE) {
    die("Query \"$sql_1\" or \"$sql_2\" failed!");
  } else {
    $sql_3 = "CREATE TABLE radcheck (
                id int(11) unsigned NOT NULL auto_increment,
                username varchar(64) NOT NULL default '',
                attribute varchar(64)  NOT NULL default '',
                op char(2) NOT NULL DEFAULT '==',
                value varchar(253) NOT NULL default '',
                PRIMARY KEY  (id),
                KEY username (username(32))
              )";
    $sql_4 = "CREATE TABLE radusergroup (
                username varchar(64) NOT NULL default '',
                groupname varchar(64) NOT NULL default '',
                priority int(11) NOT NULL default '1',
                KEY username (username(32))
              )";
    if ($conn->query($sql_3) == FALSE & $conn->query($sql_4) == FALSE) {
     die("Query \"$sql_3\" or \"$sql_4\" failed!"); 
    } else {
      echo "<br><br><p>
      <span style=\"color:green\">Vouchers flushed!</span>
      </p><br><br>";
    }
  }

  $conn->close();

}
?>

</body>

</html>
