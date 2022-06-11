<script setup>
import { loaded } from "../composables/useLoading";
import { useClosePopup, usePopup } from "../composables/usePopup";
import { watch } from "vue";

usePopup({
  title: "Téléchargement des données",
  body: "Nous téléchargeons les données... Veuillez patienter.",
  buttons: [],
});

watch(loaded, (newVal) => {
  if (newVal) {
    useClosePopup();
  }
});
</script>


