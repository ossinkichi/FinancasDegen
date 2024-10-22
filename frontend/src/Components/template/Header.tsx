import React, { useState } from "react";
import "../css/Header.css";

const Header: React.FC = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
  };

  return (
    <header className="header">
      <div className="container">
         <div className="logo">BlueBalance</div>

      <nav className={`nav ${isMenuOpen ? "open" : ""}`}>
        <ul>
          <li>
            <a href="/">Inicio</a>
          </li>
          <li>
            <a href="#services">Planos</a>
          </li>
          <li>
            <a href="#about">Sobre</a>
          </li>
          <li>
            <a href="#contact">Suporte</a>
          </li>
        </ul>
      </nav>
      </div>


      <div className="container">
        <div className="button">
          <i onClick={ () => window.location.href = "/auth" } className="bi bi-person"></i>
        </div>

        <div className="menu-icon button" onClick={toggleMenu}>
          <i className="bi bi-list"></i>
        </div>
      </div>
    </header>
  );
};

export default Header;
