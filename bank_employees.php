<?php
	ini_set ('error_reporting', 1); //Turns on error reporting - remove once everything works.
	if(isset($_GET['submit'])) {
        $bank_id = $_GET["bank-id"];
        try{
            require_once('../pdo_connect.php'); //Connect to the database
            $sql = 'SELECT employeeID, employeeFirstName, employeeLastName, managerID, branchID FROM Employee WHERE branchID = ?
            ORDER BY employeeLastName';
            $stmt = $dbc->prepare($sql);
			$stmt->bindParam(1, $bank_id);
			$stmt->execute();
            //$result = $dbc-> query($sql);
        } catch (PDOException $e){
            echo $e->getMessage();
        }
        $affected = $stmt->RowCount();
        if ($affected == 0){
			echo "We could not find a book matching that description. Please try again.";
			exit;
		}	
		else {
			$result = $stmt->fetchAll();
		}
    }
    else {
        echo "<h2>You have reached this page in error</h2>";
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Employees</title>
	<meta charset ="utf-8"> 
</head>
<body>
	<h2>Employees</h2>

	<table>
		<tr>
			<th>Employee ID</th>
			<th>First Name</th>
            <th>Last Name</th>
            <th>Manager ID</th>
            <th>Branch ID</th>

		</tr>	
		<?php foreach ($result as $auth) {
			echo "<tr>";
			echo "<td>".$auth['employeeID']."</td>";
			echo "<td>".$auth['employeeFirstName']."</td>";
            echo "<td>".$auth['employeeLastName']."</td>";
            echo "<td>".$auth['managerID']."</td>";
            echo "<td>".$auth['branchID']."</td>";
			echo "</tr>";
		}
		?>
	</table>
</body>    
</html>