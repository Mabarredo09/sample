<?php
// login.php
require_once 'config.php';

$email = $password = $emailErr = $passwordErr = "";
$loginError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $emailErr = "Please enter your email.";
    } else {
        $email = cleanInput(trim($_POST["email"]));
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $passwordErr = "Please enter your password.";
    } else {
        $password = cleanInput(trim($_POST["password"]));
    }

    // Check input errors before checking in database
    if (empty($emailErr) && empty($passwordErr)) {
        // Prepare a select statement
        $sql = "SELECT UserID, FirstName, LastName, Email, Password, UserStatus FROM Users WHERE Email = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the statement
            $stmt->bind_param("s", $param_email);

            // Set parameters
            $param_email = $email;

            // Execute the statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if email exists
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($id, $firstName, $lastName, $email, $hashed_password, $status);
                    if ($stmt->fetch()) {
                        if ($status === 'active') {
                            if (password_verify($password, $hashed_password)) {
                                // Password is correct, start a new session
                                session_start();

                                // Store data in session variables
                                $_SESSION["user_id"] = $id;
                                $_SESSION["user_name"] = $firstName . ' ' . $lastName;
                                $_SESSION["user_email"] = $email;

                                // Check if user is admin (you might have a separate role field)
                                $adminCheck = $conn->query("SELECT 1 FROM AdminUsers WHERE UserID = $id");
                                if ($adminCheck->num_rows > 0) {
                                    $_SESSION["user_role"] = "admin";
                                } else {
                                    $_SESSION["user_role"] = "user";
                                }

                                // Redirect to welcome page
                                header("location: dashboard.php");
                            } else {
                                // Password is not valid
                                $loginError = "Invalid email or password.";
                            }
                        } else {
                            $loginError = "Your account is inactive. Please contact support.";
                        }
                    }
                } else {
                    // No account found with that email
                    $loginError = "Invalid email or password.";
                }
            } else {
                $loginError = "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lending System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="container">
        <div class="login-form">
            <h2>Lending System Login</h2>
            <?php if (!empty($loginError)) {
                echo "<p class='error'>$loginError</p>";
            } ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $email; ?>"
                        class="<?php echo (!empty($emailErr)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $emailErr; ?></span>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password"
                        class="<?php echo (!empty($passwordErr)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $passwordErr; ?></span>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
                <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
            </form>
        </div>
    </div>
    <script src="js/main.js"></script>
</body>

</html>

<?php
// register.php
require_once 'config.php';

$firstName = $lastName = $email = $password = $confirmPassword = "";
$firstNameErr = $lastNameErr = $emailErr = $passwordErr = $confirmPasswordErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate first name
    if (empty(trim($_POST["firstName"]))) {
        $firstNameErr = "Please enter your first name.";
    } else {
        $firstName = cleanInput(trim($_POST["firstName"]));
    }

    // Validate last name
    if (empty(trim($_POST["lastName"]))) {
        $lastNameErr = "Please enter your last name.";
    } else {
        $lastName = cleanInput(trim($_POST["lastName"]));
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $emailErr = "Please enter your email.";
    } else {
        // Check if email already exists
        $sql = "SELECT UserID FROM Users WHERE Email = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_email);
            $param_email = trim($_POST["email"]);

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    $emailErr = "This email is already taken.";
                } else {
                    $email = cleanInput(trim($_POST["email"]));
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $passwordErr = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $passwordErr = "Password must have at least 6 characters.";
    } else {
        $password = cleanInput(trim($_POST["password"]));
    }

    // Validate confirm password
    if (empty(trim($_POST["confirmPassword"]))) {
        $confirmPasswordErr = "Please confirm password.";
    } else {
        $confirmPassword = cleanInput(trim($_POST["confirmPassword"]));
        if ($password != $confirmPassword) {
            $confirmPasswordErr = "Passwords did not match.";
        }
    }

    // Check input errors before inserting in database
    if (empty($firstNameErr) && empty($lastNameErr) && empty($emailErr) && empty($passwordErr) && empty($confirmPasswordErr)) {
        // Prepare an insert statement
        $sql = "INSERT INTO Users (FirstName, LastName, Email, Password, UserStatus) VALUES (?, ?, ?, ?, 'active')";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssss", $param_firstName, $param_lastName, $param_email, $param_password);

            // Set parameters
            $param_firstName = $firstName;
            $param_lastName = $lastName;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to login page
                header("location: login.php");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Lending System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="container">
        <div class="register-form">
            <h2>Create an Account</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="firstName" value="<?php echo $firstName; ?>"
                        class="<?php echo (!empty($firstNameErr)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $firstNameErr; ?></span>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="lastName" value="<?php echo $lastName; ?>"
                        class="<?php echo (!empty($lastNameErr)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $lastNameErr; ?></span>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $email; ?>"
                        class="<?php echo (!empty($emailErr)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $emailErr; ?></span>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password"
                        class="<?php echo (!empty($passwordErr)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $passwordErr; ?></span>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirmPassword"
                        class="<?php echo (!empty($confirmPasswordErr)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $confirmPasswordErr; ?></span>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
                <p>Already have an account? <a href="login.php">Login here</a>.</p>
            </form>
        </div>
    </div>
    <script src="js/main.js"></script>
</body>

</html>

<?php
// logout.php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("location: login.php");
exit;
?>