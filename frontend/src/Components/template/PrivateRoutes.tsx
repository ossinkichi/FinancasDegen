import { useEffect } from 'react';
import { Outlet, useNavigate } from 'react-router-dom';
import { useAuth } from './AuthContext';

const PrivateRoute: React.FC<{ requiresAssociation?: boolean }> = ({ requiresAssociation = false }) => {
  const navigate = useNavigate();
  const { isAuthenticated, isAssociated } = useAuth();

  useEffect(() => {
    if (!isAuthenticated) {
      navigate('/auth');
    } else if (requiresAssociation && !isAssociated) {
      navigate('/auth/company');
    }
  }, [isAuthenticated, isAssociated, navigate, requiresAssociation]);

  return isAuthenticated && (!requiresAssociation || isAssociated) ? <Outlet /> : null;
};

export default PrivateRoute;
