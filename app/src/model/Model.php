<?php
namespace ktc\a2\model;

use mysqli;
use ktc\a2\exception\BankException;

/**
 * Class Model
 *
 * @package ktc/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 */
class Model
{
    protected $db;

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

        if (!$this->db)
        {
            throw new BankException($this->db->connect_error);
        }


        //----------------------------------------------------------------------------
        // This is to make our life easier
        // Create your database and populate it with sample data
        $this->db->query("CREATE DATABASE IF NOT EXISTS $dbName;");

        if (!$this->db->select_db($dbName)) {
            // somethings not right.. handle it
            throw new BankException("Mysql database not available!");
        }      

        //defining strings for table creation

        $databaseUser = "CREATE TABLE `user` (
                                        `user_id` INT NOT NULL AUTO_INCREMENT, 
                                        `user_name` VARCHAR(30) UNIQUE NOT NULL,
                                        `user_first` VARCHAR(30) NOT NULL, 
                                        `user_last` VARCHAR(30) NOT NULL,
                                        `user_pass` VARCHAR(72) NOT NULL,                         
                                        `user_email` VARCHAR(30) NOT NULL,
                                        `user_phNumber` VARCHAR(30) NOT NULL,                                        
                                        `user_dob` VARCHAR(20) NOT NULL,
                                        Primary key (user_id));";

        $databaseAccount = "CREATE TABLE `account` (
                                        `account_id` INT NOT NULL AUTO_INCREMENT,
                                        `account_type` VARCHAR(30) NOT NULL, 
                                        `account_bal` DECIMAL(20,2) NOT NULL,
                                        `user_id` INT, 
                                        `account_dateStarted` VARCHAR(20) NOT NULL,                                       
                                        PRIMARY KEY (account_id),
                                        CONSTRAINT FK_user FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE)";

        $databaseTransaction = "CREATE TABLE `transaction` (
                                        `trans_id` INT NOT NULL AUTO_INCREMENT, 
                                        `trans_type` CHAR(10) NOT NULL,
                                        `trans_amount` DECIMAL(15,2) NOT NULL,
                                        `trans_datetime` VARCHAR(20) NOT NULL,
                                        `account_id` INT,                                
                                        PRIMARY KEY (trans_id),
                                        CONSTRAINT FK_account FOREIGN KEY (account_id) REFERENCES account(account_id) ON DELETE CASCADE);";



        $this->buildTable('user',$databaseUser);
        $this->buildTable('account',$databaseAccount);
        $this->buildTable('transaction',$databaseTransaction); 
        $this->buildTableData();     

            
        
       
    }
    public function buildTable($tableName, $table)
    {
        $result = $this->db->query("SHOW TABLES LIKE '$tableName';");

        if ($result->num_rows == 0) {
            // table doesn't exist
            // create it
            $result = $this->db->query($table);
            if (!$result) {
                // handle appropriately
               throw new BankException("Failed creating table ".$tableName);
            }
        }

    }
    public function buildTableData()
    {
        if (!$password = password_hash("1111", PASSWORD_BCRYPT)) {
            throw new BankException("Failed to hash entered password");
        }
        if (!$admin = password_hash("admin", PASSWORD_BCRYPT)) {
            throw new BankException("Failed to hash entered password");
        }

        //strings to insert data
        $insertUser = "INSERT INTO `user` VALUES (NULL, 'admin', 'Administrator', NULL, '$admin', 'admin@ktc.com', 'Call KTC instead', '01/01/1970'),
                                                 (NULL, 'CBishop', 'Chris', 'Bishop', '$password', 'chris@gmail.com', '1111', '20/03/1972'),
                                                 (NULL, 'MLittleLamb', 'Mary','LittleLamb', '$password', 'mary@gmail.com','2222', '01/01/2000');";

        $insertAccount = "INSERT INTO `account` VALUES (NULL,'Savings',10000,1,'01/02/2003'),
                                                       (NULL,'CreditCard',20,1,'02/02/2003'),
                                                       (NULL,'Savings',300,2,'02/12/2018'),
                                                       (NULL,'CreditCard',50,2,'02/12/2018');";

        $insertTransaction = "INSERT INTO `transaction` VALUES (NULL,'Deposit',20,'25/09/2019',1),
                                                               (NULL,'Withdraw',3,'2/02/2018',2),
                                                               (NULL,'Withdraw',40,'25/06/2019',3),
                                                               (NULL,'Deposit',50,'25/09/2019',4);";


         //check if already inserted
        $result = $this->db->query("SELECT * FROM `user`;");

        if ($result->num_rows == 0) {        

            if (!$this->db->query($insertUser)) {
                // handle appropriately
                throw new BankException("Failed creating sample user data! ".mysqli_error($this->db));
            }
            if (!$this->db->query($insertAccount)) {
                // handle appropriately
                throw new BankException("Failed creating sample account data! ".mysqli_error($this->db));
            }
            if (!$this->db->query($insertTransaction)) {
                // handle appropriately
                throw new BankException("Failed creating sample transaction data! ".mysqli_error($this->db));
            }
        }
    }

    /**
     * @return mixed
     */
    public function getDb()
    {
        return $this->db;
    }
}
