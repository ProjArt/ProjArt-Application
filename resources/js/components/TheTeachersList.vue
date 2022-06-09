<script setup>
import { user } from "../stores/auth.js";
import { API } from "../stores/api.js";
import useFetch from "../composables/useFetch";
import { ref, computed, watchEffect } from "vue";

const data = ref({
    teachers_list: [],
    current_filiere: "Filtrer par filière",
    available_filieres: [],
    complete_list: NaN
});

async function getFilieresList(){
    const response = await useFetch({
    url: API.getProfsOfMySection.path(),
    method: API.getProfsOfMySection.method,
    });

    const filieres = response.data[0][0].faculty;
    console.log("filieres", filieres)
    //Récupération du nom de chaque cours
    filieres.forEach(filiere => {
        data.value.available_filieres.push(filiere.code)
    });

    data.value.complete_list = filieres;
    console.log("completeList", data.value.complete_list);
}

function updateTeachersList(){
    const new_teachers_list = data.value.complete_list.filter(list => list.code == data.value.current_filiere)[0]
    data.value.teachers = new_teachers_list;
    data.value.current_filiere = new_teachers_list.code;
    console.log("newteacherList", data.value.teachers_list)
}

const teachers_list = computed({
    get: () => data.value.teachers_list
});

const current_filiere = computed({
    get: () => data.value.current_filiere,
});

const available_filieres = computed ({
    get: () => data.value.available_filieres
})


await getFilieresList();
</script>

<template>
    <div class="teachers">
        <h1>LISTE DES PROFESSEURS</h1>
        <h2> {{ data.current_filiere }} </h2>
        <div class="filiere-selection">
        <form class="filiere-selection-form" @change="updateTeachersList()" >
            <select class="filiere-select"  v-model="data.current_filiere">
                <option value="Filtrer par cours">Filtrer par cours </option>
                <option v-for="filiere in available_filieres"
                 v-bind:value="filiere"
                 >{{ filiere }}</option>
            </select>
        </form>
        </div>
        <ul class="class-teachers-list">
                <li v-for="teacher in data.teachers_list.gapsUsers">
                <div class="nom-utilisateur">
                <span>{{ teacher.firstname }} {{ teacher.name }}</span>
                </div>
                <div class="mail-et-classe">
                <span>{{ teacher.mail }} </span><br/>
                <div class="cours_donnes">
                    <span v-for="cours in teacher.lessons">
                        {{ cours }}
                    </span>
                </div>
                </div>
             </li>
        </ul>
    </div>
</template>


<style scoped>

</style>