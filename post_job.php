<?php
session_start();
if(!isset($_SESSION['cg_user_token'])){
    header("Location: ../login?error=login");
    exit();
}
include('../connector.php');
$queryUser = mysqli_query($connect, "SELECT * FROM User WHERE id='".$_SESSION['cg_user_token']."'");
$rowUser = mysqli_fetch_array($queryUser);

// Ensure user is a company
if($rowUser['role'] != 'company'){
    header("Location: index");
    exit();
}

// Process form submission
if(isset($_POST['post_job'])) {
    $title = mysqli_real_escape_string($connect, $_POST['title']);
    $description = mysqli_real_escape_string($connect, $_POST['description']);
    $requirements = mysqli_real_escape_string($connect, $_POST['requirements']);
    $location = mysqli_real_escape_string($connect, $_POST['location']);
    $job_type = mysqli_real_escape_string($connect, $_POST['job_type']);
    $salary_range = mysqli_real_escape_string($connect, $_POST['salary_range']);
    $deadline = mysqli_real_escape_string($connect, $_POST['deadline']);
    
    // Insert job listing
    $insert = mysqli_query($connect, "
        INSERT INTO JobListing (company_id, title, description, requirements, deadline, location, job_type, salary_range, created_at, status)
        VALUES ('".$_SESSION['cg_user_token']."', '$title', '$description', '$requirements', '$deadline', '$location', '$job_type', '$salary_range', NOW(), 'active')
    ");
    
    if($insert) {
        header("Location: manage_jobs?success=posted");
        exit();
    } else {
        $error = "An error occurred while posting the job. Please try again.";
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
    <title>Post a Job - AI Career Guidance Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no"/>
    <meta name="description" content="AI Career Guidance Platform Post a Job">
    <link rel="icon" type="image/png" href="/images/photo1754896055.jpg">

    <!-- Styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    
    <style>
        /* Additional inline styles for this specific page */
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section-title {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .tips-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .tips-card h6 {
            margin-bottom: 10px;
            font-weight: 600;
            color: #4a6fdc;
        }
        
        .tips-card ul {
            padding-left: 20px;
        }
        
        .tips-card li {
            margin-bottom: 8px;
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
                                    <i class="pe-7s-plus icon-gradient bg-premium-dark"></i>
                                </div>
                                <div>
                                    Post a New Job
                                    <div class="page-title-subheading">
                                        Create a new job listing to find qualified candidates
                                    </div>
                                </div>
                            </div>
                            <div class="page-title-actions">
                                <a href="manage_jobs" class="btn-shadow btn btn-info">
                                    <i class="fa fa-list pr-1"></i> Manage Job Listings
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Job Posting Form -->
                            <div class="main-card mb-3 card">
                                <div class="card-body">
                                    <h5 class="card-title">Job Details</h5>
                                    
                                    <?php if(isset($error)): ?>
                                    <div class="alert alert-danger">
                                        <?php echo $error; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="">
                                        <div class="form-section">
                                            <h6 class="form-section-title">Basic Information</h6>
                                            <div class="form-group">
                                                <label for="title">Job Title*</label>
                                                <input type="text" class="form-control" id="title" name="title" required placeholder="e.g. Software Developer, Marketing Manager">
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="location">Location*</label>
                                                    <input type="text" class="form-control" id="location" name="location" required placeholder="e.g. Kigali, Rwanda">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="job_type">Job Type*</label>
                                                    <select class="form-control" id="job_type" name="job_type" required>
                                                        <option value="">Select Job Type</option>
                                                        <option value="Full-time">Full-time</option>
                                                        <option value="Part-time">Part-time</option>
                                                        <option value="Contract">Contract</option>
                                                        <option value="Internship">Internship</option>
                                                        <option value="Remote">Remote</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="salary_range">Salary Range (Optional)</label>
                                                    <input type="text" class="form-control" id="salary_range" name="salary_range" placeholder="e.g. 500,000 - 800,000 RWF">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="deadline">Application Deadline*</label>
                                                    <input type="date" class="form-control" id="deadline" name="deadline" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-section">
                                            <h6 class="form-section-title">Job Description</h6>
                                            <div class="form-group">
                                                <label for="description">Detailed Description*</label>
                                                <textarea class="form-control" id="description" name="description" rows="6" required placeholder="Provide a detailed description of the job, responsibilities, and expectations."></textarea>
                                                <small class="form-text text-muted">Include information about the role, responsibilities, team structure, and company culture.</small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-section">
                                            <h6 class="form-section-title">Requirements</h6>
                                            <div class="form-group">
                                                <label for="requirements">Skills & Qualifications*</label>
                                                <textarea class="form-control" id="requirements" name="requirements" rows="6" required placeholder="List the required skills, qualifications, and experience."></textarea>
                                                <small class="form-text text-muted">Include education, experience, technical skills, and other qualifications needed for this position.</small>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" name="post_job" class="btn btn-primary">Post Job</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Tips Section -->
                            <div class="main-card mb-3 card">
                                <div class="card-body">
                                    <h5 class="card-title">Job Posting Tips</h5>
                                    
                                    <div class="tips-card">
                                        <h6><i class="fa fa-lightbulb mr-2"></i>Writing an Effective Job Title</h6>
                                        <ul>
                                            <li>Keep it clear and specific</li>
                                            <li>Include the position level (e.g., Senior, Junior)</li>
                                            <li>Avoid abbreviations or internal jargon</li>
                                            <li>Use industry-standard terminology</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="tips-card">
                                        <h6><i class="fa fa-lightbulb mr-2"></i>Creating a Compelling Description</h6>
                                        <ul>
                                            <li>Start with a brief company overview</li>
                                            <li>Clearly outline day-to-day responsibilities</li>
                                            <li>Highlight growth opportunities</li>
                                            <li>Include information about your company culture</li>
                                            <li>Mention any unique benefits or perks</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="tips-card">
                                        <h6><i class="fa fa-lightbulb mr-2"></i>Specifying Requirements</h6>
                                        <ul>
                                            <li>Distinguish between required and preferred skills</li>
                                            <li>Be realistic about experience requirements</li>
                                            <li>Include soft skills alongside technical skills</li>
                                            <li>Avoid discriminatory language</li>
                                            <li>Consider including salary information for transparency</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="tips-card">
                                        <h6><i class="fa fa-robot mr-2"></i>AI Matching</h6>
                                        <p>Our AI system will analyze your job requirements and match them with the most qualified candidates based on their skills, experience, and career interests.</p>
                                    </div>
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
            // Set minimum date for deadline (today)
            var today = new Date().toISOString().split('T')[0];
            document.getElementById('deadline').setAttribute('min', today);
            
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