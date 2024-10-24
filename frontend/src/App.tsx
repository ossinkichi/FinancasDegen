import "./App.css";
import { BrowserRouter as Router, Routes, Route} from "react-router-dom";

import PrivateRoutes from "./Components/template/PrivateRoutes"
import {AuthProvider} from "./Components/template/AuthContext"

// Importando Paginas Para As Rotas
import Home from "./pages/Home";
import LoginOrRegister from "./pages/LoginOrRegister";
import Dashboard from "./pages/Dashboard"


function App() {
  return (
    <AuthProvider>
      <Router>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/auth" element={<LoginOrRegister />} />
          <Route element={<PrivateRoutes />}>
            <Route path="/dashboard" element={<Dashboard />} />
          </Route>
      </Routes>
      </Router>
    </AuthProvider>
  );
}

export default App;















