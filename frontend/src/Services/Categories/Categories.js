import Axios from 'axios';
export default{
     deleteCategory($id){
        const res= Axios.post('categories/deleteCategory/'+$id);
        return res;
    },
     showCategories(){
        const res= Axios.get('categories/showCategories');
        return res;
    },
     showUAPs(){
        const res= Axios.get('categories/showUAPs');
        return res;
    },
     addCategory(data){
        const res= Axios.post('categories/addCategory',data);
        return res; 
    }

}