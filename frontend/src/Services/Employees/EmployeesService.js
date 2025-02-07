import Axios from "axios";

export default{
    async afficheEmployees(){
        const response=await Axios.get('employees/showEmployees');
        return response.data;
    },
    deleteEmployee($id){
        return Axios.post('employees/deleteEmployee/'+$id);
    },
     updateEmployee($id,data){
        const res= Axios.put('/employees/updateEmployee/'+$id,data);
        return res;
    },
     createEmployee(data){
        const res= Axios.post('/employees/createEmployee',data);
        return res;
    }
};