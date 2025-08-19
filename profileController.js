const Profile = require('../../models/Profile');

/**
 * Get current user's profile
 * @route GET api/profiles/me
 * @access Private
 */
exports.getCurrentProfile = async (req, res) => {
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
};

/**
 * Create or update profile
 * @route POST api/profiles
 * @access Private
 */
exports.createOrUpdateProfile = async (req, res) => {
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
};

/**
 * Get user profile by ID
 * @route GET api/profiles/user/:user_id
 * @access Private
 */
exports.getProfileByUserId = async (req, res) => {
  try {
    const profile = await Profile.findOne({ user_id: req.params.user_id });
    
    if (!profile) {
      return res.status(404).json({ msg: 'Profile not found' });
    }
    
    res.json(profile);
  } catch (err) {
    console.error(err.message);
    if (err.kind === 'ObjectId') {
      return res.status(404).json({ msg: 'Profile not found' });
    }
    res.status(500).send('Server error');
  }
};

/**
 * Get all profiles (admin only)
 * @route GET api/profiles
 * @access Private/Admin
 */
exports.getAllProfiles = async (req, res) => {
  try {
    // Check if user is admin
    if (req.session.user.role !== 'admin') {
      return res.status(403).json({ msg: 'Unauthorized' });
    }
    
    const profiles = await Profile.find().populate('user_id', 'name email role');
    res.json(profiles);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};