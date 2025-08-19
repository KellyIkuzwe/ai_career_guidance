const mongoose = require('mongoose');
const Schema = mongoose.Schema;

const ApplicationSchema = new Schema({
  user_id: {
    type: Schema.Types.ObjectId,
    ref: 'User',
    required: true
  },
  job_id: {
    type: Schema.Types.ObjectId,
    ref: 'Job',
    required: true
  },
  cv_file: {
    type: String
  },
  cover_letter: {
    type: String
  },
  status: {
    type: String,
    enum: ['pending', 'shortlisted', 'rejected', 'interviewed'],
    default: 'pending'
  },
  date_applied: {
    type: Date,
    default: Date.now
  },
  updated_at: {
    type: Date,
    default: Date.now
  }
});

// Compound index to prevent duplicate applications
ApplicationSchema.index({ user_id: 1, job_id: 1 }, { unique: true });

module.exports = mongoose.model('Application', ApplicationSchema);