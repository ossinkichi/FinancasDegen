 import {createContext, useContext, useState, ReactNode } from "react";

interface AuthContextProps {
    isAuthenticated: Boolean;
    login: () => void;
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
  
    const login = () => setIsAuthenticated(true);
    const logout = () => setIsAuthenticated(false);
  
    return (
      <AuthContext.Provider value={{ isAuthenticated, login, logout }}>
        {children}
      </AuthContext.Provider>
    );
  };
  