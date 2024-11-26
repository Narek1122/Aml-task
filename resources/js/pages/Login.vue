<template>
    <div class="container mt-5">
      <h2>Login</h2>
      <form @submit.prevent="login">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input
            type="email"
            id="email"
            v-model="email"
            class="form-control"
            :class="{ 'is-invalid': errors.email }"
          />
          <div v-if="errors.email" class="invalid-feedback">{{ errors.email[0] }}</div>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input
            type="password"
            id="password"
            v-model="password"
            class="form-control"
          />
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
      </form>
      <div v-if="hasErrors" class="alert alert-danger mt-3">
        <ul>
            {{ errors }}
          <!-- <li v-for="(message, key) in errors" :key="key">
            {{ message }}
          </li> -->
        </ul>
      </div>
      <div v-if="checkEmail" class="alert alert-info mt-3">
        Registration successful. Please check your email to verify your account.
      </div>
    </div>
  </template>

  <script>
  import { ref, computed, watch } from 'vue';
  import { useStore } from 'vuex';
  import { useRouter } from 'vue-router';

  export default {
    name: 'LoginForm',
    setup() {
      const router = useRouter();
      const store = useStore();
      const email = ref('');
      const password = ref('');
      const errors = computed(() => store.getters.errors);
      const hasErrors = computed(() => Object.keys(errors.value).length > 0);
      const checkEmail = computed(() => store.getters.isEmailChecked);

      watch(checkEmail, (newVal) => {
        if (newVal) {
          setTimeout(() => {
            store.dispatch('clearNotification');
          }, 5000);
        }
      });

      const login = async () => {
        const formData = { email: email.value, password: password.value };
        await store.dispatch('loginUser', formData);
        const token = localStorage.getItem('token');
        if (token) {
          router.push('/profile');
        }
      };

      return {
        email,
        password,
        errors,
        hasErrors,
        checkEmail,
        login,
      };
    },
  };
  </script>

  <style scoped>
  </style>
