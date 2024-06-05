<?php

/**
 * The DataLayer class defines methods that can be called to get
 * data from the database.
 *
 * @author Tien Han <tienthuyhan@gmail.com>, Sage Markwardt
 * @date   6/5/2024
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
     *
     */
    function sendPasswordReset($email)
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
                            <p><a href = "https://www.'. $_SERVER["HTTP_HOST"] .'.com/328/business-leads/password-email?key=' . $hashKey . '&email=' . $email . '">
                            https://www.www.smarkwardt.greenriverdev.com/328/business-leads/password-email.php?key=' . $hashKey . '&email=' . $email . '</a></p>
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
            if ($mailSuccess != null) {
                // set a message to display letting them know it was sent
                $this->_f3->set('errors["email"]', 'A reset email has been sent');
            }
        } else {
            // if it doesn't match, show the user an error
            $this->_f3->set('errors["email"]', 'Your email is not in the database. Sign up below.');
        }
    }
}