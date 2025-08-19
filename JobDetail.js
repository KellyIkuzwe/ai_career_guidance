import React, { useState, useEffect, useContext } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import axios from 'axios';

const JobDetail = () => {
  const { id } = useParams();
  const { currentUser } = useAuth();
  const navigate = useNavigate();
  const [job, setJob] = useState(null);
  const [loading, setLoading] = useState(true);
  const [hasApplied, setHasApplied] = useState(false);
  const [applying, setApplying] = useState(false);

  useEffect(() => {
    fetchJobDetail();
    if (currentUser && currentUser.role === 'jobseeker') {
      checkApplicationStatus();
    }
  }, [id, currentUser]);

  const fetchJobDetail = async () => {
    try {
      const response = await axios.get(`/api/jobs/${id}`);
      setJob(response.data);
    } catch (error) {
      console.error('Error fetching job:', error);
      // Mock data for development
      setJob({
        _id: id,
        title: 'Software Developer',
        company: {
          name: 'Tech Rwanda Ltd',
          logo: '/images/Logo.jpg',
          description: 'Leading technology company in Rwanda focused on digital transformation.',
          website: 'https://techrwanda.com',
          size: '50-100 employees',
          industry: 'Technology'
        },
        location: 'Kigali',
        jobType: 'Full-time',
        category: 'Technology',
        salary: '500,000 - 800,000 RWF',
        description: `We are seeking a talented Software Developer to join our growing team in Kigali. 
        
You will be responsible for developing and maintaining web applications, working closely with our design and product teams to create exceptional currentUser experiences.

Key Responsibilities:
• Develop and maintain responsive web applications using modern JavaScript frameworks
• Collaborate with cross-functional teams to define, design, and ship new features
• Write clean, maintainable, and efficient code
• Participate in code reviews and contribute to team knowledge sharing
• Troubleshoot and debug applications
• Stay up-to-date with emerging technologies and industry trends`,
        requirements: [
          'Bachelor\'s degree in Computer Science or related field',
          '2+ years of experience in web development',
          'Proficiency in JavaScript, HTML5, and CSS3',
          'Experience with React.js or similar frameworks',
          'Knowledge of Node.js and Express.js',
          'Familiarity with database systems (MongoDB, PostgreSQL)',
          'Understanding of version control (Git)',
          'Strong problem-solving skills',
          'Excellent communication skills in English and Kinyarwanda'
        ],
        benefits: [
          'Competitive salary package',
          'Health insurance coverage',
          'Professional development opportunities',
          'Flexible working hours',
          'Modern office environment',
          '25 days annual leave',
          'Transportation allowance',
          'Team building activities'
        ],
        postedAt: new Date(),
        deadline: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000),
        applicationCount: 15
      });
    }
    setLoading(false);
  };

  const checkApplicationStatus = async () => {
    try {
      const response = await axios.get(`/api/applications/check/${id}`);
      setHasApplied(response.data.hasApplied);
    } catch (error) {
      console.error('Error checking application status:', error);
    }
  };

  const handleApply = async () => {
    if (!currentUser) {
      navigate('/login', { state: { from: `/jobs/${id}` } });
      return;
    }

    if (currentUser.role !== 'jobseeker') {
      alert('Only job seekers can apply for jobs');
      return;
    }

    setApplying(true);
    try {
      await axios.post(`/api/applications/${id}`);
      setHasApplied(true);
      alert('Application submitted successfully!');
    } catch (error) {
      console.error('Error applying for job:', error);
      alert('Failed to submit application. Please try again.');
    }
    setApplying(false);
  };

  const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  const daysUntilDeadline = (deadline) => {
    const today = new Date();
    const deadlineDate = new Date(deadline);
    const diffTime = deadlineDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
  };

  if (loading) {
    return (
      <div className="d-flex justify-content-center align-items-center" style={{ minHeight: '400px' }}>
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Loading job details...</span>
        </div>
      </div>
    );
  }

  if (!job) {
    return (
      <div className="text-center py-5">
        <h2>Job Not Found</h2>
        <p>The job you're looking for doesn't exist or has been removed.</p>
        <Link to="/jobs" className="btn btn-primary">Browse Other Jobs</Link>
      </div>
    );
  }

  return (
    <div className="container-fluid">
      <div className="row">
        <div className="col-md-8">
          <div className="card">
            <div className="card-body">
              <div className="d-flex align-items-start mb-4">
                <img 
                  src={job.company.logo} 
                  alt={job.company.name}
                  className="me-3 rounded"
                  style={{ width: '80px', height: '80px', objectFit: 'cover' }}
                />
                <div className="flex-grow-1">
                  <h1 className="h2 mb-1">{job.title}</h1>
                  <h5 className="text-muted mb-2">{job.company.name}</h5>
                  <div className="d-flex flex-wrap gap-3">
                    <span><i className="bi bi-geo-alt"></i> {job.location}</span>
                    <span><i className="bi bi-briefcase"></i> {job.jobType}</span>
                    <span><i className="bi bi-tag"></i> {job.category}</span>
                    {job.salary && <span><i className="bi bi-currency-dollar"></i> {job.salary}</span>}
                  </div>
                </div>
              </div>

              <div className="mb-4">
                <h5>Job Description</h5>
                <div style={{ whiteSpace: 'pre-line' }}>
                  {job.description}
                </div>
              </div>

              {job.requirements && job.requirements.length > 0 && (
                <div className="mb-4">
                  <h5>Requirements</h5>
                  <ul>
                    {job.requirements.map((req, index) => (
                      <li key={index}>{req}</li>
                    ))}
                  </ul>
                </div>
              )}

              {job.benefits && job.benefits.length > 0 && (
                <div className="mb-4">
                  <h5>Benefits</h5>
                  <ul>
                    {job.benefits.map((benefit, index) => (
                      <li key={index}>{benefit}</li>
                    ))}
                  </ul>
                </div>
              )}

              <div className="mb-4">
                <h5>About {job.company.name}</h5>
                <p>{job.company.description}</p>
                <div className="row">
                  <div className="col-md-6">
                    <p><strong>Industry:</strong> {job.company.industry}</p>
                    <p><strong>Company Size:</strong> {job.company.size}</p>
                  </div>
                  <div className="col-md-6">
                    {job.company.website && (
                      <p><strong>Website:</strong> 
                        <a href={job.company.website} target="_blank" rel="noopener noreferrer" className="ms-2">
                          {job.company.website}
                        </a>
                      </p>
                    )}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="col-md-4">
          <div className="card sticky-top" style={{ top: '20px' }}>
            <div className="card-body">
              <div className="text-center mb-4">
                {currentUser && currentUser.role === 'jobseeker' && (
                  <button
                    onClick={handleApply}
                    disabled={hasApplied || applying}
                    className={`btn btn-lg w-100 ${hasApplied ? 'btn-success' : 'btn-primary'}`}
                  >
                    {applying ? (
                      <>
                        <span className="spinner-border spinner-border-sm me-2" role="status"></span>
                        Applying...
                      </>
                    ) : hasApplied ? (
                      'Applied ✓'
                    ) : (
                      'Apply Now'
                    )}
                  </button>
                )}

                {!currentUser && (
                  <div>
                    <Link to="/login" className="btn btn-primary btn-lg w-100 mb-2">
                      Sign in to Apply
                    </Link>
                    <p className="small text-muted">
                      Don't have an account? <Link to="/register">Sign up</Link>
                    </p>
                  </div>
                )}

                {currentUser && currentUser.role === 'company' && (
                  <div className="alert alert-info">
                    <i className="bi bi-info-circle"></i> Company accounts cannot apply for jobs
                  </div>
                )}
              </div>

              <hr />

              <div className="mb-3">
                <h6>Job Information</h6>
                <div className="small">
                  <p className="mb-2">
                    <strong>Posted:</strong> {formatDate(job.postedAt)}
                  </p>
                  <p className="mb-2">
                    <strong>Deadline:</strong> {formatDate(job.deadline)}
                    <span className="text-muted ms-2">
                      ({daysUntilDeadline(job.deadline)} days left)
                    </span>
                  </p>
                  <p className="mb-0">
                    <strong>Applications:</strong> {job.applicationCount}
                  </p>
                </div>
              </div>

              <hr />

              <div>
                <h6>Share this job</h6>
                <div className="d-flex gap-2">
                  <button className="btn btn-outline-secondary btn-sm">
                    <i className="bi bi-facebook"></i>
                  </button>
                  <button className="btn btn-outline-secondary btn-sm">
                    <i className="bi bi-twitter"></i>
                  </button>
                  <button className="btn btn-outline-secondary btn-sm">
                    <i className="bi bi-linkedin"></i>
                  </button>
                  <button 
                    className="btn btn-outline-secondary btn-sm"
                    onClick={() => navigator.clipboard.writeText(window.location.href)}
                  >
                    <i className="bi bi-link-45deg"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default JobDetail;