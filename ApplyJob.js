import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import './ApplyJob.css';

const ApplyJob = () => {
  const { jobId } = useParams();
  const navigate = useNavigate();
  const { currentUser } = useAuth();
  
  const [job, setJob] = useState(null);
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [formData, setFormData] = useState({
    coverLetter: '',
    resume: null,
    portfolioUrl: '',
    expectedSalary: '',
    availabilityDate: '',
    additionalInfo: ''
  });
  const [errors, setErrors] = useState({});

  useEffect(() => {
    fetchJobDetails();
  }, [jobId]);

  const fetchJobDetails = async () => {
    setLoading(true);
    try {
      // Mock job data - replace with actual API call
      const mockJob = {
        id: parseInt(jobId),
        title: 'Senior Software Developer',
        company: 'Tech Rwanda Ltd',
        location: 'Kigali, Rwanda',
        type: 'Full-time',
        salary: 'RWF 800,000 - 1,200,000',
        description: 'We are looking for a senior software developer to join our growing team...',
        requirements: [
          'Bachelor\'s degree in Computer Science or related field',
          '5+ years of experience in software development',
          'Proficiency in JavaScript, React, and Node.js',
          'Experience with cloud platforms (AWS, Azure)',
          'Strong problem-solving skills'
        ],
        benefits: [
          'Competitive salary',
          'Health insurance',
          'Flexible working hours',
          'Professional development opportunities',
          'Remote work options'
        ],
        postedDate: '2024-01-10',
        applicationDeadline: '2024-02-10',
        companyLogo: '/api/placeholder/80/80'
      };
      
      setTimeout(() => {
        setJob(mockJob);
        setLoading(false);
      }, 1000);
    } catch (error) {
      console.error('Error fetching job details:', error);
      setLoading(false);
    }
  };

  const handleChange = (e) => {
    const { name, value, type, files } = e.target;
    if (type === 'file') {
      setFormData(prev => ({
        ...prev,
        [name]: files[0]
      }));
    } else {
      setFormData(prev => ({
        ...prev,
        [name]: value
      }));
    }
    
    // Clear error when currentUser starts typing
    if (errors[name]) {
      setErrors(prev => ({
        ...prev,
        [name]: ''
      }));
    }
  };

  const validateForm = () => {
    const newErrors = {};
    
    if (!formData.coverLetter.trim()) {
      newErrors.coverLetter = 'Cover letter is required';
    } else if (formData.coverLetter.trim().length < 100) {
      newErrors.coverLetter = 'Cover letter must be at least 100 characters';
    }
    
    if (!formData.resume) {
      newErrors.resume = 'Resume is required';
    }
    
    if (!formData.expectedSalary.trim()) {
      newErrors.expectedSalary = 'Expected salary is required';
    }
    
    if (!formData.availabilityDate) {
      newErrors.availabilityDate = 'Availability date is required';
    }
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (!validateForm()) {
      return;
    }
    
    setSubmitting(true);
    
    try {
      // Mock application submission - replace with actual API call
      const applicationData = {
        jobId: job.id,
        currentUserId: currentUser.id,
        ...formData,
        submittedAt: new Date().toISOString()
      };
      
      console.log('Submitting application:', applicationData);
      
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 2000));
      
      // Redirect to applications page with success message
      navigate('/jobseeker/applications', { 
        state: { 
          message: `Application submitted successfully for ${job.title}!` 
        } 
      });
    } catch (error) {
      console.error('Error submitting application:', error);
      setErrors({ submit: 'Failed to submit application. Please try again.' });
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="apply-job-container">
        <div className="loading-spinner">
          <div className="spinner"></div>
          <p>Loading job details...</p>
        </div>
      </div>
    );
  }

  if (!job) {
    return (
      <div className="apply-job-container">
        <div className="error-message">
          <h2>Job not found</h2>
          <p>The job you're trying to apply for doesn't exist or has been removed.</p>
          <button onClick={() => navigate('/jobs')} className="btn-primary">
            Browse Jobs
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="apply-job-container">
      <div className="job-summary">
        <div className="job-header">
          <div className="job-info">
            <h1>{job.title}</h1>
            <p className="company-name">{job.company}</p>
            <div className="job-meta">
              <span>📍 {job.location}</span>
              <span>⏰ {job.type}</span>
              <span>💰 {job.salary}</span>
            </div>
          </div>
          <div className="company-logo">
            <img src={job.companyLogo} alt={job.company} />
          </div>
        </div>
        
        <div className="application-deadline">
          <strong>Application Deadline:</strong> {new Date(job.applicationDeadline).toLocaleDateString()}
        </div>
      </div>

      <form onSubmit={handleSubmit} className="application-form">
        <div className="form-section">
          <h2>Application Details</h2>
          
          <div className="form-group">
            <label htmlFor="coverLetter">Cover Letter *</label>
            <textarea
              id="coverLetter"
              name="coverLetter"
              value={formData.coverLetter}
              onChange={handleChange}
              rows="8"
              placeholder="Write a compelling cover letter explaining why you're the perfect fit for this position..."
              className={errors.coverLetter ? 'error' : ''}
            />
            {errors.coverLetter && <span className="error-text">{errors.coverLetter}</span>}
            <small className="char-count">
              {formData.coverLetter.length} characters (minimum 100 required)
            </small>
          </div>

          <div className="form-group">
            <label htmlFor="resume">Resume *</label>
            <input
              type="file"
              id="resume"
              name="resume"
              onChange={handleChange}
              accept=".pdf,.doc,.docx"
              className={errors.resume ? 'error' : ''}
            />
            {errors.resume && <span className="error-text">{errors.resume}</span>}
            <small>Supported formats: PDF, DOC, DOCX (Max 10MB)</small>
          </div>

          <div className="form-group">
            <label htmlFor="portfolioUrl">Portfolio URL (Optional)</label>
            <input
              type="url"
              id="portfolioUrl"
              name="portfolioUrl"
              value={formData.portfolioUrl}
              onChange={handleChange}
              placeholder="https://your-portfolio.com"
            />
            <small>Link to your online portfolio, GitHub, or LinkedIn profile</small>
          </div>

          <div className="form-row">
            <div className="form-group">
              <label htmlFor="expectedSalary">Expected Salary *</label>
              <input
                type="text"
                id="expectedSalary"
                name="expectedSalary"
                value={formData.expectedSalary}
                onChange={handleChange}
                placeholder="RWF 800,000"
                className={errors.expectedSalary ? 'error' : ''}
              />
              {errors.expectedSalary && <span className="error-text">{errors.expectedSalary}</span>}
            </div>
            
            <div className="form-group">
              <label htmlFor="availabilityDate">Available From *</label>
              <input
                type="date"
                id="availabilityDate"
                name="availabilityDate"
                value={formData.availabilityDate}
                onChange={handleChange}
                min={new Date().toISOString().split('T')[0]}
                className={errors.availabilityDate ? 'error' : ''}
              />
              {errors.availabilityDate && <span className="error-text">{errors.availabilityDate}</span>}
            </div>
          </div>

          <div className="form-group">
            <label htmlFor="additionalInfo">Additional Information (Optional)</label>
            <textarea
              id="additionalInfo"
              name="additionalInfo"
              value={formData.additionalInfo}
              onChange={handleChange}
              rows="4"
              placeholder="Any additional information you'd like to share..."
            />
          </div>
        </div>

        {errors.submit && (
          <div className="error-message">
            {errors.submit}
          </div>
        )}

        <div className="form-actions">
          <button 
            type="button" 
            onClick={() => navigate(`/jobs/${jobId}`)}
            className="btn-secondary"
          >
            Cancel
          </button>
          <button 
            type="submit" 
            disabled={submitting}
            className="btn-primary"
          >
            {submitting ? 'Submitting Application...' : 'Submit Application'}
          </button>
        </div>
      </form>

      <div className="application-tips">
        <h3>💡 Application Tips</h3>
        <ul>
          <li>Tailor your cover letter to match the job requirements</li>
          <li>Highlight relevant skills and experiences</li>
          <li>Keep your resume updated and well-formatted</li>
          <li>Research the company before applying</li>
          <li>Be honest about your salary expectations</li>
        </ul>
      </div>
    </div>
  );
};

export default ApplyJob;