import react, { useState } from "react";
import "../Components/css/LoginOurRegister.css";

import Register from "../Components/Register";
import Login from "../Components/Login";

import Footer from "../Components/template/Footer";
import Header from "../Components/template/Header";



const LoginOurRegister = () => {
  
  const [isRegister, setIsRegister] = useState(false);
  const toggleForm = () => {
    setIsRegister(!isRegister);
  };
  
  return (
    <>
      <Header />
      <section>
        {isRegister ? <Register /> : <Login />}
        <button onClick={toggleForm}>
          {isRegister ? "Já tem uma conta? Faça login" : "Já tem uma conta? Faça login'"}
        </button>
      </section>
      <Footer />
    </>
  )
}

export default LoginOurRegister;