import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { 
  Briefcase, 
  Users, 
  FileText, 
  TrendingUp,
  Plus,
  Settings,
  Eye,
  Building
} from 'lucide-react';

function CompanyDashboard() {
  const { currentUser } = useAuth();
  const [stats, setStats] = useState({
    totalJobs: 0,
    activeJobs: 0,
    totalApplications: 0,
    newApplications: 0
  });
  const [recentJobs, setRecentJobs] = useState([]);
  const [recentApplications, setRecentApplications] = useState([]);

  useEffect(() => {
    // Load data from localStorage
    const jobs = getJobsFromStorage();
    const applications = getApplicationsFromStorage();

    // Calculate stats
    const activeJobs = jobs.filter(job => job.status === 'active');
    const newApplications = applications.filter(app => app.status === 'pending');

    setStats({
      totalJobs: jobs.length,
      activeJobs: activeJobs.length,
      totalApplications: applications.length,
      newApplications: newApplications.length
    });

    // Set recent jobs (last 3)
    setRecentJobs(jobs.slice(-3).reverse());

    // Set recent applications (last 3)
    setRecentApplications(applications.slice(-3).reverse());
  }, []);

  const getStatusBadge = (status) => {
    const statusClasses = {
      'active': 'bg-green-100 text-green-800',
      'inactive': 'bg-gray-100 text-gray-800',
      'pending': 'bg-yellow-100 text-yellow-800',
      'reviewed': 'bg-blue-100 text-blue-800',
      'shortlisted': 'bg-purple-100 text-purple-800',
      'interview': 'bg-indigo-100 text-indigo-800',
      'hired': 'bg-green-100 text-green-800',
      'rejected': 'bg-red-100 text-red-800'
    };
    
    return `inline-flex px-2 py-1 text-xs font-medium rounded-full ${statusClasses[status] || statusClasses.pending}`;
  };

  return (
    <div className="space-y-8">
      {/* Header */}
      <div>
        <h1 className="text-3xl font-bold text-gray-900">Company Dashboard</h1>
        <p className="text-gray-600 mt-2">Welcome back, {currentUser?.companyName || currentUser?.name}!</p>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div className="bg-white rounded-lg shadow-sm border p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600">Total Jobs</p>
              <p className="text-3xl font-bold text-gray-900">{stats.totalJobs}</p>
            </div>
            <div className="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <Briefcase className="h-6 w-6 text-blue-600" />
            </div>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow-sm border p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600">Active Jobs</p>
              <p className="text-3xl font-bold text-gray-900">{stats.activeJobs}</p>
            </div>
            <div className="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
              <TrendingUp className="h-6 w-6 text-green-600" />
            </div>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow-sm border p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600">Total Applications</p>
              <p className="text-3xl font-bold text-gray-900">{stats.totalApplications}</p>
            </div>
            <div className="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
              <Users className="h-6 w-6 text-purple-600" />
            </div>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow-sm border p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm font-medium text-gray-600">New Applications</p>
              <p className="text-3xl font-bold text-gray-900">{stats.newApplications}</p>
            </div>
            <div className="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
              <FileText className="h-6 w-6 text-yellow-600" />
            </div>
          </div>
        </div>
      </div>

      {/* Quick Actions */}
      <div className="bg-white rounded-lg shadow-sm border p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <Link
            to="/company/post-job"
            className="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            <Plus className="h-5 w-5 mr-2" />
            Post New Job
          </Link>
          <Link
            to="/company/manage-jobs"
            className="flex items-center justify-center px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
          >
            <Settings className="h-5 w-5 mr-2" />
            Manage Jobs
          </Link>
          <Link
            to="/company/applications"
            className="flex items-center justify-center px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
          >
            <Eye className="h-5 w-5 mr-2" />
            View Applications
          </Link>
          <Link
            to="/company/profile"
            className="flex items-center justify-center px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
          >
            <Building className="h-5 w-5 mr-2" />
            Company Profile
          </Link>
        </div>
      </div>

      {/* Recent Jobs and Applications */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {/* Recent Jobs */}
        <div className="bg-white rounded-lg shadow-sm border">
          <div className="p-6 border-b flex justify-between items-center">
            <h2 className="text-lg font-semibold text-gray-900">Recent Job Postings</h2>
            <Link
              to="/company/manage-jobs"
              className="text-sm text-blue-600 hover:text-blue-800"
            >
              View All
            </Link>
          </div>
          <div className="p-6">
            {recentJobs.length === 0 ? (
              <p className="text-gray-500 text-center py-4">No jobs posted yet.</p>
            ) : (
              <div className="space-y-4">
                {recentJobs.map(job => (
                  <div key={job.id} className="flex justify-between items-start">
                    <div className="flex-1">
                      <h3 className="font-medium text-gray-900">{job.title}</h3>
                      <p className="text-sm text-gray-600">{job.location}</p>
                      <p className="text-sm text-blue-600">{job.applicationsCount || 0} applicants</p>
                    </div>
                    <div className="text-right">
                      <span className={getStatusBadge(job.status)}>
                        {job.status.charAt(0).toUpperCase() + job.status.slice(1)}
                      </span>
                      <p className="text-xs text-gray-500 mt-1">
                        {new Date(job.createdAt).toLocaleDateString()}
                      </p>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>

        {/* Recent Applications */}
        <div className="bg-white rounded-lg shadow-sm border">
          <div className="p-6 border-b flex justify-between items-center">
            <h2 className="text-lg font-semibold text-gray-900">Recent Applications</h2>
            <Link
              to="/company/applications"
              className="text-sm text-blue-600 hover:text-blue-800"
            >
              View All
            </Link>
          </div>
          <div className="p-6">
            {recentApplications.length === 0 ? (
              <p className="text-gray-500 text-center py-4">No applications yet.</p>
            ) : (
              <div className="space-y-4">
                {recentApplications.map(application => (
                  <div key={application.id} className="flex justify-between items-start">
                    <div className="flex-1">
                      <h3 className="font-medium text-gray-900">{application.applicant?.name}</h3>
                      <p className="text-sm text-gray-600">{application.job?.title}</p>
                      <p className="text-sm text-gray-500">Applied {new Date(application.createdAt).toLocaleDateString()}</p>
                    </div>
                    <div className="text-right">
                      <span className={getStatusBadge(application.status)}>
                        {application.status.charAt(0).toUpperCase() + application.status.slice(1)}
                      </span>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}

export default CompanyDashboard;