<script setup>
import { user } from "../stores/auth.js";
import { API } from "../stores/api.js";
import useFetch from "../composables/useFetch";
import { ref, computed, watchEffect } from "vue";

const data = ref({
    studentsList: [],
    teachersList: [],
    classroom: "dsasàsld",
});

async function getList() {
    const response = await useFetch({
        url: API.getClassrooms.path(),
        method: API.getClassrooms.method,
    });
    const usersList = response.data.classrooms[0].users;
    data.value.studentsList = usersList.filter((person) => person.role == "student");
    data.value.teachersList = usersList.filter((person) => person.role == "teacher");
}

async function getClassRoom() {
    const response = await useFetch({
        url: API.getClassrooms.path(),
        method: API.getClassrooms.method,
    });
    const classRoomName = response.data.classrooms[0].name;
    data.value.classroom = classRoomName;
}

const studentsList = computed({
    get: () => data.value.studentsList
});

const teachersList = computed({
    get: () => data.value.teacherslist,
});

const classroom = computed({
    get: () => data.value.classroom,
});

console.log('dataBeforeScrap', data.value)
await getList();
await getClassRoom();
console.log("dataAfterScrap", data.value);
console.log("class", classroom.value)
</script>

<template>
    <div class="studentsAndTeachers">
        <h2>Membres de la classe {{ classroom }}</h2>
        <ul class="studentsList">
        <h3> Etudiants </h3>
              <li v-for="student in data.studentsList">
                <span>Nom d'utilisateur: {{ student.username }}</span>
                <span>Email: {{ student.username }} @heig-vd.ch</span>
             </li>
        </ul>
         <ul class="teachersList">
                <h3>Enseignants </h3>
              <li v-for="teacher in data.teachersList">
                <span>Nom d'utilisateur: {{ teacher.username }}</span>
                <span>Email: {{ teacher.username }} @heig-vd.ch</span>
             </li>
        </ul>
    </div>
</template>
