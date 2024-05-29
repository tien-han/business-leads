<?php
/**
 * 328/business-leads/index.php
 * Simple MVC using the Fat-Free framework for our group project.
 * @author Garrett Ballreich, Sage Markwardt, Tien Han <tienthuyhan@gmail.com>
 * @date 5/29/2024
 * @version 1.0
 */

//Turn on error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

//Require the autoload file
require_once('vendor/autoload.php');

//Require the form validation file
require_once('model/validate.php');
//need to update composer with /classes
require_once('classes/lead.php');
require_once('classes/user.php');

$obj="";

//Instantiate the F3 Base class (Fat-Free)
$f3 = Base::instance();
$controller = new Controller($f3);

/**
 * Define a default route (homepage)
 */
$f3-> route('GET /', function() {
    $GLOBALS['controller']->homepage();
});

/**
 * Route to the "Contact Us" page
 */
$f3-> route('GET|POST /contact', function($f3) {
    $GLOBALS['controller']->contactUs();
});

//Define the FAQ route
$f3-> route('GET /faq', function() {
    //Render a view page
    $view = new Template();
    echo $view->render('views/faq.html');
});

//Define the Login route
$f3-> route('GET|POST /login', function($f3) {
    //If the user has submitted a post request (filled out the login form)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //Get submitted form data
        $email = $_POST['email'];
        $password = $_POST['password'];

        $allValid = true;

        //Perform validation on the submitted username (email)
        $emailError = '';
        if (!empty($email)) {
            if (!validateUPSEmail($email)) {
                $allValid = false;
                $emailError = 'Please enter in a valid UPS email!';
            }
        } else {
            //Email is required
            $allValid = false;
            $emailError = 'Please enter an email.';
        }
        $f3->set('SESSION.email', $email);
        $f3->set('SESSION.emailError', $emailError);

        //Perform validation on the submitted password
        $passwordError = '';
        if (!empty($password)) {
            if (!validatePassword($password)) {
                $allValid = false;
                $passwordError = 'Please enter in a valid password!';
            }
        } else {
            //Password is required
            $allValid = false;
            $passwordError = 'Please enter your password.';
        }
        $f3->set('SESSION.passwordError', $passwordError);

        //Redirect to the application dashboard
        if ($allValid) {
            $f3->reroute("dashboard");
        }
    }

    //Render a view page
    $view = new Template();
    echo $view->render('views/login.html');
});

// send user to the password-reset page
$f3->route('GET|POST /password-request', function() {
    $GLOBALS['controller']->passwordReset();
});

// render the view from the reset email link
$f3->route('GET|POST /password-email', function() {
    $GLOBALS['controller']->passwordEmail();
});

// define the signup route
$f3-> route('GET|POST /sign-up', function($f3) {
    //var_dump($_POST);
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $slic = $_POST['slic'];
        $role = $_POST['role'];
        $password = $_POST['password'];

        //construct new user object
        $f3->set('SESSION.user', new User());

        //validate first name
        if (validateName($firstName)) {
            $f3->get('SESSION.user')->setFirstName($firstName);
        } else {
            $f3->set('errors["firstNameSignUpError"]', "please enter a valid first name");
        }

        //validate last name
        if (validateName($lastName)) {
            $f3->get('SESSION.user')->setLastName($lastName);
        } else {
            $f3->set('errors["lastNameSignUpError"]', "please enter a valid last name");
        }

        //validate UPS email
        if (validateUPSEmail($email)) {
            $f3->get('SESSION.user')->setEmail($email);
        } else {
            $f3->set('errors["emailSignUpError"]', "please enter a valid UPS email");
        }

        //validate slic
        if (validSlic($slic)) {
            $f3->get('SESSION.user')->setSlic($slic);
        } else {
            $f3->set('errors["slicSignUpError"]', "please enter a valid SLIC");
        }

        //role
        if (validRole($role)) {
            $f3->get('SESSION.user')->setRole($role);
        } else {
            $f3->set('errors["roleSignUpError"]', "please enter a valid role");
        }

        //password
        if (validatePassword($password)) {
            $f3->get('SESSION.user')->setPassword($password);
        } else {
            $f3->set('errors["passwordSignUpError"]', "please enter a valid password");
        }

        //check errors[]
        if (empty($f3->get('errors'))) {
            //send email to DM to approve sign up request and add to DB
            $status = 0;
            $date = date('Y-m-d h:i:s', time());
            $subject = "Access Requested From: " . $f3->get('SESSION.user')->getFirstName()." ". $f3->get('SESSION.user')->getLastName();
            $to = "garrett.ballreich101@gmail.com";
            //msg sould be a link to appove the request
            require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

            try {
                $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
                echo 'connected to database!';
            } catch (PDOException $e) {
                die($e->getMessage());
            }

            //First
            $sql = 'INSERT INTO users (first_name, last_name, email, password, account_activated, role, created_at) 
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

            $msg = "https://garrettballreich.greenriverdev.com/328/business-leads/approval";
            mail($to, $subject, $msg);

            $f3->reroute("dashboard");
        }
    }

    $view = new Template();
    echo $view->render('views/sign-up.html');
});

