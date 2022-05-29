<script setup >
import { ref, toRaw } from "vue"
import useFetch from "../composables/useFetch";
import { API } from "../stores/api"
import { routesNames } from "../router/routes";
const isSubmitted = ref(false)
const formData = ref({})
const isAuthenticated = ref(false);

(async function getClasses() {
    const response = await useFetch({
        url: API.classes.path(),
        method: API.classes.method,
        data: {}
    });
    if (response.success === true) {
        return response.data;
    } else {
        return [];
    }
})()

const submitHandler = async () => {
    isSubmitted.value = true
    console.log(toRaw(formData.value))
    const response = await useFetch({
        url: API.register.path(),
        method: API.register.method,
        data: toRaw(formData.value)
    })
    console.log(response);
    if (response.success === true) {
        isAuthenticated.value = true;
        console.log(response);
        localStorage.setItem('token', response.access_token);
        window.location.href += routesNames.calendar.replace('/', '');
    } else {
        isAuthenticated.value = false;
    }
}
</script>
<template>
    <div class="wrapper">
        <FormKit type="form" v-model="formData" :form-class="isSubmitted ? 'hide' : 'show'" submit-label="Enregistrer"
            @submit="submitHandler">
            <h2>Création de compte</h2>
            <FormKit type="text" name="username" placeholder="unsername" validation="required" label="UserName" />
            <FormKit type="password" name="password" placeholder="password" validation="required" label="Password" />
            <FormKit type="password" name="password_confirm" placeholder="password" label="Confirm password"
                validation="required|confirm" validation-label="Password confirmation" />
            <!--
            <FormKit type="select" name="classroom_name" placeholder="class" validation="required" label="Classe" >
                <option v-for="(class, index) in classes" :value="name.id">{{ name.name }}</option>
            </FormKit>
            -->
        </FormKit>
        <div>
            <h2 v-if="isAuthenticated && isSubmitted">Compte crée</h2>
            <h2 v-else-if="!isAuthenticated && isSubmitted">Echec de la création de compte</h2>
        </div>
    </div>
</template>
<style scoped lang="scss">
.wrapper {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

:deep(.formkit-form) {
    display: flex;
    flex-direction: column;
    align-items: center;
}
</style>