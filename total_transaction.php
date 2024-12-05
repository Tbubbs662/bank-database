<?php
    ini_set ('error_reporting', 1); // Turns on error reporting - remove once everything works.
    if(isset($_GET['submit']) || isset($_GET['amount'])) {
        $amount = $_GET["amount"];
        try {
            require_once('../pdo_connect.php'); // Connect to the database

             $sql1 = 'SELECT c.customerID, c.customerFirstName, c.customerLastName, SUM(t.transactionAmount) AS total_spent
             FROM Customer c
             JOIN Transactions t ON c.customerID = t.customerID
             GROUP BY c.customerID, c.customerFirstName, c.customerLastName
             HAVING SUM(t.transactionAmount) > ?
             ORDER BY total_spent DESC';
             $stmt1 = $dbc->prepare($sql1);
             $stmt1->bindParam(1, $amount);
             $stmt1->execute();
            
        } catch (PDOException $e) {
            echo "<p class='error-message'>" . $e->getMessage() . "</p>";
        }
        $affected = $stmt1->RowCount();
        if ($affected == 0) {
            echo "We could not find a customer matching that description. Please try again.";
            exit;
        } else {
            $result1 = $stmt1->fetchAll();
        }
    } else {
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
    <h2>Customer</h2>
    <table>
        <tr>
            <th>Customer ID</th>
            <th>First</th>
            <th>Last</th>
            <th>Amount</th>
        </tr>
        <?php foreach ($result1 as $account) {
            echo "<tr>";
            echo "<td>".$account['customerID']."</td>";
            echo "<td>".$account['customerFirstName']."</td>";
            echo "<td>".$account['customerLastName']."</td>";
            echo "<td>".$account['total_spent']."</td>";
            echo "</tr>";
        } ?>
    </table>
    