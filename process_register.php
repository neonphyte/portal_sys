<!DOCTYPE html>
<html lang="en">
    
    <head>
        <title>Register Member</title>    
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

                if (empty($_POST["lname"])) {
                    $errorMsg .= "Last Name is required.<br>";
                    $success = false;
                }
                else {
                    $lname = sanitize_input($_POST["lname"]);
                }

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
                    }
                }

                if (empty($_POST["pwd"])) {
                    $errorMsg .= "Password is required.<br>";
                    $success = false;
                }
                else {
                    $pwd = $_POST["pwd"];
                }

                if (empty($_POST["pwd_confirm"])) {
                    $errorMsg .= "Please confirm your password.<br>";
                    $success = false;
                }
                else {
                    $pwd_confirm = $_POST["pwd_confirm"];
                    if ($pwd_confirm != $pwd) {
                    $errorMsg .= "Passwords do not match.";
                    $success = false;
                    }else{
                        $pwd_hashed = password_hash($_POST["pwd"], PASSWORD_DEFAULT);
                    }
                }

                if ($success) {
                	saveMemberToDB();

                }
                else {
                    echo "<div class='registration-failed'><h3>Oops!</h3>";
                    echo "<h4>The following input errors were detected: </h4>";
                    echo "<p>" . $errorMsg . "</p>";
                    echo "<div class=\"mb-3\">
                    <button type=\"submit\" class=\"btn btn-danger\" onclick=\"window.location.href='register.php'\">
                    Return to Sign Up</button></div></div>";
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
                function saveMemberToDB(){
                    global $fname, $lname, $email, $pwd_hashed, $errorMsg, $success;

                    
                    echo "<div class='registration-success'><h3>Registration successful!</h3>";
                    echo "<h4>Thank you for signing up, " . $fname . " " . $lname . "</h4>";
                    echo "<div class=\"mb-3\">
                    <button type=\"submit\" class=\"btn btn-success\" onclick=\"window.location.href='login.php'\">Log-in</button>
                    </div></div>";

                    // Create database connection.
                    $config = parse_ini_file('/var/www/private/db-config.ini');
                    if (!$config) {
                        $errorMsg = "Failed to read database config file.";
                        $success = false;
                    } 
                    else{
                        $conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['dbname']);
                        // Check connection
                        if ($conn->connect_error) {
                            $errorMsg = "Connection failed: " . $conn->connect_error;
                            $success = false;
                        }
                        else {
                            // Prepare the statement:
                            $stmt = $conn->prepare("INSERT INTO world_of_pets_members
                            (fname, lname, email, password) VALUES (?, ?, ?, ?)");
                            // Bind & execute the query statement:
                            $stmt->bind_param("ssss", $fname, $lname, $email, $pwd_hashed);
                            // echo "<p>" . $success . "</p>";

                            if (!$stmt->execute()) {
                                $errorMsg = "Execute failed: (" . $stmt->errno . ") " .
                                $stmt->error;
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
