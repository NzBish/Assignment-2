<?php
namespace ktc\a2\model;

use mysqli;
use ktc\a2\exception\BankException;

/**
 * Class Model
 *
 * @package ktc/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 * @author  K. Dempsey
 * @author  T. Crompton
 * @author  C. Bishop
 */
class Model
{
    /**
     * @var mysqli Database connection
     */
    protected $db;

    /**
     * Model constructor
     *
     * Sets the database connection variables via an external file
     * Creates the database if not already created
     *
     * @throws BankException on database connection errors
     */
    public function __construct()
    {
        $envs = getenv();
        $dbHost = $envs['MYSQL_HOST'];
        $dbName = $envs['MYSQL_DATABASE'];
        $dbUser = $envs['MYSQL_USER'];
        $dbPass = $envs['MYSQL_PASSWORD'];
        $this->db = new mysqli(
            $dbHost,
            $dbUser,
            $dbPass
        );

        if (!$this->db) {
            throw new BankException(99,$this->db->connect_error);
        }

        /**
         * This is to make our life easier
         * Create your database and populate it with sample data
         */
        $this->db->query("CREATE DATABASE IF NOT EXISTS $dbName;");

        if (!$this->db->select_db($dbName)) {
            // somethings not right.. handle it
            throw new BankException("Mysql database not available!");
        }

        /** Defining strings for table creation */
        $databaseUser = "CREATE TABLE `user` (
                                        `user_id` INT NOT NULL AUTO_INCREMENT, 
                                        `user_name` VARCHAR(30) UNIQUE NOT NULL,
                                        `user_first` VARCHAR(30) NOT NULL, 
                                        `user_last` VARCHAR(30) NOT NULL,
                                        `user_pass` VARCHAR(72) NOT NULL,                         
                                        `user_email` VARCHAR(50) NOT NULL,
                                        `user_phNumber` VARCHAR(30) NOT NULL,                                        
                                        `user_dob` DATE NOT NULL,
                                        Primary key (user_id));";

        $databaseAccount = "CREATE TABLE `account` (
                                        `account_id` INT NOT NULL AUTO_INCREMENT,
                                        `account_type` 	SET('Savings', 'CreditCard', 'Cheque') NOT NULL, 
                                        `account_bal` DECIMAL(20,2) NOT NULL,
                                        `user_id` INT, 
                                        `account_dateStarted` DATETIME NOT NULL,                                       
                                        PRIMARY KEY (account_id),
                                        CONSTRAINT FK_user FOREIGN KEY (user_id) 
                                        REFERENCES user(user_id) ON DELETE CASCADE)";

        $databaseTransaction = "CREATE TABLE `transaction` (
                                        `trans_id` INT NOT NULL AUTO_INCREMENT, 
                                        `trans_type` SET('Deposit', 'Withdraw') NOT NULL,
                                        `trans_amount` DECIMAL(15,2) NOT NULL,
                                        `trans_datetime` DATETIME NOT NULL,
                                        `account_id` INT,                                
                                        PRIMARY KEY (trans_id),
                                        CONSTRAINT FK_account FOREIGN KEY (account_id) 
                                        REFERENCES account(account_id) ON DELETE CASCADE);";

        $this->buildTable('user', $databaseUser);
        $this->buildTable('account', $databaseAccount);
        $this->buildTable('transaction', $databaseTransaction);
        $this->buildTableData();
    }

    /**
     * Model table builder
     *
     * If not already present, creates a table based on a provided query
     *
     * @param string $tableName The name of the table to be created
     * @param string $table The full query creating the table
     * @throws BankException on database connection errors
     */
    public function buildTable($tableName, $table)
    {
        $result = $this->db->query("SHOW TABLES LIKE '$tableName';");

        if ($result->num_rows == 0) {
            // table doesn't exist
            // create it
            $result = $this->db->query($table);
            if (!$result) {
                // handle appropriately
                throw new BankException(99, "Failed creating table " . $tableName);
            }
        }
    }

    /**
     * Model table data builder
     *
     * If not already present, inserts sample data into the User, Account, and Transaction tables
     *
     * @throws BankException on database connection errors
     */
    public function buildTableData()
    {
        if (!$password = password_hash("1111", PASSWORD_BCRYPT)) {
            throw new BankException(99,"Failed to hash entered password");
        }
        if (!$admin = password_hash("admin", PASSWORD_BCRYPT)) {
            throw new BankException(99,"Failed to hash entered password");
        }

        /** Strings to insert */
        $insertUser = "INSERT INTO `user` VALUES (NULL, 'admin', 'Administrator', '', '$admin',
                                                 'admin@ktc.com', 'Call KTC instead', '1970-01-01'),
                                                 (NULL, 'CBishop', 'Chris', 'Bishop', '$password',
                                                  'chris@gmail.com', '1111', '1972-03-20'),
                                                 (NULL, 'MLittleLamb', 'Mary','LittleLamb', '$password', 
                                                  'mary@gmail.com','2222', '2000-01-01');";

        $insertAccount = "INSERT INTO `account` VALUES (NULL,'Savings',10000,2,'2019-10-03 10:42:16'),
                                                       (NULL,'CreditCard',20,2,'2019-10-02 10:42:16'),
                                                       (NULL,'Savings',300,3,'2019-10-01 10:42:16'),
                                                       (NULL,'CreditCard',50,3,'2019-10-01 10:42:16');";

        $insertTransaction = "INSERT INTO `transaction` VALUES (NULL,'Deposit',20,'2019-10-04 10:42:16',1),
                                                               (NULL,'Withdraw',3,'2019-10-04 10:42:16',2),
                                                               (NULL,'Withdraw',40,'2019-10-04 10:42:16',3),
                                                               (NULL,'Deposit',50,'2019-10-04 10:42:16',4);";

         /** Check if already inserted */
        $result = $this->db->query("SELECT * FROM `user`;");

        if ($result->num_rows == 0) {
            if (!$this->db->query($insertUser)) {
                throw new BankException(99, "Failed creating sample user data! " . mysqli_error($this->db));
            }
            if (!$this->db->query($insertAccount)) {
                throw new BankException(99, "Failed creating sample account data! " . mysqli_error($this->db));
            }
            if (!$this->db->query($insertTransaction)) {
                throw new BankException(99, "Failed creating sample transaction data! " . mysqli_error($this->db));
            }
        }
    }

    /**
     * Get DB connection
     *
     * @return mixed Either a mysqli database connection or NULL
     */
    public function getDb()
    {
        return $this->db;
    }
}
