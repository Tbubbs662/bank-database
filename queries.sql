/* SELECT queries demonstrating: two-table join(done), three-table join, self-join, an
aggregate function, an aggregate function using GROUP BY and HAVING, a
text-based-search query using LIKE with wildcard(s), a subquery.*/

SELECT * FROM Branch JOIN BranchPhoneNums USING(branchID);

SELECT employeeID, employeeFirstName, employeeLastName, managerID, branchID FROM Employee WHERE branchID = ?
ORDER BY employeeLastName;

/*Customer Query*/

/*Customer Account Query*/

/*Transactions Query*/

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