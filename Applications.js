import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { 
  Search, 
  Filter, 
  Download, 
  Eye, 
  CheckCircle, 
  XCircle,
  Clock,
  Mail,
  Phone,
  MapPin,
  Calendar,
  User,
  FileText,
  Star,
  MoreVertical
} from 'lucide-react';

const Applications = () => {
  const { jobId } = useParams();
  const [applications, setApplications] = useState([]);
  const [filteredApplications, setFilteredApplications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [jobFilter, setJobFilter] = useState('all');
  const [showFilters, setShowFilters] = useState(false);
  const [selectedApplication, setSelectedApplication] = useState(null);
  const [jobs, setJobs] = useState([]);

  useEffect(() => {
    fetchApplications();
    fetchJobs();
  }, [jobId]);

  useEffect(() => {
    filterApplications();
  }, [applications, searchTerm, statusFilter, jobFilter]);

  const fetchApplications = () => {
    try {
      setLoading(true);
      const allApplications = getApplicationsFromStorage();
      
      // Filter by specific job if jobId is provided
      const filteredApps = jobId 
        ? allApplications.filter(app => app.jobId === jobId)
        : allApplications;
      
      setApplications(filteredApps);
    } catch (error) {
      console.error('Error fetching applications:', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchJobs = () => {
    try {
      const allJobs = getJobsFromStorage();
      setJobs(allJobs);
    } catch (error) {
      console.error('Error fetching jobs:', error);
    }
  };

  const filterApplications = () => {
    let filtered = applications;

    // Search filter
    if (searchTerm) {
      filtered = filtered.filter(app =>
        app.applicant?.name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        app.applicant?.email?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        app.job?.title?.toLowerCase().includes(searchTerm.toLowerCase())
      );
    }

    // Status filter
    if (statusFilter !== 'all') {
      filtered = filtered.filter(app => app.status === statusFilter);
    }

    // Job filter
    if (jobFilter !== 'all') {
      filtered = filtered.filter(app => app.jobId === jobFilter);
    }

    setFilteredApplications(filtered);
  };

  const updateApplicationStatus = (applicationId, newStatus) => {
    try {
      const allApplications = getApplicationsFromStorage();
      const updatedApplications = allApplications.map(app => 
        app.id === applicationId ? { ...app, status: newStatus } : app
      );
      
      saveApplicationsToStorage(updatedApplications);
      
      setApplications(applications.map(app => 
        app.id === applicationId ? { ...app, status: newStatus } : app
      ));
    } catch (error) {
      console.error('Error updating application status:', error);
    }
  };

  const downloadResume = (applicationId, filename) => {
    // Mock download functionality
    const link = document.createElement('a');
    link.href = '#';
    link.download = filename || 'resume.pdf';
    link.click();
    
    // In a real app, you would fetch the actual file
    alert(`Downloading ${filename || 'resume.pdf'}...`);
  };

  const getStatusBadge = (status) => {
    const styles = {
      pending: 'bg-yellow-100 text-yellow-800',
      reviewed: 'bg-blue-100 text-blue-800',
      shortlisted: 'bg-green-100 text-green-800',
      interview: 'bg-purple-100 text-purple-800',
      hired: 'bg-green-100 text-green-800',
      rejected: 'bg-red-100 text-red-800'
    };

    return (
      <span className={`inline-flex px-2 py-1 text-xs font-medium rounded-full ${styles[status] || styles.pending}`}>
        {status.charAt(0).toUpperCase() + status.slice(1)}
      </span>
    );
  };

  const ApplicationDetailModal = ({ application, onClose }) => {
    if (!application) return null;

    return (
      <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div className="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
          <div className="p-6 border-b flex justify-between items-center">
            <h2 className="text-xl font-semibold text-gray-900">
              Application Details - {application.applicant?.name}
            </h2>
            <button
              onClick={onClose}
              className="text-gray-500 hover:text-gray-700"
            >
              <XCircle className="h-6 w-6" />
            </button>
          </div>

          <div className="p-6 space-y-6">
            {/* Applicant Info */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h3 className="text-lg font-medium text-gray-900 mb-3">Applicant Information</h3>
                <div className="space-y-3">
                  <div className="flex items-center">
                    <User className="h-5 w-5 text-gray-400 mr-3" />
                    <span>{application.applicant?.name}</span>
                  </div>
                  <div className="flex items-center">
                    <Mail className="h-5 w-5 text-gray-400 mr-3" />
                    <span>{application.applicant?.email}</span>
                  </div>
                  {application.applicant?.phone && (
                    <div className="flex items-center">
                      <Phone className="h-5 w-5 text-gray-400 mr-3" />
                      <span>{application.applicant?.phone}</span>
                    </div>
                  )}
                  {application.applicant?.location && (
                    <div className="flex items-center">
                      <MapPin className="h-5 w-5 text-gray-400 mr-3" />
                      <span>{application.applicant?.location}</span>
                    </div>
                  )}
                </div>
              </div>

              <div>
                <h3 className="text-lg font-medium text-gray-900 mb-3">Job Applied For</h3>
                <div className="space-y-3">
                  <div>
                    <span className="font-medium">{application.job?.title}</span>
                  </div>
                  <div className="text-gray-600">{application.job?.location}</div>
                  <div className="flex items-center">
                    <Calendar className="h-5 w-5 text-gray-400 mr-2" />
                    <span className="text-sm">Applied on {new Date(application.createdAt).toLocaleDateString()}</span>
                  </div>
                </div>
              </div>
            </div>

            {/* Cover Letter */}
            {application.coverLetter && (
              <div>
                <h3 className="text-lg font-medium text-gray-900 mb-3">Cover Letter</h3>
                <div className="bg-gray-50 p-4 rounded-lg">
                  <p className="text-gray-700 whitespace-pre-wrap">{application.coverLetter}</p>
                </div>
              </div>
            )}

            {/* Resume */}
            <div>
              <h3 className="text-lg font-medium text-gray-900 mb-3">Resume</h3>
              <div className="flex items-center space-x-4">
                <FileText className="h-8 w-8 text-red-500" />
                <div>
                  <p className="font-medium">{application.resumeFileName || 'resume.pdf'}</p>
                  <p className="text-sm text-gray-500">
                    Uploaded {new Date(application.createdAt).toLocaleDateString()}
                  </p>
                </div>
                <button
                  onClick={() => downloadResume(application.id, application.resumeFileName)}
                  className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center"
                >
                  <Download className="h-4 w-4 mr-2" />
                  Download
                </button>
              </div>
            </div>

            {/* Status Actions */}
            <div>
              <h3 className="text-lg font-medium text-gray-900 mb-3">Update Status</h3>
              <div className="flex flex-wrap gap-2">
                <button
                  onClick={() => {
                    updateApplicationStatus(application.id, 'reviewed');
                    onClose();
                  }}
                  className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                >
                  Mark as Reviewed
                </button>
                <button
                  onClick={() => {
                    updateApplicationStatus(application.id, 'shortlisted');
                    onClose();
                  }}
                  className="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
                >
                  Shortlist
                </button>
                <button
                  onClick={() => {
                    updateApplicationStatus(application.id, 'interview');
                    onClose();
                  }}
                  className="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700"
                >
                  Schedule Interview
                </button>
                <button
                  onClick={() => {
                    updateApplicationStatus(application.id, 'hired');
                    onClose();
                  }}
                  className="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-800"
                >
                  Hire
                </button>
                <button
                  onClick={() => {
                    updateApplicationStatus(application.id, 'rejected');
                    onClose();
                  }}
                  className="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700"
                >
                  Reject
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-3xl font-bold text-gray-900">
          {jobId ? 'Job Applications' : 'All Applications'}
        </h1>
        <p className="text-gray-600 mt-2">
          {jobId ? 'Review applications for this specific job' : 'Review and manage all job applications'}
        </p>
      </div>

      {/* Search and Filter Bar */}
      <div className="bg-white rounded-lg shadow-sm border p-6">
        <div className="flex flex-col lg:flex-row gap-4">
          {/* Search */}
          <div className="flex-1 relative">
            <Search className="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
            <input
              type="text"
              placeholder="Search by applicant name, email, or job title..."
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>

          {/* Filter Button */}
          <button
            onClick={() => setShowFilters(!showFilters)}
            className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center"
          >
            <Filter className="h-5 w-5 mr-2" />
            Filters
          </button>
        </div>

        {/* Filter Options */}
        {showFilters && (
          <div className="mt-4 pt-4 border-t grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select
                value={statusFilter}
                onChange={(e) => setStatusFilter(e.target.value)}
                className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="reviewed">Reviewed</option>
                <option value="shortlisted">Shortlisted</option>
                <option value="interview">Interview</option>
                <option value="hired">Hired</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>

            {!jobId && (
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Job Position</label>
                <select
                  value={jobFilter}
                  onChange={(e) => setJobFilter(e.target.value)}
                  className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="all">All Jobs</option>
                  {jobs.map(job => (
                    <option key={job.id} value={job.id}>{job.title}</option>
                  ))}
                </select>
              </div>
            )}

            <div className="flex items-end">
              <button
                onClick={() => {
                  setSearchTerm('');
                  setStatusFilter('all');
                  setJobFilter('all');
                }}
                className="px-4 py-2 text-gray-600 hover:text-gray-800"
              >
                Clear Filters
              </button>
            </div>
          </div>
        )}
      </div>

      {/* Applications List */}
      <div className="bg-white rounded-lg shadow-sm border">
        {filteredApplications.length > 0 ? (
          <div className="divide-y">
            {filteredApplications.map((application) => (
              <div key={application.id} className="p-6 hover:bg-gray-50">
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-4">
                    <div className="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                      <User className="h-6 w-6 text-blue-600" />
                    </div>
                    <div>
                      <h3 className="text-lg font-semibold text-gray-900">
                        {application.applicant?.name || 'Unknown Applicant'}
                      </h3>
                      <p className="text-gray-600">{application.job?.title}</p>
                      <div className="flex items-center mt-1 text-sm text-gray-500 space-x-4">
                        <div className="flex items-center">
                          <Calendar className="h-4 w-4 mr-1" />
                          Applied {new Date(application.createdAt).toLocaleDateString()}
                        </div>
                        {application.applicant?.location && (
                          <div className="flex items-center">
                            <MapPin className="h-4 w-4 mr-1" />
                            {application.applicant.location}
                          </div>
                        )}
                      </div>
                    </div>
                  </div>

                  <div className="flex items-center space-x-4">
                    {getStatusBadge(application.status)}
                    
                    <div className="flex items-center space-x-2">
                      <button
                        onClick={() => setSelectedApplication(application)}
                        className="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                        title="View details"
                      >
                        <Eye className="h-5 w-5" />
                      </button>
                      
                      <button
                        onClick={() => downloadResume(application.id, application.resumeFileName)}
                        className="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors"
                        title="Download resume"
                      >
                        <Download className="h-5 w-5" />
                      </button>
                    </div>
                  </div>
                </div>

                {/* Quick Actions */}
                <div className="mt-4 flex items-center justify-between">
                  <div className="flex items-center space-x-2">
                    <Mail className="h-4 w-4 text-gray-400" />
                    <span className="text-sm text-gray-600">{application.applicant?.email}</span>
                  </div>

                  <div className="flex space-x-2">
                    {application.status === 'pending' && (
                      <button
                        onClick={() => updateApplicationStatus(application.id, 'reviewed')}
                        className="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200"
                      >
                        Mark Reviewed
                      </button>
                    )}
                    {(application.status === 'pending' || application.status === 'reviewed') && (
                      <button
                        onClick={() => updateApplicationStatus(application.id, 'shortlisted')}
                        className="px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full hover:bg-green-200"
                      >
                        Shortlist
                      </button>
                    )}
                    <button
                      onClick={() => updateApplicationStatus(application.id, 'rejected')}
                      className="px-3 py-1 text-xs bg-red-100 text-red-800 rounded-full hover:bg-red-200"
                    >
                      Reject
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="text-center py-12">
            <div className="h-24 w-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
              <FileText className="h-8 w-8 text-gray-400" />
            </div>
            <h3 className="text-lg font-medium text-gray-900 mb-2">No applications found</h3>
            <p className="text-gray-600 mb-6">
              {searchTerm || statusFilter !== 'all' || jobFilter !== 'all'
                ? 'Try adjusting your search or filter criteria'
                : 'No applications have been received yet'}
            </p>
          </div>
        )}
      </div>

      {/* Application Detail Modal */}
      {selectedApplication && (
        <ApplicationDetailModal
          application={selectedApplication}
          onClose={() => setSelectedApplication(null)}
        />
      )}
    </div>
  );
};

export default Applications;