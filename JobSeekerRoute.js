import React from 'react';
import { Navigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';

function JobSeekerRoute({ children }) {
  const { currentUser, isJobSeeker, isLoading } = useAuth();

  if (isLoading) {
    return <div>Loading...</div>;
  }

  if (!currentUser || !isJobSeeker) {
    return <Navigate to="/" />;
  }

  return children;
}

export default JobSeekerRoute;