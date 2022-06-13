<script setup>
import { user } from "../stores/auth.js";
import { API } from "../stores/api.js";
import useFetch from "../composables/useFetch";
import { ref, computed, watchEffect } from "vue";

const data = ref({
    students_list: [],
    current_course: "Filtrer par cours",
    available_courses: [],
    complete_list: NaN,
});

async function getCoursesList() {
    const response = await useFetch({
        url: API.getStudentsOfMyCourses.path(),
        method: API.getStudentsOfMyCourses.method,
    });

    const courses = response.data;
    //Récupération du nom de chaque cours
    courses.forEach((course) => {
        data.value.available_courses.push(course.code);
    });

    data.value.complete_list = courses;
    data.value.current_course = courses[0].code;
    updateStudentsList();
    data.value.current_course = "Filtrer par cours";
    console.log("completeList", courses);
}

function updateStudentsList() {
    const newStudentsList = data.value.complete_list.filter(
        (list) => list.code == data.value.current_course
    )[0];
    const newStudentsListFiltered = newStudentsList.gapsUsers.filter(person => person.is_teacher == 0);
    newStudentsList.gapsUsers = newStudentsListFiltered;

    data.value.students_list = newStudentsList;
    data.value.current_course = newStudentsList.code;
    console.log("newCourseList", data.value.students_list);
}


//Liste des étudiants par ordre alphabétique selon le nom de famille
const students_list = computed({
    get: () => data.value.students_list.gapsUsers.sort
    ((student_a, student_b) => {
         if(student_a.name[0] < student_b.name[0]){
            return - 1
         } else {
            return 1
         }
         }),
});

const teachersList = computed({
    get: () => data.value.teacherslist,
});

const classroom = computed({
    get: () => data.value.classroom,
});

const available_courses = computed({
    get: () => data.value.available_courses,
});

const renamedCourse = computed({
    get: () => {
        if(data.value.current_course != "Filtrer par cours") {
            return data.value.current_course.split("-")[0]
        } else {
             return data.value.available_courses[0].split("-")[0]
        }},
});

await getCoursesList();
</script>

<template>
    <div class="page__title">LISTE DES ETUDIANTS</div>
    <div class="subtitle_and_lesson_selection">
        <div class="page__subtitle_big">
            {{ renamedCourse }}
        </div>
        <div class="course-selection">
                <form  class="course-selection-form" @change="updateStudentsList()">
                    <select class="course-select" v-model="data.current_course">
                        <option class="option"
                            value="Filtrer par cours"
                            selected="true"
                            disabled="disabled"
                        >
                            Filtrer par cours
                        </option>
                        <option class="option"
                            v-for="course in available_courses"
                            v-bind:value="course"
                        >
                            {{ course }}
                        </option>
                    </select>
                </form>
        </div>
    </div>
    <div class="students">
        <ul class="students-list">
            <li class="student" v-for="student in students_list">
                <div class="nom-utilisateur">
                    <span class="span-nom-prenom"> {{ student.name }}</span><span class="span-nom-prenom">{{ student.firstname }}</span>
                </div>
                <div class="mail">
                    <span>{{ student.mail }} </span>
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

.subtitle_and_lesson_selection {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    margin: 3.1em 0 0 0 ;
}

.course-selection {
    margin: 0em 0.44em 0.22em 0;
}


.course-select {
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

.students {
    margin: 4.1em 1em 0 1em;
}

.students-list {
     list-style-type: none;
     padding: 0 0 0 0;
}
.student {
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

.mail{
    margin: 0 0 0 3.9rem;
    display: flex;
    text-align: justify;
    font-weight: 400;
    font-family: Poppins;
    font-size: 1.2rem;
    line-height: 1.8rem;
    align-items: center;
}

.bottom-space-adder {
    height: 7.6rem;
}
</style>
