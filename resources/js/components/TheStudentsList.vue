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
    console.log("list",  data.value.studentsList)
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


function hardCodeStudentsList (){
    data.value.studentsList.shift();
    const simulatedUser =  {
        "id": 1,
        "username": "timothee.dione",
        "gaps_id": 17449,
        "role": "student",
        "card_money": 10,
        "gaps_user": {
		"username": "timothee.dione",
		"gaps_id": 17486,
		"firstname": "Timothée",
		"name": "Dione",
		"mail": "timothee.dione@heig-vd.ch",
		"is_teacher": 0
	  },
        "pivot": {
            "classroom_name": "M49-1",
            "user_id": 1
        },
        "theme": {
            "id": 1,
            "primary": {
                "value": "49,65,120"
            },
            "secondary": {
                "value": "231,33,40"
            },
            "accent": {
                "value": "249,59,88"
            },
            "inactive": {
                "value": "146,145,148"
            },
            "text": {
                "value": "58,60,61"
            },
            "background": {
                "value": "255,255,255"
            },
            "information": {
                "value": "240,240,240"
            }
        }
    }

    for (let i = 0; i< 20; i++){
        data.value.studentsList.push(simulatedUser)
    }
    console.log('newList', data.value.studentsList)
}

hardCodeStudentsList();
</script>

<template>
    <div class="studentsAndTeachers">
        <h2>Membres de la classe {{ classroom }}</h2>
        <ul class="studentsList">
        <h3> Etudiants </h3>
              <li v-for="student in data.studentsList">
                <div class="nom-utilisateur">
                <span>{{ student.gaps_user.firstname }} {{ student.gaps_user.name }}</span>
                </div>
                <div class="mail-et-classe">
                <span>{{ student.gaps_user.mail }} </span><br/>
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