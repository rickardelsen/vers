<?php
function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    include 'db.php';
    $sql = "TRUNCATE TABLE histori";
    mysqli_query($link, $sql) or die ("Error insert : ".  mysqli_error($link));
    $target_dir = "upload/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $fileType = pathinfo($target_file,PATHINFO_EXTENSION);

    // Allow certain file formats
    if($fileType != "zip" ) {
        echo "Sorry, only zip files are allowed.";
        $uploadOk = 0;
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    $zip = new ZipArchive;
    if ($zip->open($target_file) === TRUE) {
        $zip->extractTo('upload/extract/.git/');
        $zip->close();

    } else {
        echo 'failed extract zip';
    }
    date_default_timezone_set('Asia/Jakarta');
    $dir = "upload/extract/";
    $output = array();
    chdir($dir);
    exec("git log > ../log.txt");
    $lines = file('../log.txt');
    $history = array();
    $name = array();
    foreach($lines as $line){
        if(strpos($line, 'commit ')===0){
            if(!empty($commit)){
                array_push($history, $commit);
                unset($commit);

            }
            $commit['hash'] = substr($line, strlen('commit '));
        }
        else if(strpos($line, 'Merge')===0){
            $commit['merge'] = substr($line, strlen('Merge: '));
        }
        else if(strpos($line, 'Author')===0){
            $commit['author'] = substr($line, strlen('Author: '));
            if(!in_array($commit['author'], $name)){
                array_push($name, $commit['author']);
            }
        }
        else if(strpos($line, 'Date')===0){
            $commit['date'] = substr($line, strlen('Date:   '));
            $commit['idate'] = strtotime($commit['date']);
        }
        else{
            if(isset($commit['message']) && ($line!=""))
                $commit['message'] .= $line;
            else
                $commit['message'] = $line;
        }
    }
    if(!empty($commit)) {
        array_push($history, $commit);
    }
    
    unlink('../log.txt');
    unlink('../'.basename($_FILES["fileToUpload"]["name"]));
    
    chdir('../../');
    rrmdir('upload/extract/');
    $x = sizeof($history);
    echo $x;
    for($i=0;$i<$x;$i++){
        $merge="";
        if(isset($history[$i]['merge'])){
            $merge=$history[$i]['merge'];
        }
        $sql = "INSERT INTO histori(commit,merge,author,date,idate,message) VALUES ('".$history[$i]['hash']."','".$merge."','".$history[$i]['author']."','".$history[$i]['date']."','".$history[$i]['idate']."','".mysqli_real_escape_string($link,$history[$i]['message'])."')";
        mysqli_query($link, $sql) or die ("Error insert : ".  mysqli_error($link));
    }
    if(count($name)<=1){
        header('Location: view.php');
    }else{
        session_start();
        $_SESSION['name']=$name;
        header('Location: choose.php');
    }
    
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
                    Pilih arsip histori git:
                    <input class="btn" type="file" name="fileToUpload" id="fileToUpload">
                    <input class="btn btn-primary" type="submit" value="Upload Berkas" name="submit">
                </form>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
    
    <script type="text/javascript" src="./js/bootstrap.js"></script>
    <script type="text/javascript" src="./js/jquery.js"></script>
</body>
</html>