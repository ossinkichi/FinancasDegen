import React, { useState } from "react";

import "../../Components/css/LoginOrRegister.css"

const companyForm = () => {
  const [formData, setFormData] = useState({
    nome_empresa: "",
    cnpj: "",
    razao_social: "",
    endereco: "",
    telefone: "",
    email: "",
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

  const handleSubmit =  async (
      event: React.FormEvent<HTMLFormElement>,
    ): Promise<void>  => {
    event.preventDefault();
    console.log("Dados da empresa:", formData);
  };

  return (
    <div>
      <h2>Cadastro sua Organização</h2>
      <form onSubmit={handleSubmit}>
        <label htmlFor="nome_empresa">Nome da Empresa:</label>
        <input
          type="text"
          id="nome_empresa"
          name="nome_empresa"
          value={formData.nome_empresa}
          onChange={handleChange}
          required
        />
        <br /><br />
        <label htmlFor="cnpj">CNPJ:</label>
        <input
          type="text"
          id="cnpj"
          name="cnpj"
          value={formData.cnpj}
          onChange={handleChange}
          required
          pattern="\d{14}"
          title="O CNPJ deve ter 14 dígitos numéricos"
        />
        <br /><br />

        <label htmlFor="razao_social">Razão Social:</label>
        <input
          type="text"
          id="razao_social"
          name="razao_social"
          value={formData.razao_social}
          onChange={handleChange}
        />
        <br /><br />

        <label htmlFor="endereco">Endereço Completo:</label>
        <input
          type="text"
          id="endereco"
          name="endereco"
          value={formData.endereco}
          onChange={handleChange}
          required
        />
        <br /><br />

        <label htmlFor="telefone">Telefone de Contato:</label>
        <input
          type="tel"
          id="telefone"
          name="telefone"
          value={formData.telefone}
          onChange={handleChange}
          required
          pattern="\(\d{2}\)\s\d{4,5}-\d{4}"
          title="Formato esperado: (XX) XXXX-XXXX ou (XX) XXXXX-XXXX"
        />
        <br /><br />

        <label htmlFor="email">E-mail:</label>
        <input
          type="email"
          id="email"
          name="email"
          value={formData.email}
          onChange={handleChange}
          required
        />
        <br /><br />

        <button type="submit">Cadastrar Empresa</button>
      </form>
    </div>
  );
};

export default companyForm;
