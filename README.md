# UPS Business Leads

## Project Description
This project provides a quick way for UPS drivers to submit business leads and a central area where center and division
managers can then manage and follow up on these business leads.

### The user flow for each type of user:
#### UPS Drivers
The UPS driver doesn't need to sign in; all they need is the business lead form in order to submit their business lead.

#### Center Managers
Center Managers are our general users. They have to create an account and be approved by a Division Manager in order to
see any business lead data in their dashboard. Once approved, they can only see business leads submitted for their slic
(building).

#### Division Managers
Division Managers are our admin users. They have to have an account and be approved as an admin (by another admin) in
order to see business lead data and approve other users. They will see business leads for multiple slics.

## Authors & Working URLs
- Garrett Ballreich: https://garrettballreich.greenriverdev.com/328/business-leads/
- Sage Markwardt: http://smarkwardt.greenriverdev.com/328/business-leads/
- Tien Han: https://tienthan.greenriverdev.com/328/business-leads/

## Implementation of Project Requirements
1. Separates all database/business logic using the MVC pattern.
   - Each type of file/logic has been separated into it's own file and folder. Front end HTML are all within the
      `views` folder, JavaScript within the `scripts` folder, CSS with the `styles` folder, database calls within the
      `model` folder, server-side logic in the `controller` file, classes within the `classes` file, and all routing
      within the `index.php` file.
2. Routes all URLs and leverages a templating language using the Fat-Free framework.
   - All URLs are routed in `index.php` and templating language is being used in all HTML files where data is being
      rendered from the backend.
3. Has a clearly defined database layer using PDO and prepared statements.
    - Database layer is separated into files in the `model` folder (specifically `data-layer.php`,
   `get-all-business-leads.php`, and `get-filtered-business-leads.php`) and prepared statements are used for security.
4. Data can be added and viewed.
    - New users can be added via the `Sign In` page, and seen in the `Approval` page.
    - Business leads can be added via the `Main Form`page, and seen in the `Dashboard` page.
5. Has a history of commits from both team members to a Git repository. Commits are clearly commented.
    - This can be seen by going to the repository and clicking the tab `Insights` -> `Contributors`.
    - All teammates have a history of commits with meaningful commit messages.
6. Uses OOP, and utilizes multiple classes, including at least one inheritance relationship.
   - `controller.php` has numerous examples of multiple classes being used with object oriented programming (OOP).
   - Multiple classes are used to support users, validation, leads, etc.
   - The `Admin` class inherits from the `User` class.
7. Contains full Docblocks for all PHP files and follows PEAR standards.
   - All PHP files have full Docblocks and follow PEAR standards.
8. Has full validation on the server side through PHP.
   - Validation code is available in `model/validate.php` and used in `controller.php` to validate form submissions.
9. All code is clean, clear, and well-commented. DRY (Don't Repeat Yourself) is practiced.
   - Whenever possible, removed duplicated code and separated code out into functions that only perform one action.
10. Your submission is professional and shows adequate effort for a final project in a full-stack web development course.
   - To our best ability, we have tried to be professional and put in as much effort as possible (especially as we've
    been working with a client and want this project to succeed).

## UML Class Diagram (Unified Modeling Language)
![UML_Diagram.png](images%2FUML_Diagram.png)

## Temporary Login Credentials for Testing
- email: `admin@ups.com`
- password: `admin`