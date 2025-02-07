<template>
    <div>
        <Tableau1 :id="this.id"/>
        <Tableau2 :id="this.id"/>
    </div>

</template>
<script>
import { defineComponent } from "vue";

import Tableau1 from '@/components/UAP/Tableau1.vue';
import Tableau2 from '@/components/UAP/Tableau2.vue';
export default defineComponent({
    name:"UAPView",
    components:{
        Tableau1,
        Tableau2,
    },
    props: {
        id: {
            type: String,
            required: true
        }
    }, 
});
</script>