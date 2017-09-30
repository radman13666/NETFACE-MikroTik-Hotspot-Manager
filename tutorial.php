<?php require "disabler.php";?>
<!DOCTYPE html>
<html>
<head>
<style>
h2, h3, h4, p{text-align:center;}
ul {
    list-style-type: none;
    margin: 0;
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
height:40px;


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
<h2>TUTORIAL</h2>
<h3>HOW TO SETUP A HOTSPOT</h3>
<p></p>
<h4>STEP 1: ADD A MIKROTIK ROUTER TO THIS WEB INTERFACE</h4>
<p> Click "Routers" on the menu above. Go to the "ADD ROUTER" section. 
<p> Type in the <b>IP address</b> of the router you want to add, 
    for example 10.10.1.1. </p>
<p> Next, type in the <b>Name</b> of that router. 
    This could be anything you want, for example "Conference Hall Hotspot".</p>
<p> Lastly, type in the <b>RADIUS secret</b>. This is a secret password which 
    your router uses for authentication from the RADIUS server. Without it, 
    your router will not be able to provide internet to your users. 
    We recommend setting a secure password. </p>
<p> Click <b>ADD</b> when done. 
    You will see your added router at the bottom of that page.</p>
<p></p>

</body>

</html>
