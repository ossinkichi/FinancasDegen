import react, { useState } from "react";
import "../Components/css/LoginOrRegister.css";

import Register from "../Components/Register";
import Login from "../Components/Login";

import Footer from "../Components/template/Footer";
import Header from "../Components/template/Header";

const LoginOrRegister = () => {
  
  const [isRegister, setIsRegister] = useState(false);
  const toggleForm = () => {
    setIsRegister(!isRegister);
  };
  
  return (
    <div className="page-container">
      <Header />
      <section>
        {isRegister ? <Register /> : <Login />}
        <button onClick={toggleForm}>
          {isRegister ? "Já possui uma conta? Faça login" : "Novo aqui? Cadastre-se"}
        </button>
      </section>
      <Footer />
    </div>
  )
}

export default LoginOrRegister;