import React, { useState } from "react";
import axios from "axios";
import "./css/AuthForms.css";

const Login = () => {
  const [email, setEmail] = useState<string>("");
  const [password, setPassword] = useState<string>("");

  const handleSubmit = async (
    event: React.FormEvent<HTMLFormElement>,
  ): Promise<void> => {
    event.preventDefault();
    try {
      const response = await axios.post(
        "https://174bef48-1d86-4312-8eac-ede07fbd236e-00-o3f3t4j7n7tx.picard.replit.dev:8000/user/login",
        { email, password },
      );
      if(response.data.status === "success"){
        console.log(response)
      }
    } catch (error: any) {
      console.log(error)
      if (error.response) {
        console.log(error.response.data);
        console.log(error.response.status);
        console.log(error.response.headers);
      } else {
        showError(error.message);
        console.log(error)
      }
    }
  };

  function showError(message: string): void {
    const errorElement = document.querySelector(".error");
    if (errorElement) {
      errorElement.textContent = message;
      setTimeout(() => {
        errorElement.textContent = "";
      }, 5000);
    }
  }

  const handleEmailChange = (
    event: React.ChangeEvent<HTMLInputElement>,
  ): void => {
    setEmail(event.target.value);
  };

  const handlePasswordChange = (
    event: React.ChangeEvent<HTMLInputElement>,
  ): void => {
    setPassword(event.target.value);
  };

  return (
    <form onSubmit={handleSubmit}>
      <h2>Login</h2>
      <p className="error"></p>
      <div className="form-group">
        <label htmlFor="email">Email:</label>
        <input
          type="email"
          id="email"
          value={email}
          onChange={handleEmailChange}
          required
        />
      </div>
      <div className="form-group">
        <label htmlFor="password">Senha:</label>
        <input
          type="password"
          id="password"
          value={password}
          onChange={handlePasswordChange}
          required
        />
      </div>
      <button type="submit">Confirmar</button>
    </form>
  );
};

export default Login;
