import "../Components/css/NoCompany.css";
import Header from "../Components/template/Header";
import Footer from "../Components/template/Footer";

const NoCompany = () => {
  return (
    <>
      <Header />
      <div className="no-company-container">
        <div className="img-container">
          <img src="../../public/noCompany.png" alt="" />
        </div>
        <p className="text-1">Você não está vinculado a nenhuma empresa!</p>
        <p className="text-1">Insira um codigo de convite ou cadastre uma empresa.</p>
        <form className="inviteCode">
          <input type="text" placeholder="Código de Convite" />
          <button type="submit">
            <i className="bi bi-arrow-right-short"></i>
          </button>
        </form>
      </div>
      <Footer />
    </>
  );
};

export default NoCompany;
