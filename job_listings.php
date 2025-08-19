<?php
session_start();
if(!isset($_SESSION['cg_user_token'])){
    header("Location: ../login?error=login");
    exit();
}
include('../connector.php');
$queryUser = mysqli_query($connect, "SELECT * FROM User WHERE id='".$_SESSION['cg_user_token']."'");
$rowUser = mysqli_fetch_array($queryUser);

// Get search parameters
$search = isset($_GET['search']) ? mysqli_real_escape_string($connect, $_GET['search']) : '';
$location = isset($_GET['location']) ? mysqli_real_escape_string($connect, $_GET['location']) : '';

// Build the query
$query = "
    SELECT j.*, u.name as company_name, 
    (SELECT COUNT(*) FROM Application a WHERE a.job_id = j.id AND a.user_id = '".$_SESSION['cg_user_token']."') as has_applied,
    (SELECT score FROM AIMatch am WHERE am.job_id = j.id AND am.user_id = '".$_SESSION['cg_user_token']."') as match_score
    FROM JobListing j
    JOIN User u ON j.company_id = u.id
    WHERE j.status = 'active'
";

// Add search conditions if provided
if (!empty($search)) {
    $query .= " AND (j.title LIKE '%$search%' OR j.description LIKE '%$search%' OR j.requirements LIKE '%$search%')";
}

if (!empty($location)) {
    $query .= " AND j.location LIKE '%$location%'";
}

// Sort by match score (if exists) and then by deadline
$query .= " ORDER BY match_score DESC, j.deadline ASC";

$jobs = mysqli_query($connect, $query);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Job Listings - AI Career Guidance Platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no"/>
    <meta name="description" content="AI Career Guidance Platform Job Listings">
    <link rel="icon" type="image/png" href="/images/photo1754818623.jpg">

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
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .job-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }
        
        .job-card.applied {
            border-left: 4px solid #28a745;
        }
        
        .job-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .job-card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .job-card-company {
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .job-card-match {
            background: #4a6fdc;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .job-card-details {
            margin-bottom: 15px;
        }
        
        .job-card-detail {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .job-card-detail i {
            width: 20px;
            margin-right: 10px;
            color: #6c757d;
        }
        
        .job-card-desc {
            margin-bottom: 15px;
            max-height: 80px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        
        .job-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .job-card-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .job-badge {
            background: #f8f9fa;
            color: #495057;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            border: 1px solid #e9ecef;
        }
        
        .applied-badge {
            position: absolute;
            top: 10px;
            right: -35px;
            background: #28a745;
            color: white;
            transform: rotate(45deg);
            padding: 5px 40px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .filters {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
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
                                    <i class="pe-7s-id icon-gradient bg-premium-dark"></i>
                                </div>
                                <div>
                                    Job Listings
                                    <div class="page-title-subheading">
                                        Browse available job opportunities
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search and Filters -->
                    <div class="filters">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" class="form-control" name="search" placeholder="Job title, skills, or keywords" value="<?php echo $search; ?>">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Location</label>
                                        <input type="text" class="form-control" name="location" placeholder="City or region" value="<?php echo $location; ?>">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Job Listings -->
                    <div class="row">
                        <div class="col-md-12">
                            <?php
                            if(mysqli_num_rows($jobs) > 0) {
                                while($job = mysqli_fetch_array($jobs)) {
                                    // Calculate match percentage if match exists
                                    $matchPercentage = isset($job['match_score']) ? round($job['match_score'] * 100) : null;
                                    $hasApplied = $job['has_applied'] > 0;
                                    
                                    // Get deadline info
                                    $deadline = new DateTime($job['deadline']);
                                    $today = new DateTime();
                                    $daysRemaining = $today->diff($deadline)->days;
                                    $isPastDeadline = $today > $deadline;
                            ?>
                                <div class="job-card <?php echo $hasApplied ? 'applied' : ''; ?>">
                                    <?php if($hasApplied): ?>
                                    <div class="applied-badge">Applied</div>
                                    <?php endif; ?>
                                    
                                    <div class="job-card-header">
                                        <div>
                                            <h5 class="job-card-title"><?php echo $job['title']; ?></h5>
                                            <div class="job-card-company"><?php echo $job['company_name']; ?></div>
                                        </div>
                                        <?php if($matchPercentage !== null): ?>
                                        <div class="job-card-match">
                                            <i class="fa fa-chart-line"></i> <?php echo $matchPercentage; ?>% Match
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="job-card-details">
                                        <div class="job-card-detail">
                                            <i class="fa fa-map-marker-alt"></i>
                                            <span><?php echo $job['location']; ?></span>
                                        </div>
                                        <div class="job-card-detail">
                                            <i class="fa fa-briefcase"></i>
                                            <span><?php echo $job['job_type']; ?></span>
                                        </div>
                                        <?php if(!empty($job['salary_range'])): ?>
                                        <div class="job-card-detail">
                                            <i class="fa fa-money-bill-wave"></i>
                                            <span><?php echo $job['salary_range']; ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <div class="job-card-detail">
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
                                    
                                    <div class="job-card-desc">
                                        <?php echo substr($job['description'], 0, 200) . (strlen($job['description']) > 200 ? '...' : ''); ?>
                                    </div>
                                    
                                    <div class="job-card-footer">
                                        <div>
                                            <a href="job_detail?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                            <?php if(!$hasApplied && !$isPastDeadline): ?>
                                            <a href="apply?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-outline-primary">Apply Now</a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="job-card-badges">
                                            <?php
                                            // Extract keywords from requirements (simple implementation)
                                            $keywords = explode(',', str_replace(['.', ';'], ',', $job['requirements']));
                                            $count = 0;
                                            foreach($keywords as $keyword) {
                                                $keyword = trim($keyword);
                                                if(!empty($keyword) && $count < 3) {
                                                    echo '<span class="job-badge">' . $keyword . '</span>';
                                                    $count++;
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                }
                            } else {
                                echo '<div class="alert alert-info">No job listings found matching your criteria.</div>';
                            }
                            ?>
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