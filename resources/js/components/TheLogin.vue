<script setup >
import { ref, toRaw } from "vue"
import useFetch from "../composables/useFetch";
import { API } from "../stores/api"
import { routesNames } from "../router/routes";
const isSubmitted = ref(false)
const formData = ref({})
const errorMessage = ref('')
const isAuthenticated = ref(false);

const submitHandler = async () => {
    isSubmitted.value = true
    const response = await useFetch({
        url: API.login.path(),
        method: API.login.method,
        data: toRaw(formData.value)
    })
    if (response.success === true) {
        isAuthenticated.value = true;
        localStorage.setItem('token', response.data.access_token);
        errorMessage.value = ''
        window.location.href += routesNames.calendar.replace('/', '');
    } else {
        isAuthenticated.value = false;
        errorMessage.value = response.message;
    }
}
</script>
<template>
    <div class="wrapper">
        <FormKit type="form" v-model="formData" :form-class="isSubmitted ? 'hide' : 'show'" submit-label="Login"
            @submit="submitHandler">
            <h2>Connexion</h2>
            <FormKit type="text" name="username" placeholder="unsername" validation="required" label="UserName" />
            <FormKit type="password" name="password" placeholder="password" validation="required" label="Password" />
        </FormKit>
        <div>
            <h2 v-if="isAuthenticated && isSubmitted">Connexion établie</h2>
            <h2 v-else-if="!isAuthenticated && isSubmitted">Mot de passe ou username non valid</h2>
            <p>{{ errorMessage }}</p>
        </div>
        <a class="forgot-password" href="#">Forgot password ?</a>
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