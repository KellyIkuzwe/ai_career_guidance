const express = require('express');
const router = express.Router();
const multer = require('multer');
const path = require('path');
const Application = require('../models/Application');
const Job = require('../models/Job');

// File upload configuration for CVs
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, 'public/uploads/');
  },
  filename: (req, file, cb) => {
    cb(null, `${Date.now()}_${file.originalname}`);
  }
});

const upload = multer({ 
  storage,
  fileFilter: (req, file, cb) => {
    const allowedFileTypes = ['.pdf', '.doc', '.docx'];
    const ext = path.extname(file.originalname).toLowerCase();
    if (allowedFileTypes.includes(ext)) {
      cb(null, true);
    } else {
      cb(new Error('Invalid file type. Only PDF, DOC, and DOCX are allowed.'));
    }
  },
  limits: {
    fileSize: 2 * 1024 * 1024 // 2MB limit
  }
});

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

// @route   POST api/applications
// @desc    Submit a job application
// @access  Private
router.post('/', [auth, upload.single('cv_file')], async (req, res) => {
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
});

// @route   GET api/applications/me
// @desc    Get all applications by current user
// @access  Private
router.get('/me', auth, async (req, res) => {
  try {
    const applications = await Application.find({ user_id: req.session.user._id })
      .populate('job_id', 'title company_id location job_type deadline')
      .sort({ date_applied: -1 });
    
    res.json(applications);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
});

// @route   GET api/applications/job/:job_id
// @desc    Get all applications for a specific job (company only)
// @access  Private/Company
router.get('/job/:job_id', [auth, companyOnly], async (req, res) => {
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
});

// @route   PUT api/applications/:id
// @desc    Update application status (company only)
// @access  Private/Company
router.put('/:id', [auth, companyOnly], async (req, res) => {
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
});

module.exports = router;