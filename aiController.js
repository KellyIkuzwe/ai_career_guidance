// Adjusted to actual file locations
const AIMatch = require('./AIMatch');
const Profile = require('./Profile');
const Job = require('./Job');
const User = require('./User');

/**
 * Get AI job matches for current user
 * @route GET api/ai/matches
 * @access Private
 */
exports.getUserMatches = async (req, res) => {
  try {
    // Only job seekers can access matches
    if (req.session.user.role !== 'jobseeker') {
      return res.status(403).json({ msg: 'Unauthorized' });
    }
    
    const matches = await AIMatch.find({ user_id: req.session.user._id })
      .populate('job_id')
      .sort({ score: -1 });
    
    // Enhance with company names
    const enhancedMatches = await Promise.all(matches.map(async (match) => {
      const matchObj = match.toObject();
      
      if (matchObj.job_id && matchObj.job_id.company_id) {
        const company = await User.findById(matchObj.job_id.company_id, 'name');
        if (company) {
          matchObj.job_id.company_name = company.name;
        }
      }
      
      return matchObj;
    }));
    
    res.json(enhancedMatches);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};

/**
 * Generate AI matches for a user
 * @route POST api/ai/generate-matches
 * @access Private
 */
exports.generateMatches = async (req, res) => {
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
      const normalizedScore = matchCount > 0 ? Math.min(score / (matchCount * 2), 1) : 0;
      
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
};

/**
 * Generate a career guidance report for the user
 * @route GET api/ai/report
 * @access Private
 */
exports.generateCareerReport = async (req, res) => {
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
    
    // Enhance with company names
    const enhancedMatches = await Promise.all(topMatches.map(async (match) => {
      const matchObj = match.toObject();
      
      if (matchObj.job_id && matchObj.job_id.company_id) {
        const company = await User.findById(matchObj.job_id.company_id, 'name');
        if (company) {
          matchObj.job_id.company_name = company.name;
        }
      }
      
      return matchObj;
    }));
    
    // Generate career path suggestions based on profile
    const careerPaths = [];
    
    if (profile.skills && profile.skills.toLowerCase().includes('programming')) {
      careerPaths.push('Software Development');
    }
    if (profile.skills && profile.skills.toLowerCase().includes('analysis')) {
      careerPaths.push('Data Analysis');
    }
    if (profile.skills && profile.skills.toLowerCase().includes('design')) {
      careerPaths.push('UI/UX Design');
    }
    if (profile.skills && profile.skills.toLowerCase().includes('management')) {
      careerPaths.push('Project Management');
    }
    
    // Generate report data
    const reportData = {
      profile,
      topMatches: enhancedMatches,
      careerPaths,
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
};

/**
 * Get AI-ranked candidates for a specific job (company only)
 * @route GET api/ai/candidates/:job_id
 * @access Private/Company
 */
exports.getAIRankedCandidates = async (req, res) => {
  try {
    // Only companies can access this
    if (req.session.user.role !== 'company') {
      return res.status(403).json({ msg: 'Unauthorized' });
    }
    
    const job = await Job.findById(req.params.job_id);
    
    if (!job) {
      return res.status(404).json({ msg: 'Job not found' });
    }
    
    // Check if job belongs to the company
    if (job.company_id.toString() !== req.session.user._id.toString()) {
      return res.status(403).json({ msg: 'Unauthorized' });
    }
    
    // Get all candidates who applied for this job
    const applications = await Application.find({ job_id: req.params.job_id });
    
    // Get AI matches for these candidates
    const candidates = await Promise.all(applications.map(async (application) => {
      const user = await User.findById(application.user_id, 'name email');
      const profile = await Profile.findOne({ user_id: application.user_id });
      const match = await AIMatch.findOne({ 
        user_id: application.user_id,
        job_id: req.params.job_id
      });
      
      return {
        application,
        user,
        profile,
        matchScore: match ? match.score : 0
      };
    }));
    
    // Sort by match score (descending)
    candidates.sort((a, b) => b.matchScore - a.matchScore);
    
    res.json(candidates);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};