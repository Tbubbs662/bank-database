/* SELECT queries demonstrating: two-table join(done), three-table join, self-join, an
aggregate function, an aggregate function using GROUP BY and HAVING, a
text-based-search query using LIKE with wildcard(s), a subquery.*/

SELECT * FROM Branch JOIN BranchPhoneNums USING(branchID);

SELECT employeeID, employeeFirstName, employeeLastName, managerID, branchID FROM Employee WHERE branchID = ?
ORDER BY employeeLastName;

/*Two Table Join*/
SELECT * FROM Branch JOIN BranchPhoneNums USING(branchID);

/*Three Table Join*/
SELECT DISTINCT c.customerID, c.customerFirstName, c.customerLastName, c.customerEmail, p.customerPhoneNum FROM Customer c Natural JOIN Account a 
            NATURAL JOIN CustomerPhoneNums p WHERE c.customerID = a.customerID AND a.branchID = ? ORDER BY c.customerLastName;

/*Self-Join*/
SELECT emp1.employeeID, emp1.employeeFirstName, emp1.employeeLastName, emp2.employeeFirstName AS "managerFirstName", emp2.employeeLastName AS 
"managerLastName", emp1.branchID FROM Employee AS emp1 LEFT JOIN Employee AS emp2 ON emp1.managerID = emp2.employeeID WHERE emp1.branchID = ? ORDER BY emp1.employeeLastName;



/*STORED FUNCTIONS*/
DELIMITER //
CREATE FUNCTION acct_total(customerID CHAR(32))
RETURNS FLOAT
DETERMINISTIC
BEGIN
    DECLARE total FLOAT DEFAULT 0;
    SELECT SUM(a.balance) INTO total
    FROM Customer c
    JOIN Account a ON c.customerID = a.customerID
    WHERE c.customerID = customerID;
    RETURN total;
END //
DELIMITER ;

DELIMITER //
CREATE FUNCTION branch_total(branchID CHAR(32))
RETURNS FLOAT
DETERMINISTIC
BEGIN
    DECLARE total FLOAT DEFAULT 0;
    SELECT SUM(a.balance) INTO total
    FROM Account a
    JOIN Branch b ON a.branchID = b.branchID
    WHERE b.branchID = branchID;
    RETURN total;
END //
DELIMITER ;

/*TRIGGER QUERY*/
DELIMITER $$
CREATE TRIGGER AfterTransactionInsert
AFTER INSERT ON Transaction
FOR EACH ROW
BEGIN
    -- Call the procedure to update balance
    CALL UpdateAccountBalance(NEW.accountID, NEW.transactionAmount, NEW.transactionType);
END$$
DELIMITER ;

/*PROCEDURE QUERY*/
DELIMITER $$
CREATE PROCEDURE UpdateAccountBalance(
    IN account_id INT, 
    IN trans_amount DECIMAL(10, 2), 
    IN trans_type VARCHAR(10)
)
BEGIN
    -- Update the Account balance based on the transaction type
    IF trans_type = 'deposit' THEN
        UPDATE Account
        SET Balance = Balance + trans_amount
        WHERE AccountID = account_id;
    ELSEIF trans_type = 'withdrawal' THEN
        UPDATE Account
        SET Balance = Balance - trans_amount
        WHERE AccountID = account_id;
    END IF;
END$$
DELIMITER ;


/*Customer Query*/
SELECT * FROM Account WHERE customerID = ?

/*Transactions Query*/
SELECT * FROM Transactions WHERE customerID = ? ORDER BY transactionDateTime DESC

/*Total Balance Across All Accounts*/
SELECT acct_total(?) AS total_balance

/*Create New Transaction*/
INSERT INTO Transactions (accountID, customerID, transactionType, transactionAmount, transactionDateTime, transactionID) VALUES (?, ?, ?, ?, NOW(), ?)

/*Query Using GROUP BY and HAVING*/
SELECT c.customerID, c.customerFirstName, c.customerLastName, SUM(t.transactionAmount) AS total_spent
FROM Customer c
JOIN Transactions t ON c.customerID = t.customerID
GROUP BY c.customerID, c.customerFirstName, c.customerLastName
HAVING SUM(t.transactionAmount) > ?
ORDER BY total_spent DESC

/*Individual with highest account balance for the branch*/
SELECT c.*, a.balance FROM Customer c JOIN Account a ON c.customerID = a.customerID WHERE a.BranchID = ? AND a.balance = ( SELECT MAX(balance) FROM Account WHERE BranchID = ? );

/*Total amount of all of the branches*/
SELECT branch_total(?) AS total_balance