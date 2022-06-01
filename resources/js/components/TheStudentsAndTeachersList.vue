<script setup>
import { user } from "../stores/auth.js";
import { API } from "../stores/api.js";
import useFetch from "../composables/useFetch";
import { ref, computed, watchEffect } from "vue";

const data = {
    peopleList: [],
    studentsList: [],
    teachersList: [],
    classRoom: ""
};

//fonction auto-appelée au début
(async function startUp() {
    const peopleList = await getList();
    data.peopleList = peopleList;
    data.studentsList = data.peopleList.filter(person => person.role == 'student');
    console.log("students", data.studentsList);
    data.teachersList = data.peopleList.filter(person => person.role == 'teacher');

    console.log("dataPeople", data.peopleList)
    const classRoom = await getClassRoom();
    data.classRoom = classRoom;
})();

async function getList() {
    const response = await useFetch({
        url: API.getClassrooms.path(),
        method: API.getClassrooms.method,
    });
     console.log("getList", response.data.classrooms[0].users);
    return response.data.classrooms[0].users;
}

async function getClassRoom(){
     const response = await useFetch({
        url: API.getClassrooms.path(),
        method: API.getClassrooms.method,
    });
    console.log(response.data);
    return response.data.name;
}
</script>

<template>
    <div class="studentsAndTeachers">
        <h2> Memebres de la classe {{data.classRoom}} </h2>
        <ul class="studentsList">
              <li v-for="student in data.studentsList">
                <span>Nom d'utilisateur: {{ student.username }}</span>
             </li>
        </ul>
         <ul class="teachersList">
              <li v-for="teacher in data.teachersList">
                <span>Nom d'utilisateur: {{ teacher.username }}</span>
             </li>
        </ul>
    </div>
</template>
