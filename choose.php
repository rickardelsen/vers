<?php
session_start();
$n = count($_SESSION['name']);
$nama = array();
for($i=0;$i<$n;$i++){
    $nama[$i]=$_SESSION['name'][$i];
}
array_merge($nama, $_SESSION['name']);
if(isset($_POST['submit'])){
    $_SESSION['user']=$_POST['name'];
    header('Location: view.php');
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>GIT Log Parser</title>
    <link rel="stylesheet" type="text/css" href="./css/todc-bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./css/bootstrap-theme.css">
	
</head>
<body>
    <?php
            include "./header.php";
    ?>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <form action="" method="post" enctype="multipart/form-data">
                    <select name="name">
                        <?php
                            echo $nama[0];
                            for($i=0;$i<$n;$i++){
                                echo "<option value=\"".$nama[$i]."\">".$nama[$i]."</option>";
                            }
                        ?>
                    </select>
                    <input class="btn btn-primary" type="submit" value="pilih User" name="submit">
                </form>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
    
    <script type="text/javascript" src="./js/bootstrap.js"></script>
    <script type="text/javascript" src="./js/jquery.js"></script>
</body>
</html>