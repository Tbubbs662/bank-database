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

/*PROCEDURE QUERY*/