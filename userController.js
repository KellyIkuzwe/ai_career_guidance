const bcrypt = require('bcryptjs');
// Adjusted to actual file location
const User = require('./User');

/**
 * Register a new user
 * @route POST api/users/register
 * @access Public
 */
exports.registerUser = async (req, res) => {
  try {
    const { name, email, password, role } = req.body;

    // Check if user already exists
    let user = await User.findOne({ email });
    if (user) {
      return res.status(400).json({ msg: 'User already exists' });
    }

    // Create new user
    user = new User({
      name,
      email,
      password,
      role: role || 'jobseeker' // Default to jobseeker if no role provided
    });

    // Hash password
    const salt = await bcrypt.genSalt(10);
    user.password = await bcrypt.hash(password, salt);

    // Save user
    await user.save();

    // Return user data (excluding password)
    const userData = {
      _id: user._id,
      name: user.name,
      email: user.email,
      role: user.role
    };

    res.status(201).json(userData);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};

/**
 * Authenticate user & get token
 * @route POST api/users/login
 * @access Public
 */
exports.loginUser = async (req, res) => {
  try {
    const { email, password } = req.body;

    // Check if user exists
    const user = await User.findOne({ email });
    if (!user) {
      return res.status(400).json({ msg: 'Invalid credentials' });
    }

    // Check password
    const isMatch = await bcrypt.compare(password, user.password);
    if (!isMatch) {
      return res.status(400).json({ msg: 'Invalid credentials' });
    }

    // Store user in session
    req.session.user = {
      _id: user._id,
      name: user.name,
      email: user.email,
      role: user.role
    };

    // Return user data
    res.json(req.session.user);
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Server error');
  }
};

/**
 * Get current user
 * @route GET api/users/me
 * @access Private
 */
exports.getCurrentUser = (req, res) => {
  if (!req.session.user) {
    return res.status(401).json({ msg: 'Not authenticated' });
  }
  res.json(req.session.user);
};

/**
 * Logout user / clear session
 * @route POST api/users/logout
 * @access Private
 */
exports.logoutUser = (req, res) => {
  req.session.destroy();
  res.json({ msg: 'Logged out successfully' });
};