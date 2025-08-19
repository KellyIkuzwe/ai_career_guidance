import React, { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import { Link } from 'react-router-dom';
import './MyApplications.css';

const MyApplications = () => {
  const { currentUser } = useAuth();
  const [applications, setApplications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState('all');

  useEffect(() => {
    fetchApplications();
  }, []);

  const fetchApplications = async () => {
    setLoading(true);
    try {
      // Mock data - replace with actual API call
      const mockApplications = [
        {
          id: 1,
          jobId: 101,
          jobTitle: 'Software Developer',
          company: 'Tech Rwanda Ltd',
          appliedDate: '2024-01-15',
          status: 'pending',
          location: 'Kigali, Rwanda',
          salary: 'RWF 800,000 - 1,200,000'
        },
        {
          id: 2,
          jobId: 102,
          jobTitle: 'Data Analyst',
          company: 'Analytics Pro',
          appliedDate: '2024-01-10',
          status: 'interviewed',
          location: 'Kigali, Rwanda',
          salary: 'RWF 600,000 - 900,000'
        },
        {
          id: 3,
          jobId: 103,
          jobTitle: 'UI/UX Designer',
          company: 'Design Studio',
          appliedDate: '2024-01-08',
          status: 'rejected',
          location: 'Kigali, Rwanda',
          salary: 'RWF 500,000 - 800,000'
        }
      ];
      
      setTimeout(() => {
        setApplications(mockApplications);
        setLoading(false);
      }, 1000);
    } catch (error) {
      console.error('Error fetching applications:', error);
      setLoading(false);
    }
  };

  const getStatusColor = (status) => {
    switch (status) {
      case 'pending': return '#f39c12';
      case 'interviewed': return '#3498db';
      case 'accepted': return '#27ae60';
      case 'rejected': return '#e74c3c';
      default: return '#95a5a6';
    }
  };

  const getStatusText = (status) => {
    switch (status) {
      case 'pending': return 'Under Review';
      case 'interviewed': return 'Interviewed';
      case 'accepted': return 'Accepted';
      case 'rejected': return 'Rejected';
      default: return status;
    }
  };

  const filteredApplications = applications.filter(app => {
    if (filter === 'all') return true;
    return app.status === filter;
  });

  const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  if (loading) {
    return (
      <div className="applications-container">
        <div className="loading-spinner">
          <div className="spinner"></div>
          <p>Loading your applications...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="applications-container">
      <div className="applications-header">
        <h1>My Job Applications</h1>
        <p>Track the status of your job applications</p>
      </div>

      <div className="applications-filters">
        <div className="filter-tabs">
          <button
            className={filter === 'all' ? 'active' : ''}
            onClick={() => setFilter('all')}
          >
            All ({applications.length})
          </button>
          <button
            className={filter === 'pending' ? 'active' : ''}
            onClick={() => setFilter('pending')}
          >
            Pending ({applications.filter(app => app.status === 'pending').length})
          </button>
          <button
            className={filter === 'interviewed' ? 'active' : ''}
            onClick={() => setFilter('interviewed')}
          >
            Interviewed ({applications.filter(app => app.status === 'interviewed').length})
          </button>
          <button
            className={filter === 'accepted' ? 'active' : ''}
            onClick={() => setFilter('accepted')}
          >
            Accepted ({applications.filter(app => app.status === 'accepted').length})
          </button>
          <button
            className={filter === 'rejected' ? 'active' : ''}
            onClick={() => setFilter('rejected')}
          >
            Rejected ({applications.filter(app => app.status === 'rejected').length})
          </button>
        </div>
      </div>

      <div className="applications-list">
        {filteredApplications.length === 0 ? (
          <div className="no-applications">
            <div className="no-applications-icon">📄</div>
            <h3>No applications found</h3>
            <p>
              {filter === 'all' 
                ? "You haven't applied to any jobs yet."
                : `No applications with status "${filter}".`
              }
            </p>
            <Link to="/jobs" className="btn-primary">
              Browse Jobs
            </Link>
          </div>
        ) : (
          filteredApplications.map(application => (
            <div key={application.id} className="application-card">
              <div className="application-info">
                <div className="job-details">
                  <h3 className="job-title">
                    <Link to={`/jobs/${application.jobId}`}>
                      {application.jobTitle}
                    </Link>
                  </h3>
                  <p className="company-name">{application.company}</p>
                  <div className="job-meta">
                    <span className="location">📍 {application.location}</span>
                    <span className="salary">💰 {application.salary}</span>
                  </div>
                </div>
                <div className="application-meta">
                  <div className="applied-date">
                    Applied on {formatDate(application.appliedDate)}
                  </div>
                  <div 
                    className="status-badge"
                    style={{ backgroundColor: getStatusColor(application.status) }}
                  >
                    {getStatusText(application.status)}
                  </div>
                </div>
              </div>
              <div className="application-actions">
                <Link 
                  to={`/jobs/${application.jobId}`}
                  className="btn-secondary"
                >
                  View Job
                </Link>
                {application.status === 'pending' && (
                  <button className="btn-outline">
                    Withdraw
                  </button>
                )}
              </div>
            </div>
          ))
        )}
      </div>

      {filteredApplications.length > 0 && (
        <div className="applications-summary">
          <div className="summary-stats">
            <div className="stat">
              <span className="stat-number">{applications.length}</span>
              <span className="stat-label">Total Applications</span>
            </div>
            <div className="stat">
              <span className="stat-number">
                {applications.filter(app => app.status === 'interviewed').length}
              </span>
              <span className="stat-label">Interviews</span>
            </div>
            <div className="stat">
              <span className="stat-number">
                {applications.filter(app => app.status === 'accepted').length}
              </span>
              <span className="stat-label">Offers</span>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default MyApplications;