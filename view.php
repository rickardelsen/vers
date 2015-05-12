<?php
require 'db.php';
session_start();
$user = nl2br($_SESSION['user']);
list($a,$b) = explode("<", $user);
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
            
            <div class="col-md-12">
                <table class="table table-hover">
                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Merge</th>
                        <th>Author</th>
                        <th>Date</th>
                        <th>Epoch</th>
                        <th>Message</th>
                    </tr>
                    <?php
                        
                        $sql = "SELECT * FROM histori WHERE author LIKE '".$a."%' ORDER BY id ASC";
//                        if(isset($_SESSION['user'])){
//                            $sql .= " WHERE author LIKE '".$_SESSION['user']."%'";
//                        }
//                        $sql .= " ORDER BY id asc";
                        
                        $res = mysqli_query($link, $sql);
                        $x=0;
                        $commit=0;
                        $max=0;
                        while($row = mysqli_fetch_array($res, MYSQLI_ASSOC)){
                            ?>
                    <tr>
                        <td><?php echo $row['id'];?></td>
                        <td><?php echo $row['commit'];?></td>
                        <td><?php echo $row['merge'];?></td>
                        <td><?php echo $row['author'];?></td>
                        <td><?php echo $row['date'];?></td>
                        <td><?php echo $row['idate'];?></td>
                        <td><?php echo $row['message'];?></td>
                        
                    </tr>
                            <?php
                        
                            $commit++;
                            if($x>0){
                                $rex=$epoch[$x-1]-$row['idate'];
                                if($rex>$max){
                                    $max=$rex;
                                }
                                if($rex<$min){
                                    $min=$rex;
                                }
                                $epoch[$x]=$row['idate'];
                            }else{
                                $epoch[$x]=$row['idate'];
                                $max=0;
                                $min=$epoch[$x];
                            }
                            
                            $x++;
                        }
                    ?>
                </table>
                <?php
                    echo "Commit : ".$commit."<br />";
                    echo "Min : ".$min."<br />";
                    echo "Max : ".$max."<br />";
                ?>
            </div>
            
        </div>
    </div>
    
    <script type="text/javascript" src="./js/bootstrap.js"></script>
    <script type="text/javascript" src="./js/jquery.js"></script>
</body>
</html>