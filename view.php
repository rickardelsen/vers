<?php
require 'db.php';
session_start();
// $user = nl2br($_SESSION['user']);
// list($a,$b) = explode("<", $user);

$sql = "SELECT * FROM quality";
$res = mysqli_query($link, $sql);
while($row = mysqli_fetch_array($res, MYSQLI_ASSOC)){
    $totalchg = $row['totalchg'];
    $minchg = $row['minchg'];
    $avgchg = $row['avgchg'];
    $maxchg = $row['maxchg'];
    $totalmsg = $row['totalmsg'];
    $minmsg = $row['minmsg'];
    $avgmsg = $row['avgmsg'];
    $maxmsg = $row['maxmsg'];
    $mincpd = $row['mincpd'];
    $avgcpd = $row['avgcpd'];
    $maxcpd = $row['maxcpd'];
    $mingap = $row['mingap'];
    $avggap = $row['avggap'];
    $maxgap = $row['maxgap'];

}

$a = "<b>(<font color=\"green\">A</font>)</b>";
$b = "<b>(<font color=\"blue\">B</font>)</b>";
$c = "<b>(<font color=\"red\">C</font>)</b>";
$g = "<b>(<font color=\"green\">Good</font>)</b>";
$p = "<b>(<font color=\"red\">Poor</font>)</b>";

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
                <p align="center"><h1>Changes</h1></p>
                <table class="table table-hover">
                    <tr>
                        <th>Author</th>
                        <th>Number of Commits</th>
                        <th>Min Changes</th>
                        <th>Average Changes</th>
                        <th>Max Changes</th>
                        <th>Total Changes</th>
                    </tr>
                    <?php
                        
                        $sql = "SELECT idate, author, count(changes) as cons, min(changes) as mins, avg(changes) as avgs, max(changes) as maxs, sum(changes) as sums FROM histori WHERE changes > 0 GROUP BY author";
//                        if(isset($_SESSION['user'])){
//                            $sql .= " WHERE author LIKE '".$_SESSION['user']."%'";
//                        }
//                        $sql .= " ORDER BY id asc";
                        
                        $res = mysqli_query($link, $sql);
                        $x=0;
                        while($row = mysqli_fetch_array($res, MYSQLI_ASSOC)){
                            $author[$x]['nama']=$row['author'];
                            ?>
                    <tr>
                        <td><?php echo htmlentities($row['author']);?></td>
                        <td><?php echo $row['cons'];?></td>
                        <td><?php echo $row['mins'];if($row['mins']>=$minchg){echo $g;}else{echo $p;}?></td>
                        <td><?php echo $row['avgs'];?></td>
                        <td><?php echo $row['maxs'];?></td>
                        <td><?php echo $row['sums'];?></td>
                        
                    </tr>
                            <?php
                        $x++;

                        }
                        $sql = "SELECT MAX(day) as cpd from histori";
                        $res = mysqli_query($link, $sql);
                        while($row = mysqli_fetch_array($res, MYSQLI_ASSOC)){
                            $maxday=$row['cpd'];
                        }    
                    ?>
                </table>
                
            </div>

            <div class="col-md-12">
                <p align="center"><h1>Message</h1></p>
                <table class="table table-hover">
                    <tr>
                        <th>Author</th>
                        <th>Number of Commits</th>
                        <th>Min Messages</th>
                        <th>Average Messages</th>
                        <th>Max Messages</th>
                        <th>Total Messages</th>
                    </tr>
                    <?php
                        
                        $sql = "SELECT idate, author, count(changes) as cons, min(message) as minm, avg(message) as avgm, max(message) as maxm, sum(message) as summ FROM histori WHERE changes > 0 GROUP BY author";
