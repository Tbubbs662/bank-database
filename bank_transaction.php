<?php
	ini_set ('error_reporting', 1); //Turns on error reporting - remove once everything works.
	if(isset($_GET['submit']) || isset($_GET['account-id'])) {
        $account_id = $_GET["account-id"];
        try{
            require_once('../pdo_connect.php'); //Connect to the database
            $sql1 = 'SELECT * FROM Transactions WHERE accountID = ?';
            $stmt1 = $dbc->prepare($sql1);
			$stmt1->bindParam(1, $account_id);
			$stmt1->execute();
        } catch (PDOException $e){
            echo $e->getMessage();
        }
        $affected = $stmt1->RowCount();
        if ($affected == 0){
			echo "We could not find a transaction matching that description. Please try again.";
			exit;
		}	
		else {
			$result = $stmt1->fetchAll();
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
    <title>transaction</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2, h3 {
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

        .menu-button {
            display: inline-block;
            margin: 20px auto; 
            background-color: #007BFF; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            text-align: center;
        }

        .menu-button:hover {
            background-color: #0056b3; 
        }

        .button-div {
            display: flex;
            justify-content: center; 
            align-items: center; 
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <h2>Transactions for Account <?php echo htmlspecialchars($account_id); ?></h2>
    <table>
        <tr>
            <th>Transaction ID</th>
            <th>Account ID</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Date & Time</th>
        </tr>
        <?php foreach ($result as $transactions) {
            echo "<tr>";
            echo "<td>".$transactions['transactionID']."</td>";
            echo "<td>".$transactions['accountID']."</td>";
            echo "<td>".$transactions['transactionType']."</td>";
            echo "<td>".$transactions['transactionAmount']."</td>";
            echo "<td>".$transactions['transactionDateTime']."</td>";
            echo "</tr>";
        } ?>
    </table>
    <div class="button-div">
        <button class="menu-button" type="button" onclick="window.location.href='index.html';">Menu</button>
    </div>
</body>
</html>