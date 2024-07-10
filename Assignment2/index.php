<?php
session_start();
include("database.php");
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}


$user_id = $_SESSION["user_id"]; 

$user_query = "SELECT full_name FROM users WHERE userid = $user_id";
$user_result = mysqli_query($conn, $user_query);

if ($user_result && mysqli_num_rows($user_result) > 0) {
    $user_row = mysqli_fetch_assoc($user_result);
    $user_name = $user_row['full_name'];
} else {
    $user_name = "User";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>User Dashboard</title>
    <style>
            table, th, td {
    border: 1px solid cyan;
    border-radius: 10px;
    padding: 10px;
  }
    </style>
</head>
<body>
<div class="container">
        <h1>Welcome, <?php echo $user_name; ?>!</h1>
        <a href="logout.php" class="btn btn-warning">Logout</a>
    </div>

    <div class = "container">

    

    <form action="index.php" method="post">
        <h1>Add a new task</h1><br>
        
        <input type="text" name="taskname", placeholder = "Full Name:"><br>
        <label>Due Time</label><br>
        <input type="time" name="duetime", placeholder = "Add Time:"><br>
        <label>Priority</label>
            <select name="priority">
                <option value="High">High</option>
                <option value="Medium">Medium</option>
                <option value="Low">Low</option>
            </select><br>
        <input type="submit" name="submit" value="Add">

        <h1>Progress</h1><br>
        <?php 
        $query = "SELECT * FROM tasks WHERE userid = $user_id";
        $resul_query = mysqli_query($conn, $query);
        if(mysqli_num_rows($resul_query) > 0){
            while($rows = mysqli_fetch_assoc($resul_query)){
                echo "<input type='radio' name='task_status' value='".$rows["taskid"]."'> ";
                echo $rows["taskid"]. " ", $rows["name"]." ", $rows["due_date"]. " " ,$rows["status"]." ".$rows["priority"];
                echo "<br>";
            }
        }
        ?>
        <input type="submit" name="prgs_submit" value="Update Status">
    </form>

    <h1>Task Table</h1>
    <table id="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Due Time</th>
                <th>Status</th>
                <th>Priority</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $query = "SELECT * FROM tasks WHERE userid = $user_id";
        $resul_query = mysqli_query($conn, $query);
        if(mysqli_num_rows($resul_query) > 0){
            while($rows = mysqli_fetch_assoc($resul_query)){
                echo "<tr><td>". $rows["taskid"]. "</td><td>" . $rows["name"]. "</td><td>". $rows["due_date"]. "</td><td>". $rows["status"]. "</td><td>". $rows["priority"]. "</td><td><form action='index.php' method='post' style='display:inline;'><input type='hidden' name='taskid' value='".$rows["taskid"]."'><button type='submit' name='tasksubmit'>🗑️</button></form></td></tr>";
                //echo "<td><form action='index.php' method='post' style='display:inline;'><input type='hidden' name='taskid' value='".$rows["taskid"]."'><button type='submit' name='tasksubmit'>🗑️</button></form></td></tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $task_name = $_POST["taskname"];
    $due_time = $_POST["duetime"];
    $task_id = $_POST["taskid"];
    $status = "Pending";
    $priority = $_POST["priority"];


    // For deletion..
    if(isset($_POST["tasksubmit"]) && !empty($task_id)){
        $check_sql = "SELECT * FROM tasks WHERE taskid = $task_id AND userid = $user_id";
        $check_rslt = mysqli_query($conn, $check_sql);
        if(mysqli_num_rows($check_rslt) > 0){
            $dlt_sql = "DELETE FROM tasks WHERE taskid = $task_id AND userid = $user_id";
            if(mysqli_query($conn, $dlt_sql)){
                //echo "Task is deleted";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Task ID is not found";
        }
    }
    // For adding
    elseif(!empty($task_name) && !empty($due_time) && isset($_POST["submit"])){
        $sql = "INSERT INTO tasks (taskid, name, due_date, status, priority, userid) VALUES (NULL, '$task_name', '$due_time', '$status', '$priority', '$user_id')";
        if(mysqli_query($conn, $sql)){
            echo "Task added";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
    // For updating...
    elseif(isset($_POST["prgs_submit"])){
        if(isset($_POST["task_status"])){
            $task_id = $_POST["task_status"];
            $update_sql = "UPDATE tasks SET status='COMPLETED' WHERE taskid=$task_id AND userid=$user_id";
            if(mysqli_query($conn, $update_sql)){
                echo "Task status updated to Done";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "No task selected";
        }
    }
    // Refresh the page..
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

mysqli_close($conn);
?>
