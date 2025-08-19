const mongoose = require('mongoose');
const Schema = mongoose.Schema;

const FeedbackSchema = new Schema({
  from_user_id: {
    type: Schema.Types.ObjectId,
    ref: 'User',
    required: true
  },
  to_user_id: {
    type: Schema.Types.ObjectId,
    ref: 'User',
    required: true
  },
  application_id: {
    type: Schema.Types.ObjectId,
    ref: 'Application',
    required: true
  },
  message: {
    type: String,
    required: true
  },
  rating: {
    type: Number,
    min: 1,
    max: 5
  },
  created_at: {
    type: Date,
    default: Date.now
  }
});

module.exports = mongoose.model('Feedback', FeedbackSchema);