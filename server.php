<?php
session_start();
include('connector.php');

// User Login
if (isset($_POST['userlogin'])) {
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);
    $role = mysqli_real_escape_string($connect, $_POST['role']);
    
    // Authenticate user by email, password, and role
    // Removed dependency on a non-guaranteed 'deleted' column to avoid SQL errors
    $query = mysqli_query($connect, "SELECT * FROM User WHERE email='$email' AND password='$password' AND role='$role'");
    
    if (mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_array($query);
        $_SESSION['cg_user_token'] = $row['id'];
        $_SESSION['cg_user_code'] = $row['code'];
        $_SESSION['cg_user_role'] = $row['role'];
        
        // Redirect users to dashboard index regardless of role for now
        if ($row['role'] == 'jobseeker') {
            header("Location: index");
        } elseif ($row['role'] == 'company') {
            header("Location: index");
        } elseif ($row['role'] == 'admin') {
            header("Location: index");
        }
    } else {
        header("Location: login?error=invalid");
    }
}

// User Registration
if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($connect, $_POST['confirm_password']);
    $role = mysqli_real_escape_string($connect, $_POST['role']);
    
    // Check if email exists
    $check_email = mysqli_query($connect, "SELECT * FROM User WHERE email='$email'");
    if (mysqli_num_rows($check_email) > 0) {
        header("Location: signup?error=exists");
        exit();
    }
    
    // Check if passwords match
    if ($password != $confirm_password) {
        header("Location: signup?error=password");
        exit();
    }
    
    // Generate unique code
    $code = uniqid();
    
    // Insert user
    $insert_user = mysqli_query($connect, "INSERT INTO User (code, name, email, password, role) VALUES ('$code', '$name', '$email', '$password', '$role')");
    
    if ($insert_user) {
        // Get the inserted user ID
        $user_id = mysqli_insert_id($connect);
        
        // Create empty profile for job seeker
        if ($role == 'jobseeker') {
            mysqli_query($connect, "INSERT INTO CareerProfile (user_id, education, skills, interests, experience) VALUES ('$user_id', '', '', '', '')");
        }
        
        header("Location: login?success=registered");
    } else {
        header("Location: signup?error=failed");
    }
}
?>