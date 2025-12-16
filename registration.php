<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$name = $email = "";
$nameErr = $emailErr = $passwordErr = $confirmErr = "";
$success = "";

$users = file_exists("users.json")
    ? json_decode(file_get_contents("users.json"), true)
    : [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // NAME
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = trim($_POST["name"]);
    }

    // EMAIL
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    } else {
        $email = trim($_POST["email"]);
    }

    // PASSWORD
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = $_POST["password"];

        if (strlen($password) < 6) {
            $passwordErr = "Password must be at least 6 characters";
        } elseif (!preg_match("/[0-9]/", $password)) {
            $passwordErr = "Password must include at least one number";
        } elseif (!preg_match("/[!@#$%^&*]/", $password)) {
            $passwordErr = "Password must include at least one special character";
        }
    }

    // CONFIRM PASSWORD
    if (empty($_POST["confirm_password"])) {
        $confirmErr = "Confirm password is required";
    } elseif ($_POST["password"] !== $_POST["confirm_password"]) {
        $confirmErr = "Passwords do not match";
    }

    // CHECK DUPLICATE EMAIL
    if (empty($emailErr)) {
        foreach ($users as $user) {
            if ($user["email"] === $email) {
                $emailErr = "Email already registered";
                break;
            }
        }
    }

    // SAVE DATA
    if (
        empty($nameErr) &&
        empty($emailErr) &&
        empty($passwordErr) &&
        empty($confirmErr)
    ) {
        $users[] = [
            "name" => $name,
            "email" => $email,
            "password" => password_hash($_POST["password"], PASSWORD_DEFAULT)
        ];

        file_put_contents("users.json", json_encode($users, JSON_PRETTY_PRINT));

        $success = "Registration successful!";
        $name = $email = "";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="registration-box">
    <h2>Register</h2>

    <?php if ($success): ?>
        <div class="success-box"><?= $success ?></div>
    <?php endif; ?>

    <form method="post">

        <input type="text" name="name" placeholder="Full Name"
               value="<?= htmlspecialchars($name) ?>">
        <div class="error-box"><?= $nameErr ?></div>

        <input type="text" name="email" placeholder="Email"
               value="<?= htmlspecialchars($email) ?>">
        <div class="error-box"><?= $emailErr ?></div>

        <input type="password" name="password" placeholder="Password">
        <div class="error-box"><?= $passwordErr ?></div>

        <input type="password" name="confirm_password" placeholder="Confirm Password">
        <div class="error-box"><?= $confirmErr ?></div>

        <button type="submit">Register</button>
    </form>
</div>

</body>
</html>
