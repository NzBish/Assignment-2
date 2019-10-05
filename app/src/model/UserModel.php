<?php
namespace ktc\a2\model;

use ktc\a2\Exception\BankException;

/**
 * Class UserModel
 *
 * @package ktc/a2
 * @author  Andrew Gilman <a.gilman@massey.ac.nz>
 * @author  K. Dempsey
 * @author  T. Crompton
 * @author  C. Bishop
 */
class UserModel extends Model
{
    private $id;

    private $userName;

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

    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($userName)
    {
        $this->userName = mysqli_real_escape_string($this->db, $userName);

        return $this;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = mysqli_real_escape_string($this->db, $firstName);

        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = mysqli_real_escape_string($this->db, $lastName);
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = mysqli_real_escape_string($this->db, $email);

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = mysqli_real_escape_string($this->db, $phone);

        return $this;
    }

    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth($date)
    {
        $this->dateOfBirth = mysqli_real_escape_string($this->db, $date);

        return $this;
    }

    public function check($name)
    {
        $result = $this->db->query("SELECT * FROM `user` WHERE `user_name` = '$name';");
        if ($result->num_rows < 1) {
            return false;
        }
        return true;
    }

    public function load($name)
    {
        if (!$result = $this->db->query("SELECT * FROM `user` WHERE `user_name` = '$name';")) {
            throw new BankException(99, 'DB query failed: ' . mysqli_error($this->db));
        }
        if ($result->num_rows < 1) {
            throw new BankException(99, 'No user found with username ' . $name);
        }
        if ($result->num_rows == 1) {
            $result = $result->fetch_assoc();
            $this->id = $result['user_id'];
            $this->userName = $name;
            $this->firstName = $result['user_first'];
            $this->lastName = $result['user_last'];
            $this->password = $result['user_pass'];
            $this->email = $result['user_email'];
            $this->phone = $result['user_phNumber'];
            $this->dateOfBirth = $result['user_dob'];
        } else {
            throw new BankException(99, 'Username ' . $name . ' is not unique');
        }

        return $this;
    }

    public function save()
    {
        $id = $this->id;
        $uName = $this->userName ?? "NULL";
        $fName = $this->firstName ?? "NULL";
        $lName = $this->lastName ?? "NULL";
        $password = $this->password ?? "NULL";
        $email = $this->email ?? "NULL";
        $phNumber = $this->phone ?? "NULL";
        $dob = $this->dateOfBirth ?? "NULL";
        if (!isset($this->id)) {
            // New user - Perform INSERT
            if (!$result = $this->db->query("INSERT INTO `user` VALUES
                                        (NULL,'$uName','$fName','$lName','$password','$email','$phNumber','$dob');")) {
                throw new BankException(7);
            }
            $this->id = $this->db->insert_id;
        } else {
            // saving existing user - perform UPDATE
            if (!$result = $this->db->query("UPDATE `user` SET
                                        `user_name` = '$uName',
                                        `user_first` = '$fName',
                                        `user_last` = '$lName',
                                        `user_pass` = '$password',
                                        `user_email` = '$email',
                                        `user_phNumber` = '$phNumber',
                                        `user_dob` = '$dob'
                                         WHERE `id` = $id;")) {
                throw new BankException(7);
            }
        }

        return $this;
    }
}
