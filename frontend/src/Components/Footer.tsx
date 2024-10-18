import React from "react";
import "./css/Footer.css";

const Footer: React.FC = () => {
  return (
    <footer className="footer">
        <div className="footer-links">
          <h4 className="logo">BlueBalance</h4>
          <a href="#accounting">Inicio</a>
          <a href="#investments">Planos</a>
          <a href="#loans">Sobre</a>
          <a href="#loans">Suporte</a>
        </div>
        <div className="footer-copyright">
          <p>&copy; 2024 DegenDev. Todos os direitos reservados.</p>
        </div>
    </footer>
  );
};

export default Footer;
