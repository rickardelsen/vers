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
session_start();
if(isset($_POST["submit"])) {
    include 'db.php';
    //Clear DB
    $sql = "TRUNCATE TABLE histori";
    mysqli_query($link, $sql) or die ("Error insert : ".  mysqli_error($link));
    //Prepare folder
    $target_dir = "upload/";
    $id = uniqid();
    if($_POST['submit']=="Upload Berkas"){
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $fileType = pathinfo($target_file,PATHINFO_EXTENSION);

        //Upload mecha
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
            $zip->extractTo("upload/".$id."/.git/");
            $zip->close();

        } else {
            echo "failed extract zip";
        }
    }
    if($_POST['submit']=="Clone"){
        mkdir("upload/".$id."/");
        exec("git clone ".$_POST['link']." upload/".$id."/");
    }
    date_default_timezone_set('Asia/Jakarta');
    $dir = "upload/".$id."/";
    chdir($dir);
    exec("git log > ../log.txt");
    $lines = file('../log.txt');
    $history = array();
    $name = array();
    //extract info from git info
    foreach($lines as $line){
        if(strpos($line, 'commit ')===0){
            if(!empty($commit)){
                array_push($history, $commit);
                unset($commit);

            }
            $commit['hash'] = substr($line, strlen('commit '),-1);
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
                $commit['message'] .= preg_replace('/\s+/', '', $line);
            else
                $commit['message'] = preg_replace('/\s+/', '', $line);
        }
    }
    if(!empty($commit)) {
        array_push($history, $commit);
    }
    
    //Insert to DB
    $x = sizeof($history);
    date_default_timezone_set("Asia/Jakarta");
    $tg=$history[$x-1]['idate'];
    $tgl = date("Y-m-d", $tg);
    $epoch = strtotime($tgl." 00:00:00");
    for($i=0;$i<$x;$i++){
        $history[$i]['day']=(int) (($history[$i]['idate']-$epoch)/86400)+1;
    }
    
    echo $x;
    for($i=0;$i<$x;$i++){
        $merge="";
        if(isset($history[$i]['merge'])){
            $merge=$history[$i]['merge'];
        }
        $sql = "INSERT INTO histori(commit,merge,author,date,idate,message,day) VALUES ('".$history[$i]['hash']."','".$merge."','".mysql_escape_string($history[$i]['author'])."','".$history[$i]['date']."','".$history[$i]['idate']."','".strlen($history[$i]['message'])."',".$history[$i]['day'].")";
        mysqli_query($link, $sql) or die ("Error insert : ".  mysqli_error($link));
    }
    //Extract diff info in every submitted commit
    for($i=$x-2;$i>=0;$i--){
        // echo $history[$i]['hash'].$history[$i+1]['hash'];
        $true=1;
        exec("git diff --stat ".$history[$i]['hash']." ".$history[$i+1]['hash']." > ../diff.txt");
        $diff = file('../diff.txt');
        if(!isset($diff[0])){
            $true=0;
        }
        // echo "++".filesize('../diff.txt')."++";
        if($true==1){
            $diff = array_reverse($diff);
            $string=explode(", ", $diff[0]);
            //if there is insertions and deletions
            if(count($string)>2){
                $insertion=explode(" ", $string[1]);
                $insert=$insertion[0];
                $deletion=explode(" ", $string[2]);
                $delete=$deletion[0];
                $changes=$insert+$delete;
            }
            //if there is just insertions or deletions
            else{
                $change=explode(" ", $string[1]);
                $changes=$change[0];
            }
            // echo "===".$changes;
            $sql = "UPDATE histori SET changes=".$changes." WHERE commit='".$history[$i]['hash']."'";
            mysqli_query($link, $sql) or die ("Error insert : ".  mysqli_error($link));
        }
    }
    //check exclude file already set or not. n=0 -> not set already
    $exclude = file('.git/info/exclude');
    $n=0;
    for($i=0;$i<count($exclude);$i++){
        if(substr($exclude[$i],0,1)!="#"){
            $n++;
        }
    }
    $_SESSION['exclude']=$n;
    //check remote repo already set or not
    $confg = file('.git/config');
    $cf=0;
    for($i=0;$i<count($confg);$i++){
        if(strpos($confg[$i], '[remote')===0){
            $cf++;
        }
    }
    $_SESSION['config']=$cf;
    exec("git status > ../stats.txt");
    $stats = file('../stats.txt');
    $stats = array_reverse($stats);
    $st=0;
    if(strpos($stats[0],"nothing to commit, working directory clean")===0){
        $st=1;
    }
    $_SESSION['stat']=$st;
    exec("gradle > ../build.txt");
    $build = file('../build.txt');
    $build = array_reverse($build);
    $bl=0;
    if(strpos($build[2],"BUILD SUCCESSFUL")===0){
        $bl=1;
    }
    $_SESSION['build']=$bl;
    var_dump($build);
    //clear working folder and files
    // unlink('../log.txt');
    // unlink('../diff.txt');
    // if($_POST['submit']=="Upload Berkas"){
    //     unlink('../'.basename($_FILES["fileToUpload"]["name"]));
    // }
    // chdir('../../');
    // exec("attrib -s -h upload/".$id."/.git");
    // rrmdir('upload/'.$id);
    header('Location: view.php');
    // if(count($name)<=1){
    //     header('Location: view.php');
    // }else{
    //     session_start();
    //     $_SESSION['name']=$name;
    //     header('Location: choose.php');
    // }
    
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
                    Pilih arsip histori git :
                    <input class="btn" type="file" name="fileToUpload" id="fileToUpload">
                    <input class="btn btn-primary" type="submit" value="Upload Berkas" name="submit"><br />
                    Atau masukkan link repository git :
                    <input class="form-control" type="text" name="link" placeholder="Link repository Git">
                    <input class="btn btn-primary" type="submit" value="Clone" name="submit">
                </form>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
    
    <script type="text/javascript" src="./js/bootstrap.js"></script>
    <script type="text/javascript" src="./js/jquery.js"></script>
</body>
</html>