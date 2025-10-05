<?php
session_start();
if(!isset($_SESSION['cg_user_token'])){
    header("Location: login?error=login");
    exit();
}
include('connector.php');
$queryUser = mysqli_query($connect, "SELECT * FROM User WHERE id='".$_SESSION['cg_user_token']."'");
$rowUser = mysqli_fetch_array($queryUser);

if($rowUser['role'] == 'jobseeker') {
    $queryProfile = mysqli_query($connect, "SELECT * FROM CareerProfile WHERE user_id='".$_SESSION['cg_user_token']."'");
    $rowProfile = mysqli_fetch_array($queryProfile);
}

// Process form submission
if(isset($_POST['update_profile'])) {
    $education = mysqli_real_escape_string($connect, $_POST['education']);
    $skills = mysqli_real_escape_string($connect, $_POST['skills']);
    $interests = mysqli_real_escape_string($connect, $_POST['interests']);
    $experience = mysqli_real_escape_string($connect, $_POST['experience']);
    
    // Update profile information
    $update = mysqli_query($connect, "UPDATE CareerProfile SET 
        education = '$education',
        skills = '$skills',
        interests = '$interests',
        experience = '$experience'
        WHERE user_id = '".$_SESSION['cg_user_token']."'");
    
    // Handle profile picture upload
    if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif');
        $filename = $_FILES['profile_pic']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(in_array(strtolower($ext), $allowed)) {
            $new_filename = time() . '.' . $ext;
            $destination = 'profiles/' . $new_filename;
            
            if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], $destination)) {
                mysqli_query($connect, "UPDATE CareerProfile SET profile_picture = '$destination' WHERE user_id = '".$_SESSION['cg_user_token']."'");
            }
        }
    }
    
    // Redirect to avoid form resubmission
    header("Location: profile?success=updated");
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Profile - AI Career Guidance Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no"/>
    <meta name="description" content="AI Career Guidance Platform Profile">
    <link rel="icon" type="image/png" href="/images/photo1754818428.jpg">

    <!-- Styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    
    <style>
        /* Additional inline styles for this specific page */
        .profile-header {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .profile-info {
            padding-left: 20px;
        }
        
        .profile-info h4 {
            margin-bottom: 5px;
        }
        
        .profile-info p {
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .progress-container {
            margin-top: 15px;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section-title {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        <?php include('components/header.php'); ?>
        
        <div class="app-main">
            <div class="app-sidebar sidebar-shadow">
                <?php include('components/sidebar.php'); ?>
            </div>
            
            <div class="app-main__outer">
                <div class="app-main__inner">
                    <div class="app-page-title">
                        <div class="page-title-wrapper">
                            <div class="page-title-heading">
                                <div class="page-title-icon">
                                    <i class="pe-7s-user icon-gradient bg-premium-dark"></i>
                                </div>
                                <div>
                                    My Profile
                                    <div class="page-title-subheading">
                                        Manage your personal and career information
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php
                    if(isset($_GET['success']) && $_GET['success'] == 'updated'){
                        echo '<div class="alert alert-success">Your profile has been successfully updated!</div>';
                    }
                    ?>
                    
                    <?php if($rowUser['role'] == 'jobseeker'): ?>
                    <!-- Job Seeker Profile -->
                    <div class="profile-header">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo !empty($rowProfile['profile_picture']) ? $rowProfile['profile_picture'] : 'profiles/default.png'; ?>" class="profile-pic" alt="Profile Picture">
                            <div class="profile-info">
                                <h4><?php echo $rowUser['name']; ?></h4>
                                <p><i class="fa fa-envelope"></i> <?php echo $rowUser['email']; ?></p>
                                
                                <?php
                                // Calculate profile completion
                                $completion = 0;
                                
                                if(isset($rowProfile)) {
                                    if(!empty($rowProfile['education'])) $completion += 25;
                                    if(!empty($rowProfile['skills'])) $completion += 25;
                                    if(!empty($rowProfile['interests'])) $completion += 25;
                                    if(!empty($rowProfile['experience'])) $completion += 25;
                                }
                                
                                $barClass = 'bg-danger';
                                if($completion > 30) $barClass = 'bg-warning';
                                if($completion > 70) $barClass = 'bg-success';
                                ?>
                                
                                <div class="progress-container">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Profile Completion</span>
                                        <span><?php echo $completion; ?>%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar <?php echo $barClass; ?>" role="progressbar" style="width: <?php echo $completion; ?>%" aria-valuenow="<?php echo $completion; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="main-card mb-3 card">
                                <div class="card-body">
                                    <h5 class="card-title">Career Profile Information</h5>
                                    
                                    <form method="POST" action="" enctype="multipart/form-data">
                                        <div class="form-section">
                                            <h6 class="form-section-title">Profile Picture</h6>
                                            <div class="form-group">
                                                <label>Update Profile Picture</label>
                                                <input type="file" class="form-control-file" name="profile_pic">
                                                <small class="form-text text-muted">Maximum file size: 2MB. Allowed formats: JPG, JPEG, PNG, GIF</small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-section">
                                            <h6 class="form-section-title">Education</h6>
                                            <div class="form-group">
                                                <label>Education Background</label>
                                                <textarea class="form-control" name="education" rows="4" placeholder="Describe your educational background, degrees, certifications, etc."><?php echo isset($rowProfile['education']) ? $rowProfile['education'] : ''; ?></textarea>
                                                <small class="form-text text-muted">Include your degrees, institutions attended, graduation years, and any relevant certifications.</small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-section">
                                            <h6 class="form-section-title">Skills</h6>
                                            <div class="form-group">
                                                <label>Professional Skills</label>
                                                <textarea class="form-control" name="skills" rows="4" placeholder="List your professional skills"><?php echo isset($rowProfile['skills']) ? $rowProfile['skills'] : ''; ?></textarea>
                                                <small class="form-text text-muted">Include both technical and soft skills. Separate skills with commas or line breaks.</small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-section">
                                            <h6 class="form-section-title">Interests</h6>
                                            <div class="form-group">
                                                <label>Career Interests</label>
                                                <textarea class="form-control" name="interests" rows="4" placeholder="Describe your career interests and goals"><?php echo isset($rowProfile['interests']) ? $rowProfile['interests'] : ''; ?></textarea>
                                                <small class="form-text text-muted">Share your professional interests, industries you're interested in, and career goals.</small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-section">
                                            <h6 class="form-section-title">Experience</h6>
                                            <div class="form-group">
                                                <label>Work Experience</label>
                                                <textarea class="form-control" name="experience" rows="4" placeholder="Describe your work experience"><?php echo isset($rowProfile['experience']) ? $rowProfile['experience'] : ''; ?></textarea>
                                                <small class="form-text text-muted">Include your work history, internships, volunteer work, and any relevant projects.</small>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php elseif($rowUser['role'] == 'company'): ?>
                    <!-- Company Profile Redirect -->
                    <script>window.location = 'company_profile';</script>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Toggle sidebar on mobile
            $('.mobile-toggle-nav').click(function() {
                $('.app-sidebar').toggleClass('active');
                $('.app-sidebar__overlay').toggleClass('d-block');
            });
            
            $('.close-sidebar-btn').click(function() {
                $('.app-sidebar').toggleClass('closed');
                $('.app-main').toggleClass('sidebar-closed');
                $('.app-header').toggleClass('sidebar-closed');
            });
            
            // Close sidebar when clicking outside on mobile
            $('.app-sidebar__overlay').click(function() {
                $('.app-sidebar').removeClass('active');
                $(this).removeClass('d-block');
            });
        });
    </script>
</body>
</html>