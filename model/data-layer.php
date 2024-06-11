<?php

/**
 * The DataLayer class defines methods that can be called to get
 * data from the database.
 *
 * @author Tien Han <tienthuyhan@gmail.com>, Sage Markwardt
 * @date   6/10/2024
 */
class DataLayer
{
    /**
     * Check if the plaintext user-entered password matches the database
     * hashed password for the given email.
     *
     * @param string $email the given email
     * @param string $password the given password
     * @return bool true if the password matches the db, false if not
     */
    static function passwordMatches($email, $password): bool
    {
        //1. Connect to the database
        require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

        try {
            $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        } catch (PDOException $e) {
            die($e->getMessage());
        }

        //2. Prepare the SQL statement
        $sql = "SELECT password FROM users WHERE email='" . $email . "'";
        $statement = $dbh->prepare($sql);

        //3. Execute the SQL statement
        $statement->execute();

        //4. Process the SQL query results
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $dbPassword = $result['password'];

        //Compare the plaintext entered password with the hashed database password
        if (password_verify($password, $dbPassword)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $email
     * @return mixed|void
     * returns the user based on the email
     */
    static function getUser($email)
    {
        //1. Connect to the database
        require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

        try {
            $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        } catch (PDOException $e) {
            die($e->getMessage());
        }

        //2. Prepare the SQL statement
        $sql = "SELECT id, first_name, last_name, email, account_activated, role FROM users WHERE email='" . $email . "'";
        $statement = $dbh->prepare($sql);

        //3. Execute the SQL statement
        $statement->execute();

        //4. Process the SQL query results
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * This method will check the user exists in the database
     * and then send a password reset email with a link to another page.
     * @param $email string user email
     * @return $mailSuccess bool if the email was sent
     */
    static function sendPasswordReset($email) : bool
    {
        // if the email is valid, initiate the database lookup
        require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

        try {
            $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        } catch (PDOException $e) {
            die($e->getMessage());
        }

        // prep the statement
        $sql = "SELECT * FROM users WHERE email = :email";
        $statement = $dbh->prepare($sql);

        // bind the parameters
        $statement->bindParam(":email", $email);
        $statement->execute();

        // process the results of the query to make sure the email exists
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if ($row['email'] == $email) {
            // if it matches an email in the database, send the email
            // start by creating the expiration time in 24 hours (d+1)
            $expFormat = mktime(
                date("H"), date("i"), date("s"), date("m"), date("d") + 1, date("Y")
            );
            // put that date into datetime format
            $expDate = date("Y-m-d H:i:s", $expFormat);

            // create the key using md5 to hash their email + a string
            // (note this will not work using email and numbers on the same line)
            $hashKey = md5($email . "hash_this_word");
            // add a random substring to the key as well
            $addKey = substr(md5(uniqid(rand(), 1)), 3, 10);
            $hashKey = $hashKey . $addKey;

            // now insert this key into the database for later access
            // overwrite if there is already a key sent prior to prevent issues
            $sql = "INSERT INTO password_reset_temp (email, `key`, expDate)
                                VALUES (:email, :key, :expDate)
                                ON DUPLICATE KEY UPDATE `key` = :key, expDate = :expDate";
            $statement = $dbh->prepare($sql);

            // bind the parameters and execute
            $statement->bindParam(":email", $email);
            $statement->bindParam(":expDate", $expDate);
            $statement->bindParam(":key", $hashKey);
            $statement->execute();

            // create the email message with the link to reset the password
            // $_SERVER["HTTP_HOST"] will fill in the host's url
            $message = '<p>Dear ' . ucfirst($row['first_name']) . ',</p>
                            <p>Please click on the link to reset your password:</p>
                            <br>
                            <p><a href = "https://' . $_SERVER["HTTP_HOST"] . '/328/business-leads/password-email?key=' . $hashKey . '&email=' . $email . '">
                            https://' . $_SERVER["HTTP_HOST"] . '/328/business-leads/password-email?key=' . $hashKey . '&email=' . $email . '</a></p>
                            <br>
                            <p>This link will expire after 24 hours. If you did not request this email, 
                            please let your supervisor know. </p>';

            $to = $email;
            $subject = "Password Reset Request";

            // Set headers for HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            // Send email
            $mailSuccess = mail($to, $subject, $message, $headers);
            return $mailSuccess;
        }
        // if the row was not grabbed, user does not exist
        return false;
    }

    /**
     * This function will check if the email and key provided
     * match up with the email and key in the database.
     *
     * @param $date dateTime current date
     * @param $email string user email
     * @param $key string the hashed key from the email link
     * @return array of results
     */
    static function checkPasswordResetKey($email, $key) : array
    {
        require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

        try {
            $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        } catch (PDOException $e) {
            die($e->getMessage());
        }

        // check the database matches - the email and key should be in the same row
        $sql = "SELECT * FROM `password_reset_temp` WHERE `key`= :key and `email`= :email";
        $statement = $dbh->prepare($sql);

        // bind the parameters and execute
        $statement->bindParam(":email", $email);
        $statement->bindParam(":key", $key);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        // handle the boolean we get if the statement fails
        if (!$row){
            // if the link is invalid, the page should not load
            $resetError = 'The link used to access this page is expired.';
            $GLOBALS['f3']->set('SESSION.resetError, $resetError');
            $GLOBALS['f3']->reroute("error");
        }

        return $row;
    }

    /**
     * This function will UPDATE a user's password
     * from the old to their chosen new one.
     * @param $password
     * @return bool
     */
    static function resetPassword($hashKey, $email, $password) : bool
    {
        require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

        try {
            $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        } catch (PDOException $e) {
            die($e->getMessage());
        }

        if (Validate::validatePassword($password)) {
            // update their password AND delete the reset key at the same time
            $sql = "UPDATE users SET password = :password WHERE email = :email;
                                DELETE FROM password_reset_temp WHERE `key`= :key AND `email`= :email;";
            $statement = $dbh->prepare($sql);
            $statement->bindParam(":password", $password);
            $statement->bindParam(":email", $email);
            $statement->bindParam(":key", $hashKey);
            $statement->execute();
            return true;
        } else {
            return false;
        }
    }

    /**
     * This script function will delete a key from the database if the user
     * clicks on a password reset email and the site sees it's expired.
     * @param $email
     * @return void
     */
    static function deleteExpiredResetKey($email) : void
    {
        // Require database connection credentials
        require_once $_SERVER['DOCUMENT_ROOT'].'/../config.php';
        try {
            // instantiate the PDO database Object
            $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        }
        catch (PDOException $e){
            die($e->getMessage());
        }

        $sql = "DELETE FROM password_reset_temp WHERE email = :email";
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":email", $email);
        $statement->execute();
        $resetError = "The link you used is expired. Please request a new one.";
        $GLOBALS['f3']->set('SESSION.resetError', $resetError);
        $GLOBALS['f3']->reroute("error");
    }

    /**
     * @param $firstName
     * @param $lastName
     * @param $email
     * @param $password
     * @param $status
     * @param $role
     * @param $date
     * @return void
     * adds a user to the database
     */
    static function addUser($firstName, $lastName, $email, $password, $status,$role, $date)
    {
        require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

        try {
            $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        $sql = 'INSERT INTO users ( first_name, last_name, email, password, account_activated, role, created_at) 
                   VALUES(:First, :Last, :Email, :Password, :AccountActivated, :Role, :CreatedAt)';

        $statement = $dbh->prepare($sql);
        $statement->bindParam(':First', $firstName);
        $statement->bindParam(':Last', $lastName);
        $statement->bindParam(':Email', $email);
        $statement->bindParam(':Password', $password);
        $statement->bindParam(':AccountActivated', $status);
        $statement->bindParam(':Role', $role);
        $statement->bindParam(':CreatedAt', $date);

        $statement->execute();
    }

    /**
     * @return void
     * displays only users that have not been approved
     */
    static function displayUnapproved()
    {
        //connect to db
        require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

        try {
            $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
            //echo 'connected to database!';
        } catch (PDOException $e) {
            die($e->getMessage());
        }

        $sql = "SELECT * FROM users WHERE account_activated = 0";

        //prepare the statement
        $statement = $dbh->prepare($sql);

        //execute the statement
        $statement->execute();

        //process
        //get the array
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        //add to session
        $GLOBALS['f3']->set('approval', $result);
    }
    static function addLead($businessName, $businessAddress, $contactName, $businessPhone, $contactEmail, $driverName, $driverID, $slic)
    {
        require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

        try {
            $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
            echo 'connected to database!';
        } catch (PDOException $e) {
            die($e->getMessage());
        }
        //first
        $date = date('Y-m-d h:i:s', time());

        $sql = 'INSERT INTO leads (name, address, contact_name, contact_phone, contact_email, ups_employee_name,
                        ups_employee_id, slic, created_at) 
                VALUES(:Name, :Address, :Contact, :Phone, :Email, :Driver, :DriverID, :Slic, :Created_at)';

        $statement = $dbh->prepare($sql);
        $statement->bindParam(':Name', $businessName);
        $statement->bindParam(':Address', $businessAddress);
        $statement->bindParam(':Contact', $contactName);
        $statement->bindParam(':Phone', $businessPhone);
        $statement->bindParam(':Email', $contactEmail);
        $statement->bindParam(':Driver', $driverName);
        $statement->bindParam(':DriverID', $driverID);
        $statement->bindParam(':Slic', $slic);
        $statement->bindParam(':Created_at', $date);

        $statement->execute();
    }
    static function approveUser()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

            try {
                $GLOBALS['f3']->_dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
                echo 'connected to database!';
            } catch (PDOException $e) {
                die($e->getMessage());
            }

            $id = $_POST['id'];

            //define query
            $sql = 'UPDATE users Set account_activated = 1  WHERE id = :ID ';

            //prepare the statement
            $statement = $GLOBALS['f3']->_dbh->prepare($sql);

            //bind parameters
            $statement->bindParam(':ID', $id);


            $statement->execute();
            echo $id;

        }
    }
    static function deleteUser()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

            try {
                $GLOBALS['f3']->_dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
                echo 'connected to database!';
            } catch (PDOException $e) {
                die($e->getMessage());
            }
            $id = $_POST['id'];
            $sql = 'DELETE FROM users WHERE id = :ID ';
            //prepare the statement
            $statement = $GLOBALS['f3']->_dbh->prepare($sql);

            //bind parameters
            $statement->bindParam(':ID', $id);

            $statement->execute();
        }
    }
}