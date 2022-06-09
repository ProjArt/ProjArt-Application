<script setup>
import { user } from "../stores/auth.js";
import { API } from "../stores/api.js";
import useFetch from "../composables/useFetch";
import { ref, computed, watchEffect } from "vue";

const data = ref({
    students_list: [],
    current_course: "Filtrer par cours",
    available_courses: [],
    complete_list: NaN
});

async function getCoursesList(){
    const response = await useFetch({
    url: API.getStudentsOfMyCourses.path(),
    method: API.getStudentsOfMyCourses.method,
    });

    const courses = response.data;
    //Récupération du nom de chaque cours
    courses.forEach(course => {
        data.value.available_courses.push(course.code)
    });

    data.value.complete_list = courses;
    console.log("completeList", data.value.complete_list);
}

const students_list = computed({
    get: () => data.value.students_list
});

const teachersList = computed({
    get: () => data.value.teacherslist,
});

const classroom = computed({
    get: () => data.value.classroom,
});

const available_courses = computed ({
    get: () => data.value.available_courses
})

function updateStudentsList(){
    const newStudentsList = data.value.complete_list.filter(list => list.code == data.value.current_course)[0]
    data.value.students_list = newStudentsList;
    data.value.current_course = newStudentsList.code;
    console.log("newStudentList", data.value.students_list)
}



await getCoursesList();
</script>

<template>
    <div class="students">
        <h1>Liste des étudiants</h1>
        <h2> {{ data.current_course }} </h2>
        <div class="course-selection">
        <form class="course-selection-form" @change="updateStudentsList()" >
            <select class="course-select"  v-model="data.current_course">
                <option value="Filtrer par cours">Filtrer par cours </option>
                <option v-for="course in available_courses"
                 v-bind:value="course"
                 >{{ course }}</option>
            </select>
        </form>
        </div>
        <ul class="class-students-list">
        <h3> Etudiants </h3>
                <li v-for="student in data.students_list.gapsUsers">
                <div class="nom-utilisateur">
                <span>{{ student.firstname }} {{ student.name }}</span>
                </div>
                <div class="mail-et-classe">
                <span>{{ student.mail }} </span><br/>
                </div>
             </li>
        </ul>
    </div>
</template>


<style scoped>
.studentsAndTeachers {
    list-style-type: none;
}




</style>