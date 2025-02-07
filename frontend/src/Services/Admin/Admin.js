import Axios from "axios";
export default{
    count(){
        return Axios.get('Admin/count');
    },
    getUapInfos(){
        return Axios.get('Admin/getUapInfos');
    },
    getUAPData(){
        return Axios.get('Admin/getUAPData');
    },
    getTacheData(){
        return Axios.get('Admin/getTacheData');
    },
    getCategoriesData(){
        return Axios.get('Admin/getCategoriesData');
    },
    fetchTacheEvaluation(){
        return Axios.get('Admin/fetchTacheEvaluation');
    },
    fetchNotifications(){
        return Axios.get('Admin/fetchNotifications');
    },
    notificatioIsRead(id){
        return Axios.put('Admin/notificatioIsRead/'+id);
    },
    showFormateurs(){
        return Axios.get('Admin/showFormateurs');
    },
    deleteFormateur(id){
        return Axios.delete('Admin/deleteFormateur/'+id);
    },
    updateFormateur(id, data) {
    let newData = new FormData();
    newData.append('name', data.name);
    newData.append('lastname', data.lastname);
    newData.append('email', data.email);
    newData.append('image', data.image);
    return Axios.post('Admin/updateFormateur/' + id, newData);
    },
    createTrainer(data){
        let newData = new FormData();
        newData.append('name', data.name);
        newData.append('lastname', data.lastname);
        newData.append('email', data.email);
        newData.append('password', data.email);
        newData.append('image', data.image);
        return Axios.post('Admin/createTrainer',newData);
    }

}