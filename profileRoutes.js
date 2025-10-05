const express = require('express');
const router = express.Router();
const multer = require('multer');
const path = require('path');
// Adjusted to actual file location
const Profile = require('./Profile');

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

// @route   GET api/profiles/me
// @desc    Get current user's profile
// @access  Private
router.get('/me', auth, async (req, res) => {
  try {
    const profile = await Profile.findOne({ user_id: req.session.user._id });
    
    if (!profile) {
      return res.status(404).json({ msg: 'Profile not found' });
    }
    
    res.json(profile);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
});

// @route   POST api/profiles
// @desc    Create or update profile
// @access  Private
router.post('/', auth, upload.single('cv_file'), async (req, res) => {
  try {
    const { education, skills, interests, experience } = req.body;
    
    // Build profile object
    const profileFields = {};
    profileFields.user_id = req.session.user._id;
    if (education) profileFields.education = education;
    if (skills) profileFields.skills = skills;
    if (interests) profileFields.interests = interests;
    if (experience) profileFields.experience = experience;
    if (req.file) profileFields.cv_file = `/uploads/${req.file.filename}`;
    
    let profile = await Profile.findOne({ user_id: req.session.user._id });
    
    if (profile) {
      // Update existing profile
      profile = await Profile.findOneAndUpdate(
        { user_id: req.session.user._id },
        { $set: profileFields },
        { new: true }
      );
    } else {
      // Create new profile
      profile = new Profile(profileFields);
      await profile.save();
    }
    
    res.json(profile);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
});

module.exports = router;