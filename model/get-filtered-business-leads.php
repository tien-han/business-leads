<?php

/**
 * This PHP script pulls business leads from the database depending on the user's SLIC.
 *
 * @author Garrett Ballreich, Tien Han <tienthuyhan@gmail.com>
 * @date   6/11/2024
 */

//Get the user's SLIC for filtering
$userSlic = $_SESSION['user']->getSlic();

//1. Connect to the database
require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';
try {
    $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
} catch (PDOException $e) {
    die($e->getMessage());
}

//2. Prepare the SQL statement
$sql = "SELECT * FROM leads WHERE slic = :Slic";
$statement = $dbh->prepare($sql);
$statement->bindParam(':Slic', $userSlic);

//3. Execute the SQL statement
$statement->execute();

//4. Process the SQL query results
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

//Save leads into a session in case we need to access it another way
$_SESSION['lead'] = $result;

//5. Echo out results so that JavaScript can pick it up
echo json_encode($result);