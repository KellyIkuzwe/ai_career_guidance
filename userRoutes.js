const express = require('express');
const router = express.Router();
// Adjusted to actual file location
const userController = require('./userController');

// @route   POST api/users/register
// @desc    Register a new user
// @access  Public
router.post('/register', userController.registerUser);

// @route   POST api/users/login
// @desc    Authenticate user & get token
// @access  Public
router.post('/login', userController.loginUser);

// @route   GET api/users/me
// @desc    Get current user
// @access  Private
router.get('/me', userController.getCurrentUser);

// @route   POST api/users/logout
// @desc    Logout user / clear session
// @access  Private
router.post('/logout', userController.logoutUser);

module.exports = router;