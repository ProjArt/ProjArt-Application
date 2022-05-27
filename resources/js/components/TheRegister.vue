<script setup >
import { ref, toRaw } from "vue"
import useFetch from "../composables/useFetch";
import { API } from "../stores/api"
const isSubmitted = ref(false)
const formData = ref({})
const isAuthenticated = ref(false);
console.log(window.location.href)

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
        localStorage.setItem('token', response.access_token);
        /* window.location.href += "signup"; */
    } else {
        isAuthenticated.value = false;
    }
}
</script>
<template>
    <div>
        <FormKit type="form" v-model="formData" :form-class="isSubmitted ? 'hide' : 'show'" submit-label="Enregistrer"
            @submit="submitHandler">
            <h2>Création de compte</h2>
            <FormKit type="text" name="username" placeholder="unsername" validation="required" label="UserName" />
            <FormKit type="password" name="password" placeholder="password" validation="required" label="Password" />
            <FormKit type="password" name="password_confirm" label="Confirm password" help="Confirm your new password"
                validation="required|confirm" validation-visibility="live" validation-label="Password confirmation" />
        </FormKit>
        <div>
            <h2 v-if="isAuthenticated && isSubmitted">Compte crée</h2>
            <h2 v-else-if="!isAuthenticated && isSubmitted">Echec de la création de compte</h2>
        </div>
    </div>
</template>
<style>
</style>