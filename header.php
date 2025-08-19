<?php 
    if(isset($_SESSION['cg_user_token'])){
        include('../connector.php');
        $queryUser = mysqli_query($connect,"SELECT * FROM User WHERE id='".$_SESSION['cg_user_token']."'");
        $rowUser = mysqli_fetch_array($queryUser);
        
        if($rowUser['role'] == 'jobseeker') {
            $queryProfile = mysqli_query($connect,"SELECT * FROM CareerProfile WHERE user_id='".$_SESSION['cg_user_token']."'");
            $rowProfile = mysqli_fetch_array($queryProfile);
        }
    }
?>
<link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
<div class="app-header header-shadow">
    <div class="app-header__logo">
        <div class="logo-src" style="background: /images/photo1754818205.jpg) !important;">
            <img src="/images/photo1754818205.jpg" style="width: 100%;">
        </div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>
    <div class="app-header__menu">
        <span>
            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>
    <div class="app-header__content">
        <div class="app-header-left">
            <div class="search-wrapper">
                <div class="input-holder">
                    <input type="text" class="search-input" placeholder="Type to search">
                    <button class="search-icon"><span></span></button>
                </div>
                <button class="close"></button>
            </div>
            <ul class="header-menu nav">
                <li class="nav-item">
                    <a href="javascript:void(0);" class="nav-link">
                        <i class="nav-link-icon fa fa-database"> </i>
                        Statistics
                    </a>
                </li>
                <?php if($rowUser['role'] == 'jobseeker'): ?>
                <li class="btn-group nav-item">
                    <a href="job_listings" class="nav-link">
                        <i class="nav-link-icon fa fa-briefcase"></i>
                        Jobs
                    </a>
                </li>
                <?php elseif($rowUser['role'] == 'company'): ?>
                <li class="btn-group nav-item">
                    <a href="post_job" class="nav-link">
                        <i class="nav-link-icon fa fa-plus-circle"></i>
                        Post Job
                    </a>
                </li>
                <?php endif; ?>
                <li class="dropdown nav-item">
                    <a href="settings" class="nav-link">
                        <i class="nav-link-icon fa fa-cog"></i>
                        Settings
                    </a>
                </li>
            </ul>
        </div>
        <div class="app-header-right">
            <div class="header-btn-lg pr-0">
                <div class="widget-content p-0">
                    <div class="widget-content-wrapper">
                        <div class="widget-content-left">
                            <div class="btn-group">
                                <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
                                    <?php
                                        $profileImage = isset($rowProfile) && !empty($rowProfile['profile_picture']) ? $rowProfile['profile_picture'] : 'profiles/default.png';
                                    ?>
                                    <img width="42" class="rounded-circle" src="<?php echo $profileImage; ?>" alt="">
                                    <i class="fa fa-angle-down ml-2 opacity-8"></i>
                                </a>
                                <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu dropdown-menu-right">
                                    <button type="button" tabindex="0" class="dropdown-item" onclick="window.location='profile';">Profile</button>
                                    <button type="button" tabindex="0" class="dropdown-item" onclick="window.location='settings';">Settings</button>
                                    <button type="button" tabindex="0" class="dropdown-item" onclick="window.location='logout';">Logout</button>
                                </div>
                            </div>
                        </div>
                        <div class="widget-content-left ml-3 header-user-info">
                            <div class="widget-heading">
                                <?php echo $rowUser['name']; ?>
                            </div>
                            <div class="widget-subheading">
                                <?php echo ucfirst($rowUser['role']); ?> Account
                            </div>
                        </div>
                        <div class="widget-content-right header-user-info ml-3">
                            <button type="button" class="btn-shadow p-1 btn btn-primary btn-sm">
                                <i class="fa text-white fa-user pr-1 pl-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>