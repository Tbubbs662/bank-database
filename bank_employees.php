<?php
	ini_set ('error_reporting', 1); //Turns on error reporting - remove once everything works.
	if(isset($_GET['submit']) || isset($_GET['bank-id'])) {
        $bank_id = $_GET["bank-id"];
        try{
            require_once('../pdo_connect.php'); //Connect to the database
            $sql = 'SELECT employeeID, employeeFirstName, employeeLastName, managerID, branchID FROM Employee WHERE branchID = ?
            ORDER BY employeeLastName';
			$sql2 = 'SELECT DISTINCT c.customerID, c.customerFirstName, c.customerLastName, c.customerEmail FROM Customer c
			JOIN Account a ON c.customerID = a.customerID
			WHERE a.branchID = ?
			ORDER BY c.customerLastName;';
            $stmt = $dbc->prepare($sql);
			$stmt->bindParam(1, $bank_id);
			$stmt->execute();
			$stmt2 = $dbc->prepare($sql2);
			$stmt2->bindParam(1, $bank_id);
			$stmt2->execute();

            //$result = $dbc-> query($sql);
        } catch (PDOException $e){
            echo $e->getMessage();
        }
        $affected = $stmt->RowCount();
        if ($affected == 0){
			echo "We could not find a bank matching that description. Please try again.";
			exit;
		}	
		else {
			$result = $stmt->fetchAll();
			$result2 = $stmt2->fetchAll();
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
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2 {
            color: #333;
        }

        .error-message {
            color: red;
            font-weight: bold;
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h2>Branch <?php echo htmlspecialchars($bank_id); ?>: Employees</h2>
    <table>
        <tr>
            <th>Employee ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Manager ID</th>
            <th>Branch ID</th>
        </tr>
        <?php foreach ($result as $auth) { ?>
            <tr>
                <td><?php echo htmlspecialchars($auth['employeeID']); ?></td>
                <td><?php echo htmlspecialchars($auth['employeeFirstName']); ?></td>
                <td><?php echo htmlspecialchars($auth['employeeLastName']); ?></td>
                <td><?php echo htmlspecialchars($auth['managerID']); ?></td>
                <td><?php echo htmlspecialchars($auth['branchID']); ?></td>
            </tr>
        <?php } ?>
    </table>

	<h2>Branch <?php echo htmlspecialchars($bank_id); ?>: Customers</h2>
    <table>
        <tr>
            <th>Customer ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Customer Email</th>
            <th>Branch ID</th>
        </tr>
        <?php foreach ($result2 as $cust) { ?>
            <tr>
                <td><?php echo htmlspecialchars($cust['customerID']); ?></td>
                <td><?php echo htmlspecialchars($cust['customerFirstName']); ?></td>
                <td><?php echo htmlspecialchars($cust['customerLastName']); ?></td>
                <td><?php echo htmlspecialchars($cust['customerEmail']); ?></td>
                <td><?php echo htmlspecialchars($auth['branchID']); ?></td>
            </tr>
        <?php } ?>
    </table>

	
</body>
</html>