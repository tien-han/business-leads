<?php

/**
 * The Controller class defines methods that can be called for routing
 * within the UPS Business Leads project.
 *
 * @author Tien Han <tienthuyhan@gmail.com>
 * @date   6/01/2024
 */
class Controller
{
    //Instance variables
    private $_f3; //Fat-Free Router

    /**
     * Constructor creates a controller object, which instantiates Fat-Free
     *
     * @param Base $f3 Fat-Free
     */
    function __construct($f3)
    {
        $this->_f3 = $f3;
    }

    /**
     * The method for rendering the homepage
     *
     * @return void
     */
    function homepage(): void
    {
        //Render a view page
        $view = new Template();
        echo $view->render('views/homepage.html');
    }

    /**
     * The method for rendering the Contact Us page
     *
     * @return void
     */
    function contactUs(): void
    {
        //If the user has submitted a post request (filled out the contact us form)
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            //Get all form responses
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $email = $_POST['email'];
            $concern = $_POST['concern'];

            $allValid = true;
            $this->_f3->set('SESSION.contactSubmitted', false);

            //Perform validation on the submitted first name
            $firstNameError = '';
            if (!empty($firstName)) {
                if (!validateName($firstName)) {
                    $allValid = false;
                    $firstNameError = 'Please enter in an alphabetic first name!';
                }
            } else {
                //First name is required
                $allValid = false;
                $firstNameError = 'Please enter your first name.';
            }
            $this->_f3->set('SESSION.firstName', $firstName);
            $this->_f3->set('SESSION.firstNameError', $firstNameError);

            //Perform validation on the submitted last name
            $lastNameError = '';
            if (!empty($lastName)) {
                if (!validateName($lastName)) {
                    $allValid = false;
                    $lastNameError = 'Please enter in an alphabetic last name!';
                }
            } else {
                //Last name is required
                $allValid = false;
                $lastNameError = 'Please enter your last name.';
            }
            $this->_f3->set('SESSION.lastName', $lastName);
            $this->_f3->set('SESSION.lastNameError', $lastNameError);

            //Perform validation on the submitted email
            $emailError = '';
            if (!empty($email)) {
                if (!validateEmail($email)) {
                    $allValid = false;
                    $emailError = 'Please enter in a valid email!';
                }
            } else {
                //Email is required
                $allValid = false;
                $emailError = 'Please enter an email.';
            }
            $this->_f3->set('SESSION.email', $email);
            $this->_f3->set('SESSION.emailError', $emailError);

            //Ensure that there is text in the concern text box
            $concernError = '';
            if (empty($concern)) {
                //Concern is required
                $allValid = false;
                $concernError = 'Please enter your concern.';
            }
            $this->_f3->set('SESSION.concern', $concern);
            $this->_f3->set('SESSION.concernError', $concernError);

            //If everything is valid, let the user know that their response has been sent
            if ($allValid) {
                $this->_f3->set('SESSION.contactSubmitted', true);
            }
        }

