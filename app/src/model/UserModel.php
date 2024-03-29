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
    /**
     * @var int User ID
     */
    private $id;

    /**
     * @var string Username
     */
    private $userName;

    /**
     * @var string User first name
     */
    private $firstName;

    /**
     * @var string User last name
     */
    private $lastName;

    /**
     * @var string BCRYPT password
     */
    private $password;

    /**
     * @var string User email address
     */
    private $email;

    /**
     * @var string User phone number
     */
    private $phone;

    /**
     * @var string User date of birth
     */
    private $dateOfBirth;

    /**
     * Get User ID
     *
     * @return int User ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get username
     *
     * @return string Username
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set username
     *
     * @param string $userName The new username
     * @return $this A UserModel
     */
    public function setUserName($userName)
    {
        $this->userName = mysqli_real_escape_string($this->db, $userName);

        return $this;
    }

    /**
     * Get User first name
     *
     * @return string User first name
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set User first name
     *
     * @param string $firstName The new first name for the user
     * @return $this A UserModel
     */
    public function setFirstName($firstName)
    {
        $this->firstName = mysqli_real_escape_string($this->db, $firstName);

        return $this;
    }

    /**
     * Get User last name
     *
     * @return string User last name
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set User last name
     *
     * @param string $lastName The new last name for the user
     * @return $this A UserModel
     */
    public function setLastName($lastName)
    {
        $this->lastName = mysqli_real_escape_string($this->db, $lastName);
        return $this;
    }

    /**
     * Get password
     *
     * @return string BCRYPT password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password The new BCRYPT password
     * @return $this A UserModel
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get User email address
     *
     * @return string User email address
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set User email address
     *
     * @param string $email The new email address for the user
     * @return $this A UserModel
     */
    public function setEmail($email)
    {
        $this->email = mysqli_real_escape_string($this->db, $email);
        return $this;
    }

    /**
     * Get User phone number
     *
     * @return string User phone number
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set User phone number
     *
     * @param string $phone The new phone number for the user
     * @return $this A UserModel
     */
    public function setPhone($phone)
    {
        $this->phone = mysqli_real_escape_string($this->db, $phone);
        return $this;
    }

    /**
     * Get User date of birth
     *
     * @return string User date of birth
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set User date of birth
     *
     * @param string $date The new date of birth for the user
     * @return $this A UserModel
     */
    public function setDateOfBirth($date)
    {
        $this->dateOfBirth = mysqli_real_escape_string($this->db, $date);
        return $this;
    }

    /**
     * User check
     *
     * Checks if a username is already present in the database
     *
     * @param string $name Username
     * @return bool True if the specified username is already present
     * @throws BankException on database errors
     */
    public function check($name)
    {
        if (!$result = $this->db->query("SELECT * FROM `user` WHERE `user_name` = '$name';")) {
            throw new BankException(99, 'DB query failed: ' . mysqli_error($this->db));
        }
        if ($result->num_rows < 1) {
            return false;
        }
        return true;
    }

    /**
     * User load
     *
     * Loads user information from the database into this UserModel
     *
     * @param string $name Username
     * @return $this A UserModel
     * @throws BankException on database connection errors
     */
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

    /**
     * User save
     *
     * Saves user information from this UserModel into the database
     *
     * @return $this A UserModel
     * @throws BankException on database connection errors
     */
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
