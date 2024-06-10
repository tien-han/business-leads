<?php

/**
 * This PHP script pulls business leads from the database depending on the user's SLIC.
 *
 * @author Tien Han <tienthuyhan@gmail.com>
 * @date   6/7/2024
 */

//1. Connect to the database
require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';
try {
    $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
} catch (PDOException $e) {
    die($e->getMessage());
}

//2. Prepare the SQL statement
$sql = "SELECT * FROM leads";
$statement = $dbh->prepare($sql);

//3. Execute the SQL statement
$statement->execute();

//4. Process the SQL query results
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

//5. Echo out results so that JavaScript can pick it up
echo json_encode($result);