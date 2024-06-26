<?php

/**
 * The Controller class defines methods that can be called for routing
 * within the UPS Business Leads project.
 *
 * @author Tien Han <tienthuyhan@gmail.com>, Sage Markwardt
 * @date   6/05/2024
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
     * It will allow the user to send a message to the administrator.
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
                if (!Validate::validateName($firstName)) {
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
                if (!Validate::validateName($lastName)) {
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
                if (!Validate::validateEmail($email)) {
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

        //We have to unset the contact us session details here (rather than session_destroy(),
        //because session_destroy() would also remove our user log in session.
        unset($_SESSION['firstName']);
        unset($_SESSION['lastName']);
        unset($_SESSION['email']);
        unset($_SESSION['concern']);
        unset($_SESSION['contactSubmitted']);
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
     * This will allow the user to log in
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
                if (!Validate::validateUPSEmail($email)) {
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

            //Check password against what's in the database
            if (!DataLayer::passwordMatches($email, $password)) {
                $allValid = false;
            };

            //Redirect to the application dashboard
            if ($allValid) {
                //Save the user data into the hive for the session
                $userDetails = DataLayer::getUser($email);

                //TODO: Handle a case where the user isn't activated yet

                //If the person is an admin (Division Manager)
                if ($userDetails['role'] == "Division Manager") {
                    $user = new Admin(
                        $userDetails['first_name'],
                        $userDetails['last_name'],
                        $userDetails['email'],
                        $userDetails['slic']);
                } else {
                    $user = new User(
                        $userDetails['first_name'],
                        $userDetails['last_name'],
                        $userDetails['email'],
                        $userDetails['slic']);
                }

                $this->_f3->set('SESSION.user', $user);

                $this->_f3->reroute("dashboard");
            }
        }

        //Render a view page
        $view = new Template();
        echo $view->render('views/login.html');
    }

    /**
     * The method for rendering the reset password request email page
     * This page will allow the user to request an email with a special link
     * to reset their password. It will not send if their email isn't in the
     * database.
     * @return void
     */
    function passwordReset(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //Get submitted form data
            $email = $_POST['email'];

            // Validate the email before attempting recovery email
            if (!empty($email)) {
                if (!Validate::validateUPSEmail($email)) {
                    // add the error to the errors array
                    $this->_f3->set('errors["email"]', 'Please enter a valid UPS email!');
                }
                if (empty($this->_f3->get('errors'))) {
                    // save whether the email was sent or not
                   $success = DataLayer::sendPasswordReset($email);
                   // use the success variable to determine what to show
                    if ($success != null) {
                        // set a message to display letting them know it was sent
                        $this->_f3->set('errors["email"]', 'A reset email has been sent');
                    }
                } else {
                    // if it doesn't match, show the user an error
                    $this->_f3->set('errors["email"]', 'Your email is not in the database. Sign up below.');
                }
                }
            }
        $view = new Template();
        echo $view->render('views/password-request.html');
    }

    /**
     * The function for displaying the password-email view.
     * This page will only be accessed using the link sent in an email,
     * so if the correct items aren't in the URL it will not load.
     * @return void
     */
    function passwordEmail()
    {
        $resetError = '';
        // if the link doesn't have a key OR an email, die
        if (!isset($_GET["key"]) || !isset($_GET["email"])) {
            $resetError = "The link used to access this page is invalid.";
            $this->_f3->set('SESSION.resetError', $resetError);
            $this->_f3->reroute("error");
        }

        // if the correct items are in the link used to reach this page AND
        // the page has not been posted (don't allow use of the link twice)
        if (isset($_GET["key"]) && isset($_GET["email"])) {
            // assign variables
            $hashKey = $_GET["key"];
            $email = $_GET["email"];
            // get today's date to check against the expiration
            $curDate = date("Y-m-d H:i:s");
            // connect to the database
            $row = DataLayer::checkPasswordResetKey($email, $hashKey);

            // check the date of the code (should not be later than the expiration date)
            $expDate = $row['expDate'];
            if ($expDate >= $curDate) {
                // check if the new password has been entered
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // if the page has posted, set the password
                    $password = $_POST["password"];
                    // hash the password
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    // check if it's a valid password according to our site
                    if (DataLayer::resetPassword($hashKey, $email, $password)){
                        $this->_f3->set('errors["reset"]', "Password changed");
                    } else {
                        $this->_f3->set('errors["reset"]', "Please enter a valid password");
                    }
                }
            } else {
                // if we get here, their code was expired
                // we should delete it and not load the page
                DataLayer::deleteExpiredResetKey($email);
            }
        }
        $view = new Template();
        echo $view->render('views/password-reset.html');
    }

    /**
     * The method for rendering the signup page
     * The form on this page will allow users to create an account.
     * @return void
     */
    function signUp(): void
    {
        require $_SERVER['DOCUMENT_ROOT'] . '/../config.php';

        try {
            $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
            //echo 'connected to database!';
        } catch (PDOException $e) {
            $this->_f3->reroute("error");
        }
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
            if (Validate::validateName($firstName)) {
                $this->_f3->get('SESSION.user')->setFirstName($firstName);
            } else {
                $this->_f3->set('errors["firstNameSignUpError"]', "please enter a valid first name");
            }

            //validate last name
            if (Validate::validateName($lastName)) {
                $this->_f3->get('SESSION.user')->setLastName($lastName);
            } else {
                $this->_f3->set('errors["lastNameSignUpError"]', "please enter a valid last name");
            }

            //validate UPS email
            if (Validate::validateUPSEmail($email)) {
                $this->_f3->get('SESSION.user')->setEmail($email);
            } else {
                $this->_f3->set('errors["emailSignUpError"]', "please enter a valid UPS email");
            }

            //validate slic
            if (Validate::validSlic($slic)) {
                $this->_f3->get('SESSION.user')->setSlic($slic);
            } else {
                $this->_f3->set('errors["slicSignUpError"]', "please enter a valid SLIC");
            }

            //role
            if (Validate::validRole($role)) {
                $this->_f3->get('SESSION.user')->setRole($role);
            } else {
                $this->_f3->set('errors["roleSignUpError"]', "please enter a valid role");
            }

            //Perform validation on the submitted password
            $passwordError = '';
            if (!empty($password)) {
                if (!Validate::validatePassword($password)) {
                    $allValid = false;
                    $passwordError = 'Please enter in a valid password!';
                } else {
                    //If the password is valid, hash it to save into the database
                    $password = password_hash($password, PASSWORD_DEFAULT);
                }
            } else {
                //Password is required
                $allValid = false;
                $passwordError = 'Please enter your password.';
            }
            $this->_f3->set('SESSION.passwordError', $passwordError);

            //check errors[]
            if (empty($this->_f3->get('errors'))) {
                //send email to DM to approve sign up request and add to DB
                $status = 0;
                $date = date('Y-m-d h:i:s', time());
                $subject = "Access Requested From: " . $this->_f3->get('SESSION.user')->getFirstName()." ". $this->_f3->get('SESSION.user')->getLastName();
                $to = "garrett.ballreich101@gmail.com";
                $headers = array(
                    'From' => 'webmaster@example.com',
                    'Reply-To' => 'webmaster@example.com',
                    'X-Mailer' => 'PHP/' . phpversion()
                );

                DataLayer::addUser($firstName, $lastName, $email, $password, $status, $role, $date);

                $msg = "https://garrettballreich.greenriverdev.com/328/business-leads/approval";
                mail($to, $subject, $msg, $headers);

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
        DataLayer::displayUnapproved();

        $view = new Template();
        echo $view->render('views/approval.html');
    }

    /**
     * Method for rendering the Leads Form, where business leads are submitted
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
            if (Validate::validateName($businessName)) {
                $this->_f3->get('SESSION.lead')->setBusinessName($businessName);
            } else {
                $this->_f3->set('errors["businessNameError"]', "please enter a valid name");
            }

            //validate contact name
            if (Validate::validateName($contactName)) {
                $this->_f3->get('SESSION.lead')->setContactName($contactName);
            } else {
                $this->_f3->set('errors["contactNameError"]', "please enter a valid contact name");
            }

            //validate contact lastname
            if (Validate::validateName($contactLastName)) {
                $this->_f3->get('SESSION.lead')->setContactLastName($contactLastName);
            } else {
                $this->_f3->set('errors["contactLastNameError"]', "please enter a valid contact last name");
            }

            //validate phone number
            if (Validate::validPhone($businessPhone)) {
                $this->_f3->get('SESSION.lead')->setBusinessPhone($businessPhone);
            } else {
                $this->_f3->set('errors["businessPhoneError"]', "please enter a valid phone number");
            }

            //validate email
            if (Validate::validateEmail($contactEmail)) {
                $this->_f3->get('SESSION.lead')->setContactEmail($contactEmail);
            } else {
                $this->_f3->set('errors["contactEmailError"]', "please enter a valid email address");
            }

            //validate driver name
            if (Validate::validateName($driverName)) {
                $this->_f3->get('SESSION.lead')->setDriverName($driverName);
            } else {
                $this->_f3->set('errors["driverNameError"]', "please enter a valid contact last name");
            }

            //validate employee ID
            if (Validate::validEmployeeID($driverID)) {
                $this->_f3->get('SESSION.lead')->setDriverID($driverID);
            } else {
                $this->_f3->set('errors["driverIDError"]', "please enter a valid employee ID");
            }

            //validate slic
            if (Validate::validSlic($slic)) {
                $this->_f3->get('SESSION.lead')->setSlic($slic);
            } else {
                $this->_f3->set('errors["slicError"]', "please enter a slic in the North West Division");
            }

            //check errors[]
            if (empty($this->_f3->get('errors'))) {
                //add lead object to DB
                //email lead object to perspective center manager
                DataLayer::addLead($businessName, $businessAddress, $contactName, $businessPhone, $contactEmail, $driverName, $driverID, $slic);

                $this->_f3->reroute('form-summary');
            }
        }

        //Render a view page
        $view = new Template();
        echo $view->render('views/form.html');
    }

    /**
     * Method for rendering the Leads Form's summary page
     * which will show the information entered on the form
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
     * which will display information about business leads
     * related to the specific user.
     * @return void
     */
    function dashboard(): void
    {
        //Render a view page
        $view = new Template();
        echo $view->render('views/dashboard.html');
    }

    /**
     * This will render the approval page
     * where an admin can approve an account request
     * @return void
     */
    function approveRequest(): void
    {
        DataLayer::approveUser();
        $view = new Template();
        echo $view->render('views/approveRequest.html');
    }

    /**
     * This will render the page for deleting an account request
     * @return void
     */
    function deleteRequest(): void
    {
        DataLayer::deleteUser();
        $view = new Template();
        echo $view->render('views/deleteRequest.html');
    }

    /**
     * This will render the error page
     * which will be displayed if someone uses an old password reset link.
     * @return void
     */
    function error(): void
    {
        //Render a view page
        $view = new Template();
        echo $view->render('views/error.html');
    }
}