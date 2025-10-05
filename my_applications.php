<?php
session_start();
if(!isset($_SESSION['cg_user_token'])){
    header("Location: login?error=login");
    exit();
}
include('connector.php');
$queryUser = mysqli_query($connect, "SELECT * FROM User WHERE id='".$_SESSION['cg_user_token']."'");
$rowUser = mysqli_fetch_array($queryUser);

// Ensure user is a job seeker
if($rowUser['role'] != 'jobseeker'){
    header("Location: index");
    exit();
}

// Get all applications by the user
$applicationsQuery = mysqli_query($connect, "
    SELECT a.*, j.title, j.company_id, j.location, j.job_type, j.deadline, 
           u.name as company_name, DATE_FORMAT(a.date_applied, '%d %b %Y') as formatted_date
    FROM Application a
    JOIN JobListing j ON a.job_id = j.id
    JOIN User u ON j.company_id = u.id
    WHERE a.user_id = '".$_SESSION['cg_user_token']."'
    ORDER BY a.date_applied DESC
");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>My Applications - AI Career Guidance Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no"/>
    <meta name="description" content="AI Career Guidance Platform My Applications">
    <link rel="icon" type="image/png" href="/images/photo1754896056.jpg">

    <!-- Styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    
    <style>
        /* Additional inline styles for this specific page */
        .application-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .application-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .application-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            background-color: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .application-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .application-company {
            color: #6c757d;
        }
        
        .application-date {
            font-size: 14px;
            color: #6c757d;
        }
        
        .application-body {
            padding: 20px;
        }
        
        .application-details {
            margin-bottom: 15px;
        }
        
        .application-detail {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .application-detail i {
            width: 20px;
            margin-right: 10px;
            color: #6c757d;
        }
        
        .application-status {
            padding: 15px 20px;
            border-top: 1px solid #e9ecef;
            background-color: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .status-badge-pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }
        
        .status-badge-shortlisted {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        
        .status-badge-rejected {
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }
        
        .status-badge-interviewed {
            background-color: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
        }
        
        .feedback-badge {
            background-color: #4a6fdc;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
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
                                    <i class="pe-7s-note2 icon-gradient bg-premium-dark"></i>
                                </div>
                                <div>
                                    My Applications
                                    <div class="page-title-subheading">
                                        Track the status of your job applications
                                    </div>
                                </div>
                            </div>
                            <div class="page-title-actions">
                                <a href="job_listings" class="btn-shadow btn btn-info">
                                    <i class="fa fa-search pr-1"></i> Browse More Jobs
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <?php if(isset($_GET['success']) && $_GET['success'] == 'applied'): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> Your application has been submitted successfully!
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <?php if(mysqli_num_rows($applicationsQuery) > 0): ?>
                                <?php while($application = mysqli_fetch_array($applicationsQuery)): ?>
                                    <div class="application-card">
                                        <div class="application-header">
                                            <div>
                                                <h5 class="application-title"><?php echo $application['title']; ?></h5>
                                                <div class="application-company"><?php echo $application['company_name']; ?></div>
                                            </div>
                                            <div class="application-date">
                                                Applied on <?php echo $application['formatted_date']; ?>
                                            </div>
                                        </div>
                                        <div class="application-body">
                                            <div class="application-details">
                                                <div class="application-detail">
                                                    <i class="fa fa-map-marker-alt"></i>
                                                    <span><?php echo $application['location']; ?></span>
                                                </div>
                                                <div class="application-detail">
                                                    <i class="fa fa-briefcase"></i>
                                                    <span><?php echo $application['job_type']; ?></span>
                                                </div>
                                                <div class="application-detail">
                                                    <i class="fa fa-calendar-alt"></i>
                                                    <span>Deadline: <?php echo date('d M Y', strtotime($application['deadline'])); ?></span>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6>Cover Letter:</h6>
                                                    <div class="p-3 bg-light rounded">
                                                        <?php 
                                                        echo !empty($application['cover_letter']) 
                                                             ? (strlen($application['cover_letter']) > 200 
                                                                ? substr($application['cover_letter'], 0, 200) . '...' 
                                                                : $application['cover_letter'])
                                                             : '<em>No cover letter provided</em>';
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Uploaded Documents:</h6>
                                                    <div class="p-3 bg-light rounded">
                                                        <?php if(!empty($application['cv_file'])): ?>
                                                            <a href="<?php echo $application['cv_file']; ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                                <i class="fa fa-file-pdf"></i> View CV/Resume
                                                            </a>
                                                        <?php else: ?>
                                                            <em>No documents available</em>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="application-status">
                                            <div>
                                                <?php
                                                $statusClass = '';
                                                switch($application['status']) {
                                                    case 'pending':
                                                        $statusClass = 'status-badge-pending';
                                                        break;
                                                    case 'shortlisted':
                                                        $statusClass = 'status-badge-shortlisted';
                                                        break;
                                                    case 'rejected':
                                                        $statusClass = 'status-badge-rejected';
                                                        break;
                                                    case 'interviewed':
                                                        $statusClass = 'status-badge-interviewed';
                                                        break;
                                                    default:
                                                        $statusClass = 'status-badge-pending';
                                                }
                                                ?>
                                                <span class="status-badge <?php echo $statusClass; ?>">
                                                    <?php echo ucfirst($application['status']); ?>
                                                </span>
                                                
                                                <?php
                                                // Check if feedback exists
                                                $feedbackQuery = mysqli_query($connect, "
                                                    SELECT * FROM Feedback 
                                                    WHERE to_user_id = '".$_SESSION['cg_user_token']."' 
                                                    AND application_id = '".$application['id']."'
                                                ");
                                                
                                                if(mysqli_num_rows($feedbackQuery) > 0):
                                                ?>
                                                <span class="feedback-badge ml-2" data-toggle="modal" data-target="#feedbackModal<?php echo $application['id']; ?>">
                                                    <i class="fa fa-comments"></i> Feedback Available
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <a href="job_detail?id=<?php echo $application['job_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-eye"></i> View Job
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php
                                    // If feedback exists, create modal for it
                                    if(isset($feedbackQuery) && mysqli_num_rows($feedbackQuery) > 0):
                                        $feedback = mysqli_fetch_array($feedbackQuery);
                                    ?>
                                    <!-- Feedback Modal -->
                                    <div class="modal fade" id="feedbackModal<?php echo $application['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="feedbackModalLabel">Employer Feedback</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Feedback from:</strong> <?php echo $application['company_name']; ?></p>
                                                    <p><strong>Date:</strong> <?php echo date('d M Y', strtotime($feedback['created_at'])); ?></p>
                                                    
                                                    <?php if(!empty($feedback['rating'])): ?>
                                                    <p><strong>Rating:</strong> 
                                                        <?php 
                                                        for($i = 1; $i <= 5; $i++) {
                                                            if($i <= $feedback['rating']) {
                                                                echo '<i class="fa fa-star text-warning"></i>';
                                                            } else {
                                                                echo '<i class="fa fa-star text-muted"></i>';
                                                            }
                                                        }
                                                        ?>
                                                    </p>
                                                    <?php endif; ?>
                                                    
                                                    <div class="p-3 bg-light rounded">
                                                        <?php echo nl2br($feedback['message']); ?>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> You haven't applied for any jobs yet. <a href="job_listings">Browse available jobs</a> and start applying!
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