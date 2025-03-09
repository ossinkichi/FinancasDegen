 import {createContext, useContext, useState, ReactNode } from "react";

interface AuthContextProps {
    isAuthenticated: Boolean;
    isAssociated: Boolean;
    login: (company: number) => void;
    logout: () => void;
}

const AuthContext = createContext<AuthContextProps | undefined>(undefined)

export const useAuth = () => {
    const context = useContext(AuthContext);
    if(!context){
        throw new Error("Necessário um contexto!!")
    } 
    return context
}

export const AuthProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
    const [isAuthenticated, setIsAuthenticated] = useState(false);
    const [isAssociated, setIsAssociated] = useState(false)
  
    const login = (company: number) => {
      setIsAssociated(!!company)
      setIsAuthenticated(true)
    };
  
    const logout = () => setIsAuthenticated(false);
  
    return (
      <AuthContext.Provider value={{ isAuthenticated, login, isAssociated ,logout }}>
        {children}
      </AuthContext.Provider>
    );
  };
  