        //Render a view page
        $view = new Template();
        echo $view->render('views/contact.html');
        session_destroy();
    }

    /**
     * The method for rendering the FAQ page
     *
     * @return void
     */
    function faq(): void
    {
        $view = new Template();
        echo $view->render('views/faq.html');
    }

    /**
     * The method for rendering the login page
     *
     * @return void
     */
    function login(): void
    {
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
            $this->_f3->set('SESSION.email', $email);
            $this->_f3->set('SESSION.emailError', $emailError);

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
            $this->_f3->set('SESSION.passwordError', $passwordError);

            //Redirect to the application dashboard
            if ($allValid) {
                $this->_f3->reroute("dashboard");
            }
        }

        //Render a view page
        $view = new Template();
        echo $view->render('views/login.html');
    }

    /**
     * The method for rendering the reset password request email page
     *
     * @return void
     */
    function passwordReset(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Get submitted form data
            $email = $_POST['email'];

            // Validate the email before attempting recovery email
            if (!empty($email)) {
                //TODO: turn validation on for this page after testing is done
                /*if (!validateUPSEmail($email)) {
                    // add the error to the errors array
                    $this->_f3->set('errors["email"]', 'Please enter a valid UPS email!');
                }*/
                if (empty($this->_f3->get('errors'))) {
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
                        $sql = "INSERT INTO password_reset_temp (email, `key`, expDate)
                                VALUES (:email, :key, :expDate)";
                        $statement = $dbh->prepare($sql);

                        // bind the parameters and execute
                        $statement->bindParam(":email", $email);
                        $statement->bindParam(":expDate", $expDate);
                        $statement->bindParam(":key", $hashKey);
                        $statement->execute();

                        // create the email message with the link to reset the password
                        // TODO: make this work with other links so it's usable for Tien + Garrett
                        $message = '<p>Dear ' . ucfirst($row['first_name']) . ',</p>
                            <p>Please click on the link to reset your password:</p>
                            <br>
                            <p><a href = "https://www.smarkwardt.greenriverdev.com/328/business-leads/password-email?key=' . $hashKey . '&email=' . $email . '">
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
        }
        $view = new Template();
        echo $view->render('views/password-request.html');
    }

    //TODO: Add in PHP Doc block about what this method renders
    function passwordEmail()
    {
        // if the correct items are in the link used to reach this page AND
        // the page has not been posted (don't allow use of the link twice)
        if (isset($_GET["key"]) && isset($_GET["email"])) {
            // assign variables
            $hashKey = $_GET["key"];
            $email = $_GET["email"];
            echo $email;
            // get today's date to check against the expiration
            $curDate = date("Y-m-d H:i:s");
            // connect to the database
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
            $statement->bindParam(":key", $hashKey);
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                // if the link is invalid, the page should not load
                die('The link used to access this page is invalid.');
            }

            // check the date of the code
            $expDate = $row['expDate'];
            if ($expDate >= $curDate) {
                // check if
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // if the page has posted, set the password
                    $password = $_POST["password"];
                    echo $password;
                    // check if it's a valid password
                    if (validatePassword($password)) {
                        $sql = "UPDATE users SET password = :password WHERE email = :email";
                        $statement = $dbh->prepare($sql);
                        $statement->bindParam(":password", $password);
                        $statement->bindParam(":email", $email);
                        $statement->execute();
                    } else {
                        $this->_f3->set('errors["reset"]', "please enter a valid password");
                    }
                }
            } else {
                // if we get here, their code was expired
                die("This code is expired");
            }
        }
        $view = new Template();
        echo $view->render('views/password-reset.html');
    }

    /**
     * The method for rendering the signup page
     *
     * @return void
     */
    function signUp(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $email = $_POST['email'];
            $slic = $_POST['slic'];
            $role = $_POST['role'];
            $password = $_POST['password'];

            //construct new user object
            $this->_f3->set('SESSION.user', new User());

            //validate first name
            if (validateName($firstName)) {
                $this->_f3->get('SESSION.user')->setFirstName($firstName);
            } else {
                $this->_f3->set('errors["firstNameSignUpError"]', "please enter a valid first name");
            }

            //validate last name
            if (validateName($lastName)) {
                $this->_f3->get('SESSION.user')->setLastName($lastName);
            } else {
                $this->_f3->set('errors["lastNameSignUpError"]', "please enter a valid last name");
            }

            //validate UPS email
            if (validateUPSEmail($email)) {
                $this->_f3->get('SESSION.user')->setEmail($email);
            } else {
                $this->_f3->set('errors["emailSignUpError"]', "please enter a valid UPS email");
            }

            //validate slic
            if (validSlic($slic)) {
                $this->_f3->get('SESSION.user')->setSlic($slic);
            } else {
                $this->_f3->set('errors["slicSignUpError"]', "please enter a valid SLIC");
            }

            //role
            if (validRole($role)) {
                $this->_f3->get('SESSION.user')->setRole($role);
            } else {
                $this->_f3->set('errors["roleSignUpError"]', "please enter a valid role");
            }

            //password
            if (validatePassword($password)) {
                $this->_f3->get('SESSION.user')->setPassword($password);
            } else {
                $this->_f3->set('errors["passwordSignUpError"]', "please enter a valid password");
            }

            //check errors[]
            if (empty($this->_f3->get('errors'))) {
                //send email to DM to approve sign up request and add to DB
                $status = 0;
                $date = date('Y-m-d h:i:s', time());
                $subject = "Access Requested From: " . $this->_f3->get('SESSION.user')->getFirstName()." ". $this->_f3->get('SESSION.user')->getLastName();
                $to = "garrett.ballreich101@gmail.com";
                //msg should be a link to approve the request
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

                $this->_f3->reroute("dashboard");
            }
        }

        $view = new Template();
        echo $view->render('views/sign-up.html');
    }

    /**
     * The method for rendering the approval page.
     * All new users to approval will be listed on this page.
     *
     * @return void
     */
    function approval(): void
    {
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
    }

    /**
     * Method for rendering the main form, where business leads are submitted
     *
     * @return void
     */
    function mainForm(): void
    {
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
            $this->_f3->set("SESSION.lead", new Lead());
            //set each variable to an object field

            //validate name
            //all letter and not null
            if (validateName($businessName)) {
                $this->_f3->get('SESSION.lead')->setBusinessName($businessName);
            } else {
                $this->_f3->set('errors["businessNameError"]', "please enter a valid name");
            }

            //validate contact name
            if (validateName($contactName)) {
                $this->_f3->get('SESSION.lead')->setContactName($contactName);
            } else {
                $this->_f3->set('errors["contactNameError"]', "please enter a valid contact name");
            }

            //validate contact lastname
            if (validateName($contactLastName)) {
                $this->_f3->get('SESSION.lead')->setContactLastName($contactLastName);
            } else {
                $this->_f3->set('errors["contactLastNameError"]', "please enter a valid contact last name");
            }

            //validate phone number
            if (validPhone($businessPhone)) {
                $this->_f3->get('SESSION.lead')->setBusinessPhone($businessPhone);
            } else {
                $this->_f3->set('errors["businessPhoneError"]', "please enter a valid phone number");
            }

            //validate email
            if (validateEmail($contactEmail)) {
                $this->_f3->get('SESSION.lead')->setContactEmail($contactEmail);
            } else {
                $this->_f3->set('errors["contactEmailError"]', "please enter a valid email address");
            }

            //validate driver name
            if (validateName($driverName)) {
                $this->_f3->get('SESSION.lead')->setDriverName($driverName);
            } else {
                $this->_f3->set('errors["driverNameError"]', "please enter a valid contact last name");
            }

            //validate employee ID
            if (validEmployeeID($driverID)) {
                $this->_f3->get('SESSION.lead')->setDriverID($driverID);
            } else {
                $this->_f3->set('errors["driverIDError"]', "please enter a valid employee ID");
            }

            //validate slic
            if (validSlic($slic)) {
                $this->_f3->get('SESSION.lead')->setSlic($slic);
            } else {
                $this->_f3->set('errors["slicError"]', "please enter a slic in the North West Division");
            }

            //check errors[]
            if (empty($this->_f3->get('errors'))) {
                //add lead object to DB
                //email lead object to perspective center manager
                $this->_f3->reroute('form-summary');
            }
        }

        //Render a view page
        $view = new Template();
        echo $view->render('views/form.html');
    }

    /**
     * Method for rendering the main form's summary page
     *
     * @return void
     */
    function formSummary(): void
    {
        //Render a view page
        $view = new Template();
        echo $view->render('views/form-summary.html');
    }

    /**
     * Method for rendering the dashboard page
     *
     * @return void
     */
    function dashboard(): void
    {
        //Render a view page
        $view = new Template();
        echo $view->render('views/dashboard.html');
    }
}