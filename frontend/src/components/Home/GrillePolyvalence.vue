<template>
    <div class="section-heading" style="display: grid; grid-template-columns: 1fr;">
        <section>
            <!-- Titre aligné avec la première carte -->
            <h1 style="margin-left:0px; text-align: center;">UAP<br><em :style="{color:'purple'}">Production</em></h1>
        </section>
        <section style="margin-top:30px; text-align: center;">
            <p>UAP Production est un leader dans le domaine de la production industrielle, 
            <br>spécialisé dans la fabrication de pièces de haute précision pour divers secteurs.</p>

            <v-container>
                <v-row>
                    <!-- Première ligne avec 3 cartes -->
                    <v-col cols="12" sm="4" id="1">
                        <v-card class="mx-auto" max-width="344">
                            <v-img height="200px" :src="require('@/assets/images/UAP1.jpg')" cover></v-img>
                            <v-card-title>UAP 1</v-card-title>
                            <v-card-subtitle>1,000 miles of wonder</v-card-subtitle>
                            <v-card-actions>
                                <v-btn color="orange-lighten-2" text="Explorer" @click="scrolltoUAP(1)"></v-btn>
                                <v-spacer></v-spacer>
                            </v-card-actions>
                        </v-card>
                    </v-col>

                    <v-col cols="12" sm="4" id="2">
                        <v-card class="mx-auto" max-width="344">
                            <v-img height="200px" :src="require('@/assets/images/UAP2.jpg')" cover></v-img>
                            <v-card-title>UAP 2</v-card-title>
                            <v-card-subtitle>1,000 miles of wonder</v-card-subtitle>
                            <v-card-actions>
                                <v-btn color="orange-lighten-2" text="Explorer" @click="scrolltoUAP(2)"></v-btn>
                                <v-spacer></v-spacer>
                                
                            </v-card-actions>
                            
                        </v-card>
                    </v-col>

                    <v-col cols="12" sm="4" id="3">
                        <v-card class="mx-auto" max-width="344">
                            <v-img height="200px" :src="require('@/assets/images/UAP3.jpeg')" cover></v-img>
                            <v-card-title>UAP 3</v-card-title>
                            <v-card-subtitle>1,000 miles of wonder</v-card-subtitle>
                            <v-card-actions>
                                <v-btn color="orange-lighten-2" text="Explorer" @click="scrolltoUAP(3)"></v-btn>
                                <v-spacer></v-spacer>
                                
                            </v-card-actions>
                            
                        </v-card>
                    </v-col>
                </v-row>

                <v-row>
                    <!-- Deuxième ligne avec 3 cartes -->
                    <v-col cols="12" sm="4" id="4">
                        <v-card class="mx-auto" max-width="344">
                            <v-img height="200px" :src="require('@/assets/images/UAP4.jpg')" cover></v-img>
                            <v-card-title>UAP 4</v-card-title>
                            <v-card-subtitle>1,000 miles of wonder</v-card-subtitle>
                            <v-card-actions>
                                <v-btn color="orange-lighten-2" text="Explorer" @click="scrolltoUAP(4)"></v-btn>
                                <v-spacer></v-spacer>
                                
                            </v-card-actions>
                            
                        </v-card>
                    </v-col>

                    <v-col cols="12" sm="4" id="5">
                        <v-card class="mx-auto" max-width="344">
                            <v-img height="200px" :src="require('@/assets/images/UAP5.jpg')" cover></v-img>
                            <v-card-title>UAP 5</v-card-title>
                            <v-card-subtitle>1,000 miles of wonder</v-card-subtitle>
                            <v-card-actions>
                                <v-btn color="orange-lighten-2" text="Explorer" @click="scrolltoUAP(5)"></v-btn>
                                <v-spacer></v-spacer>
                                
                            </v-card-actions>
                           
                        </v-card>
                    </v-col>

                </v-row>
            </v-container>
        </section>
    </div>
</template>
<script>
  export default {
    data(){
        return {
            show:false,
        }
    },

    methods:{
        scrolltoUAP(id){
            this.$router.push(`/UAPView/${id}`);
        },
    }
  }
</script>
