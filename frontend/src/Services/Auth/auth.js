import Axios from "axios";
import { AuthStore } from "@/store/auth";

export default {
    async login(email, password) {
        const store = AuthStore();
        try {
            const response = await Axios.post("login", {
                email,
                password,
            });
            if (response.status === 200) {
                store.login(response.data.data.token, response.data.data.user,response.data.data.isAdmin,true);
                return response.data;
            }
        } catch (error) {
            console.error("Erreur lors de la connexion :", error.response || error);
            throw error;
        }
    },
    

    async SignUp(user){
        let data =new FormData();
        data.append('name',user.name);
        data.append('lastname',user.lastname);
        data.append('email',user.email);
        data.append('password',user.password);
        data.append('image',user.photo);
        const store = AuthStore();
        try{
            const res=await Axios.post('SignUp',data);
            if(res.status === 200){
                store.login(res.data.data.token, res.data.data.user,res.data.data.isAdmin,true);
            }
        }catch(err){
            console.log("erreur lors de l'inscription",err);
        }
     },
      sendEmailSignUp(email){
        const res= Axios.post('sendEmailSignUp',email);
        return res;
     },
};
