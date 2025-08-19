import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import './JobListings.css';

const JobListings = () => {
  const { currentUser } = useAuth();
  const [jobs, setJobs] = useState([]);
  const [filteredJobs, setFilteredJobs] = useState([]);
  const [loading, setLoading] = useState(true);
  const [filters, setFilters] = useState({
    search: '',
    location: '',
    jobType: '',
    salaryRange: '',
    company: ''
  });
  const [sortBy, setSortBy] = useState('newest');
  const [currentPage, setCurrentPage] = useState(1);
  const jobsPerPage = 12;

  useEffect(() => {
    fetchJobs();
  }, []);

  useEffect(() => {
    applyFilters();
  }, [jobs, filters, sortBy]);

  const fetchJobs = async () => {
    setLoading(true);
    try {
      // Mock job data - replace with actual API call
      const mockJobs = [
        {
          id: 1,
          title: 'Senior Software Developer',
          company: 'Tech Rwanda Ltd',
          location: 'Kigali, Rwanda',
          type: 'Full-time',
          remote: false,
          salary: 'RWF 800,000 - 1,200,000',
          description: 'We are looking for a senior software developer to join our growing team...',
          skills: ['JavaScript', 'React', 'Node.js', 'AWS'],
          postedDate: '2024-01-15',
          applicationDeadline: '2024-02-15',
          companyLogo: '/api/placeholder/60/60',
          featured: true
        },
        {
          id: 2,
          title: 'Data Analyst',
          company: 'Analytics Pro',
          location: 'Kigali, Rwanda',
          type: 'Full-time',
          remote: true,
          salary: 'RWF 600,000 - 900,000',
          description: 'Join our data team to help businesses make data-driven decisions...',
          skills: ['Python', 'SQL', 'Tableau', 'Excel'],
          postedDate: '2024-01-14',
          applicationDeadline: '2024-02-14',
          companyLogo: '/api/placeholder/60/60',
          featured: false
        },
        {
          id: 3,
          title: 'UI/UX Designer',
          company: 'Design Studio Rwanda',
          location: 'Kigali, Rwanda',
          type: 'Part-time',
          remote: false,
          salary: 'RWF 400,000 - 600,000',
          description: 'Create beautiful and intuitive currentUser interfaces for web and mobile apps...',
          skills: ['Figma', 'Adobe XD', 'Sketch', 'Prototyping'],
          postedDate: '2024-01-13',
          applicationDeadline: '2024-02-13',
          companyLogo: '/api/placeholder/60/60',
          featured: false
        },
        {
          id: 4,
          title: 'Digital Marketing Manager',
          company: 'Marketing Hub',
          location: 'Kigali, Rwanda',
          type: 'Full-time',
          remote: true,
          salary: 'RWF 700,000 - 1,000,000',
          description: 'Lead our digital marketing efforts across multiple channels...',
          skills: ['SEO', 'Google Ads', 'Social Media', 'Analytics'],
          postedDate: '2024-01-12',
          applicationDeadline: '2024-02-12',
          companyLogo: '/api/placeholder/60/60',
          featured: true
        },
        {
          id: 5,
          title: 'Project Manager',
          company: 'Business Solutions Ltd',
          location: 'Kigali, Rwanda',
          type: 'Contract',
          remote: false,
          salary: 'RWF 900,000 - 1,300,000',
          description: 'Manage complex projects and coordinate cross-functional teams...',
          skills: ['Agile', 'Scrum', 'Project Management', 'Leadership'],
          postedDate: '2024-01-11',
          applicationDeadline: '2024-02-11',
          companyLogo: '/api/placeholder/60/60',
          featured: false
        },
        {
          id: 6,
          title: 'Mobile App Developer',
          company: 'Mobile Innovations',
          location: 'Kigali, Rwanda',
          type: 'Full-time',
          remote: true,
          salary: 'RWF 750,000 - 1,100,000',
          description: 'Develop cutting-edge mobile applications for iOS and Android...',
          skills: ['React Native', 'Flutter', 'iOS', 'Android'],
          postedDate: '2024-01-10',
          applicationDeadline: '2024-02-10',
          companyLogo: '/api/placeholder/60/60',
          featured: false
        }
      ];
      
      setTimeout(() => {
        setJobs(mockJobs);
        setLoading(false);
      }, 1000);
    } catch (error) {
      console.error('Error fetching jobs:', error);
      setLoading(false);
    }
  };

  const applyFilters = () => {
    let filtered = [...jobs];

    // Search filter
    if (filters.search) {
      const searchTerm = filters.search.toLowerCase();
      filtered = filtered.filter(job =>
        job.title.toLowerCase().includes(searchTerm) ||
        job.company.toLowerCase().includes(searchTerm) ||
        job.description.toLowerCase().includes(searchTerm) ||
        job.skills.some(skill => skill.toLowerCase().includes(searchTerm))
      );
    }

    // Location filter
    if (filters.location) {
      filtered = filtered.filter(job =>
        job.location.toLowerCase().includes(filters.location.toLowerCase())
      );
    }

    // Job type filter
    if (filters.jobType) {
      filtered = filtered.filter(job => job.type === filters.jobType);
    }

    // Company filter
    if (filters.company) {
      filtered = filtered.filter(job =>
        job.company.toLowerCase().includes(filters.company.toLowerCase())
      );
    }

    // Salary range filter
    if (filters.salaryRange) {
      // Simple salary filtering logic - can be enhanced
      filtered = filtered.filter(job => {
        const salary = job.salary.toLowerCase();
        switch (filters.salaryRange) {
          case 'under-500k':
            return salary.includes('400,000') || salary.includes('300,000');
          case '500k-800k':
            return salary.includes('600,000') || salary.includes('700,000') || salary.includes('750,000');
          case '800k-1200k':
            return salary.includes('800,000') || salary.includes('900,000') || salary.includes('1,000,000');
          case 'over-1200k':
            return salary.includes('1,200,000') || salary.includes('1,300,000');
          default:
            return true;
        }
      });
    }

    // Sort jobs
    filtered.sort((a, b) => {
      switch (sortBy) {
        case 'newest':
          return new Date(b.postedDate) - new Date(a.postedDate);
        case 'oldest':
          return new Date(a.postedDate) - new Date(b.postedDate);
        case 'title':
          return a.title.localeCompare(b.title);
        case 'company':
          return a.company.localeCompare(b.company);
        case 'featured':
          return b.featured - a.featured;
        default:
          return 0;
      }
    });

    setFilteredJobs(filtered);
    setCurrentPage(1);
  };

  const handleFilterChange = (e) => {
    const { name, value } = e.target;
    setFilters(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const clearFilters = () => {
    setFilters({
      search: '',
      location: '',
      jobType: '',
      salaryRange: '',
      company: ''
    });
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) return '1 day ago';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
    return date.toLocaleDateString();
  };

  // Pagination
  const totalPages = Math.ceil(filteredJobs.length / jobsPerPage);
  const startIndex = (currentPage - 1) * jobsPerPage;
  const currentJobs = filteredJobs.slice(startIndex, startIndex + jobsPerPage);

  const renderPagination = () => {
    if (totalPages <= 1) return null;

    const pages = [];
    for (let i = 1; i <= totalPages; i++) {
      pages.push(
        <button
          key={i}
          onClick={() => setCurrentPage(i)}
          className={`page-btn ${currentPage === i ? 'active' : ''}`}
        >
          {i}
        </button>
      );
    }

    return (
      <div className="pagination">
        <button
          onClick={() => setCurrentPage(prev => Math.max(prev - 1, 1))}
          disabled={currentPage === 1}
          className="page-btn"
        >
          ← Previous
        </button>
        {pages}
        <button
          onClick={() => setCurrentPage(prev => Math.min(prev + 1, totalPages))}
          disabled={currentPage === totalPages}
          className="page-btn"
        >
          Next →
        </button>
      </div>
    );
  };

  if (loading) {
    return (
      <div className="job-listings-container">
        <div className="loading-spinner">
          <div className="spinner"></div>
          <p>Loading job opportunities...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="job-listings-container">
      <div className="listings-header">
        <h1>Job Opportunities in Rwanda</h1>
        <p>Discover your next career opportunity with leading companies in Rwanda</p>
      </div>

      <div className="filters-section">
        <div className="filters-grid">
          <div className="filter-group">
            <input
              type="text"
              name="search"
              placeholder="Search jobs, companies, skills..."
              value={filters.search}
              onChange={handleFilterChange}
              className="search-input"
            />
          </div>
          
          <div className="filter-group">
            <select
              name="location"
              value={filters.location}
              onChange={handleFilterChange}
            >
              <option value="">All Locations</option>
              <option value="Kigali">Kigali</option>
              <option value="Butare">Butare</option>
              <option value="Musanze">Musanze</option>
              <option value="Remote">Remote</option>
            </select>
          </div>
          
          <div className="filter-group">
            <select
              name="jobType"
              value={filters.jobType}
              onChange={handleFilterChange}
            >
              <option value="">All Job Types</option>
              <option value="Full-time">Full-time</option>
              <option value="Part-time">Part-time</option>
              <option value="Contract">Contract</option>
              <option value="Freelance">Freelance</option>
              <option value="Internship">Internship</option>
            </select>
          </div>
          
          <div className="filter-group">
            <select
              name="salaryRange"
              value={filters.salaryRange}
              onChange={handleFilterChange}
            >
              <option value="">All Salary Ranges</option>
              <option value="under-500k">Under RWF 500,000</option>
              <option value="500k-800k">RWF 500,000 - 800,000</option>
              <option value="800k-1200k">RWF 800,000 - 1,200,000</option>
              <option value="over-1200k">Over RWF 1,200,000</option>
            </select>
          </div>
        </div>
        
        <div className="filter-actions">
          <button onClick={clearFilters} className="clear-filters-btn">
            Clear Filters
          </button>
          <select
            value={sortBy}
            onChange={(e) => setSortBy(e.target.value)}
            className="sort-select"
          >
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="title">Title A-Z</option>
            <option value="company">Company A-Z</option>
            <option value="featured">Featured First</option>
          </select>
        </div>
      </div>

      <div className="results-info">
        <p>
          Showing {startIndex + 1}-{Math.min(startIndex + jobsPerPage, filteredJobs.length)} of {filteredJobs.length} jobs
          {filters.search && ` for "${filters.search}"`}
        </p>
      </div>

      <div className="jobs-grid">
        {currentJobs.length === 0 ? (
          <div className="no-jobs">
            <div className="no-jobs-icon">🔍</div>
            <h3>No jobs found</h3>
            <p>Try adjusting your search criteria or filters</p>
            <button onClick={clearFilters} className="btn-primary">
              Clear All Filters
            </button>
          </div>
        ) : (
          currentJobs.map(job => (
            <div key={job.id} className={`job-card ${job.featured ? 'featured' : ''}`}>
              {job.featured && <div className="featured-badge">Featured</div>}
              
              <div className="job-header">
                <div className="company-logo">
                  <img src={job.companyLogo} alt={job.company} />
                </div>
                <div className="job-meta">
                  <span className="posted-date">{formatDate(job.postedDate)}</span>
                  {job.remote && <span className="remote-badge">Remote</span>}
                </div>
              </div>
              
              <div className="job-content">
                <h3 className="job-title">
                  <Link to={`/jobs/${job.id}`}>{job.title}</Link>
                </h3>
                <p className="company-name">{job.company}</p>
                <p className="job-location">📍 {job.location}</p>
                <p className="job-description">{job.description}</p>
                
                <div className="job-skills">
                  {job.skills.slice(0, 3).map(skill => (
                    <span key={skill} className="skill-tag">{skill}</span>
                  ))}
                  {job.skills.length > 3 && (
                    <span className="skill-tag more">+{job.skills.length - 3} more</span>
                  )}
                </div>
              </div>
              
              <div className="job-footer">
                <div className="job-details">
                  <span className="job-type">{job.type}</span>
                  <span className="salary">{job.salary}</span>
                </div>
                <div className="job-actions">
                  <Link to={`/jobs/${job.id}`} className="btn-secondary">
                    View Details
                  </Link>
                  {currentUser && currentUser.role === 'jobseeker' && (
                    <Link to={`/jobs/${job.id}/apply`} className="btn-primary">
                      Apply Now
                    </Link>
                  )}
                </div>
              </div>
            </div>
          ))
        )}
      </div>

      {renderPagination()}
    </div>
  );
};

export default JobListings;