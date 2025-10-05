// Adjusted to actual file locations
const Job = require('./Job');
const User = require('./User');

/**
 * Create a new job
 * @route POST api/jobs
 * @access Private/Company
 */
exports.createJob = async (req, res) => {
  try {
    const {
      title,
      description,
      requirements,
      location,
      job_type,
      salary_range,
      deadline
    } = req.body;

    const newJob = new Job({
      company_id: req.session.user._id,
      title,
      description,
      requirements,
      location,
      job_type,
      salary_range,
      deadline,
      status: 'active'
    });

    const job = await newJob.save();
    res.json(job);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};

/**
 * Get all jobs (with optional filters)
 * @route GET api/jobs
 * @access Public
 */
exports.getAllJobs = async (req, res) => {
  try {
    const { search, location, job_type, limit } = req.query;
    
    // Build query
    const query = { status: 'active' };
    
    if (search) {
      query.$or = [
        { title: { $regex: search, $options: 'i' } },
        { description: { $regex: search, $options: 'i' } },
        { requirements: { $regex: search, $options: 'i' } }
      ];
    }
    
    if (location) {
      query.location = { $regex: location, $options: 'i' };
    }
    
    if (job_type) {
      query.job_type = job_type;
    }
    
    let jobQuery = Job.find(query).sort({ created_at: -1 });
    
    // Apply limit if provided
    if (limit) {
      jobQuery = jobQuery.limit(parseInt(limit));
    }
    
    const jobs = await jobQuery;

    // Get company names for each job
    const jobsWithCompanyNames = await Promise.all(jobs.map(async (job) => {
      const company = await User.findById(job.company_id, 'name');
      const jobObj = job.toObject();
      jobObj.company_name = company ? company.name : 'Unknown Company';
      return jobObj;
    }));
    
    res.json(jobsWithCompanyNames);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};

/**
 * Get job by ID
 * @route GET api/jobs/:id
 * @access Public
 */
exports.getJobById = async (req, res) => {
  try {
    const job = await Job.findById(req.params.id);
    
    if (!job) {
      return res.status(404).json({ msg: 'Job not found' });
    }
    
    // Get company name
    const company = await User.findById(job.company_id, 'name');
    const jobObj = job.toObject();
    jobObj.company_name = company ? company.name : 'Unknown Company';
    
    res.json(jobObj);
  } catch (err) {
    console.error(err.message);
    if (err.kind === 'ObjectId') {
      return res.status(404).json({ msg: 'Job not found' });
    }
    res.status(500).send('Server error');
  }
};

/**
 * Update a job
 * @route PUT api/jobs/:id
 * @access Private/Company
 */
exports.updateJob = async (req, res) => {
  try {
    const job = await Job.findById(req.params.id);
    
    if (!job) {
      return res.status(404).json({ msg: 'Job not found' });
    }
    
    // Check user ownership
    if (job.company_id.toString() !== req.session.user._id.toString()) {
      return res.status(403).json({ msg: 'Unauthorized' });
    }
    
    // Update fields
    const {
      title,
      description,
      requirements,
      location,
      job_type,
      salary_range,
      deadline,
      status
    } = req.body;
    
    if (title) job.title = title;
    if (description) job.description = description;
    if (requirements) job.requirements = requirements;
    if (location) job.location = location;
    if (job_type) job.job_type = job_type;
    if (salary_range) job.salary_range = salary_range;
    if (deadline) job.deadline = deadline;
    if (status) job.status = status;
    
    job.updated_at = Date.now();
    
    await job.save();
    res.json(job);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};

/**
 * Delete a job (soft delete)
 * @route DELETE api/jobs/:id
 * @access Private/Company
 */
exports.deleteJob = async (req, res) => {
  try {
    const job = await Job.findById(req.params.id);
    
    if (!job) {
      return res.status(404).json({ msg: 'Job not found' });
    }
    
    // Check user ownership
    if (job.company_id.toString() !== req.session.user._id.toString()) {
      return res.status(403).json({ msg: 'Unauthorized' });
    }
    
    // Soft delete
    job.status = 'deleted';
    await job.save();
    
    res.json({ msg: 'Job deleted' });
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};

/**
 * Get all jobs by current company
 * @route GET api/jobs/company/me
 * @access Private/Company
 */
exports.getCompanyJobs = async (req, res) => {
  try {
    const jobs = await Job.find({ 
      company_id: req.session.user._id,
      status: { $ne: 'deleted' }
    }).sort({ created_at: -1 });
    
    res.json(jobs);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};