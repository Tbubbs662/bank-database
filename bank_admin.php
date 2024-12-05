<?php
    try {
        require_once('../pdo_connect.php');
        $sql = 'SELECT * FROM Branch JOIN BranchPhoneNums USING(branchID);';
        $result = $dbc-> query($sql);
    } catch (PDOException $error) {
        echo $error->getMessage();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
    <h2>Bank Branches</h2>
    <style>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-family: Arial, sans-serif;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        h2 {
            font-family: Arial, sans-serif;
            margin-bottom: 10px;
            color: #333;
        }
    </style>
    <table>
        <tr>
            <th>Branch Identification</th>
            <th>Name</th>
            <th>City</th>
            <th>Street</th>
            <th>State</th>
            <th>Zip</th>
            <th>Phone Number</th>
        </tr>    
        <?php foreach ($result as $branch) {
            echo "<tr>";
            echo "<td><a href='bank_employees.php?bank-id=".$branch['branchID']."'>".$branch['branchID']."</a></td>";
            echo "<td>".$branch['branchName']."</td>";
            echo "<td>".$branch['branchCity']."</td>";
            echo "<td>".$branch['branchStreet']."</td>";
            echo "<td>".$branch['branchState']."</td>";
            echo "<td>".$branch['branchZip']."</td>";
            echo "<td>".$branch['branchPhoneNum']."</td>";
            echo "</tr>";
        } ?>
    </table>

    <form method="get" action="total_transaction.php">
        <div class="form-container">
            <label for="amount">See Customers with Total Transactions Amount Over:</label>
            <input type="text" name="amount" id="amount">
            <input type="submit" name="submit" value="Submit">
        </div>
    </form>


    <div class="button-div">
        <button class="menu-button" type="button" onclick="window.location.href='index.html';">Menu</button>
    </div>
</body>
</html>