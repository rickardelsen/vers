<?php
require 'db.php';
session_start();
if(isset($_POST['update'])){
    $sql = "UPDATE quality SET totalchg=".$_POST['totalchg'].", minchg=".$_POST['minchg'].", avgchg=".$_POST['avgchg'].", maxchg=".$_POST['maxchg'].", totalmsg=".$_POST['totalmsg'].", minmsg=".$_POST['minmsg'].", avgmsg=".$_POST['avgmsg'].", maxmsg=".$_POST['maxmsg'].", mincpd=".$_POST['mincpd'].", avgcpd=".$_POST['avgcpd'].", maxcpd=".$_POST['maxcpd'].", mingap=".$_POST['mingap'].", avggap=".$_POST['avggap'].", maxgap=".$_POST['maxgap'];
    mysqli_query($link, $sql) or die ("Error insert : ".  mysqli_error($link));
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
            
            <div class="col-md-12">
                <p align="center"><h1>Quality Config</h1><h6>(Set value to zero for ignore parameter quality check)</h6></p>
                <form action="" method="post" enctype="multipart/form-data">
                <table class="table table-hover">
                
                    <tr>
                        <th>Parameter</th>
                        <th>Total</th>
                        <th>Minimum</th>
                        <th>Average</th>
                        <th>Maximum</th>
                    </tr>
                    <?php
                        $sql = "SELECT * FROM quality";
                        $res = mysqli_query($link, $sql);
                        while($row = mysqli_fetch_array($res, MYSQLI_ASSOC)){
                    ?>
                    
                    <tr>
                        
                        <td>Changes</td>
                        <td><input class="form-control" type="text" name="totalchg" value="<?php echo $row['totalchg'];?>"></input></td>
                        <td><input class="form-control" type="text" name="minchg" value="<?php echo $row['minchg'];?>"></input></td>
                        <td><input class="form-control" type="text" name="avgchg" value="<?php echo $row['avgchg'];?>"></input></td>
                        <td><input class="form-control" type="text" name="maxchg" value="<?php echo $row['maxchg'];?>"></input></td>
                        
                    </tr>
                    <tr>
                        
                        <td>Messages</td>
                        <td><input class="form-control" type="text" name="totalmsg" value="<?php echo $row['totalmsg'];?>"></input></td>
                        <td><input class="form-control" type="text" name="minmsg" value="<?php echo $row['minmsg'];?>"></input></td>
                        <td><input class="form-control" type="text" name="avgmsg" value="<?php echo $row['avgmsg'];?>"></input></td>
                        <td><input class="form-control" type="text" name="maxmsg" value="<?php echo $row['maxmsg'];?>"></input></td>
                        
                    </tr>
                    <tr>
                        
                        <td>Commit/Day</td>
                        <td></td>
                        <td><input class="form-control" type="text" name="mincpd" value="<?php echo $row['mincpd'];?>"></input></td>
                        <td><input class="form-control" type="text" name="avgcpd" value="<?php echo $row['avgcpd'];?>"></input></td>
                        <td><input class="form-control" type="text" name="maxcpd" value="<?php echo $row['maxcpd'];?>"></input></td>
                        
                    </tr>
                    <tr>
                        
                        <td>Gap</td>
                        <td></td>
                        <td><input class="form-control" type="text" name="mingap" value="<?php echo $row['mingap'];?>"></input></td>
                        <td><input class="form-control" type="text" name="avggap" value="<?php echo $row['avggap'];?>"></input></td>
                        <td><input class="form-control" type="text" name="maxgap" value="<?php echo $row['maxgap'];?>"></input></td>
                        
                    </tr>
                    <?php
                        }
                    ?>
                    
                           
                </table>
                <input class="btn btn-primary" type="submit" name="update" value="Update"></input>
                </form> 
            </div>

            
                
           
        </div>
    </div>
    
    <script type="text/javascript" src="./js/bootstrap.js"></script>
    <script type="text/javascript" src="./js/jquery.js"></script>
</body>
</html>