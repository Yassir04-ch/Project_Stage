<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { loginUser } from "@/services/authService";

const router = useRouter();

const form = reactive({
  email: "",
  password: "",
});

const errors = ref({});
const loading = ref(false);

const submitlogin = async () => {
  loading.value = true;
  errors.value = {};

  try {

    const response = await loginUser(form);

    localStorage.setItem("token", response.data.data.token);

    const user = response.data.data.user;

    console.log(user);

    if (user.role.id == 1) {

      router.push("/dashboard");

    } else{
      router.push("/profile");

    }

  } catch (error) {

    if (error.response?.status === 422) {

      errors.value = error.response.data.errors;

    } else {

      console.log(error.response?.data);

    }

  } finally {

    loading.value = false;

  }
};
</script>

<template>
  <div class="login-page">

    <h1>Login</h1>

    <form @submit.prevent="submitlogin">

      <input
        v-model="form.email"
        type="email"
        placeholder="Email"
      />
      
      <p class="error" v-if="errors.email">
        {{ errors.email[0] }}
      </p>

      <input
        v-model="form.password"
        type="password"
        placeholder="Password"
      />

      <p class="error" v-if="errors.password">
        {{ errors.password[0] }}
      </p>

      <button type="submit" :disabled="loading">
        {{ loading ? "Loading..." : "Login" }}
      </button>

    </form>

  </div>
</template>

<style scoped>
.login-page {
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
}
</style>