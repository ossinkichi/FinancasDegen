import React, { useState } from "react";
import "./css/AuthForms.css";
import axios from "axios";

const Register = () => {
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    password: "",
    cpf: "",
    dateOfBirth: "",
    gender: "",
    phone: "",
  });

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>,
  ) => {
    const { name, value } = e.target;
    setFormData((prevData) => ({
      ...prevData,
      [name]: value,
    }));
  };

  const handleSubmit = async (event: React.FormEvent) => {
    event.preventDefault();
    try {
      const response = await axios.post(
        "https://174bef48-1d86-4312-8eac-ede07fbd236e-00-o3f3t4j7n7tx.picard.replit.dev:8000/user/register",
      );
      if (response.data.status === "success") {
        console.log(response);
      }
    } catch (error: any) {
      if (error.response) {
        console.log(error.response.data);
        console.log(error.response.status);
        console.log(error.response.headers);
      }
      showError(error.messasge);
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

  return (
    <div className="register-container">
      <form onSubmit={handleSubmit}>
        <h2>Registre-se</h2>
        <p className="error"></p>
        <div className="form-group">
          <label htmlFor="name">Nome:</label>
          <input
            type="text"
            id="name"
            name="name"
            value={formData.name}
            onChange={handleChange}
            required
          />
        </div>
        <div className="form-group">
          <label htmlFor="email">E-mail:</label>
          <input
            type="email"
            id="email"
            name="email"
            value={formData.email}
            onChange={handleChange}
            required
          />
        </div>
        <div className="form-group">
          <label htmlFor="password">Senha:</label>
          <input
            type="password"
            id="password"
            name="password"
            value={formData.password}
            onChange={handleChange}
            required
          />
        </div>
        <div className="form-group">
          <label htmlFor="cpf">CPF:</label>
          <input
            type="text"
            id="cpf"
            name="cpf"
            value={formData.cpf}
            onChange={handleChange}
            required
          />
        </div>
        <div className="form-group">
          <label htmlFor="dateOfBirth">Data de Nascimento:</label>
          <input
            type="date"
            id="dateOfBirth"
            name="dateOfBirth"
            value={formData.dateOfBirth}
            onChange={handleChange}
            required
          />
        </div>
        <div className="form-group">
          <label htmlFor="gender">Gênero:</label>
          <select
            id="gender"
            name="gender"
            value={formData.gender}
            onChange={handleChange}
            required
          >
            <option value="">Selecione...</option>
            <option value="male">Masculino</option>
            <option value="female">Feminino</option>
            {/* <option value="transformer">Transformer</option>
            <option value="travesti">Kirito do GGO</option>
            <option value="airplane">Helicóptero de Combate</option>
            <option value="goku">Goku</option>
            <option value="robot">RobôCop Gay</option>
            <option value="NoHomo">No Homo</option> */}
          </select>
        </div>
        <div className="form-group">
          <label htmlFor="phone">Telefone:</label>
          <input
            type="tel"
            id="phone"
            name="phone"
            value={formData.phone}
            onChange={handleChange}
            required
          />
        </div>
        <button type="submit">Confirmar</button>
      </form>
    </div>
  );
};

export default Register;
