<!DOCTYPE html>
<html lang="en">
    
    <head>
        <title>Login Member</title>    
        <?php
            include "inc/head.inc.php";
        ?>
    </head>

    <body>
        <?php
            include "inc/nav.inc.php";
        ?>
        <main class="container">
            <?php
                $lname = $email = $pwd = $pwd_confirm = $errorMsg = "";
                $fname = sanitize_input($_POST["fname"]);
                $success = true;



                if (empty($_POST["email"])) {
                    $errorMsg .= "Email is required.<br>";
                    $success = false;
                    $authenticate = false;
                }
                else {
                    $email = sanitize_input($_POST["email"]);
                    // Additional check to make sure e-mail address is well-formed.
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errorMsg .= "Invalid email format.<br>";
                        $success = false;
                        $authenticate = false;
                    }
                }

                if (empty($_POST["pwd"])) {
                    $errorMsg .= "Password is required.<br>";
                    $success = false;
                }
                else {
                    $pwd = $_POST["pwd"];
                }

               
                authenticateUser();

                if ($success) {
                    echo "<div class='registration-success'><h3>Login successful!</h3>";
                    
                }
                else {
                    echo "<div class='registration-failed'><h3>Oops!</h3>";
                }
                


                /*
                * Helper function that checks input for malicious or unwanted content.
                */
                function sanitize_input($data) {
                    $data = trim($data);
                    $data = stripslashes($data);
                    $data = htmlspecialchars($data);
                    return $data;
                }

                /*
                * Helper function to write the member data to the database.
                */
                

                function authenticateUser()
                {
                    global $fname, $lname, $email, $pwd_hashed, $errorMsg, $success;

                    // Create database connection.
                    $config = parse_ini_file('/var/www/private/db-config.ini');
                    if (!$config)
                    {
                        $errorMsg = "Failed to read database config file.";
                        $success = false;
                    }
                    else
                    {
                        $conn = new mysqli(
                            $config['servername'],
                            $config['username'],
                            $config['password'],
                            $config['dbname']
                        );

                        // Check connection
                        if ($conn->connect_error)
                        {
                            $errorMsg = "Connection failed: " . $conn->connect_error;
                            $success = false;
                        }
                        else
                        {
                            // Prepare the statement:
                            $stmt = $conn->prepare("SELECT * FROM world_of_pets_members WHERE email=?");
                            
                            // Bind & execute the query statement:
                            $stmt->bind_param("s", $email);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($result->num_rows > 0)
                            {
                                // Note that email field is unique, so should only have
                                // one row in the result set.
                                $row = $result->fetch_assoc();
                                $fname = $row["fname"];
                                $lname = $row["lname"];
                                $pwd_hashed = $row["password"];

                                // Check if the password matches:
                                if (!password_verify($_POST["pwd"], $pwd_hashed))
                                {

                                    // Don't be too specific with the error message - hackers don't
                                    // need to know which one they got right or wrong. :)
                                    $errorMsg = "Email not found or password doesn't match...";
                                    $success = false;
                                }
                            }
                                else
                                {
                                    $errorMsg = "Email not found or password doesn't match...";
                                    $success = false;
                                }
                                $stmt->close();
                        }

                        $conn->close();
                    }
                }

            ?>
        </main>
        <?php
            include "inc/footer.inc.php";
        ?>
    </body>
</html>
