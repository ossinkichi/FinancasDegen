import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import "./CompanyForm.css";

// Definição do tipo de estado para os dados do formulário
interface FormData {
  companyName: string;
  companyCNPJ: string;
  companyEmail: string;
  street: string;
  number: string;
  neighborhood: string;
  city: string;
  state: string;
}

// Definição do tipo de estado para os erros de validação
interface FormErrors {
  companyName: string;
  companyCNPJ: string;
  companyEmail: string;
  street: string;
  number: string;
  neighborhood: string;
  city: string;
  state: string;
}

const CompanyForm: React.FC = () => {
  const navigate = useNavigate();
  // Estado para armazenar os dados do formulário
  const [formData, setFormData] = useState<FormData>({
    companyName: "",
    companyCNPJ: "",
    companyEmail: "",
    street: "",
    number: "",
    neighborhood: "",
    city: "",
    state: "",
  });

  // Estado para armazenar as mensagens de erro
  const [errors, setErrors] = useState<FormErrors>({
    companyName: "",
    companyCNPJ: "",
    companyEmail: "",
    street: "",
    number: "",
    neighborhood: "",
    city: "",
    state: "",
  });

  const validateForm = (): boolean => {
    let formErrors: FormErrors = { ...errors };
    let valid = true;

    if (!formData.companyName) {
      formErrors.companyName = "O nome da empresa é obrigatório";
      valid = false;
    } else {
      formErrors.companyName = "";
    }

    if (!formData.companyCNPJ || formData.companyCNPJ.length !== 14) {
      formErrors.companyCNPJ = "CNPJ inválido (14 dígitos)";
      valid = false;
    } else {
      formErrors.companyCNPJ = "";
    }

    if (!formData.companyEmail || !/\S+@\S+\.\S+/.test(formData.companyEmail)) {
      formErrors.companyEmail = "Email inválido";
      valid = false;
    } else {
      formErrors.companyEmail = "";
    }

    if (!formData.street) {
      formErrors.street = "O campo rua é obrigatório";
      valid = false;
    } else {
      formErrors.street = "";
    }

    if (!formData.number) {
      formErrors.number = "O número é obrigatório";
      valid = false;
    } else {
      formErrors.number = "";
    }

    if (!formData.neighborhood) {
      formErrors.neighborhood = "O bairro é obrigatório";
      valid = false;
    } else {
      formErrors.neighborhood = "";
    }

    if (!formData.city) {
      formErrors.city = "A cidade é obrigatória";
      valid = false;
    } else {
      formErrors.city = "";
    }

    if (!formData.state) {
      formErrors.state = "O estado é obrigatório";
      valid = false;
    } else {
      formErrors.state = "";
    }

    setErrors(formErrors);
    return valid;
  };
    const handleSubmit = async (event: React.FormEvent) => {
    event.preventDefault();
    if (validateForm()) {
      try {
        const response = await axios.post("/api/company", formData);
        if (response.status === 201) {
          navigate("/dashboard");
        }
      } catch (error) {
        console.log(error);
      }
      setFormData({
        companyName: "",
        companyCNPJ: "",
        companyEmail: "",
        street: "",
        number: "",
        neighborhood: "",
        city: "",
        state: "",
      });
    } else {
      console.log("Erro ao enviar dados. Verifique os campos.");
    }
  };

  const handleInputChange = (
    event: React.ChangeEvent<HTMLInputElement>,
  ): void => {
    const { id, value } = event.target;
    setFormData((prevData) => ({
      ...prevData,
      [id]: value,
    }));
  };

  return (
    <form className="register-company-form" onSubmit={handleSubmit}>
      <h2>Cadastre Sua Empresa</h2>

      <div className="form-group">
        <label htmlFor="companyName">Nome da Empresa</label>
        <input
          type="text"
          id="companyName"
          placeholder="Nome da Empresa"
          value={formData.companyName}
          onChange={handleInputChange}
          required
        />
        {errors.companyName && (
          <span className="error">{errors.companyName}</span>
        )}
      </div>

      <div className="form-group">
        <label htmlFor="companyCNPJ">CNPJ</label>
        <input
          type="text"
          id="companyCNPJ"
          placeholder="CNPJ"
          value={formData.companyCNPJ}
          onChange={handleInputChange}
          required
        />
        {errors.companyCNPJ && (
          <span className="error">{errors.companyCNPJ}</span>
        )}
      </div>

      <div className="form-group">
        <label htmlFor="companyEmail">Email</label>
        <input
          type="email"
          id="companyEmail"
          placeholder="Email da Empresa"
          value={formData.companyEmail}
          onChange={handleInputChange}
          required
        />
        {errors.companyEmail && (
          <span className="error">{errors.companyEmail}</span>
        )}
      </div>

      <div className="form-group">
        <label htmlFor="street">Rua</label>
        <input
          type="text"
          id="street"
          placeholder="Rua"
          value={formData.street}
          onChange={handleInputChange}
          required
        />
        {errors.street && <span className="error">{errors.street}</span>}
      </div>

      <div className="form-inline">
        <div className="form-group">
          <label htmlFor="number">Número</label>
          <input
            type="text"
            id="number"
            placeholder="Número"
            value={formData.number}
            onChange={handleInputChange}
            required
          />
          {errors.number && <span className="error">{errors.number}</span>}
        </div>
        <div className="form-group">
          <label htmlFor="neighborhood">Bairro</label>
          <input
            type="text"
            id="neighborhood"
            placeholder="Bairro"
            value={formData.neighborhood}
            onChange={handleInputChange}
            required
          />
          {errors.neighborhood && (
            <span className="error">{errors.neighborhood}</span>
          )}
        </div>
      </div>

      <div className="form-inline">
        <div className="form-group">
          <label htmlFor="city">Cidade</label>
          <input
            type="text"
            id="city"
            placeholder="Cidade"
            value={formData.city}
            onChange={handleInputChange}
            required
          />
          {errors.city && <span className="error">{errors.city}</span>}
        </div>
        <div className="form-group">
          <label htmlFor="state">Estado</label>
          <input
            type="text"
            id="state"
            placeholder="Estado"
            value={formData.state}
            onChange={handleInputChange}
            required
          />
          {errors.state && <span className="error">{errors.state}</span>}
        </div>
      </div>

      <button type="submit" className="submit-button">
        Cadastrar Empresa
      </button>
    </form>
  );
};

export default CompanyForm;
