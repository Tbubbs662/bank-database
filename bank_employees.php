<?php
	ini_set ('error_reporting', 1); //Turns on error reporting - remove once everything works.
	if(isset($_GET['submit']) || isset($_GET['bank-id'])) {
        $bank_id = $_GET["bank-id"];
        try{
            require_once('../pdo_connect.php'); //Connect to the database
            $sql = 'SELECT emp1.employeeID, emp1.employeeFirstName, emp1.employeeLastName, emp2.employeeFirstName AS "managerFirstName", emp2.employeeLastName AS "managerLastName", emp1.branchID FROM Employee AS emp1 LEFT JOIN Employee AS emp2 ON emp1.managerID = emp2.employeeID WHERE emp1.branchID = ? ORDER BY emp1.employeeLastName;';
			$sql2 = 'SELECT DISTINCT c.customerID, c.customerFirstName, c.customerLastName, c.customerEmail FROM Customer c
			JOIN Account a ON c.customerID = a.customerID
			WHERE a.branchID = ?
			ORDER BY c.customerLastName;';
            $sql3 = 'SELECT branch_total(?) AS total_balance';
            $stmt = $dbc->prepare($sql);
			$stmt->bindParam(1, $bank_id);
			$stmt->execute();
			$stmt2 = $dbc->prepare($sql2);
			$stmt2->bindParam(1, $bank_id);
			$stmt2->execute();
            $stmt3 = $dbc->prepare($sql3);
            $stmt3->bindParam(1, $bank_id);
            $stmt3->execute();

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
            $total_balance_result = $stmt3->fetch(PDO::FETCH_ASSOC);
            $total_balance = $total_balance_result['total_balance'] ?? 'N/A';
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
    <h3>Total balance of assets: $<?php echo htmlspecialchars($total_balance); ?></h3>
    <table>
        <tr>
            <th>Employee ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Manager First Name</th>
            <th>Manager Last Name</th>
            <th>Branch ID</th>
        </tr>
        <?php foreach ($result as $auth) { ?>
            <tr>
                <td><?php echo htmlspecialchars($auth['employeeID']); ?></td>
                <td><?php echo htmlspecialchars($auth['employeeFirstName']); ?></td>
                <td><?php echo htmlspecialchars($auth['employeeLastName']); ?></td>
                <td><?php echo htmlspecialchars($auth['managerFirstName']); ?></td>
                <td><?php echo htmlspecialchars($auth['managerLastName']); ?></td>
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
                <td><a href="bank_customer.php?customer-id=<?php echo urlencode($cust['customerID']); ?>">
                <?php echo htmlspecialchars($cust['customerID']); ?>
    </a></td>
                <td><?php echo htmlspecialchars($cust['customerFirstName']); ?></td>
                <td><?php echo htmlspecialchars($cust['customerLastName']); ?></td>
                <td><?php echo htmlspecialchars($cust['customerEmail']); ?></td>
                <td><?php echo htmlspecialchars($auth['branchID']); ?></td>
            </tr>
        <?php } ?>
    </table>

	
</body>
</html>