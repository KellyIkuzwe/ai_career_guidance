const express = require('express');
const router = express.Router();
const Job = require('../models/Job');

// Middleware to check if user is authenticated
const auth = (req, res, next) => {
  if (!req.session.user) {
    return res.status(401).json({ msg: 'Not authenticated' });
  }
  next();
};

// Middleware to check if user is a company
const companyOnly = (req, res, next) => {
  if (!req.session.user || req.session.user.role !== 'company') {
    return res.status(403).json({ msg: 'Unauthorized' });
  }
  next();
};

// @route   POST api/jobs
// @desc    Create a new job
// @access  Private/Company
router.post('/', [auth, companyOnly], async (req, res) => {
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
});

// @route   GET api/jobs
// @desc    Get all jobs (with optional filters)
// @access  Public
router.get('/', async (req, res) => {
  try {
    const { search, location, job_type } = req.query;
    
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
    
    const jobs = await Job.find(query).sort({ created_at: -1 });
    res.json(jobs);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
});

// @route   GET api/jobs/:id
// @desc    Get job by ID
// @access  Public
router.get('/:id', async (req, res) => {
  try {
    const job = await Job.findById(req.params.id);
    
    if (!job) {
      return res.status(404).json({ msg: 'Job not found' });
    }
    
    res.json(job);
  } catch (err) {
    console.error(err.message);
    if (err.kind === 'ObjectId') {
      return res.status(404).json({ msg: 'Job not found' });
    }
    res.status(500).send('Server error');
  }
});

// @route   PUT api/jobs/:id
// @desc    Update a job
// @access  Private/Company
router.put('/:id', [auth, companyOnly], async (req, res) => {
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
});

// @route   DELETE api/jobs/:id
// @desc    Delete a job (soft delete)
// @access  Private/Company
router.delete('/:id', [auth, companyOnly], async (req, res) => {
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
});

// @route   GET api/jobs/company/me
// @desc    Get all jobs by current company
// @access  Private/Company
router.get('/company/me', [auth, companyOnly], async (req, res) => {
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
});

module.exports = router;