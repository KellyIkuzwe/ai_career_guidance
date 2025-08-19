<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>AI Career Guidance Platform | Sign Up</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="/images/photo1754818039.jpg">
  <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i'>
  <link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.8.1/css/all.css'>
  <link rel="stylesheet" href="loginstyle.css">

  <style>
    /* Additional inline styles for this specific page */
    .login-card {
      max-width: 500px;
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
      margin-bottom: 20px;
    }
    
    .role-toggle {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }
    
    .role-toggle label {
      padding: 10px 20px;
      border: 1px solid #ddd;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .role-toggle label:first-child {
      border-radius: 5px 0 0 5px;
    }
    
    .role-toggle label:last-child {
      border-radius: 0 5px 5px 0;
    }
    
    .role-toggle input[type="radio"] {
      display: none;
    }
    
    .role-toggle input[type="radio"]:checked + label {
      background-color: #4a6fdc;
      border-color: #4a6fdc;
      color: white;
    }
    
    .company-fields,
    .jobseeker-fields {
      display: none;
    }
    
    .active {
      display: block;
    }
  </style>
</head>
<body>

<div class="login-card">
  <div class="login-card-content">
    <div class="header">
      <div class="logo">
        <img src="/images/photo1754818039.jpg" style="width: 70%;">
      </div>
      <h2>AI CAREER <span class="highlight">GUIDANCE</span></h2>
      <h3>Create Your Account</h3>
      <?php
        if(isset($_GET['error'])){
          if($_GET['error'] == 'exists'){
            echo "<div class='error-message'>
              Email already exists! Please use a different email or login.
            </div>";
          } elseif($_GET['error'] == 'password'){
            echo "<div class='error-message'>
              Passwords do not match! Please try again.
            </div>";
          }
        }
      ?>
    </div>
    <div class="form">
      <form method="POST" action="server.php">
        <div class="role-toggle">
          <input type="radio" id="jobseeker" name="role" value="jobseeker" checked>
          <label for="jobseeker">Job Seeker</label>
          <input type="radio" id="company" name="role" value="company">
          <label for="company">Company</label>
        </div>

        <div class="form-field">
          <div class="icon">
            <i class="far fa-user"></i>
          </div>
          <input type="text" id="name-field" placeholder="Full Name" required name="name">
        </div>
        
        <div class="form-field">
          <div class="icon">
            <i class="far fa-envelope"></i>
          </div>
          <input type="email" placeholder="Email Address" required name="email">
        </div>
        
        <div class="form-field">
          <div class="icon">
            <i class="fas fa-lock"></i>
          </div>
          <input type="password" placeholder="Password" required name="password">
        </div>
        
        <div class="form-field">
          <div class="icon">
            <i class="fas fa-lock"></i>
          </div>
          <input type="password" placeholder="Confirm Password" required name="confirm_password">
        </div>

        <div class="jobseeker-fields active">
          <!-- Job Seeker Specific Fields -->
        </div>

        <div class="company-fields">
          <!-- Company Specific Fields -->
        </div>

        <button type="submit" name="register">
          Create Account
        </button>
      </form>
      <p style="text-align: center; margin-top: 15px;">
        Already have an account? <a href="login">Login</a>
      </p>
    </div>
  </div>
  <div class="login-card-footer">
    <a href="index">Back to Home</a>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const jobseekerRadio = document.getElementById('jobseeker');
    const companyRadio = document.getElementById('company');
    const jobseekerFields = document.querySelector('.jobseeker-fields');
    const companyFields = document.querySelector('.company-fields');
    const nameField = document.getElementById('name-field');
    
    jobseekerRadio.addEventListener('change', function() {
      if (this.checked) {
        jobseekerFields.classList.add('active');
        companyFields.classList.remove('active');
        nameField.placeholder = 'Full Name';
      }
    });
    
    companyRadio.addEventListener('change', function() {
      if (this.checked) {
        companyFields.classList.add('active');
        jobseekerFields.classList.remove('active');
        nameField.placeholder = 'Company Name';
      }
    });
  });
</script>

</body>
</html>