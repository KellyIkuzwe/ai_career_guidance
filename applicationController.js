// Adjusted to actual file locations
const Application = require('./Application');
const Job = require('./Job');
const User = require('./User');

/**
 * Submit a job application
 * @route POST api/applications
 * @access Private
 */
exports.submitApplication = async (req, res) => {
  try {
    const { job_id, cover_letter } = req.body;
    
    // Check if job exists and is active
    const job = await Job.findOne({ _id: job_id, status: 'active' });
    if (!job) {
      return res.status(404).json({ msg: 'Job not found or no longer active' });
    }
    
    // Check if already applied
    const existingApplication = await Application.findOne({
      user_id: req.session.user._id,
      job_id
    });
    
    if (existingApplication) {
      return res.status(400).json({ msg: 'You have already applied for this job' });
    }
    
    // Check if deadline has passed
    if (new Date(job.deadline) < new Date()) {
      return res.status(400).json({ msg: 'Application deadline has passed' });
    }
    
    // Create application
    const newApplication = new Application({
      user_id: req.session.user._id,
      job_id,
      cover_letter,
      status: 'pending'
    });
    
    if (req.file) {
      newApplication.cv_file = `/uploads/${req.file.filename}`;
    }
    
    await newApplication.save();
    res.status(201).json(newApplication);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};

/**
 * Get all applications by current user
 * @route GET api/applications/me
 * @access Private
 */
exports.getUserApplications = async (req, res) => {
  try {
    const applications = await Application.find({ user_id: req.session.user._id })
      .populate('job_id', 'title company_id location job_type deadline')
      .sort({ date_applied: -1 });
    
    // Enhance with company names
    const enhancedApplications = await Promise.all(applications.map(async (application) => {
      const appObj = application.toObject();
      
      if (appObj.job_id && appObj.job_id.company_id) {
        const company = await User.findById(appObj.job_id.company_id, 'name');
        if (company) {
          appObj.job_id.company_name = company.name;
        }
      }
      
      return appObj;
    }));
    
    res.json(enhancedApplications);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};

/**
 * Get all applications for a specific job (company only)
 * @route GET api/applications/job/:job_id
 * @access Private/Company
 */
exports.getJobApplications = async (req, res) => {
  try {
    const job = await Job.findById(req.params.job_id);
    
    if (!job) {
      return res.status(404).json({ msg: 'Job not found' });
    }
    
    // Check if job belongs to the company
    if (job.company_id.toString() !== req.session.user._id.toString()) {
      return res.status(403).json({ msg: 'Unauthorized' });
    }
    
    const applications = await Application.find({ job_id: req.params.job_id })
      .populate('user_id', 'name email')
      .sort({ date_applied: -1 });
    
    res.json(applications);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};

/**
 * Update application status (company only)
 * @route PUT api/applications/:id
 * @access Private/Company
 */
exports.updateApplicationStatus = async (req, res) => {
  try {
    const { status } = req.body;
    
    const application = await Application.findById(req.params.id);
    
    if (!application) {
      return res.status(404).json({ msg: 'Application not found' });
    }
    
    // Check if the job belongs to the company
    const job = await Job.findById(application.job_id);
    
    if (!job || job.company_id.toString() !== req.session.user._id.toString()) {
      return res.status(403).json({ msg: 'Unauthorized' });
    }
    
    application.status = status;
    application.updated_at = Date.now();
    
    await application.save();
    res.json(application);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};

/**
 * Get application by ID
 * @route GET api/applications/:id
 * @access Private
 */
exports.getApplicationById = async (req, res) => {
  try {
    const application = await Application.findById(req.params.id)
      .populate('job_id')
      .populate('user_id', 'name email');
    
    if (!application) {
      return res.status(404).json({ msg: 'Application not found' });
    }
    
    // Check if user owns the application or is the company that posted the job
    const isApplicant = application.user_id._id.toString() === req.session.user._id.toString();
    
    // Get the job to check if the current user is the company that posted it
    const job = await Job.findById(application.job_id);
    const isEmployer = job && job.company_id.toString() === req.session.user._id.toString();
    
    if (!isApplicant && !isEmployer) {
      return res.status(403).json({ msg: 'Unauthorized' });
    }
    
    res.json(application);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};