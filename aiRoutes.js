const express = require('express');
const router = express.Router();
const AIMatch = require('../models/AIMatch');
const Profile = require('../models/Profile');
const Job = require('../models/Job');

// Middleware to check if user is authenticated
const auth = (req, res, next) => {
  if (!req.session.user) {
    return res.status(401).json({ msg: 'Not authenticated' });
  }
  next();
};

// @route   GET api/ai/matches
// @desc    Get AI job matches for current user
// @access  Private
router.get('/matches', auth, async (req, res) => {
  try {
    // Only job seekers can access matches
    if (req.session.user.role !== 'jobseeker') {
      return res.status(403).json({ msg: 'Unauthorized' });
    }
    
    const matches = await AIMatch.find({ user_id: req.session.user._id })
      .populate('job_id')
      .sort({ score: -1 });
    
    res.json(matches);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
});

// @route   POST api/ai/generate-matches
// @desc    Generate AI matches for a user
// @access  Private
router.post('/generate-matches', auth, async (req, res) => {
  try {
    // Only job seekers can generate matches
    if (req.session.user.role !== 'jobseeker') {
      return res.status(403).json({ msg: 'Unauthorized' });
    }
    
    const userId = req.session.user._id;
    
    // Get user profile
    const profile = await Profile.findOne({ user_id: userId });
    
    if (!profile) {
      return res.status(400).json({ msg: 'Please complete your profile first' });
    }
    
    // Get active jobs
    const jobs = await Job.find({ status: 'active' });
    
    // Array to store new matches
    const matches = [];
    
    for (const job of jobs) {
      // Simple matching algorithm
      let score = 0;
      let matchCount = 0;
      
      // Check if profile skills match job requirements
      if (profile.skills && job.requirements) {
        const profileSkills = profile.skills.toLowerCase().split(/,|\s+/).filter(Boolean);
        const jobRequirements = job.requirements.toLowerCase().split(/,|\s+/).filter(Boolean);
        
        // Count matching skills/requirements
        for (const skill of profileSkills) {
          if (jobRequirements.some(req => req.includes(skill) || skill.includes(req))) {
            score += 1;
            matchCount += 1;
          }
        }
      }
      
      // Check if profile interests match job description
      if (profile.interests && job.description) {
        const profileInterests = profile.interests.toLowerCase().split(/,|\s+/).filter(Boolean);
        const jobDescription = job.description.toLowerCase();
        
        for (const interest of profileInterests) {
          if (jobDescription.includes(interest)) {
            score += 0.5;
            matchCount += 1;
          }
        }
      }
      
      // Normalize score (0 to 1)
      const normalizedScore = matchCount > 0 ? score / (matchCount * 2) : 0;
      
      // Create or update match record
      const existingMatch = await AIMatch.findOne({ user_id: userId, job_id: job._id });
      
      if (existingMatch) {
        existingMatch.score = normalizedScore;
        existingMatch.updated_at = Date.now();
        await existingMatch.save();
        matches.push(existingMatch);
      } else {
        const newMatch = new AIMatch({
          user_id: userId,
          job_id: job._id,
          score: normalizedScore
        });
        await newMatch.save();
        matches.push(newMatch);
      }
    }
    
    res.json({ msg: 'Matches generated successfully', count: matches.length });
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
});

// @route   GET api/ai/report
// @desc    Generate a career guidance report for the user
// @access  Private
router.get('/report', auth, async (req, res) => {
  try {
    // Only job seekers can access reports
    if (req.session.user.role !== 'jobseeker') {
      return res.status(403).json({ msg: 'Unauthorized' });
    }
    
    const userId = req.session.user._id;
    
    // Get user profile
    const profile = await Profile.findOne({ user_id: userId });
    
    if (!profile) {
      return res.status(400).json({ msg: 'Please complete your profile first' });
    }
    
    // Get user's top matches
    const topMatches = await AIMatch.find({ user_id: userId })
      .populate('job_id')
      .sort({ score: -1 })
      .limit(5);
    
    // Generate report data
    const reportData = {
      profile,
      topMatches,
      recommendations: [
        {
          category: 'Skills Development',
          suggestions: [
            'Consider enhancing your technical skills in programming languages',
            'Develop stronger communication and presentation abilities',
            'Gain experience with project management methodologies'
          ]
        },
        {
          category: 'Career Opportunities',
          suggestions: [
            'Explore roles in the growing tech sector in Rwanda',
            'Consider internship opportunities with international organizations',
            'Look into entrepreneurship programs for young professionals'
          ]
        }
      ],
      generatedAt: new Date()
    };
    
    res.json(reportData);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
});

module.exports = router;