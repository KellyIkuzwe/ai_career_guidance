<div class="app-sidebar__inner">
    <ul class="vertical-nav-menu">
        <li class="app-sidebar__heading">Dashboard</li>
        <li>
            <a href="index" class="mm-active">
                <i class="metismenu-icon pe-7s-menu"></i>
                Dashboard
            </a>
        </li>
        
        <?php if($rowUser['role'] == 'jobseeker'): ?>
        <!-- Job Seeker Menu -->
        <li class="app-sidebar__heading">Career</li>
        <li>
            <a href="profile">
                <i class="metismenu-icon pe-7s-user"></i>
                My Profile
            </a>
            <a href="job_listings">
                <i class="metismenu-icon pe-7s-id"></i>
                Browse Jobs
            </a>
            <a href="my_applications">
                <i class="metismenu-icon pe-7s-note2"></i>
                My Applications
            </a>
            <a href="ai_matches">
                <i class="metismenu-icon pe-7s-graph2"></i>
                AI Job Matches
            </a>
            <a href="feedback">
                <i class="metismenu-icon pe-7s-comment"></i>
                Feedback
            </a>
            <a href="reports">
                <i class="metismenu-icon pe-7s-download"></i>
                Career Reports
            </a>
        </li>
        <?php elseif($rowUser['role'] == 'company'): ?>
        <!-- Company Menu -->
        <li class="app-sidebar__heading">Recruitment</li>
        <li>
            <a href="company_profile">
                <i class="metismenu-icon pe-7s-culture"></i>
                Company Profile
            </a>
            <a href="post_job">
                <i class="metismenu-icon pe-7s-plus"></i>
                Post New Job
            </a>
            <a href="manage_jobs">
                <i class="metismenu-icon pe-7s-display2"></i>
                Manage Jobs
            </a>
            <a href="applications">
                <i class="metismenu-icon pe-7s-users"></i>
                Applications
            </a>
            <a href="ai_candidates">
                <i class="metismenu-icon pe-7s-graph1"></i>
                AI Candidate Matches
            </a>
            <a href="send_feedback">
                <i class="metismenu-icon pe-7s-mail"></i>
                Send Feedback
            </a>
            <a href="company_reports">
                <i class="metismenu-icon pe-7s-graph"></i>
                Reports
            </a>
        </li>
        <?php endif; ?>
        
        <li class="app-sidebar__heading">Account</li>
        <li>
            <a href="settings">
                <i class="metismenu-icon pe-7s-config"></i>
                Settings
            </a>
            <a href="logout">
                <i class="metismenu-icon pe-7s-angle-left-circle"></i>
                Logout
            </a>
        </li>
    </ul>
</div>