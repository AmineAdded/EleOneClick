import Axios from 'axios';
import { AuthStore } from "../store/auth";

export default {
    async ChangerInfosFormateur(data, id) {
        let user = new FormData();
        user.append('name', data.name);
        user.append('lastname', data.lastname);
        user.append('email', data.email);
        user.append('image', data.image);
    
        try {
            const response = await Axios.post(`changerInfosFormateur/${id}`, user);
    
            if (response.data.success) {
                const store = AuthStore();
                console.log(response.data.data.image);
                store.updateUser(response.data.data.name, response.data.data.lastname, response.data.data.email,response.data.data.image);
            } else {
                console.log("Réponse serveur :", response.data);
            }
        } catch (error) {
            console.error("Erreur lors de la mise à jour :", error.response ? error.response.data : error);
        }
    },
    

     ChangerPassword(email) {
        return  Axios.post('ChangerPassword', email);
    }
    
};
