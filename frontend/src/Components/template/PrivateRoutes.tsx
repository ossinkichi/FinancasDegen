import React from 'react';
import { Outlet, Navigate } from 'react-router-dom';
import { useAuth } from './AuthContext';

const PrivateRoute: React.FC = () => {
  const { isAuthenticated, isAssociated } = useAuth();
  if(!isAssociated) return <Navigate to="/company" />

  return isAuthenticated ? <Outlet /> : <Navigate to="/auth" />
};

export default PrivateRoute;
