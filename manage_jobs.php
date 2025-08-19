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

// Handle job status updates
if(isset($_GET['action']) && isset($_GET['id'])) {
    $action = mysqli_real_escape_string($connect, $_GET['action']);
    $jobId = mysqli_real_escape_string($connect, $_GET['id']);
    
    if($action == 'close' || $action == 'reopen') {
        $status = ($action == 'close') ? 'closed' : 'active';
        
        // Update job status
        $updateQuery = mysqli_query($connect, "
            UPDATE JobListing 
            SET status = '$status' 
            WHERE id = '$jobId' AND company_id = '".$_SESSION['cg_user_token']."'
        ");
        
        if($updateQuery) {
            $statusMessage = ($action == 'close') ? 'closed' : 'reopened';
            $successMessage = "Job has been $statusMessage successfully!";
        } else {
            $errorMessage = "Failed to update job status.";
        }
    } elseif($action == 'delete') {
        // Instead of actually deleting, mark as deleted (soft delete)
        $updateQuery = mysqli_query($connect, "
            UPDATE JobListing 
            SET status = 'deleted' 
            WHERE id = '$jobId' AND company_id = '".$_SESSION['cg_user_token']."'
        ");
        
        if($updateQuery) {
            $successMessage = "Job has been deleted successfully!";
        } else {
            $errorMessage = "Failed to delete job.";
        }
    }
}

// Get job listings by this company
$jobsQuery = mysqli_query($connect, "
    SELECT j.*,
           (SELECT COUNT(*) FROM Application a WHERE a.job_id = j.id) as applications_count,
           (SELECT COUNT(*) FROM Application a WHERE a.job_id = j.id AND a.status = 'shortlisted') as shortlisted_count
    FROM JobListing j
    WHERE j.company_id = '".$_SESSION['cg_user_token']."' AND j.status != 'deleted'
    ORDER BY j.created_at DESC
");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Manage Jobs - AI Career Guidance Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no"/>
    <meta name="description" content="AI Career Guidance Platform Manage Jobs">
    <link rel="icon" type="image/png" href="/images/photo1754896232.jpg">

    <!-- Styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    
    <style>
        /* Additional inline styles for this specific page */
        .job-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .job-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .job-card.closed {
            opacity: 0.7;
            border-left: 4px solid #dc3545;
        }
        
        .job-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            background-color: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .job-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .job-date {
            font-size: 14px;
            color: #6c757d;
        }
        
        .job-body {
            padding: 20px;
        }
        
        .job-details {
            margin-bottom: 15px;
        }
        
        .job-detail {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .job-detail i {
            width: 20px;
            margin-right: 10px;
            color: #6c757d;
        }
        
        .job-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 15px;
        }
        
        .job-stat {
            padding: 10px 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }
        
        .job-stat i {
            margin-right: 10px;
            color: #4a6fdc;
        }
        
        .job-stat .value {
            font-weight: 600;
            margin-right: 5px;
        }
        
        .job-footer {
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        
        .job-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .badge-active {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        
        .badge-closed {
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
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
                                    <i class="pe-7s-display2 icon-gradient bg-premium-dark"></i>
                                </div>
                                <div>
                                    Manage Jobs
                                    <div class="page-title-subheading">
                                        View and manage your job listings
                                    </div>
                                </div>
                            </div>
                            <div class="page-title-actions">
                                <a href="post_job" class="btn-shadow btn btn-info">
                                    <i class="fa fa-plus-circle pr-1"></i> Post New Job
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <?php if(isset($successMessage)): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle mr-2"></i> <?php echo $successMessage; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(isset($errorMessage)): ?>
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-circle mr-2"></i> <?php echo $errorMessage; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_GET['success']) && $_GET['success'] == 'posted'): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle mr-2"></i> Job has been posted successfully!
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?php if(mysqli_num_rows($jobsQuery) > 0): ?>
                                <?php while($job = mysqli_fetch_array($jobsQuery)): ?>
                                    <div class="job-card <?php echo $job['status'] == 'closed' ? 'closed' : ''; ?>">
                                        <div class="job-header">
                                            <div>
                                                <h5 class="job-title"><?php echo $job['title']; ?></h5>
                                                <div class="job-date">Posted on <?php echo date('d M Y', strtotime($job['created_at'])); ?></div>
                                            </div>
                                            <div>
                                                <?php if($job['status'] == 'active'): ?>
                                                <span class="badge-status badge-active">Active</span>
                                                <?php else: ?>
                                                <span class="badge-status badge-closed">Closed</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="job-body">
                                            <div class="job-details">
                                                <div class="job-detail">
                                                    <i class="fa fa-map-marker-alt"></i>
                                                    <span><?php echo $job['location']; ?></span>
                                                </div>
                                                <div class="job-detail">
                                                    <i class="fa fa-briefcase"></i>
                                                    <span><?php echo $job['job_type']; ?></span>
                                                </div>
                                                <?php if(!empty($job['salary_range'])): ?>
                                                <div class="job-detail">
                                                    <i class="fa fa-money-bill-wave"></i>
                                                    <span><?php echo $job['salary_range']; ?></span>
                                                </div>
                                                <?php endif; ?>
                                                <div class="job-detail">
                                                    <i class="fa fa-calendar-alt"></i>
                                                    <span>
                                                        Deadline: <?php echo date('d M Y', strtotime($job['deadline'])); ?>
                                                        <?php 
                                                        $today = new DateTime();
                                                        $deadline = new DateTime($job['deadline']);
                                                        $isPastDeadline = $today > $deadline;
                                                        
                                                        if($isPastDeadline): ?>
                                                        <span class="badge badge-danger">Expired</span>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="job-stats">
                                                <div class="job-stat">
                                                    <i class="fa fa-file-alt"></i>
                                                    <span class="value"><?php echo $job['applications_count']; ?></span>
                                                    <span class="text">Applications</span>
                                                </div>
                                                <div class="job-stat">
                                                    <i class="fa fa-user-check"></i>
                                                    <span class="value"><?php echo $job['shortlisted_count']; ?></span>
                                                    <span class="text">Shortlisted</span>
                                                </div>
                                                <div class="job-stat">
                                                    <i class="fa fa-eye"></i>
                                                    <span class="value">N/A</span>
                                                    <span class="text">Views</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="job-footer">
                                            <div class="job-actions">
                                                <a href="applications?job_id=<?php echo $job['id']; ?>" class="btn btn-info btn-sm">
                                                    <i class="fa fa-users"></i> View Applications
                                                </a>
                                                
                                                <a href="job_detail?id=<?php echo $job['id']; ?>&view=employer" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-eye"></i> View Job
                                                </a>
                                                
                                                <a href="edit_job?id=<?php echo $job['id']; ?>" class="btn btn-secondary btn-sm">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                
                                                <?php if($job['status'] == 'active'): ?>
                                                <a href="manage_jobs?action=close&id=<?php echo $job['id']; ?>" class="btn btn-warning btn-sm" onclick="return confirm('Are you sure you want to close this job? It will no longer receive applications.');">
                                                    <i class="fa fa-pause"></i> Close
                                                </a>
                                                <?php else: ?>
                                                <a href="manage_jobs?action=reopen&id=<?php echo $job['id']; ?>" class="btn btn-success btn-sm">
                                                    <i class="fa fa-play"></i> Reopen
                                                </a>
                                                <?php endif; ?>
                                                
                                                <a href="manage_jobs?action=delete&id=<?php echo $job['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this job? This action cannot be undone.');">
                                                    <i class="fa fa-trash"></i> Delete
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle mr-2"></i> You haven't posted any jobs yet. <a href="post_job">Post a job</a> to start receiving applications!
                                </div>
                            <?php endif; ?>
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