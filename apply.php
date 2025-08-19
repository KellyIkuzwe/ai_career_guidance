<?php
session_start();
if(!isset($_SESSION['cg_user_token'])){
    header("Location: ../login?error=login");
    exit();
}
include('../connector.php');
$queryUser = mysqli_query($connect, "SELECT * FROM User WHERE id='".$_SESSION['cg_user_token']."'");
$rowUser = mysqli_fetch_array($queryUser);

// Ensure job ID is provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: job_listings");
    exit();
}

$jobId = mysqli_real_escape_string($connect, $_GET['id']);

// Get job details
$jobQuery = mysqli_query($connect, "
    SELECT j.*, u.name as company_name
    FROM JobListing j
    JOIN User u ON j.company_id = u.id
    WHERE j.id = '$jobId' AND j.status = 'active'
");

if(mysqli_num_rows($jobQuery) == 0) {
    header("Location: job_listings");
    exit();
}

$job = mysqli_fetch_array($jobQuery);

// Check if already applied
$checkQuery = mysqli_query($connect, "
    SELECT * FROM Application 
    WHERE user_id = '".$_SESSION['cg_user_token']."' AND job_id = '$jobId'
");

if(mysqli_num_rows($checkQuery) > 0) {
    header("Location: job_detail?id=$jobId&error=already_applied");
    exit();
}

// Get deadline info
$deadline = new DateTime($job['deadline']);
$today = new DateTime();
$isPastDeadline = $today > $deadline;

if($isPastDeadline) {
    header("Location: job_detail?id=$jobId&error=deadline_passed");
    exit();
}

// Get user profile
$profileQuery = mysqli_query($connect, "SELECT * FROM CareerProfile WHERE user_id = '".$_SESSION['cg_user_token']."'");
$profile = mysqli_fetch_array($profileQuery);

// Process form submission
if(isset($_POST['submit_application'])) {
    // Handle file upload
    $cv_file = '';
    if(isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] == 0) {
        $allowed = array('pdf', 'doc', 'docx');
        $filename = $_FILES['cv_file']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(in_array(strtolower($ext), $allowed)) {
            $new_filename = time() . '_' . $_SESSION['cg_user_token'] . '.' . $ext;
            $destination = 'uploads/' . $new_filename;
            
            if(move_uploaded_file($_FILES['cv_file']['tmp_name'], $destination)) {
                $cv_file = $destination;
            } else {
                $error = "Failed to upload your CV. Please try again.";
            }
        } else {
            $error = "Invalid file type. Please upload a PDF, DOC, or DOCX file.";
        }
    } else {
        $error = "Please upload your CV.";
    }
    
    if(!isset($error)) {
        $cover_letter = mysqli_real_escape_string($connect, $_POST['cover_letter']);
        
        // Insert application
        $insert = mysqli_query($connect, "
            INSERT INTO Application (user_id, job_id, cv_file, cover_letter, date_applied, status)
            VALUES ('".$_SESSION['cg_user_token']."', '$jobId', '$cv_file', '$cover_letter', NOW(), 'pending')
        ");
        
        if($insert) {
            header("Location: my_applications?success=applied");
            exit();
        } else {
            $error = "An error occurred while submitting your application. Please try again.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Apply for <?php echo $job['title']; ?> - AI Career Guidance Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no"/>
    <meta name="description" content="AI Career Guidance Platform Job Application">
    <link rel="icon" type="image/png" href="../images/logo.png">

    <!-- Styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    
    <style>
        /* Additional inline styles for this specific page */
        .job-summary {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .job-summary h6 {
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .job-summary p {
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .job-summary .deadline {
            color: #dc3545;
            font-weight: 500;
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
        
        .profile-preview {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .profile-preview h6 {
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .profile-preview p {
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .profile-completion-alert {
            margin-top: 15px;
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
                                    <i class="pe-7s-paper-plane icon-gradient bg-premium-dark"></i>
                                </div>
                                <div>
                                    Apply for Job
                                    <div class="page-title-subheading">
                                        Submit your application for <?php echo $job['title']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="page-title-actions">
                                <a href="job_detail?id=<?php echo $jobId; ?>" class="btn-shadow btn btn-info">
                                    <i class="fa fa-arrow-left pr-1"></i> Back to Job Details
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Application Form -->
                            <div class="main-card mb-3 card">
                                <div class="card-body">
                                    <h5 class="card-title">Application Form</h5>
                                    
                                    <?php if(isset($error)): ?>
                                    <div class="alert alert-danger">
                                        <?php echo $error; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="job-summary">
                                        <h6><?php echo $job['title']; ?></h6>
                                        <p><strong>Company:</strong> <?php echo $job['company_name']; ?></p>
                                        <p><strong>Location:</strong> <?php echo $job['location']; ?></p>
                                        <p class="deadline"><strong>Deadline:</strong> <?php echo date('d M Y', strtotime($job['deadline'])); ?></p>
                                    </div>
                                    
                                    <form method="POST" action="" enctype="multipart/form-data">
                                        <div class="form-section">
                                            <h6 class="form-section-title">Upload Your CV</h6>
                                            <div class="form-group">
                                                <label>CV/Resume (PDF, DOC, or DOCX)*</label>
                                                <input type="file" class="form-control-file" name="cv_file" required>
                                                <small class="form-text text-muted">Maximum file size: 2MB</small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-section">
                                            <h6 class="form-section-title">Cover Letter</h6>
                                            <div class="form-group">
                                                <label>Write a cover letter explaining why you're a good fit for this position</label>
                                                <textarea class="form-control" name="cover_letter" rows="6" placeholder="Dear Hiring Manager,"></textarea>
                                                <small class="form-text text-muted">A well-written cover letter can significantly increase your chances of getting an interview.</small>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" name="submit_application" class="btn btn-primary">Submit Application</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Profile Preview -->
                            <div class="main-card mb-3 card">
                                <div class="card-body">
                                    <h5 class="card-title">Your Profile Preview</h5>
                                    <p class="text-muted">This information will be visible to the employer</p>
                                    
                                    <div class="profile-preview">
                                        <h6><?php echo $rowUser['name']; ?></h6>
                                        <p><i class="fa fa-envelope mr-2"></i> <?php echo $rowUser['email']; ?></p>
                                        
                                        <?php
                                        // Calculate profile completion
                                        $completion = 0;
                                        if(isset($profile)) {
                                            if(!empty($profile['education'])) $completion += 25;
                                            if(!empty($profile['skills'])) $completion += 25;
                                            if(!empty($profile['interests'])) $completion += 25;
                                            if(!empty($profile['experience'])) $completion += 25;
                                        }
                                        
                                        $barClass = 'bg-danger';
                                        if($completion > 30) $barClass = 'bg-warning';
                                        if($completion > 70) $barClass = 'bg-success';
                                        ?>
                                        
                                        <div class="mt-3">
                                            <p class="mb-1">Profile Completion: <?php echo $completion; ?>%</p>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar <?php echo $barClass; ?>" role="progressbar" style="width: <?php echo $completion; ?>%" aria-valuenow="<?php echo $completion; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        
                                        <?php if($completion < 100): ?>
                                        <div class="profile-completion-alert alert alert-warning mt-3">
                                            <i class="fa fa-exclamation-triangle"></i> Complete your profile to increase your chances of being noticed by employers.
                                            <div class="mt-2">
                                                <a href="profile" class="btn btn-sm btn-warning">Update Profile</a>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($profile['skills'])): ?>
                                        <div class="mt-3">
                                            <h6>Skills</h6>
                                            <p><?php echo $profile['skills']; ?></p>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($profile['education'])): ?>
                                        <div class="mt-3">
                                            <h6>Education</h6>
                                            <p><?php echo $profile['education']; ?></p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Application Tips -->
                            <div class="main-card mb-3 card">
                                <div class="card-body">
                                    <h5 class="card-title">Application Tips</h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fa fa-check-circle text-success mr-2"></i>
                                            Review the job requirements carefully
                                        </li>
                                        <li class="mb-2">
                                            <i class="fa fa-check-circle text-success mr-2"></i>
                                            Highlight relevant skills and experience in your cover letter
                                        </li>
                                        <li class="mb-2">
                                            <i class="fa fa-check-circle text-success mr-2"></i>
                                            Use a professional, well-formatted CV
                                        </li>
                                        <li class="mb-2">
                                            <i class="fa fa-check-circle text-success mr-2"></i>
                                            Personalize your application for this specific job
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
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