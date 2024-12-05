<?php
    ini_set ('error_reporting', 1); // Turns on error reporting - remove once everything works.
    if(isset($_GET['submit']) || isset($_GET['customer-id'])) {
        $customer_id = $_GET["customer-id"];
        try {
            require_once('../pdo_connect.php'); // Connect to the database
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add-transaction'])) {
                $account_id = $_POST['account-id'];
                $transaction_type = $_POST['transaction-type'];
                $transaction_amount = $_POST['transaction-amount'];
                $prefix = "TXN";
                $unique_id = uniqid(); 
                $transaction_id = "{$prefix}{$unique_id}";


                    // Fetch accounts and transactions for the customer
                
                
                $sql_insert = 'INSERT INTO Transactions (accountID, customerID, transactionType, transactionAmount, transactionDateTime, transactionID)
                               VALUES (?, ?, ?, ?, NOW(), ?)';
                $stmt_insert = $dbc->prepare($sql_insert);
                $stmt_insert->bindParam(1, $account_id);
                $stmt_insert->bindParam(2, $customer_id);
                $stmt_insert->bindParam(3, $transaction_type);
                $stmt_insert->bindParam(4, $transaction_amount);
                $stmt_insert->bindParam(5, $transaction_id);
                $stmt_insert->execute();

                header("Location: " . $_SERVER['PHP_SELF'] . "?customer-id=" . urlencode($customer_id));

                echo "<p class='success-message'>Transaction added successfully!</p>";
            }

             $sql1 = 'SELECT * FROM Account WHERE customerID = ?';
             $stmt1 = $dbc->prepare($sql1);
             $stmt1->bindParam(1, $customer_id);
             $stmt1->execute();
 
             $sql2 = 'SELECT * FROM Transactions WHERE customerID = ? ORDER BY transactionDateTime DESC';
             $stmt2 = $dbc->prepare($sql2);
             $stmt2->bindParam(1, $customer_id);
             $stmt2->execute();
 
            
        } catch (PDOException $e) {
            echo "<p class='error-message'>" . $e->getMessage() . "</p>";
        }
        $affected = $stmt1->RowCount();
        if ($affected == 0) {
            echo "We could not find a customer matching that description. Please try again.";
            exit;
        } else {
            $result1 = $stmt1->fetchAll();
            $result2 = $stmt2->fetchAll();
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
        <?php foreach ($result1 as $account) {
            echo "<tr>";
            echo "<td>".$account['balance']."</td>";
            echo "<td>".$account['accountType']."</td>";
            echo "<td>".$account['minBalanceReq']."</td>";
            echo "<td>".$account['fee']."</td>";
            echo "<td>".$account['dateOpened']."</td>";
            echo "<td>".$account['apy']."</td>";
            echo "<td>".$account['apr']."</td>";
            echo "<td><a href='bank_transaction.php?account-id=".$account['accountID']."'>".$account['accountID']."</a></td>";
            echo "</tr>";
        } ?>
    </table>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?customer-id=" . urlencode($customer_id)); ?>">
    <label for="account-id">Account ID:</label>
    <select name="account-id" id="account-id" required>
        <?php foreach ($result1 as $account) { ?>
            <option value="<?php echo htmlspecialchars($account['accountID']); ?>">
                <?php echo htmlspecialchars($account['accountID']); ?>
            </option>
        <?php } ?>
    </select>
    <br><br>

    <label for="transaction-type">Transaction Type:</label>
    <select name="transaction-type" id="transaction-type" required>
        <option value="Deposit">Deposit</option>
        <option value="Withdrawal">Withdrawal</option>
    </select>
    <br><br>

    <label for="transaction-amount">Transaction Amount:</label>
    <input type="number" name="transaction-amount" id="transaction-amount" step="0.01" required>
    <br><br>

    <button type="submit" name="add-transaction" class="menu-button">Add Transaction</button>
</form>
    <h3>Transactions</h3>
    <table style="padding-top: 100px">
        <tr>
            <th>Transaction ID</th>
            <th>Account ID</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Date & Time</th>
        </tr>
        <?php foreach ($result2 as $transactions) {
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

    <h3>Add a Transaction</h3>

</body>
</html>