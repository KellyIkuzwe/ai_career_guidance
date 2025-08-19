import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { Plus, Edit, Trash2, Eye, ToggleLeft, ToggleRight, MapPin, DollarSign, Users } from "lucide-react";

export default function ManageJobs() {
  const [jobs, setJobs] = useState([]);
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

  // Fetch jobs from localStorage
  useEffect(() => {
    const fetchJobs = () => {
      try {
        const storedJobs = getJobsFromStorage();
        setJobs(storedJobs);
      } catch (err) {
        console.error("Failed to fetch jobs:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchJobs();
  }, []);

  // Delete a job
  const deleteJob = async (jobId) => {
    if (!window.confirm("Are you sure you want to delete this job?")) return;
    try {
      const updatedJobs = jobs.filter((job) => job.id !== jobId);
      setJobs(updatedJobs);
      saveJobsToStorage(updatedJobs);
    } catch (err) {
      console.error("Failed to delete job:", err);
    }
  };

  // Toggle job status
  const toggleJobStatus = async (jobId, currentStatus) => {
    const newStatus = currentStatus === "active" ? "inactive" : "active";
    try {
      const updatedJobs = jobs.map((job) =>
        job.id === jobId ? { ...job, status: newStatus } : job
      );
      setJobs(updatedJobs);
      saveJobsToStorage(updatedJobs);
    } catch (err) {
      console.error("Failed to update job status:", err);
    }
  };

  const getStatusBadge = (status) => {
    const statusClasses = {
      'active': 'bg-green-100 text-green-800',
      'inactive': 'bg-gray-100 text-gray-800',
      'draft': 'bg-yellow-100 text-yellow-800'
    };
    
    return `inline-flex px-2 py-1 text-xs font-medium rounded-full ${statusClasses[status] || statusClasses.inactive}`;
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold text-gray-900">Manage Job Postings</h1>
          <p className="text-gray-600 mt-2">View and manage all your job postings</p>
        </div>
        <button
          onClick={() => navigate("/company/post-job")}
          className="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center"
        >
          <Plus className="h-5 w-5 mr-2" />
          Post New Job
        </button>
      </div>

      {/* Jobs Grid */}
      {jobs.length === 0 ? (
        <div className="text-center py-12 bg-white rounded-lg shadow-sm border">
          <Briefcase className="h-12 w-12 text-gray-400 mx-auto mb-4" />
          <h3 className="text-lg font-medium text-gray-900 mb-2">No jobs posted yet</h3>
          <p className="text-gray-600 mb-6">Get started by posting your first job opening</p>
          <button
            onClick={() => navigate("/company/post-job")}
            className="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center"
          >
            <Plus className="h-5 w-5 mr-2" />
            Post Your First Job
          </button>
        </div>
      ) : (
        <div className="grid grid-cols-1 gap-6">
          {jobs.map((job) => (
            <div key={job.id} className="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
              <div className="flex justify-between items-start">
                <div className="flex-1">
                  <div className="flex items-start justify-between mb-3">
                    <div>
                      <h3 className="text-xl font-semibold text-gray-900">{job.title}</h3>
                      <p className="text-gray-600">{job.company}</p>
                    </div>
                    <span className={getStatusBadge(job.status)}>
                      {job.status.charAt(0).toUpperCase() + job.status.slice(1)}
                    </span>
                  </div>

                  <div className="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-4">
                    <div className="flex items-center">
                      <MapPin className="h-4 w-4 mr-1" />
                      {job.location}
                    </div>
                    {job.salaryMin && job.salaryMax && (
                      <div className="flex items-center">
                        <DollarSign className="h-4 w-4 mr-1" />
                        {job.salaryMin.toLocaleString()} - {job.salaryMax.toLocaleString()} {job.currency}
                      </div>
                    )}
                    <div className="flex items-center">
                      <Users className="h-4 w-4 mr-1" />
                      {job.applicationsCount || 0} applications
                    </div>
                    <div>
                      Posted: {new Date(job.createdAt).toLocaleDateString()}
                    </div>
                  </div>

                  <div className="flex flex-wrap gap-2 mb-4">
                    <span className="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                      {job.type}
                    </span>
                    <span className="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
                      {job.experienceLevel}
                    </span>
                    <span className="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                      {job.category}
                    </span>
                    {job.remote && (
                      <span className="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full">
                        Remote
                      </span>
                    )}
                  </div>

                  <p className="text-gray-700 text-sm line-clamp-2">
                    {job.description}
                  </p>
                </div>
              </div>

              {/* Action Buttons */}
              <div className="flex justify-end items-center space-x-2 mt-4 pt-4 border-t">
                <button
                  onClick={() => navigate(`/company/edit-job/${job.id}`)}
                  className="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                  title="Edit job"
                >
                  <Edit className="h-5 w-5" />
                </button>

                <button
                  onClick={() => toggleJobStatus(job.id, job.status)}
                  className="p-2 text-yellow-600 hover:bg-yellow-100 rounded-lg transition-colors"
                  title="Toggle job status"
                >
                  {job.status === "active" ? (
                    <ToggleRight className="h-5 w-5" />
                  ) : (
                    <ToggleLeft className="h-5 w-5" />
                  )}
                </button>

                <button
                  onClick={() => navigate(`/company/applications/${job.id}`)}
                  className="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors"
                  title="View applications"
                >
                  <Eye className="h-5 w-5" />
                </button>

                <button
                  onClick={() => deleteJob(job.id)}
                  className="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors"
                  title="Delete job"
                >
                  <Trash2 className="h-5 w-5" />
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}