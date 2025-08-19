<?php
session_start();
if(!isset($_SESSION['cg_user_token'])){
    header("Location: ../login?error=login");
    exit();
}
include('../connector.php');
$queryUser = mysqli_query($connect, "SELECT * FROM User WHERE id='".$_SESSION['cg_user_token']."'");
$rowUser = mysqli_fetch_array($queryUser);

// Ensure user is a job seeker
if($rowUser['role'] != 'jobseeker'){
    header("Location: index");
    exit();
}

// Get profile completion status for showing prompts
$profileQuery = mysqli_query($connect, "SELECT * FROM CareerProfile WHERE user_id = '".$_SESSION['cg_user_token']."'");
$profile = mysqli_fetch_array($profileQuery);

// Calculate profile completion
$completion = 0;
if(isset($profile)) {
    if(!empty($profile['education'])) $completion += 25;
    if(!empty($profile['skills'])) $completion += 25;
    if(!empty($profile['interests'])) $completion += 25;
    if(!empty($profile['experience'])) $completion += 25;
}

// Get all AI matches for this user
$matchesQuery = mysqli_query($connect, "
    SELECT m.*, j.title, j.description, j.location, j.job_type, j.deadline, j.company_id, 
           u.name as company_name,
           (SELECT COUNT(*) FROM Application a WHERE a.job_id = j.id AND a.user_id = '".$_SESSION['cg_user_token']."') as has_applied
    FROM AIMatch m
    JOIN JobListing j ON m.job_id = j.id
    JOIN User u ON j.company_id = u.id
    WHERE m.user_id = '".$_SESSION['cg_user_token']."'
    ORDER BY m.score DESC
");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>AI Job Matches - AI Career Guidance Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no"/>
    <meta name="description" content="AI Career Guidance Platform AI Job Matches">
    <link rel="icon" type="image/png" href="/images/photo1754896170.jpg">

    <!-- Styles -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    
    <style>
        /* Additional inline styles for this specific page */
        .match-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            margin-bottom: 20px;
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
        }
        
        .match-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .match-score {
            position: absolute;
            top: 0;
            right: 0;
            width: 70px;
            height: 70px;
            background-color: #4a6fdc;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            border-bottom-left-radius: 10px;
            font-weight: 700;
            z-index: 1;
        }
        
        .match-score .percentage {
            font-size: 20px;
            line-height: 1;
        }
        
        .match-score .text {
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .match-header {
            padding: 20px;
            background-color: #f8f9fa;
            position: relative;
        }
        
        .match-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            padding-right: 60px; /* Space for the match score */
        }
        
        .match-company {
            color: #6c757d;
        }
        
        .match-body {
            padding: 20px;
        }
        
        .match-details {
            margin-bottom: 15px;
        }
        
        .match-detail {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .match-detail i {
            width: 20px;
            margin-right: 10px;
            color: #6c757d;
        }
        
        .match-description {
            margin-bottom: 20px;
            max-height: 80px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        
        .match-footer {
            padding: 15px 20px;
            background-color: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e9ecef;
        }
        
        .match-reasons {
            background-color: #f0f7ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .match-reasons h6 {
            color: #4a6fdc;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .match-reasons ul {
            padding-left: 20px;
            margin-bottom: 0;
        }
        
        .match-reasons li {
            margin-bottom: 5px;
        }
        
        .AI-explanation {
            background: linear-gradient(135deg, #4a6fdc, #6c8ddd);
            padding: 20px;
            border-radius: 10px;
            color: white;
            margin-bottom: 20px;
        }
        
        .AI-explanation h5 {
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .AI-explanation h5 i {
            margin-right: 10px;
        }
        
        .profile-completion-alert {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
                                    <i class="pe-7s-graph2 icon-gradient bg-premium-dark"></i>
                                </div>
                                <div>
                                    AI Job Matches
                                    <div class="page-title-subheading">
                                        Jobs that match your skills and career interests
                                    </div>
                                </div>
                            </div>
                            <div class="page-title-actions">
                                <a href="job_listings" class="btn-shadow btn btn-info">
                                    <i class="fa fa-search pr-1"></i> Browse All Jobs
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <!-- AI Explanation -->
                            <div class="AI-explanation">
                                <h5><i class="fa fa-robot"></i> How AI Matching Works</h5>
                                <p>Our AI analyzes your profile information (skills, education, interests, and experience) and compares them with job requirements to find the best matches for you.</p>
                                <p class="mb-0">Jobs with higher match percentages are more likely to be a good fit based on your profile data. To improve your matches, keep your profile information complete and up-to-date.</p>
                            </div>
                            
                            <?php if($completion < 100): ?>
                            <!-- Profile Completion Alert -->
                            <div class="profile-completion-alert">
                                <div class="d-flex">
                                    <div class="mr-3">
                                        <i class="fa fa-exclamation-circle fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="alert-heading">Your profile is only <?php echo $completion; ?>% complete</h5>
                                        <p>Complete your profile to get more accurate job matches and increase your visibility to employers.</p>
                                        <a href="profile" class="btn btn-warning btn-sm">Update Your Profile</a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- AI Matches -->
                            <?php if(mysqli_num_rows($matchesQuery) > 0): ?>
                                <?php while($match = mysqli_fetch_array($matchesQuery)):
                                    $matchPercentage = round($match['score'] * 100);
                                    $hasApplied = $match['has_applied'] > 0;
                                    
                                    // Get deadline info
                                    $deadline = new DateTime($match['deadline']);
                                    $today = new DateTime();
                                    $daysRemaining = $today->diff($deadline)->days;
                                    $isPastDeadline = $today > $deadline;
                                ?>
                                    <div class="match-card">
                                        <div class="match-score">
                                            <div class="percentage"><?php echo $matchPercentage; ?>%</div>
                                            <div class="text">Match</div>
                                        </div>
                                        <div class="match-header">
                                            <h5 class="match-title"><?php echo $match['title']; ?></h5>
                                            <div class="match-company"><?php echo $match['company_name']; ?></div>
                                        </div>
                                        <div class="match-body">
                                            <div class="match-details">
                                                <div class="match-detail">
                                                    <i class="fa fa-map-marker-alt"></i>
                                                    <span><?php echo $match['location']; ?></span>
                                                </div>
                                                <div class="match-detail">
                                                    <i class="fa fa-briefcase"></i>
                                                    <span><?php echo $match['job_type']; ?></span>
                                                </div>
                                                <div class="match-detail">
                                                    <i class="fa fa-calendar-alt"></i>
                                                    <span>
                                                        Deadline: <?php echo date('d M Y', strtotime($match['deadline'])); ?>
                                                        <?php if($isPastDeadline): ?>
                                                        <span class="badge badge-danger">Expired</span>
                                                        <?php elseif($daysRemaining <= 3): ?>
                                                        <span class="badge badge-warning">Closing Soon</span>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="match-description">
                                                <?php echo $match['description']; ?>
                                            </div>
                                            
                                            <div class="match-reasons">
                                                <h6><i class="fa fa-lightbulb mr-2"></i>Why You Match</h6>
                                                <?php
                                                // Generate dynamic match reasons based on profile data
                                                echo '<ul>';
                                                
                                                if(!empty($profile['skills'])) {
                                                    echo '<li>Your skills align with this position\'s requirements</li>';
                                                }
                                                
                                                if(!empty($profile['education'])) {
                                                    echo '<li>Your educational background matches what they\'re looking for</li>';
                                                }
                                                
                                                if(!empty($profile['interests'])) {
                                                    echo '<li>This position aligns with your career interests</li>';
                                                }
                                                
                                                if(!empty($profile['experience'])) {
                                                    echo '<li>You have relevant experience for this role</li>';
                                                }
                                                
                                                echo '</ul>';
                                                ?>
                                            </div>
                                        </div>
                                        <div class="match-footer">
                                            <div>
                                                <?php if($hasApplied): ?>
                                                <span class="badge badge-success">
                                                    <i class="fa fa-check"></i> Already Applied
                                                </span>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <a href="job_detail?id=<?php echo $match['job_id']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-eye"></i> View Details
                                                </a>
                                                
                                                <?php if(!$hasApplied && !$isPastDeadline): ?>
                                                <a href="apply?id=<?php echo $match['job_id']; ?>" class="btn btn-outline-primary btn-sm">
                                                    <i class="fa fa-paper-plane"></i> Apply Now
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle mr-2"></i> No job matches found. Complete your profile to receive AI-powered job recommendations.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Run AI Matching Button (for demonstration) -->
                    <div class="text-center mt-4 mb-4">
                        <a href="#" class="btn btn-primary" onclick="runAIMatching(); return false;">
                            <i class="fa fa-sync"></i> Update AI Matches
                        </a>
                        <p class="text-muted mt-2">
                            <small>Our AI automatically updates your matches daily. Click the button to force an immediate update.</small>
                        </p>
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
        
        function runAIMatching() {
            // Show loading spinner
            const originalBtnHtml = $('.btn-primary').html();
            $('.btn-primary').html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            $('.btn-primary').attr('disabled', true);
            
            // This would typically be an AJAX call to run the AI matching script
            // For demonstration, we'll just simulate a delay
            setTimeout(function() {
                $('.btn-primary').html(originalBtnHtml);
                $('.btn-primary').attr('disabled', false);
                
                // Show success message
                $('<div class="alert alert-success mt-3">' +
                  '<i class="fa fa-check-circle mr-2"></i> AI matching process completed successfully! Your job matches have been updated.' +
                  '</div>').insertAfter('.btn-primary').hide().fadeIn();
                
                // Reload the page after a brief delay
                setTimeout(function() {
                    location.reload();
                }, 3000);
            }, 2000);
        }
    </script>
</body>
</html>