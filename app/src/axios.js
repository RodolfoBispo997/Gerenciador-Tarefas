import axios from "axios";
import router from "./router"


//PEGA A URL DO ARQUVIO .ENV E VALIDA COOKIES
const axiosClient = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    
    //CRIA  COOKIES PARA MANDAR COM A SESSÃO
    // withCredentials: true,
    // withXSRFToken: true
});


//AQUI ELE PEGA O TOKEN PARA TODAS AS ROTAS
axiosClient.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});


//Intercepta as respostas da API
axiosClient.interceptors.response.use((response)=>{
    return response;
}, error =>{
    if(error.response && error.response.status === 401){
        //Remove o token antigo
        localStorage.removeItem('token');
        //Redireciona quando da erro de autorização
        router.push({ name: 'Login' }).catch(() => {});
    }

    throw error;
})

export default axiosClient;