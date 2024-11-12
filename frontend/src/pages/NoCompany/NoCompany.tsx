import React, { useEffect, useState } from "react";
import "./NoCompany.css";
import Header from "../../Components/template/Header";
import Footer from "../../Components/template/Footer";
import CompanyForm from "./CompanyForm"

import noCompanyIMG_0 from "../../assets/noCompany.png";
import noCompanyIMG_1 from "../../assets/noCompany_.png";

const ImageSwitcher: React.FC = () => {
  const [currentImage, setCurrentImage] = useState(noCompanyIMG_1);

  useEffect(() => {
    const intervalId = setInterval(() => {
      setCurrentImage((prevImage) =>
        prevImage === noCompanyIMG_0 ? noCompanyIMG_1 : noCompanyIMG_0,
      );
    }, 800);

    return () => clearInterval(intervalId);
  }, []);

  return <img src={currentImage} alt="No Company" />;
};

const Invite: React.FC = () => {
  return (
    <form className="inviteCode">
      <input type="text" placeholder="Código de Convite" />
      <button type="submit">
        <i className="bi bi-arrow-right-short"></i>
      </button>
    </form>
)}

const NoCompany: React.FC = () => {
  
  const [content, setContent] = useState<"code" | "form">("code");

  const handleContentChange = (type: "code" | "form") => {
    setContent(type);
  };
  
  useEffect(() => {
    const elementClass = content === "code" ? ".inviteCode" : ".register-company-form";
    const element = document.querySelector(elementClass);
    if (element) {
      element.scrollIntoView({ behavior: "smooth" });
    }
  }, [content]);

  


  return (
    <div className="page-container">
      <Header />
      <div className="no-company-container">
        <div className="img-container">
          <ImageSwitcher />
        </div>
        <p className="text-1">Você não está vinculado a nenhuma empresa!</p>
        <p className="text-1">
          <span
            className="text-button"
            onClick={() => handleContentChange("code")}
          >
            Insira um código de convite
          </span>{" "}
          ou{" "}
          <span
            className="text-button"
            onClick={() => handleContentChange("form")}
          >
            cadastre uma empresa.
          </span>
        </p>
        {content === "code" ? <Invite /> : <CompanyForm />}
        <Footer />
      </div>
    </div>
  );
};

export default NoCompany;
