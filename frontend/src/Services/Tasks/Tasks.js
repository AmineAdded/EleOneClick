import Axios from 'axios';
export default{
     deleteTask($id){
        const res= Axios.post('taches/deleteTaches/'+$id);
        return res;
    },
     showTasks(){
        const res= Axios.get('taches/showTaches');
        return res;
    },
     addTask(data){
        const res= Axios.post('taches/addTaches',data);
        return res;
        
    }
}