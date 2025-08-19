import React from 'react';
import { Routes, Route } from 'react-router-dom';
import { useAuth } from './context/AuthContext';

// Layout Components
import Navbar from './components/layout/Navbar';
import Footer from './components/layout/Footer';

// Public Pages
import Home from './pages/Home';
import Login from './pages/Login';
import Register from './pages/Register';
import JobListings from './pages/JobListings';
import JobDetail from './pages/JobDetail';

// Protected Pages - Job Seeker
import JobSeekerDashboard from './pages/jobseeker/Dashboard';
import Profile from './pages/jobseeker/Profile';
import MyApplications from './pages/jobseeker/MyApplications';
import ApplyJob from './pages/jobseeker/ApplyJob';
import AIMatches from './pages/jobseeker/AIMatches';

// Protected Pages - Company
import CompanyDashboard from './pages/company/Dashboard';
import ManageJobs from './pages/company/ManageJobs';
import PostJob from './pages/company/PostJob';
import EditJob from './pages/company/EditJob';
import Applications from './pages/company/Applications';

// Route Protection Components
import PrivateRoute from './components/routes/PrivateRoute';
import JobSeekerRoute from './components/routes/JobSeekerRoute';
import CompanyRoute from './components/routes/CompanyRoute';

function App() {
  const { isLoading } = useAuth();

  if (isLoading) {
    return (
      <div className="d-flex justify-content-center align-items-center" style={{ height: '100vh' }}>
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  return (
    <>
      <Navbar />
      <main className="container py-4">
        <Routes>
          {/* Public Routes */}
          <Route path="/" element={<Home />} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="/jobs" element={<JobListings />} />
          <Route path="/jobs/:id" element={<JobDetail />} />

          {/* Job Seeker Routes */}
          <Route path="/dashboard" element={
            <PrivateRoute>
              <JobSeekerRoute>
                <JobSeekerDashboard />
              </JobSeekerRoute>
            </PrivateRoute>
          } />
          <Route path="/profile" element={
            <PrivateRoute>
              <JobSeekerRoute>
                <Profile />
              </JobSeekerRoute>
            </PrivateRoute>
          } />
          <Route path="/my-applications" element={
            <PrivateRoute>
              <JobSeekerRoute>
                <MyApplications />
              </JobSeekerRoute>
            </PrivateRoute>
          } />
          <Route path="/apply/:id" element={
            <PrivateRoute>
              <JobSeekerRoute>
                <ApplyJob />
              </JobSeekerRoute>
            </PrivateRoute>
          } />
          <Route path="/ai-matches" element={
            <PrivateRoute>
              <JobSeekerRoute>
                <AIMatches />
              </JobSeekerRoute>
            </PrivateRoute>
          } />

          {/* Company Routes */}
          <Route path="/company/dashboard" element={
            <PrivateRoute>
              <CompanyRoute>
                <CompanyDashboard />
              </CompanyRoute>
            </PrivateRoute>
          } />
          <Route path="/company/manage-jobs" element={
            <PrivateRoute>
              <CompanyRoute>
                <ManageJobs />
              </CompanyRoute>
            </PrivateRoute>
          } />
          <Route path="/company/post-job" element={
            <PrivateRoute>
              <CompanyRoute>
                <PostJob />
              </CompanyRoute>
            </PrivateRoute>
          } />
          <Route path="/company/edit-job/:id" element={
            <PrivateRoute>
              <CompanyRoute>
                <EditJob />
              </CompanyRoute>
            </PrivateRoute>
          } />
          <Route path="/company/applications/:jobId" element={
            <PrivateRoute>
              <CompanyRoute>
                <Applications />
              </CompanyRoute>
            </PrivateRoute>
          } />
        </Routes>
      </main>
      <Footer />
    </>
  );
}

export default App;