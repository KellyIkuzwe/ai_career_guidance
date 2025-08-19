import React from 'react';
import { Navigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';

function CompanyRoute({ children }) {
  const { currentUser, isCompany, isLoading } = useAuth();

  if (isLoading) {
    return <div>Loading...</div>;
  }

  if (!currentUser || !isCompany) {
    return <Navigate to="/" />;
  }

  return children;
}

export default CompanyRoute;