<script setup>
import { user } from "../stores/auth.js";
import { API } from "../stores/api.js";
import useFetch from "../composables/useFetch";
import { ref, computed, watchEffect } from "vue";

const data = ref({
    students_list: [],
    current_course: "WebMobUI",
    available_courses: [],
    complete_list: NaN
});

async function getList() {
    const response = await useFetch({
        url: API.getClassrooms.path(),
        method: API.getClassrooms.method,
    });
    const usersList = response.data.classrooms[0].users;
    data.value.students_list = usersList.filter((person) => person.role == "student");
    data.value.teachersList = usersList.filter((person) => person.role == "teacher");
    console.log("list",  data.value.students_list)
}

async function getClassRoom() {
    const response = await useFetch({
        url: API.getClassrooms.path(),
        method: API.getClassrooms.method,
    });
    const classRoomName = response.data.classrooms[0].name;
    data.value.classroom = classRoomName;
}

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

console.log('dataBeforeScrap', data.value)
await getList();
await getClassRoom();
await getCoursesList();
console.log("dataAfterScrap", data.value);
console.log("class", classroom.value)


function hardCodeStudentsList (){
    data.value.students_list.shift();
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
        data.value.students_list.push(simulatedUser)
    }
    console.log('newListHardCodee', data.value.students_list)
}

function updateStudentsList(){
    console.log("ancient", data.value.students_list)
    let helloList = data.value.complete_list.filter(list => list.code == data.value.current_course)
    console.log("helloList", helloList[0])
    let  newList = data.value.complete_list.filter(list => { list.code == data.value.current_course})
    data.value.students_list = newList[0];
}

hardCodeStudentsList();
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
              <li v-for="student in data.students_list">
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