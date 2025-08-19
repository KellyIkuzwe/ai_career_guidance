<?php
session_start();
if(!isset($_SESSION['cg_user_token'])){
    header("Location: ../login?error=login");
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
    <title>Dashboard - AI Career Guidance Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no"/>
    <meta name="description" content="AI Career Guidance Platform Dashboard">
    <link rel="icon" type="image/png" href="/images/photo1754818301.jpg">

    <!-- Styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">

    <style>
        /* Additional inline styles for this specific page */
        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-widget {
            display: flex;
            align-items: center;
            padding: 20px;
        }
        
        .stat-widget .icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
            color: white;
        }
        
        .bg-primary-light {
            background-color: rgba(74, 111, 220, 0.2);
            color: #4a6fdc;
        }
        
        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        
        .bg-info-light {
            background-color: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
        }
        
        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }
        
        .stats-details h3 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        
        .stats-details p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
        
        .activity-item {
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
            display: flex;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-top: 6px;
            margin-right: 10px;
        }
        
        .job-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        
        .job-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #4a6fdc;
        }
        
        .job-card-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .job-card-title {
            font-weight: 600;
            font-size: 16px;
            margin: 0;
        }
        
        .job-card-company {
            color: #6c757d;
            font-size: 14px;
        }
        
        .job-card-match {
            background: #4a6fdc;
            color: white;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .job-card-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            align-items: center;
        }
        
        .job-card-location {
            color: #6c757d;
            font-size: 13px;
        }
        
        .job-card-deadline {
            color: #dc3545;
            font-size: 13px;
            font-weight: 500;
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
                                    <i class="pe-7s-home icon-gradient bg-premium-dark"></i>
                                </div>
                                <div>
                                    Dashboard
                                    <div class="page-title-subheading">
                                        Welcome to your AI Career Guidance Platform dashboard
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if($rowUser['role'] == 'jobseeker'): ?>
                    <!-- Job Seeker Dashboard -->
                    <div class="row">
                        <div class="col-md-6 col-xl-3">
                            <div class="card mb-3 widget-content dashboard-card">
                                <div class="stat-widget">
                                    <div class="icon bg-primary-light">
                                        <i class="fa fa-search text-primary"></i>
                                    </div>
                                    <div class="stats-details">
                                        <?php
                                        // Count AI matches
                                        $matchesQuery = mysqli_query($connect, "SELECT COUNT(*) as count FROM AIMatch WHERE user_id = '".$_SESSION['cg_user_token']."'");
                                        $matchesCount = mysqli_fetch_array($matchesQuery)['count'];
                                        ?>
                                        <h3><?php echo $matchesCount; ?></h3>
                                        <p>AI Job Matches</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card mb-3 widget-content dashboard-card">
                                <div class="stat-widget">
                                    <div class="icon bg-success-light">
                                        <i class="fa fa-file-alt text-success"></i>
                                    </div>
                                    <div class="stats-details">
                                        <?php
                                        // Count applications
                                        $applicationsQuery = mysqli_query($connect, "SELECT COUNT(*) as count FROM Application WHERE user_id = '".$_SESSION['cg_user_token']."'");
                                        $applicationsCount = mysqli_fetch_array($applicationsQuery)['count'];
                                        ?>
                                        <h3><?php echo $applicationsCount; ?></h3>
                                        <p>Applications</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card mb-3 widget-content dashboard-card">
                                <div class="stat-widget">
                                    <div class="icon bg-info-light">
                                        <i class="fa fa-comment-alt text-info"></i>
                                    </div>
                                    <div class="stats-details">
                                        <?php
                                        // Count feedback
                                        $feedbackQuery = mysqli_query($connect, "SELECT COUNT(*) as count FROM Feedback WHERE to_user_id = '".$_SESSION['cg_user_token']."'");
                                        $feedbackCount = mysqli_fetch_array($feedbackQuery)['count'];
                                        ?>
                                        <h3><?php echo $feedbackCount; ?></h3>
                                        <p>Feedback</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card mb-3 widget-content dashboard-card">
                                <div class="stat-widget">
                                    <div class="icon bg-warning-light">
                                        <i class="fa fa-download text-warning"></i>
                                    </div>
                                    <div class="stats-details">
                                        <?php
                                        // Count reports
                                        $reportsQuery = mysqli_query($connect, "SELECT COUNT(*) as count FROM Report WHERE user_id = '".$_SESSION['cg_user_token']."'");
                                        $reportsCount = mysqli_fetch_array($reportsQuery)['count'];
                                        ?>
                                        <h3><?php echo $reportsCount; ?></h3>
                                        <p>Career Reports</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-7">
                            <div class="main-card mb-3 card dashboard-card">
                                <div class="card-header">
                                    Top AI Job Matches
                                    <div class="btn-actions-pane-right">
                                        <a href="ai_matches" class="btn btn-sm btn-primary">View All</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // Get top AI matches
                                    $topMatchesQuery = mysqli_query($connect, "
                                        SELECT m.*, j.title, j.company_id, j.location, j.deadline, u.name as company_name
                                        FROM AIMatch m
                                        JOIN JobListing j ON m.job_id = j.id
                                        JOIN User u ON j.company_id = u.id
                                        WHERE m.user_id = '".$_SESSION['cg_user_token']."'
                                        ORDER BY m.score DESC
                                        LIMIT 3
                                    ");
                                    
                                    if(mysqli_num_rows($topMatchesQuery) > 0) {
                                        while($match = mysqli_fetch_array($topMatchesQuery)) {
                                            $matchPercentage = round($match['score'] * 100);
                                            ?>
                                            <div class="job-card">
                                                <div class="job-card-header">
                                                    <div>
                                                        <h5 class="job-card-title"><?php echo $match['title']; ?></h5>
                                                        <span class="job-card-company"><?php echo $match['company_name']; ?></span>
                                                    </div>
                                                    <span class="job-card-match"><?php echo $matchPercentage; ?>% Match</span>
                                                </div>
                                                <div class="job-card-footer">
                                                    <span class="job-card-location"><i class="fa fa-map-marker-alt"></i> <?php echo $match['location']; ?></span>
                                                    <span class="job-card-deadline">Deadline: <?php echo date('d M Y', strtotime($match['deadline'])); ?></span>
                                                </div>
                                                <div class="mt-2">
                                                    <a href="job_detail?id=<?php echo $match['job_id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                                    <a href="apply?id=<?php echo $match['job_id']; ?>" class="btn btn-sm btn-outline-primary">Apply Now</a>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        echo '<p class="text-center py-3">No job matches found. Complete your profile to get AI-powered job recommendations.</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="main-card mb-3 card dashboard-card">
                                <div class="card-header">
                                    Recent Activities
                                </div>
                                <div class="card-body">
                                    <div class="activity-list">
                                        <?php
                                        // Get application activity
                                        $activityQuery = mysqli_query($connect, "
                                            SELECT a.*, j.title, DATE_FORMAT(a.date_applied, '%d %b %Y') as formatted_date
                                            FROM Application a
                                            JOIN JobListing j ON a.job_id = j.id
                                            WHERE a.user_id = '".$_SESSION['cg_user_token']."'
                                            ORDER BY a.date_applied DESC
                                            LIMIT 5
                                        ");
                                        
                                        if(mysqli_num_rows($activityQuery) > 0) {
                                            while($activity = mysqli_fetch_array($activityQuery)) {
                                                ?>
                                                <div class="activity-item">
                                                    <div class="activity-dot bg-primary"></div>
                                                    <div>
                                                        <strong>Applied for <?php echo $activity['title']; ?></strong>
                                                        <div class="text-muted small"><?php echo $activity['formatted_date']; ?></div>
                                                        <div class="mt-1">
                                                            <span class="badge badge-<?php 
                                                                if($activity['status'] == 'pending') echo 'warning';
                                                                else if($activity['status'] == 'shortlisted') echo 'success';
                                                                else if($activity['status'] == 'rejected') echo 'danger';
                                                                else echo 'info';
                                                            ?>">
                                                                <?php echo ucfirst($activity['status']); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        } else {
                                            echo '<p class="text-center py-3">No recent activities. Start exploring jobs and applying!</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="main-card mb-3 card dashboard-card">
                                <div class="card-header">
                                    Career Profile Completion
                                </div>
                                <div class="card-body">
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
                                    
                                    <h5 class="mb-3"><?php echo $completion; ?>% Complete</h5>
                                    <div class="progress mb-3" style="height: 10px;">
                                        <div class="progress-bar <?php echo $barClass; ?>" role="progressbar" style="width: <?php echo $completion; ?>%" aria-valuenow="<?php echo $completion; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    
                                    <?php if($completion < 100): ?>
                                    <div class="alert alert-info mb-0">
                                        Complete your profile to get better job matches and increase your visibility to employers.
                                        <div class="mt-2">
                                            <a href="profile" class="btn btn-sm btn-info">Update Profile</a>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-success mb-0">
                                        Great job! Your profile is complete, which maximizes your visibility to potential employers.
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php elseif($rowUser['role'] == 'company'): ?>
                    <!-- Company Dashboard -->
                    <div class="row">
                        <div class="col-md-6 col-xl-3">
                            <div class="card mb-3 widget-content dashboard-card">
                                <div class="stat-widget">
                                    <div class="icon bg-primary-light">
                                        <i class="fa fa-briefcase text-primary"></i>
                                    </div>
                                    <div class="stats-details">
                                        <?php
                                        // Count job listings
                                        $jobsQuery = mysqli_query($connect, "SELECT COUNT(*) as count FROM JobListing WHERE company_id = '".$_SESSION['cg_user_token']."'");
                                        $jobsCount = mysqli_fetch_array($jobsQuery)['count'];
                                        ?>
                                        <h3><?php echo $jobsCount; ?></h3>
                                        <p>Active Jobs</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card mb-3 widget-content dashboard-card">
                                <div class="stat-widget">
                                    <div class="icon bg-success-light">
                                        <i class="fa fa-users text-success"></i>
                                    </div>
                                    <div class="stats-details">
                                        <?php
                                        // Count applications
                                        $applicationsQuery = mysqli_query($connect, "
                                            SELECT COUNT(*) as count 
                                            FROM Application a
                                            JOIN JobListing j ON a.job_id = j.id
                                            WHERE j.company_id = '".$_SESSION['cg_user_token']."'
                                        ");
                                        $applicationsCount = mysqli_fetch_array($applicationsQuery)['count'];
                                        ?>
                                        <h3><?php echo $applicationsCount; ?></h3>
                                        <p>Applications</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card mb-3 widget-content dashboard-card">
                                <div class="stat-widget">
                                    <div class="icon bg-info-light">
                                        <i class="fa fa-user-check text-info"></i>
                                    </div>
                                    <div class="stats-details">
                                        <?php
                                        // Count shortlisted
                                        $shortlistedQuery = mysqli_query($connect, "
                                            SELECT COUNT(*) as count 
                                            FROM Application a
                                            JOIN JobListing j ON a.job_id = j.id
                                            WHERE j.company_id = '".$_SESSION['cg_user_token']."' AND a.status = 'shortlisted'
                                        ");
                                        $shortlistedCount = mysqli_fetch_array($shortlistedQuery)['count'];
                                        ?>
                                        <h3><?php echo $shortlistedCount; ?></h3>
                                        <p>Shortlisted</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card mb-3 widget-content dashboard-card">
                                <div class="stat-widget">
                                    <div class="icon bg-warning-light">
                                        <i class="fa fa-chart-line text-warning"></i>
                                    </div>
                                    <div class="stats-details">
                                        <?php
                                        // Calculate conversion rate
                                        $conversionRate = ($applicationsCount > 0) ? round(($shortlistedCount / $applicationsCount) * 100) : 0;
                                        ?>
                                        <h3><?php echo $conversionRate; ?>%</h3>
                                        <p>Conversion Rate</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-7">
                            <div class="main-card mb-3 card dashboard-card">
                                <div class="card-header">
                                    Recent Applications
                                    <div class="btn-actions-pane-right">
                                        <a href="applications" class="btn btn-sm btn-primary">View All</a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Applicant</th>
                                                    <th>Job Title</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Get recent applications
                                                $applicationsQuery = mysqli_query($connect, "
                                                    SELECT a.*, j.title, u.name as applicant_name, DATE_FORMAT(a.date_applied, '%d %b %Y') as formatted_date
                                                    FROM Application a
                                                    JOIN JobListing j ON a.job_id = j.id
                                                    JOIN User u ON a.user_id = u.id
                                                    WHERE j.company_id = '".$_SESSION['cg_user_token']."'
                                                    ORDER BY a.date_applied DESC
                                                    LIMIT 5
                                                ");
                                                
                                                if(mysqli_num_rows($applicationsQuery) > 0) {
                                                    while($application = mysqli_fetch_array($applicationsQuery)) {
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $application['applicant_name']; ?></td>
                                                            <td><?php echo $application['title']; ?></td>
                                                            <td><?php echo $application['formatted_date']; ?></td>
                                                            <td>
                                                                <span class="badge badge-<?php 
                                                                    if($application['status'] == 'pending') echo 'warning';
                                                                    else if($application['status'] == 'shortlisted') echo 'success';
                                                                    else if($application['status'] == 'rejected') echo 'danger';
                                                                    else echo 'info';
                                                                ?>">
                                                                    <?php echo ucfirst($application['status']); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="view_application?id=<?php echo $application['id']; ?>" class="btn btn-sm btn-info">View</a>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                } else {
                                                    echo '<tr><td colspan="5" class="text-center">No applications received yet.</td></tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="main-card mb-3 card dashboard-card">
                                <div class="card-header">
                                    Quick Actions
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-column">
                                        <a href="post_job" class="btn btn-primary mb-3">
                                            <i class="fa fa-plus-circle mr-2"></i> Post New Job
                                        </a>
                                        <a href="company_profile" class="btn btn-info mb-3">
                                            <i class="fa fa-building mr-2"></i> Update Company Profile
                                        </a>
                                        <a href="ai_candidates" class="btn btn-success mb-3">
                                            <i class="fa fa-search mr-2"></i> Find AI Matched Candidates
                                        </a>
                                        <a href="company_reports" class="btn btn-warning">
                                            <i class="fa fa-download mr-2"></i> Generate Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="main-card mb-3 card dashboard-card">
                                <div class="card-header">
                                    Job Listings Overview
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <?php
                                                // Get job statistics
                                                $jobStatsQuery = mysqli_query($connect, "
                                                    SELECT j.id, j.title, 
                                                        (SELECT COUNT(*) FROM Application WHERE job_id = j.id) as application_count,
                                                        DATE_FORMAT(j.deadline, '%d %b %Y') as formatted_deadline
                                                    FROM JobListing j
                                                    WHERE j.company_id = '".$_SESSION['cg_user_token']."' AND j.status = 'active'
                                                    ORDER BY j.deadline ASC
                                                    LIMIT 5
                                                ");
                                                
                                                if(mysqli_num_rows($jobStatsQuery) > 0) {
                                                    while($job = mysqli_fetch_array($jobStatsQuery)) {
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex justify-content-between">
                                                                    <div>
                                                                        <h6 class="mb-1"><?php echo $job['title']; ?></h6>
                                                                        <span class="text-muted small">Applications: <?php echo $job['application_count']; ?></span>
                                                                    </div>
                                                                    <div>
                                                                        <span class="text-danger small">Deadline: <?php echo $job['formatted_deadline']; ?></span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                } else {
                                                    echo '<tr><td class="text-center">No active job listings. <a href="post_job">Post a job</a> to start receiving applications.</td></tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
            
            // Search functionality
            $('.search-icon').click(function() {
                $('.search-wrapper').addClass('active');
                $('.search-input').focus();
            });
            
            $('.close').click(function() {
                $('.search-wrapper').removeClass('active');
            });
        });
    </script>
</body>
</html>