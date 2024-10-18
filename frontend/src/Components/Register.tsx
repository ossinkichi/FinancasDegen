import React, { useState } from "react";
import "./css/Register.css";

const Register: React.FC = () => {
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    password: "",
    identification: "",
    dateOfBirth: "",
    gender: "",
    phone: "",
  });

  const [error, setError] = useState("");

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>,
  ) => {
    const { name, value } = e.target;
    setFormData((prevData) => ({
      ...prevData,
      [name]: value,
    }));
  };

  const handleSubmit = (event: React.FormEvent) => {
    event.preventDefault();

  
    if (!formData.name || !formData.email || !formData.password) {
      setError("Por favor, preencha todos os campos obrigatórios.");
      return;
    }

    
    setError("");

    console.log("Dados do Usuário:", formData);
  };

  return (
    <div className="register-container">
      <h2>Registrar</h2>
      {error && <p className="error">{error}</p>}
      <form onSubmit={handleSubmit}>
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
          <label htmlFor="identification">Identificação:</label>
          <input
            type="text"
            id="identification"
            name="identification"
            value={formData.identification}
            onChange={handleChange}
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
          />
        </div>
        <div className="form-group">
          <label htmlFor="gender">Gênero:</label>
          <select
            id="gender"
            name="gender"
            value={formData.gender}
            onChange={handleChange}
          >
            <option value="">Selecione...</option>
            <option value="male">Masculino</option>
            <option value="female">Feminino</option>
            <option value="transformer">Transformer</option>
            <option value="travesti">Kirito do GGO</option>
            <option value="airplane">Helicóptero de Combate</option>
            <option value="goku">Goku</option>
            <option value="robot">RobôCop Gay</option>
            <option value="NoHomo">No Homo</option>
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
          />
        </div>
        <button type="submit">Registrar</button>
      </form>
    </div>
  );
};

export default Register;
