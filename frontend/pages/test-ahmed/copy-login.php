<?php
session_start(); 

$max_attempts = 3; 
$lockout_time = 60; 

$user = [
  "manager@makeItAll" => ["password" => "Manager123", "role" => "Manager", "managerCode" => "Code123"],
  "employee@makeItAll" => ["password" => "Employee123", "role" => "Employee"]
];

$error = "";
$managerError = "";

$lockout_remaining = 0;
if (isset($_SESSION['lockout_time'])) {
  $lockout_remaining = $_SESSION['lockout_time'] - time();
  if ($lockout_remaining <= 0) {
    // Lockout expired, reset
    unset($_SESSION['lockout_time']);
    $_SESSION['login_attempts'] = 0;
    $lockout_remaining = 0;
  }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["Email"]) && isset($_POST["Password"]) && $lockout_remaining <= 0) {
  $email = $_POST["Email"];
  $password = $_POST["Password"];

  if (isset($user[$email]) && $user[$email]["password"] === $password) {
    unset($_SESSION['login_attempts']);
    unset($_SESSION['lockout_time']);

    $_SESSION["email"] = $email;
    $_SESSION["role"] = $user[$email]["role"];

    if ($_SESSION["role"] === "Manager") {
      $_SESSION["awaitingManagerCode"] = true; 
    } else {
      header("Location: Hs-Employee.html");//this is where you enter your employee dashboard page
      exit();
    }
  } else {
    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;

    if ($_SESSION['login_attempts'] >= $max_attempts) {
      $_SESSION['lockout_time'] = time() + $lockout_time;
      $lockout_remaining = $lockout_time;
      $error = "Too many failed attempts. Account locked for $lockout_time seconds.";
    } else {
      $remaining_attempts = $max_attempts - $_SESSION['login_attempts'];
      $error = "Invalid email or password. You have $remaining_attempts attempt(s) remaining.";
    }
  }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["managerCode"])) {
  $managerCode = $_POST["managerCode"];
  if (isset($_SESSION["email"])) {
    $email = $_SESSION["email"];
    if ($managerCode === $user[$email]["managerCode"]) {
      unset($_SESSION["awaitingManagerCode"]);
      header("Location: Hs-Manager.html");//this is where you enter your managar dashboard page
      exit();
    } else {
      $managerError = "Invalid manager code.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Make-It-All Login</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="loginContainer">
    <img src="makeItAll.png" alt="Make-It-All Logo" class="logo">
    <h2>Login to Make-It-All</h2>

    <?php if (isset($_SESSION["awaitingManagerCode"]) && $_SESSION["awaitingManagerCode"]): ?>
      <!-- Manager Code Verification -->
      <form method="POST">
        <input type="text" name="managerCode" placeholder="Enter your manager code" required>
        <button type="submit">Submit</button>
        <?php if ($managerError): ?>
          <p class="error"><?= $managerError ?></p>
        <?php endif; ?>
      </form>

    <?php else: ?>
      <!-- Regular Login Form -->
      <form method="POST">
        <input type="email" name="Email" placeholder="Your email" required <?= $lockout_remaining > 0 ? 'disabled' : '' ?>>
        <input type="password" name="Password" placeholder="Enter password" required <?= $lockout_remaining > 0 ? 'disabled' : '' ?>>

        <!-- Show Password Checkbox -->
        <div class="show-password-container">
          <input type="checkbox" id="showPassword" <?= $lockout_remaining > 0 ? 'disabled' : '' ?>>
          <label for="showPassword">Show Password</label>
        </div>

        <!-- Error / Countdown display -->
        <p class="error">
          <?php
          if ($lockout_remaining > 0) {
            echo "Too many failed attempts. Account locked for $lockout_remaining second(s).";
          } elseif ($error) {
            echo $error;
          }
          ?>
        </p>

        <button type="submit" <?= $lockout_remaining > 0 ? 'disabled' : '' ?>>Login</button>

        <!-- Password Reset Button (frontend only) -->
        <button type="button" onclick="alert('Password reset instructions would be sent to your email.');"
          <?= $lockout_remaining > 0 ? 'disabled' : '' ?> style="margin-top: 10px; background-color:#333; color:#FFD700;">
          Forgot Password?
        </button>

        <script>
          // Show/hide password
          const passwordField = document.querySelector('input[name="Password"]');
          const showPasswordCheckbox = document.getElementById('showPassword');
          showPasswordCheckbox.addEventListener('change', function () {
            passwordField.type = this.checked ? 'text' : 'password';
          });

          // Countdown for lockout
          let lockoutSeconds = <?= $lockout_remaining ?>;
          if (lockoutSeconds > 0) {
            const errorElement = document.querySelector('.error');
            const loginButton = document.querySelector('button[type="submit"]');
            const inputs = document.querySelectorAll('input');
            const resetButton = document.querySelector('button[type="button"]');

            const countdown = setInterval(() => {
              lockoutSeconds--;
              errorElement.textContent = `Too many failed attempts. Account locked for ${lockoutSeconds} second(s).`;

              if (lockoutSeconds <= 0) {
                clearInterval(countdown);
                errorElement.textContent = '';
                loginButton.disabled = false;
                inputs.forEach(input => input.disabled = false);
                resetButton.disabled = false;
              }
            }, 1000);
          }
        </script>
      </form>
    <?php endif; ?>
  </div>
</body>

</html>