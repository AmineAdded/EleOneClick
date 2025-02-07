import axios from "axios";
import '@/plugins/axios';

export default {
    GetAllPersonsUAP(id) {
        return axios.get(`getUAPpersons/${id}`);
    }
};