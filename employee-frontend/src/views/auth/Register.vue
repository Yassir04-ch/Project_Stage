<script setup>
import { reactive, ref } from "vue";
import { registerUser } from "@/services/authService";

const form = reactive({
  firstname: "",
  lastname: "",
  email: "",
  password: "",
  password_confirmation: "",
  cin: "",
  telephone: "",
  adresse: "",
  genre: "",
  date_naissance: "",
  date_embauche: "",
  type_contrat: "",
  salaire: "",
  photo: null,
  role_id: 1,
});

const errors = ref({});
const loading = ref(false);

const handleFile = (event) => {
  form.photo = event.target.files[0];
};

const submitRegister = async () => {
  loading.value = true;
  errors.value = {};

  try {
    const formData = new FormData();

    formData.append("firstname", form.firstname);
    formData.append("lastname", form.lastname);
    formData.append("cin", form.cin);
    formData.append("email", form.email);
    formData.append("password", form.password);
    formData.append("password_confirmation", form.password_confirmation);
    formData.append("role_id", form.role_id);

    if (form.telephone) formData.append("telephone", form.telephone);
    if (form.adresse) formData.append("adresse", form.adresse);
    if (form.genre) formData.append("genre", form.genre);
    if (form.date_naissance) formData.append("date_naissance", form.date_naissance);
    if (form.date_embauche) formData.append("date_embauche", form.date_embauche);
    if (form.type_contrat) formData.append("type_contrat", form.type_contrat);
    if (form.salaire) formData.append("salaire", form.salaire);

    if (form.photo) {
      formData.append("photo", form.photo);
    }

    const response = await registerUser(formData);

    localStorage.setItem("token", response.data.token);

    alert("Register success");
    console.log(response);

  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      console.log(error);
    }
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="register-page">
    <h1>Register</h1>

    <form @submit.prevent="submitRegister">

      <input v-model="form.firstname" type="text" placeholder="Firstname" />
      <p class="error" v-if="errors.firstname">{{ errors.firstname[0] }}</p>

      <input v-model="form.lastname" type="text" placeholder="Lastname" />
      <p class="error" v-if="errors.lastname">{{ errors.lastname[0] }}</p>

      <input v-model="form.email" type="email" placeholder="Email" />
      <p class="error" v-if="errors.email">{{ errors.email[0] }}</p>

      <input v-model="form.cin" type="text" placeholder="CIN" />
      <p class="error" v-if="errors.cin">{{ errors.cin[0] }}</p>

      <input v-model="form.telephone" type="text" placeholder="Telephone" />

      <textarea v-model="form.adresse" placeholder="Adresse"></textarea>

      <select v-model="form.genre">
        <option disabled value="">Select gender</option>
        <option value="homme">Homme</option>
        <option value="femme">Femme</option>
      </select>

      <input v-model="form.date_naissance" type="date" />
      <input v-model="form.date_embauche" type="date" />

      <select v-model="form.type_contrat">
        <option disabled value="">Type contrat</option>
        <option value="CDI">CDI</option>
        <option value="CDD">CDD</option>
        <option value="Stage">Stage</option>
        <option value="Freelance">Freelance</option>
        <option value="Interim">Interim</option>
      </select>

      <input v-model="form.salaire" type="number" placeholder="Salaire" />

      <input type="file" @change="handleFile" />

      <input v-model="form.password" type="password" placeholder="Password" />
      <p class="error" v-if="errors.password">{{ errors.password[0] }}</p>

      <input
        v-model="form.password_confirmation"
        type="password"
        placeholder="Confirm Password"
      />

      <button type="submit" :disabled="loading">
        {{ loading ? "Loading..." : "Register" }}
      </button>

    </form>
  </div>
</template>

<style scoped>
.register-page {
  width: 450px;
  margin: 40px auto;
}

form {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.error {
  color: red;
  font-size: 12px;
  margin-top: -8px;
}
</style>