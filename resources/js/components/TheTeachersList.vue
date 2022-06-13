<script setup>
import { user } from "../stores/auth.js";
import { API } from "../stores/api.js";
import useFetch from "../composables/useFetch";
import { ref, computed, watchEffect } from "vue";

const data = ref({
    teachers_lists: [],
    current_filiere: "Filtrer par filière",
    available_filieres: [],
    teachers_list_to_show: []
});


async function getFilieresList() {
    const response = await useFetch({
        url: API.getProfsOfMySection.path(),
        method: API.getProfsOfMySection.method,
    });

    const noms_filieres_disponibles = Object.keys(response.data);
    const data_filieres = response.data;
    data.value.available_filieres = noms_filieres_disponibles;

    noms_filieres_disponibles.forEach((nom_filiere) => {
        const new_teachers_list = { code: nom_filiere };
        new_teachers_list.teachers = data_filieres[nom_filiere];
        // console.log(newFiliere)
        data.value.teachers_lists.push(new_teachers_list);
    });

    data.value.current_filiere = data.value.available_filieres[0]
    updateTeachersList();
    data.value.current_filiere = "Filtrer par filière";

}

function updateTeachersList() {
    const complete_list = data.value.teachers_lists
    console.log("complete_list", complete_list, "current_filiere", data.value.current_filiere)
    const new_teachers_list = complete_list.filter(jsonList => jsonList.code == data.value.current_filiere)[0].teachers

    data.value.teachers_list_to_show = new_teachers_list
    console.log(data.value.teachers_list_to_show)

    updateCoursesLists ()
}

//Cette fonction permet d'ajouter une virgule à tous les cours des profs sauf le dernier
function updateCoursesLists(){
    data.value.teachers_list_to_show.forEach(teacher => {
        const courses_with_virgule = [];
        for(let i = 0; i < teacher.lessons.length - 1; i ++ ){
            courses_with_virgule[i] = `${teacher.lessons[0]}, `;
        }
       courses_with_virgule.push(teacher.lessons[teacher.lessons.length -1])    
       teacher.lessons = courses_with_virgule;
    });
}

const available_filieres = computed({
    get: () => data.value.available_filieres,
});

const teachers_list_to_show = computed ({
    get: () => data.value.teachers_list,
})

const current_filiere = computed({
    get: () => {
        if(data.value.current_filiere != "Filtrer par filière") {
            return data.value.current_filiere
        } else {
             return data.value.available_filieres[0]
        }},
});

await getFilieresList();
</script>

<template>
        <div class="page__title">LISTE DES PROFESSEURS</div>
        <div class="subtitle_and_filiere_selection">
        <div class="page__subtitle_big">{{ current_filiere }}</div>
        <div class="filiere-selection">
            <form class="filiere-selection-form" @change="updateTeachersList()">
                <select class="filiere-select" v-model="data.current_filiere">
                    <option class="option" value="Filtrer par filière" disabled="disabled" selected="true">
                        Filtrer par filière
                    </option>
                    <option
                        v-for="filiere in available_filieres"
                        v-bind:value="filiere"
                    >
                        {{ filiere }}
                    </option>
                </select>
            </form>
        </div>
        </div>
        <div class="teachers">
        <ul class="teachers-list">
            <li class="teacher" v-for="teacher in data.teachers_list_to_show">
                <div class="nom-utilisateur">
                    <span class="span-nom-prenom"> {{ teacher.name }}</span><span class="span-nom-prenom">{{ teacher.firstname }}</span> 
                </div>
                <div class="mail-lessons">
                <div class="mail"><span>{{ teacher.mail }}</span></div>
                <div class="lessons"><span v-for="lesson in teacher.lessons" >{{ lesson }} </span></div>
                </div>
            </li>
        </ul>
    </div>
    <div class="bottom-space-adder"></div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&display=swap');
.page__title {
    font-family: "Poppins";
    font-style: normal;
    font-weight: 900;
    font-size: 26px;
    line-height: 39px;
    color: var(--rg-text-color);
}
.page__subtitle_big {
    font-size: 23px;
    font-family: "Poppins", sans-serif;
    font-weight: 900;
    margin: 0em 0em 0.44em 0.22em;
    line-height: 23px;
}

.subtitle_and_filiere_selection {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    margin: 3.1em 0 0 0 ;
}

.filiere-selection {
    margin: 0em 0.44em 0.22em 0;
}


.filiere-select {
    background-color: var(--accent-color);
    color: var(--background-color);
    padding: 0.1em 0.1em;
    border-radius: 0.3em;
    border-color: var(--accent-color);
    font-family: Poppins;
}

.option {
    background-color: var(--background-color);
    color: var(--text-color);
}

.option:hover {
    background-color: var(--accent-color);
    color: var(--background-color);
}

.teachers {
    margin: 4.1em 1em 0 1em;
}

.teachers-list {
     list-style-type: none;
     padding: 0 0 0 0;
}
.teacher {
    margin: 0 0 1em 0;
    display: flex;
    flex-direction: row;
    background-color: var(--information-color);
    border-radius: 1em;
}

.nom-utilisateur {
   background-color: var(--primary-color);
    margin: 0.7em 0 0.7em 0.7em;
    border-radius: 1rem;
    width: 11.1rem;
    font-size: 1.4rem;
    color: var(--background-color);
    font-family: 'Poppins';
    display: flex;
    align-items: flex-start;
    font-weight: 700;
    line-height: 2.1rem;
    flex-direction: column;
    justify-content: center;
    padding: 0.75rem 0 0.75rem 0;
}

.span-nom-prenom {
    margin: 0 0 0 1rem;
}

.span-course-list{
    margin: 1.3rem 0 0 0;
    text-align: 
}

.mail-lessons{
   margin: 0 0 0 3.9rem;
    display: flex;
    text-align: justify;
    font-weight: 400;
    text-align: left;
    font-family: Poppins;
    font-size: 1.2rem;
    line-height: 1.8rem;
    justify-content: center;
    /* justify-content: center; */
    /* align-items: center; */
    text-align: left;
    flex-direction: column;
}

.mail {
    font-size: 1.4rem;
}

.lessons {
    margin: 1.3rem 0 0 0;
    font-size: 1.4rem;
}

.bottom-space-adder {
    height: 7.6rem;
}
</style>

