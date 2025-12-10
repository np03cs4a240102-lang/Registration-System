<?php
$errors = [];
$success = "";

if (isset($_POST['submit'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // VALIDATION
    if ($name == "") $errors['name'] = "Name is required";
    if ($email == "") $errors['email'] = "Email is required";
    if ($password == "") $errors['password'] = "Password is required";
    if ($confirm == "") $errors['confirm'] = "Confirm password is required";

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (!empty($password) && strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    }

    if (!empty($password) && !empty($confirm) && $password !== $confirm) {
        $errors['confirm'] = "Passwords do not match";
    }

    if (empty($errors)) {
        $jsonData = file_get_contents("users.json");
        $users = json_decode($jsonData, true);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $newUser = [
            "name" => $name,
            "email" => $email,
            "password" => $hashedPassword
        ];

        $users[] = $newUser;

        file_put_contents("users.json", json_encode($users, JSON_PRETTY_PRINT));

        $success = "Registration successful!";
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

    <!-- SUCCESS MESSAGE -->
    <?php if ($success): ?>
        <div class="success-box"><?= $success ?></div>
    <?php endif; ?>

    <!-- ERROR MESSAGES -->
    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $msg): ?>
                <?= $msg ?><br>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="name" placeholder="Enter Name" value="<?= isset($name) ? $name : '' ?>">
        <input type="email" name="email" placeholder="Enter Email" value="<?= isset($email) ? $email : '' ?>">
        <input type="password" name="password" placeholder="Enter Password">
        <input type="password" name="confirm_password" placeholder="Confirm Password">

        <button type="submit" name="submit">Register</button>
    </form>

</div>

</body>
</html>
