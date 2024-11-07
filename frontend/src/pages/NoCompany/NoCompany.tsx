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

const NoCompany: React.FC = () => {

  const [content, setContent] = useState("code");

  function changeContent(): void{
    if(content === "code"){
      setContent("form");
    }else if(content === "form"){
      setContent("code");
    }
  }
  
  return (
    <div className="page-container">
      <Header />
      <div className="no-company-container">
        <div className="img-container">
          <ImageSwitcher />
        </div>
        <p className="text-1">Você não está vinculado a nenhuma empresa!</p>
        <button></button>
        <form className="inviteCode">
          <input type="text" placeholder="Código de Convite" />
          <button type="submit">
            <i className="bi bi-arrow-right-short"></i>
          </button>
        </form>
      </div>
      <Footer />
    </div>
  );
};

export default NoCompany;