//                        if(isset($_SESSION['user'])){
//                            $sql .= " WHERE author LIKE '".$_SESSION['user']."%'";
//                        }
//                        $sql .= " ORDER BY id asc";
                        
                        $res = mysqli_query($link, $sql);
                        $x=0;
                        while($row = mysqli_fetch_array($res, MYSQLI_ASSOC)){
                            $author[$x]['nama']=$row['author'];
                            ?>
                    <tr>
                        <td><?php echo htmlentities($row['author']);?></td>
                        <td><?php echo $row['cons'];?></td>
                        <td><?php echo $row['minm'];?></td>
                        <td><?php echo $row['avgm'];?></td>
                        <td><?php echo $row['maxm'];?></td>
                        <td><?php echo $row['summ'];?></td>
                        
                    </tr>
                            <?php
                        $x++;

                        }
                        $sql = "SELECT MAX(day) as cpd from histori";
                        $res = mysqli_query($link, $sql);
                        while($row = mysqli_fetch_array($res, MYSQLI_ASSOC)){
                            $maxday=$row['cpd'];
                        }    
                    ?>
                </table>
                
            </div>

            <div class="col-md-12">
                <p align="center"><h1>Commit/Day</h1></p>
                <table class="table table-hover">
                    <tr>
                        <th>Author</th>
                        <th>Min Commit/day</th>
                        <th>Average Commit/day</th>
                        <th>Max Commit/day</th>
                    </tr>
                    <?php
                        for($i=0;$i<$x;$i++){
                            $sql = "SELECT author, day, COUNT(day) as cpd from histori where author='".$author[$i]['nama']."' group by day";
                            $res = mysqli_query($link, $sql);
                            while($row = mysqli_fetch_array($res, MYSQLI_ASSOC)){
                                $author[$i]['day'][$row['day']]=$row['cpd'];
                            }
                        }
                        for($i=0;$i<$x;$i++){
                            ?>
                        
                        
                    <tr>
                        <td><?php echo htmlentities($author[$i]['nama']);?></td>
                        <td><?php 
                        if(count($author[$i]['day'])==$maxday){
                            echo min($author[$i]['day']);
                        }else{
                            echo "0";
                        }
                        ?></td>
                        <td><?php echo array_sum($author[$i]['day'])/$maxday;?></td>
                        <td><?php echo max($author[$i]['day']);?></td>
                        
                    </tr>
                            <?php
                            
                        }
                    ?>
                </table>
                
            </div>
            <div class="col-md-12">
                <p align="center"><h1>Commit Gap</h1></p>
                <table class="table table-hover">
                    <tr>
                        <th>Author</th>
                        <th>Min Gap</th>
                        <th>Average Gap</th>
                        <th>Max Gap</th>
                    </tr>
                    <?php
                        for($i=0;$i<$x;$i++){
                            $sql = "SELECT author, idate from histori where author='".$author[$i]['nama']."' order by idate asc";
                            $res = mysqli_query($link, $sql);
                            $min=0;
                            $max=0;
                            $prev=0;
                            $temp=0;
                            $sum=0;
                            $cnt=0;
                            while($row = mysqli_fetch_array($res, MYSQLI_ASSOC)){
                                if($cnt==0){
                                    $prev=$row['idate'];
                                }else{
                                    $temp=$row['idate']-$prev;
                                    $sum+=$temp;
                                    if($cnt==1){
                                        $min=$temp;
                                    }
                                    if($min>$temp){
                                        $min=$temp;
                                    }
                                    if($max<$temp){
                                        $max=$temp;
                                    }
                                    $prev=$row['idate'];
                                }
                                $cnt++;
                            }
                        
                        
                            ?>
                        
                        
                    <tr>
                        <td><?php echo htmlentities($author[$i]['nama']);?></td>
                        <td><?php echo $min;?></td>
                        <td><?php if($cnt>1){echo $sum/($cnt-1);}else{echo "0";}?></td>
                        <td><?php echo $max;?></td>
                        
                    </tr>
                            <?php
                            
                        }
                    ?>
                </table>
                
            </div>
            <div class="col-md-12">
                <p align="center"><h1>Whole Project Properties</h1></p>
                <table class="table table-hover">
                    <tr>
                        <th>Exclude</th>
                        <th>Config</th>
                        <th>Status</th>
                        <th>Build</th>
                    </tr>
                    <?php
                        
                            ?>
                        
                        
                    <tr>
                        <td><?php 
                        if($_SESSION['exclude']>0){
                            echo "Configured";
                        }else{
                            echo "Unconfigured";
                        }
                        ?></td>
                        <td><?php 
                        if($_SESSION['config']>0){
                            echo "Configured";
                        }else{
                            echo "Unconfigured";
                        }
                        ?></td>
                        <td><?php 
                        if($_SESSION['stat']>0){
                            echo "Clean";
                        }else{
                            echo "Dirty";
                        }
                        ?></td>
                        <td><?php 
                        if($_SESSION['build']>0){
                            echo "Successful";
                        }else{
                            echo "Failed";
                        }
                        ?></td>
                        
                    </tr>
                            <?php
                            
                        
                    ?>
                </table>
                
            </div>
        </div>
    </div>
    
    <script type="text/javascript" src="./js/bootstrap.js"></script>
    <script type="text/javascript" src="./js/jquery.js"></script>
</body>
</html>