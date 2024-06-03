<?php

/**
 * The DataLayer class defines methods that can be called to get
 * data from the database.
 *
 * @author Tien Han <tienthuyhan@gmail.com>
 * @date   6/3/2024
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
}