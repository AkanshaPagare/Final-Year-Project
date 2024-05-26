<?php
session_start();

include("connection.php");
include("functions.php");

$message = ''; // Initialize message variable

if($_SERVER['REQUEST_METHOD'] == "POST") {
    // Something was posted
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email already exists
    $query = "SELECT * FROM login WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0) {
        // Email already exists
        $message = "Email already exists!";
    } else {
        // Email does not exist, proceed with registration
        if(!empty($email) && !empty($password) && !is_numeric($email)) {
            // Save to database
$user_id = generateRandomId($conn, 10);
           // $user_id = generateRandomId(10);

            $query = "INSERT INTO login (user_id, user_name, email, password) VALUES ('$user_id', '$user_name', '$email', '$password')";
            mysqli_query($conn, $query);

            header("Location: login.php");
            die;
        } else {
            $message = "Please enter valid information!";
        }
    }
}
// Function to generate a random ID
function generateRandomId($conn, $length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
 
  do {
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    $query = "SELECT user_id FROM login WHERE user_id = '$randomString'";
    $result = mysqli_query($conn, $query);
  } while (mysqli_num_rows($result) > 0);

  return $randomString;
}

/*
function generateRandomId($length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $randomString = '';
 
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
  }
  return $randomString;

}
*/
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      background-color: #f5f5f5;
    }

    .signup-box {
      width: 430px;
      padding: 2rem;
      border: 1px solid #ccc;
      border-radius: 0.25rem;
      background-color: #fff;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
      text-align: left;
    }

    .message-box {
      margin-bottom: 1rem;
      padding: 1rem;
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
      border-radius: 0.25rem;
      position: relative;
    }

    .message-box-close {
      position: absolute;
      top: 0.5rem;
      right: 0.5rem;
      cursor: pointer;
    }

    .logo-box {
      margin-bottom: 2rem;
      text-align:center;
    }

    .logo-box img {
      max-width: 50%;
      height: auto;
    }

    .form-group label {
      font-weight: bold;
    }

    .form-check {
      margin-bottom: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .form-check-input {
      margin-top: 0.25rem;
    }

    .form button {
      margin-top: 1rem;
    }

    .text-center {
      margin-top: 1rem;
    }

    .btn-equal-size {
      padding: 0.5rem 1rem;
      font-size: 1rem;
      line-height: 1.5;
      border-radius: 0.25rem;
    }
  </style>
</head>

<body>
  <div class="signup-box">
    <?php if(!empty($message)) { ?>
      <div class="message-box">
        <span class="message-box-close" onclick="this.parentElement.style.display='none';">&times;</span>
        <?php echo $message; ?>
      </div>
    <?php } ?>
    <div class="logo-box">
      <img src="logo.png" alt="Logo">
    </div>
    <h2 class="text-center">Sign Up</h2>
    <form class="form" method="post">
    <div class="form-group">
        <label for="user_name">User name</label>
        <input type="text" name="user_name" class="form-control" id="user_name" placeholder="Enter User name">
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" class="form-control" id="password" placeholder="Enter your password">
      </div>
      <div class="form-check">
        <input type="checkbox" class="form-check-input" id="rememberMe">
        <label class="form-check-label" for="rememberMe">Remember me</label>
      </div>
      <hr>
      <button type="submit" class="btn btn-primary btn-equal-size">Sign Up</button>
    </form>

    <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
  </div>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
 
</body>

</html>
