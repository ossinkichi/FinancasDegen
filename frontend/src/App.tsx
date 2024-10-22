import "./App.css";
import { BrowserRouter as Router, Routes, Route} from "react-router-dom";
import React from "react";
import LoginOrRegister from "./pages/LoginOrRegister";
import Home from "./pages/Home";

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/auth" element={<LoginOrRegister />} />
      </Routes>
    </Router>
  );
}

export default App;
