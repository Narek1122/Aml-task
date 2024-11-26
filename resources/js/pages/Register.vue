<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div v-if="registrationStatus" class="alert alert-success">
          {{ registrationStatus }}
        </div>
        <div class="card">
          <div class="card-header">
            <h3>Register</h3>
          </div>
          <div class="card-body">
            <form @submit.prevent="handleSubmit">
              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  v-model="formData.name"
                  :class="{'is-invalid': errors.name}"
                />
                <div v-if="errors.name" class="invalid-feedback">
                  {{ errors.name[0] }}
                </div>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="formData.email"
                  :class="{'is-invalid': errors.email}"
                />
                <div v-if="errors.email" class="invalid-feedback">
                  {{ errors.email[0] }}
                </div>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="formData.password"
                  :class="{'is-invalid': errors.password}"
                />
                <div v-if="errors.password" class="invalid-feedback">
                  {{ errors.password[0] }}
                </div>
              </div>

              <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="password_confirmation"
                  v-model="formData.password_confirmation"
                  :class="{'is-invalid': errors.password_confirmation}"
                />
                <div v-if="errors.password_confirmation" class="invalid-feedback">
                  {{ errors.password_confirmation[0] }}
                </div>
              </div>

              <button type="submit" class="btn btn-primary">Register</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

export default {
  name: 'RegistrationForm',
  setup() {
    const store = useStore();
    const router = useRouter();

    const formData = ref({
      name: '',
      email: '',
      password: '',
      password_confirmation: '',
    });

    const handleSubmit = async () => {
      await store.dispatch('registerUser', formData.value);

      if (store.getters.registrationStatus) {
        router.push('/login');
      }
    };

    return {
      formData,
      handleSubmit,
      registrationStatus: store.getters.registrationStatus,
      errors: store.getters.errors,
    };
  },
};
</script>

<style scoped>
.invalid-feedback {
  display: block;
}
</style>
