<?php
session_start();
if(!isset($_SESSION['cg_user_token'])){
    header("Location: login?error=login");
    exit();
}
include('connector.php');
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
    SELECT j.*, u.name as company_name,
    (SELECT COUNT(*) FROM Application a WHERE a.job_id = j.id AND a.user_id = '".$_SESSION['cg_user_token']."') as has_applied,
    (SELECT score FROM AIMatch am WHERE am.job_id = j.id AND am.user_id = '".$_SESSION['cg_user_token']."') as match_score
    FROM JobListing j
    JOIN User u ON j.company_id = u.id
    WHERE j.id = '$jobId'
");

if(mysqli_num_rows($jobQuery) == 0) {
    header("Location: job_listings");
    exit();
}

$job = mysqli_fetch_array($jobQuery);
$matchPercentage = isset($job['match_score']) ? round($job['match_score'] * 100) : null;
$hasApplied = $job['has_applied'] > 0;

// Get deadline info
$deadline = new DateTime($job['deadline']);
$today = new DateTime();
$daysRemaining = $today->diff($deadline)->days;
$isPastDeadline = $today > $deadline;

// Get similar jobs based on match score
$similarJobsQuery = mysqli_query($connect, "
    SELECT j.id, j.title, j.company_id, j.location, u.name as company_name,
    (SELECT score FROM AIMatch WHERE job_id = j.id AND user_id = '".$_SESSION['cg_user_token']."') as match_score
    FROM JobListing j
    JOIN User u ON j.company_id = u.id
    WHERE j.id != '$jobId' AND j.status = 'active'
    ORDER BY match_score DESC
    LIMIT 3
");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo $job['title']; ?> - AI Career Guidance Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no"/>
    <meta name="description" content="AI Career Guidance Platform Job Details">
    <link rel="icon" type="image/png" href="/images/photo1754818849.jpg">

    <!-- Styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    
    <style>
        /* Additional inline styles for this specific page */
        .job-detail-header {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .job-detail-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .job-detail-company {
            font-size: 18px;
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        .job-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .job-meta-item {
            display: flex;
            align-items: center;
        }
        
        .job-meta-item i {
            width: 20px;
            margin-right: 8px;
            color: #4a6fdc;
        }
        
        .match-badge {
            background: #4a6fdc;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 500;
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .job-detail-section {
            margin-bottom: 30px;
        }
        
        .job-detail-section h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .job-detail-section ul {
            padding-left: 20px;
        }
        
        .job-detail-section ul li {
            margin-bottom: 8px;
        }
        
        .similar-job-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        
        .similar-job-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .similar-job-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .similar-job-company {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .similar-job-match {
            background: #e9ecef;
            color: #495057;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        
        .action-buttons {
            margin-top: 30px;
            display: flex;
            gap: 10px;
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
                                    <i class="pe-7s-id icon-gradient bg-premium-dark"></i>
                                </div>
                                <div>
                                    Job Details
                                    <div class="page-title-subheading">
                                        View detailed information about this job opportunity
                                    </div>
                                </div>
                            </div>
                            <div class="page-title-actions">
                                <a href="job_listings" class="btn-shadow btn btn-info">
                                    <i class="fa fa-arrow-left pr-1"></i> Back to Job Listings
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Job Details -->
                            <div class="main-card mb-3 card">
                                <div class="card-body">
                                    <div class="job-detail-header">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h2 class="job-detail-title"><?php echo $job['title']; ?></h2>
                                                <div class="job-detail-company"><?php echo $job['company_name']; ?></div>
                                            </div>
                                            <?php if($matchPercentage !== null): ?>
                                            <div class="match-badge">
                                                <i class="fa fa-chart-line"></i> <?php echo $matchPercentage; ?>% Match
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="job-meta">
                                            <div class="job-meta-item">
                                                <i class="fa fa-map-marker-alt"></i>
                                                <span><?php echo $job['location']; ?></span>
                                            </div>
                                            <div class="job-meta-item">
                                                <i class="fa fa-briefcase"></i>
                                                <span><?php echo $job['job_type']; ?></span>
                                            </div>
                                            <?php if(!empty($job['salary_range'])): ?>
                                            <div class="job-meta-item">
                                                <i class="fa fa-money-bill-wave"></i>
                                                <span><?php echo $job['salary_range']; ?></span>
                                            </div>
                                            <?php endif; ?>
                                            <div class="job-meta-item">
                                                <i class="fa fa-calendar-alt"></i>
                                                <span>
                                                    Deadline: <?php echo date('d M Y', strtotime($job['deadline'])); ?>
                                                    <?php if($isPastDeadline): ?>
                                                    <span class="badge badge-danger">Expired</span>
                                                    <?php elseif($daysRemaining <= 3): ?>
                                                    <span class="badge badge-warning">Closing Soon</span>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="action-buttons">
                                            <?php if($hasApplied): ?>
                                            <button class="btn btn-success" disabled>
                                                <i class="fa fa-check"></i> Applied
                                            </button>
                                            <?php elseif(!$isPastDeadline): ?>
                                            <a href="apply?id=<?php echo $job['id']; ?>" class="btn btn-primary">
                                                <i class="fa fa-paper-plane"></i> Apply Now
                                            </a>
                                            <?php else: ?>
                                            <button class="btn btn-secondary" disabled>
                                                <i class="fa fa-times"></i> Deadline Passed
                                            </button>
                                            <?php endif; ?>
                                            <button class="btn btn-outline-primary" onclick="saveJob(<?php echo $job['id']; ?>)">
                                                <i class="fa fa-bookmark"></i> Save Job
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="job-detail-section">
                                        <h3>Job Description</h3>
                                        <div>
                                            <?php echo nl2br($job['description']); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="job-detail-section">
                                        <h3>Requirements</h3>
                                        <div>
                                            <?php echo nl2br($job['requirements']); ?>
                                        </div>
                                    </div>
                                    
                                    <?php if($matchPercentage !== null): ?>
                                    <div class="job-detail-section">
                                        <h3>Why You Match</h3>
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <p>Our AI has determined that you're a <?php echo $matchPercentage; ?>% match for this position based on:</p>
                                                <ul>
                                                    <li>Your skills and the job requirements</li>
                                                    <li>Your career interests and the job description</li>
                                                    <li>Your education and experience</li>
                                                </ul>
                                                <p class="mb-0">
                                                    <a href="ai_matches" class="btn btn-sm btn-info">
                                                        <i class="fa fa-robot"></i> View All AI Matches
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Company Info -->
                            <div class="main-card mb-3 card">
                                <div class="card-body">
                                    <h5 class="card-title">About the Company</h5>
                                    <div class="text-center mb-3">
                                        <img src="/images/photo1754818849.jpg" alt="<?php echo $job['company_name']; ?>" style="max-width: 100px; max-height: 100px;">
                                    </div>
                                    <h6 class="text-center mb-3"><?php echo $job['company_name']; ?></h6>
                                    <p>This company is looking for talented individuals to join their team.</p>
                                    <div class="text-center">
                                        <a href="company_view?id=<?php echo $job['company_id']; ?>" class="btn btn-sm btn-outline-primary">View Company Profile</a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Similar Jobs -->
                            <div class="main-card mb-3 card">
                                <div class="card-body">
                                    <h5 class="card-title">Similar Jobs</h5>
                                    
                                    <?php
                                    if(mysqli_num_rows($similarJobsQuery) > 0) {
                                        while($similarJob = mysqli_fetch_array($similarJobsQuery)) {
                                            $similarMatchPercentage = isset($similarJob['match_score']) ? round($similarJob['match_score'] * 100) : 0;
                                    ?>
                                    <div class="similar-job-card">
                                        <h6 class="similar-job-title"><?php echo $similarJob['title']; ?></h6>
                                        <div class="similar-job-company"><?php echo $similarJob['company_name']; ?></div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small"><?php echo $similarJob['location']; ?></span>
                                            <span class="similar-job-match"><?php echo $similarMatchPercentage; ?>% Match</span>
                                        </div>
                                        <div class="mt-2">
                                            <a href="job_detail?id=<?php echo $similarJob['id']; ?>" class="btn btn-sm btn-outline-primary">View Job</a>
                                        </div>
                                    </div>
                                    <?php
                                        }
                                    } else {
                                        echo '<p class="text-center">No similar jobs found.</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <!-- Application Tips -->
                            <div class="main-card mb-3 card">
                                <div class="card-body">
                                    <h5 class="card-title">Application Tips</h5>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fa fa-check-circle text-success mr-2"></i>
                                            Tailor your CV to match the job requirements
                                        </li>
                                        <li class="mb-2">
                                            <i class="fa fa-check-circle text-success mr-2"></i>
                                            Write a personalized cover letter
                                        </li>
                                        <li class="mb-2">
                                            <i class="fa fa-check-circle text-success mr-2"></i>
                                            Highlight relevant skills and experience
                                        </li>
                                        <li class="mb-2">
                                            <i class="fa fa-check-circle text-success mr-2"></i>
                                            Research the company before applying
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
        
        function saveJob(jobId) {
            // This is a placeholder function for saving jobs
            alert('Job saved to your favorites!');
            // In a real implementation, you would make an AJAX call to save the job
        }
    </script>
</body>
</html>