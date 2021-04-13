<?php
 
$ALGORITHM = 'AES-256-CBC';
$IV    = '12dasdq3g5b2434b';
 
$error = '';
 
if (isset($_POST) && isset($_POST['action'])) {
  
  $password   = isset($_POST['password']) && $_POST['password']!='' ? $_POST['password'] : null;
  $action = isset($_POST['action']) && in_array($_POST['action'],array('c','d')) ? $_POST['action'] : null;
  $file     = isset($_FILES) && isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK ? $_FILES['file'] : null;
  
  if ($password === null) {
    $error .= 'Invalid Password<br>';
  }
  if ($action === null) {
    $error .= 'Invalid Action<br>';
  }
  if ($file === null) {
    $error .= 'Errors occurred while elaborating the file<br>';
  }
  
  if ($error === '') {
  
    $contenuto = '';
    $nomefile  = '';
  
    $contenuto = file_get_contents($file['tmp_name']);
    $filename  = $file['name'];
  
    switch ($action) {
      case 'c':
        $contenuto = openssl_encrypt($contenuto, $ALGORITHM, $password, 0, $IV);
        $filename  = $filename . '.crypto';
        break;
      case 'd':
        $contenuto = openssl_decrypt($contenuto, $ALGORITHM, $password, 0, $IV);
        $filename  = preg_replace('#\.crypto$#','',$filename);
        break;
    }
    
    if ($contenuto === false) {
      $error .= 'Errors occurred while encrypting/decrypting the file ';
    }
    
    if ($error === '') {
    
      header("Pragma: public");
      header("Pragma: no-cache");
      header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
      header("Cache-Control: post-check=0, pre-check=0", false);
      header("Expires: 0");
      header("Content-Type: application/octet-stream");
      header("Content-Disposition: attachment; filename=\"" . $filename . "\";");
      $size = strlen($contenuto);
      header("Content-Length: " . $size);
      echo $contenuto;    
      die;
      
    }
  
  }
  
}
 
 
?>
<!DOCTYPE html>
<html>
<head>
  <title>Encrypt File / Decrypt File</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
</head>
<body>
  <div class="container">
    <h1>Encrypt And Decrypt File</h1>
    <form method="POST" enctype="multipart/form-data">
      <input type="file" name="file" class="form-control">
      <label for="action">Action</label>
      <select class="form-control" name="action">
        <option value="">-- Choose --</option>
        <option value="c">Encrypt</option>
        <option value="d">Decrypt</option>
      </select>
      <label for="password">Password To Encrypt Your File</label>
      <input type="password" name="password" class="form-control"><br>
      <button type="submit" class="btn btn-primary">Execute</button>
    </form>
    <?php if ($error != ''){?>
      <br>
        <div class="col-12 alert alert-danger" role="alert">
          <?php echo $error;?>
      </div>
    <?php }?>
  </div>

</body>
</html>