$f3-> route('GET|POST /approval', function() {
    //Render a view page
    //connect to db
    require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

    try {
        $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        echo 'connected to database!';
    } catch (PDOException $e) {
        die($e->getMessage());
    }

    $sql = "SELECT * FROM users WHERE account_activated = 0";

    //prepare the statement
    $statement = $dbh->prepare($sql);

    //execute the statement
    $statement->execute();

    //process
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    echo "<h1>Approve Requests</h1>";
    echo "<ol>";
    foreach($result as $row){
        echo "<li>".$row['last_name']. ", ".$row['first_name']."-".$row['']."</li>";
    }
    echo "</ol>";
    $view = new Template();
    echo $view->render('views/approval.html');
});

//Define the MAIN FORM route
$f3-> route('GET|POST /form', function($f3) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $businessName = $_POST['businessName'];
        $businessAddress = $_POST['businessAddress'];
        $contactName = $_POST['firstName'];
        $contactLastName = $_POST['lastName'];
        $businessPhone = $_POST['phoneNum'];
        $contactEmail = $_POST['email'];
        $driverName = $_POST['driverName'];
        $driverID = $_POST['driverID'];
        $slic = $_POST['slic'];

        //construct new lead object
        //add it to the session
        //with all unknown values until they pass validation
        $f3->set("SESSION.lead", new Lead());
        //set each variable to an object field

        //validate name
        //all letter and not null
        if (validateName($businessName)) {
            $f3->get('SESSION.lead')->setBusinessName($businessName);
        } else {
            $f3->set('errors["businessNameError"]', "please enter a valid name");
        }
        //validate address

        //validate contact name
        if (validateName($contactName)) {
            $f3->get('SESSION.lead')->setContactName($contactName);
        } else {
            $f3->set('errors["contactNameError"]', "please enter a valid contact name");
        }

        //validate contact lastname
        if (validateName($contactLastName)) {
            $f3->get('SESSION.lead')->setContactLastName($contactLastName);
        } else {
            $f3->set('errors["contactLastNameError"]', "please enter a valid contact last name");
        }

        //validate phone number
        if (validPhone($businessPhone)) {
            $f3->get('SESSION.lead')->setBusinessPhone($businessPhone);
        } else {
            $f3->set('errors["businessPhoneError"]', "please enter a valid phone number");
        }

        //validate email
        if (validateEmail($contactEmail)) {
            $f3->get('SESSION.lead')->setContactEmail($contactEmail);
        } else {
            $f3->set('errors["contactEmailError"]', "please enter a valid email address");
        }

        //validate driver name
        if (validateName($driverName)) {
            $f3->get('SESSION.lead')->setDriverName($driverName);
        } else {
            $f3->set('errors["driverNameError"]', "please enter a valid contact last name");
        }

        //validate employee ID
        if (validEmployeeID($driverID)) {
            $f3->get('SESSION.lead')->setDriverID($driverID);
        } else {
            $f3->set('errors["driverIDError"]', "please enter a valid employee ID");
        }

        //validate slic
        if (validSlic($slic)) {
            $f3->get('SESSION.lead')->setSlic($slic);
        } else {
            $f3->set('errors["slicError"]', "please enter a slic in the North West Division");
        }

        //check errors[]
        if (empty($f3->get('errors'))) {
            //add lead object to DB
            //email lead object to perspective center manager
            $f3->reroute('formSummary');
        }
    }

    //Render a view page
    $view = new Template();
    echo $view->render('views/form.html');
});

//Define the form summary route
$f3-> route('GET /formSummary', function() {
    //Render a view page
    $view = new Template();
    echo $view->render('views/formSummary.html');
});

//Define the dashboard route
$f3-> route('GET /dashboard', function() {
    //Render a view page
    $view = new Template();
    echo $view->render('views/dashboard.html');
});

//Run Fat-Free
$f3->run();