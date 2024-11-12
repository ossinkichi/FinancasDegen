import { useState } from "react";
import "./LoginOrRegister.css";
import "./AuthForms.css";

import Register from "./Register";
import Login from "./Login";

import Footer from "../../Components/template/Footer";
import Header from "../../Components/template/Header";

const LoginOrRegister = () => {
  const [isRegister, setIsRegister] = useState(false);
  const toggleForm = () => {
    setIsRegister(!isRegister);
  };

  return (
    <div className="auth-container">
      <Header />
      <section>
        {isRegister ? <Register /> : <Login />}
        <button onClick={toggleForm}>
          {isRegister
            ? "Já possui uma conta? Faça login"
            : "Novo aqui? Cadastre-se"}
        </button>
      </section>
      <Footer />
    </div>
  );
};

export default LoginOrRegister;
