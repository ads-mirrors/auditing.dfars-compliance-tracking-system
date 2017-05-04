<?php 
include ("connect/connect.php");

// Authentication
function sec_session_start() {
    $session_name = 'sec_session_id';   // Set a custom session name
    $secure = false; // Set to true is using https
    // This stops JavaScript being able to access the session id.
    $httponly = true;
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../login.php?error=7");
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"],
        $cookieParams["domain"],
        $secure,
        $httponly);
    // Sets the session name to the one set above.
    session_name($session_name);
    session_start();            // Start the PHP session
    session_regenerate_id(true);    // regenerated the session, delete the old one.
}

// Determines if login attempt is successful or not
function login($email, $password, $conn) {
    // Using prepared statements means that SQL injection is not possible.
    if ($stmt = $conn->prepare("SELECT user_id, user_email, user_password, user_temp_flag, user_locked_flag
        FROM user
       WHERE user_email = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
        // get variables from result.
        $stmt->bind_result($user_id, $username, $db_password, $temp, $locked);
        $stmt->fetch();
        if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
			if ($locked == 'Y') {
				header('Location: ../login.php?error=6');
				exit;
			}
            else {
                // Check if the password in the database matches
                // the password the user submitted. We are using
                // the password_verify function to avoid timing attacks.
                if (password_verify($password, $db_password)) {
                    // Password is correct!
                    // XSS protection as we might print this value. Will require if password is temporary
                    $username = preg_replace("/[^a-zA-Z0-9_\-@.]+/",
											"",
											$username);
                    $_SESSION['username'] = $username;
					// Check for temporary password that needs a permanent reset
					if ($temp == 'Y') {
						header('Location: ../permanent_reset_page.php');
						exit;
					}

                    // XSS protection as we might print this value. Will require if password is temporary
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
					// Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    $_SESSION['login_string'] = hash('sha512',
                              $db_password . $user_browser);
					$_SESSION['expire'] = time() + 900;
					
                    // Login successful.
					return true;
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    $conn->query("INSERT INTO login_attempts(user_id, time)
                                    VALUES ('$user_id', '$now')");
									
					// Check if this is the 5th unsuccessful attempt in 2 hours
					// Assume a brute force attack
					if (checkbrute($user_id, $conn) == true) {
						// Account will be locked
						// Send an email to user saying their account is locked and return to login page
						lock_account($conn, $username);
					}
                    header('Location: ../login.php?error=1');
					//echo $username.' '.$email;
					exit;
                }
            }
        } else {
            // No user exists.
            header('Location: ../login.php?error=1');
			exit;
        }
    }
}

// For logging failed login attempts
function checkbrute($user_id, $conn) {
    // Get timestamp of current time
    $now = time();
    // All login attempts are counted from the past 2 hours.
    $valid_attempts = $now - (2 * 60 * 60);
    if ($stmt = $conn->prepare("SELECT time
                             FROM login_attempts
                             WHERE user_id = ?
                            AND time > '$valid_attempts'")) {
        $stmt->bind_param('i', $user_id);
        // Execute the prepared query.
        $stmt->execute();
        $stmt->store_result();
        // If there have been 5 (or more if an error occurred) failed logins
        if ($stmt->num_rows >= 5) {
            return true;
        } else {
            return false;
        }
    }
}

function lock_account($conn, $username) {
	$stmt = $conn->prepare("UPDATE user SET user_locked_flag = 'Y' WHERE user_email = ?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	
	// Send email
	// Recipients
	$to = $username;

	// Subject
	$subject = 'G2 Ops Account Locked';

	// Message
	$message = '
	<html>
	<head>
	<title>G2 Ops Account Locked</title>
	<style>
		body {
			background-image: url("/rebus/img/background-clear-cropped.jpg");
			background-position: right bottom;
			background-size: contain;
			background-repeat: no-repeat;
			background-color: #eeeeee;
		}
		
		#prefooter {
			min-height: 100%;
			margin-bottom: -53px;	
			background-repeat: no-repeat; 
			background-size: contain;
			background-position: center 30px;
			font-size: 18px;
		}

		#prefooter:after {
			content: "";
			display: block;
			height: 53px
		}

		footer {
			height: 30px;
		}

		footer {
			background-color: silver ;
			bottom: 0;
			padding-top: 0px;
			text-decoration: small-caps;
			text-align: center;
			color: black;
			border: 1px solid black;
			font-size: 20px;
		}
	</style>
	</head>
	<body>
	<div id="prefooter">
	<p>For your security, your G2 Ops account has been locked. Contact support@g2-ops.com for resolution.</p>

	<div id="footer">
	<footer>
		G2 Ops &diams;
		205 Business Park Dr #200 &diams;
		Virginia Beach, VA 23462 &diams;
		(757) 965-8330 &diams;
	</footer>
	</div>
	</body>
	</html>
	';

	// To send HTML mail, the Content-type header must be set
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';

	// Additional headers
	$headers[] = 'To: ' . $to;
	$headers[] = 'From: G2 Ops <G2 Ops Email>';
	// Bcc if you want to be notified when an email is reset 
	// $headers[] = 'Bcc: example@g2-ops.com';

	// Mail it
	mail($to, $subject, $message, implode("\r\n", $headers));

	logout("lock"); 
}	
	
function login_check($conn) {
    // Check if all session variables are set and last activity was < 15 minutes ago
    if (isset($_SESSION['user_id'],
                        $_SESSION['username'],
                        $_SESSION['login_string'])
		&& time() < $_SESSION['expire']) {
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
        if ($stmt = $conn->prepare("SELECT user_password
                                      FROM user
                                      WHERE user_id = ? LIMIT 1")) {
            // Bind "$user_id" to parameter.
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
                if (hash_equals($login_check, $login_string) ){
                    // Logged In!!!!
                    return true;
                } else {
                    // Not logged in
                    return false;
                }
            } else {
                // Not logged in
                return false;
            }
        } else {
            // Not logged in
            return false;
        }
    } else {
        // Not logged in
        return false;
    }
}

// Processes a user logout and kills all sessions
function logout($loc) {
	// Unset all session values
	$_SESSION = array();
	// get session parameters
	$params = session_get_cookie_params();
	// Delete the actual cookie.
	setcookie(session_name(),
			'', time() - 900,
			$params["path"],
			$params["domain"],
			$params["secure"],
			$params["httponly"]);
	// Destroy session
	session_unset();
	session_destroy();
	
	// Redirect to login screen with error indicator
	if ($loc == "web") {
		header('Location: login.php?error=0');
	}
	else if ($loc == "php") {
		header('Location: ../login.php?error=0');
	}
	else if ($loc == "unauthweb") {
		header('Location: login.php?error=3');
	}
	else if ($loc == "unauthphp") {
		header('Location: ../login.php?error=3');
	}
	else if ($loc == "lock") {
		header('Location: ../login.php?error=5');
	}
	exit;
}

// Data Manipulation/Expression
// Return organizations 
function get_orgs($userid, $conn) {
	include ("connect/connect.php");
	
	// Set user session variable
	$sql = 'SET @user = "'.$userid.'"';
	mysqli_query($conn, $sql) or die(mysqli_error($conn));

	// Gather user's organizations
	$sql = 'SELECT DISTINCT org_name, org_id FROM v_user_reference';
	$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	mysqli_close($conn);
	return $result;
}

// Returns system types
function get_systype($conn) {
	$sql = 'SELECT type_id, type_name FROM sys_type';
	$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	mysqli_close($conn);
	return $result;
}
?>