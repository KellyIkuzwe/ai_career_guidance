const mongoose = require('mongoose');
const Schema = mongoose.Schema;

const AIMatchSchema = new Schema({
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
  score: {
    type: Number,
    required: true,
    min: 0,
    max: 1
  },
  created_at: {
    type: Date,
    default: Date.now
  },
  updated_at: {
    type: Date,
    default: Date.now
  }
});

// Compound index to prevent duplicate matches
AIMatchSchema.index({ user_id: 1, job_id: 1 }, { unique: true });

module.exports = mongoose.model('AIMatch', AIMatchSchema);