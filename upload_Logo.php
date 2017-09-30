<?php 
function uploadMikLogo($router_addr, $logo_file) { 
  $trg_file_path = "/hotspot/$logo_file";
  $conn_id = ftp_connect($router_addr);
  $conn = new mysqli("localhost", "mgt", "admin_mgt", "manager_db");
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  $sql = "select * from login where id = 1";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $login_result = ftp_login($conn_id, $row["username"], $row["password"]);
  if (ftp_put($conn_id, $trg_file_path, 
              "/var/www/html/logo/" . $logo_file, FTP_BINARY)) {
    echo "Successfully uploaded your logo to router " . $router_addr . "\n\n";
  } else {
    echo "There was a problem uploading your logo to router " . $router_addr .
          "\n\n";
  }
  $conn->close();
  ftp_close($conn_id);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $target_dir = "logo/";
  $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
  $uploadOk = 1;
  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
  // Check if image file is a actual image or fake image
  if(isset($_POST["submit"])) {
      $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
      if($check !== false) {
          echo "File is an image - " . $check["mime"] . ".";
          $uploadOk = 1;
      } else {
          echo "File is not an image.";
          $uploadOk = 0;
      }
  }
  // Check if file already exists
  if (file_exists($target_file)) {
      echo "Sorry, the logo already exists.";
      $uploadOk = 0;
  }
  // Check file size
  $KiB = 1024;
  if ($_FILES["fileToUpload"]["size"] > (500 * $KiB)) {
      echo "Sorry, your file is too large.";
      $uploadOk = 0;
  }
  // Allow only PNG file format
  if($imageFileType != "png") {
      echo "Sorry, only PNG files are allowed.";
      $uploadOk = 0;
  }
  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
      echo "Sorry, your logo was not uploaded.";
  // if everything is ok, try to upload file
  } else {
      if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],
                              $target_file)) {
          rename($target_file, $target_dir . "company_logo.png");
          $conn = new mysqli("localhost", "radius", "radpass", "radius");
          if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
          }
          $sql_1 = "select nasname from nas";
          $result = $conn->query($sql_1);
          if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
              uploadMikLogo($row["nasname"], "company_logo.png");
            }
          }
          echo "Successfully uploaded your logo to Web UI.\n\n";
          $conn->close();
          header("Location: general_settings.php");
      } else {
          echo "There was an problem while uploading your logo to Web UI.\n\n";
      }
  }

}
?> 

