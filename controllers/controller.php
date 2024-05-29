<?php

/**
 * The Controller class defines methods that can be called for routing
 * within the UPS Business Leads project.
 *
 * @author Tien Han <tienthuyhan@gmail.com>
 * @date   5/29/2024
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

    function passwordReset() : void {
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
                        // TODO: remove the connection echo
                        echo 'connected to database!';
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
                    if ($row['email'] == $email){
                        // if it matches an email in the database, send the email
                        // start by creating the expiration time in 24 hours (d+1)
                        $expFormat = mktime(
                            date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y")
                        );
                        // put that date into datetime format
                        $expDate = date("Y-m-d H:i:s",$expFormat);

                        // create the key using md5 to hash their email + a string
                        // (note this will not work using email and numbers on the same line)
                        $hashKey = md5($email."hash_this_word");
                        // add a random substring to the key as well
                        $addKey = substr(md5(uniqid(rand(),1)),3,10);
                        $hashKey = $hashKey . $addKey;

                        // now insert this key into the database for later access
                        $sql = 'INSERT INTO password_reset_temp (email, key, expDate)
                                VALUES (:email, :key, :expDate)';
                        $statement = $dbh->prepare($sql);

                        // bind the parameters and execute
                        $statement->bindParam(":email", $email);
                        $statement->bindParam(":expDate", $expDate);
                        $statement->bindParam(":key", $hashKey);
                        $statement->execute();

                        // create the email message with the link to reset the password
                        // TODO: make this work with other links so it's usable for Tien + Garrett
                        $message = '<p>Dear '. ucfirst($row['first_name']) . ',</p>
                            <p>Please click on the link to reset your password:</p>
                            <br>
                            <p><a href = "https://www.smarkwardt.greenriverdev.com/328/business-leads/password-reset.html?key='.$hashKey.'&email='.$email.'">
                            https://www.www.smarkwardt.greenriverdev.com/328/business-leads/password-reset.php?key='.$hashKey.'&email='.$email.'</a></p>
                            <br>
                            <p>This link will expire after 24 hours. If you did not request this forgotten password email, 
                            please let your supervisor know. </p>';

                        // $headers = "From: no-reply@UPSLeads.com" . "\r\n";
                        $to = $email;
                        $subject = "Password Reset Request";

                        // Set headers for HTML email
                        $headers  = "MIME-Version: 1.0" . "\r\n";
                        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                        // Send email
                        $mailSuccess = mail($to, $subject, $message, $headers);
                        if ($mailSuccess != null){
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
        echo $view->render('views/password-reset.html');
    }
}