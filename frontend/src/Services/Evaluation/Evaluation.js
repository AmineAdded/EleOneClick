import Axios from 'axios';

export default {
    async showEvaluation() {
        try {
            const response = await Axios.get('/evaluation/showEvaluations');
            return response.data;
        } catch (error) {
            console.error("Erreur lors de la récupération des données", error);
            return null; 
        }
    },
    async UpdateEmployee(data){
        try{  
            const res= await Axios.post('/evaluation/UpdateEmployees',data);
            return res;

        }catch(error){
            console.log("Erreur lors de la modification des évaluations",error);
        }
    },
    async showEvaluationTask($employeeId) { 
      
        try {
          const res = await Axios.get(`/evaluation/getEvaluationTasks/`+$employeeId);
          return res.data;
        } catch (error) {
          console.error("Erreur lors de la récupération des évaluations de l'employé", error);
        }
      }
      
};
