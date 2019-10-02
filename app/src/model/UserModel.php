<?php
namespace ctk\a2\model;

use ctk\a2\Exception\BankException;

/**
 * Class UserModel
 *
 * @package ctk/a2
 * @author
 */
class UserModel extends Model
{
    private $id;

    private $firstName;

    private $lastName;

    private $password;

    private $email;

    private $phone;

    private $dateOfBirth;

    public function getId()
    {
        return $this->id;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone(string $phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(string $date)
    {
        $this->dateOfBirth = $date;

        return $this;
    }

    public function load($id)
    {
        if (!$result = $this->db->query(
            "SELECT * FROM `user` WHERE `user_id` = $id;")) {
            throw new BankException('No user found with id '.$id);
        }
        if($result->num_rows > 0) {
            $result = $result->fetch_assoc();
            $this->id = $id;
            $this->firstName = $result['user_fName'];
            $this->lastName = $result['user_lName'];
            $this->password = $result['user_pass'];
            $this->email = $result['user_email'];
            $this->phone = $result['user_phNumber'];
            $this->dateOfBirth = $result['user_dob'];
        }

        return $this;
    }

    public function save()
    {
        $id = $this->id;
        $fName = $this->firstName ?? "NULL";
        $lName = $this->lastName ?? "NULL";
        $password = $this->password ?? "NULL";
        $email = $this->email ?? "NULL";
        $phNumber = $this->phone ?? "NULL";
        $dob = $this->dateOfBirth ?? "NULL";
        if (!isset($this->id)) {
            // New user - Perform INSERT
            if (!$result = $this->db->query("INSERT INTO `user` VALUES
                                        (NULL,'$fName','$lName','$password','$email','$phNumber','$dob');"))
            {
                throw new BankException("Insert user failed");
            }
            $this->id = $this->db->insert_id;
        } else {
            // saving existing user - perform UPDATE
            if (!$result = $this->db->query("UPDATE `user` SET
                                        `user_fName` = '$fName',
                                        `user_lName` = '$lName',
                                        `user_pass` = '$password',
                                        `user_email` = '$email',
                                        `user_phNumber` = '$phNumber',
                                        `user_dob` = '$dob'
                                         WHERE `id` = $id;")) {
                throw new BankException("Update account failed");
            }
        }

        return $this;
    }
}
