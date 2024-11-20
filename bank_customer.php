<?php
	ini_set ('error_reporting', 1); //Turns on error reporting - remove once everything works.
	if(isset($_GET['submit']) || isset($_GET['customer-id'])) {
        $customer_id = $_GET["customer-id"];
        try{
            require_once('../pdo_connect.php'); //Connect to the database
            $sql = 'SELECT * FROM Account WHERE customerID = ?';
            $stmt = $dbc->prepare($sql);
			$stmt->bindParam(1, $customer_id);
			$stmt->execute();
            //$result = $dbc-> query($sql);
        } catch (PDOException $e){
            echo $e->getMessage();
        }
        $affected = $stmt->RowCount();
        if ($affected == 0){
			echo "We could not find a customer matching that description. Please try again.";
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
    <title>Customer</title>
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
    <h2>Customer <?php echo htmlspecialchars($customer_id); ?></h2>
    <table>
        <tr>
            <th>Balance</th>
            <th>Type</th>
            <th>Minimum Balance Required</th>
            <th>Fee</th>
            <th>Date Opened</th>
            <th>APY</th>
            <th>APR</th>
            <th>Account ID</th>
        </tr>
        <?php foreach ($result as $account) {
            echo "<tr>";
            echo "<td>".$account['balance']."</td>";
            echo "<td>".$account['accountType']."</td>";
            echo "<td>".$account['minBalanceReq']."</td>";
            echo "<td>".$account['fee']."</td>";
            echo "<td>".$account['dateOpened']."</td>";
            echo "<td>".$account['apy']."</td>";
            echo "<td>".$account['apr']."</td>";
            echo "<td>".$account['accountID']."</td>";
            echo "</tr>";
        } ?>
    </table>
</body>
</html>