import "./App.css";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";

// Autenticação e restrições de acesso
import PrivateRoutes from "./Components/template/PrivateRoutes";
import { AuthProvider } from "./Components/template/AuthContext";

// Importando Paginas Para As Rotas
import Home from "./pages/Home";
import LoginOrRegister from "./pages/Auth/LoginOrRegister";
import Dashboard from "./pages/Dashboard/Dashboard";
import NotFound from "./pages/NotFound";
import NoCompany from "./pages/NoCompany/NoCompany";

function App() {
  return (
    <AuthProvider>
      <Router>
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/auth" element={<LoginOrRegister />} />
          <Route path="/auth/company" element={<NoCompany />} />

          <Route element={<PrivateRoutes requiresAssociation={true} />}>
            <Route path="/dashboard" element={<Dashboard />} />
          </Route>

          <Route path="*" element={<NotFound />} />
        </Routes>
      </Router>
    </AuthProvider>
  );
}

export default App;
