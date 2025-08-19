<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AI Career Guidance Platform | Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="/images/photo1754818038.jpg">
  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i'>
  <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.8.1/css/all.css'>
  <link rel="stylesheet" href="loginstyle.css">

  <style>
    /* Additional inline styles for this specific page */
    .login-card {
      max-width: 450px;
    }
    
    .logo {
      background-color: #fff;
      border-radius: 50%;
      padding: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .header h2 {
      margin-top: 15px;
    }
    
    .header h3 {
      margin-bottom: 15px;
    }
    
    .form-field {
      margin-bottom: 25px;
    }
  </style>
</head>
<body>

<div class="login-card">
  <div class="login-card-content">
    <div class="header">
      <div class="logo">
        <img src="/images/photo1754818038.jpg" style="width: 70%;">
      </div>
      <h2>AI CAREER <span class="highlight">GUIDANCE</span></h2>
      <h3>User Login</h3>
      <?php
        if(isset($_GET['error']) AND $_GET['error'] == 'invalid'){
          echo "<div class='error-message'>
            Invalid email or password!
          </div>";
        } elseif(isset($_GET['error']) AND $_GET['error'] == 'login'){
          echo "<div class='error-message'>
            Please login to your account!
          </div>";
        } elseif(isset($_GET['success']) AND $_GET['success'] == 'registered'){
          echo "<div class='success-message'>
            Registration successful! You can now login.
          </div>";
        }
      ?>
    </div>
    <div class="form">
      <form method="POST" action="server.php">
        <div class="form-field username">
          <div class="icon">
            <i class="far fa-envelope"></i>
          </div>
          <input type="email" placeholder="Email Address" required name="email">
        </div>
        <div class="form-field password">
          <div class="icon">
            <i class="fas fa-lock"></i>
          </div>
          <input type="password" placeholder="Password" required name="password">
        </div>
        <div class="form-field" style="margin-bottom: 10px; text-align: center;">
          <label>
            <input type="radio" name="role" value="jobseeker" checked> Job Seeker
          </label>
          &nbsp;&nbsp;
          <label>
            <input type="radio" name="role" value="company"> Company
          </label>
        </div>

        <button type="submit" name="userlogin">
          Login
        </button>
      </form>
      <p style="text-align: center; margin-top: 15px;">
        Don't have an account yet? <a href="signup">Signup</a>
      </p>
    </div>
  </div>
  <div class="login-card-footer">
    <a href="index">Back to Home</a>
  </div>
</div>

</body>
</html>