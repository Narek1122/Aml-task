<template>
    <div class="container mt-5" v-if="status !== 'loading'">
      <h2 v-if="user">Welcome, {{ user.name }}</h2>
      <p v-if="user">Email: {{ user.email }}</p>
      <p v-if="user">Account created at: {{ user.created_at }}</p>

      <div v-else>
        <p>User data is not available.</p>
      </div>

      <div class="my-4">
        <h3>Blogs</h3>
        <ul class="list-group">
          <li v-for="blog in blogs" :key="blog.id" class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong>{{ blog.title }}</strong>
              <p>{{ blog.content }}</p>
              <img v-if="blog.image" :src="blog.image" alt="Blog Image" class="img-thumbnail" style="width: 100px; height: 100px;" />
            </div>
            <div>
              <button @click="editBlog(blog)" class="btn btn-warning btn-sm me-2">Edit</button>
              <button @click="deleteBlog(blog.id)" class="btn btn-danger btn-sm">Delete</button>
            </div>
          </li>
        </ul>

        <form @submit.prevent="handleSubmit" class="card p-4 mt-4" enctype="multipart/form-data">
          <div class="mb-3">
            <input v-model="form.title" type="text" class="form-control" placeholder="Blog Title" required />
          </div>
          <div class="mb-3">
            <textarea v-model="form.content" class="form-control" rows="3" placeholder="Blog Content" required></textarea>
          </div>
          <div class="mb-3">
            <input type="file" @change="handleFileChange" class="form-control" />
          </div>
          <div v-if="form.image" class="mb-3">
            <img :src="form.imagePreview" alt="Selected Image" class="img-thumbnail" style="width: 100px; height: 100px;" />
          </div>
          <button type="submit" class="btn btn-primary">{{ isEdit ? 'Update' : 'Create' }} Blog</button>
        </form>
      </div>
    </div>

    <div v-if="status === 'loading'" class="text-center mt-5">
      <p>Loading user data...</p>
    </div>

    <div v-if="status === 'error'" class="text-center mt-5">
      <p>Error: Could not load user data. Please try again later.</p>
    </div>
  </template>

  <script>
  import { computed, ref, onMounted, watch } from 'vue';
  import { useStore } from 'vuex';
  import { useRouter } from 'vue-router';

  export default {
    name: 'BlogComponent',
    setup() {
      const store = useStore();
      const router = useRouter();
      const user = computed(() => store.getters.user);
      const status = computed(() => store.getters.status);
      const blogs = computed(() => store.getters.blogs);
      const errors = computed(() => store.getters.errors);

      const form = ref({
        title: '',
        content: '',
        image: null,
        imagePreview: '',
      });

      const isEdit = ref(false);
      const currentBlogId = ref(null);

      onMounted(() => {
        store.dispatch('fetchUserData');
        store.dispatch('fetchUserBlogs', { page: 1 });
      });

      watch(user, (newUser) => {
        if (!newUser) {
          localStorage.removeItem('token');
          router.push('/login');
        }
      });

      const handleFileChange = (event) => {
        const file = event.target.files[0];
        if (file) {
          form.value.image = file;
          form.value.imagePreview = URL.createObjectURL(file);
        }
      };

      const handleSubmit = async () => {
        const blogData = { ...form.value };
        const formData = new FormData();
        formData.append('title', blogData.title);
        formData.append('content', blogData.content);
        if (blogData.image) {
          formData.append('image', blogData.image);
        }

        if (isEdit.value) {
          await store.dispatch('updateBlog', { id: currentBlogId.value, blog: formData });
        } else {
          await store.dispatch('createBlog', formData);
        }

        form.value.title = '';
        form.value.content = '';
        form.value.image = null;
        form.value.imagePreview = '';
        isEdit.value = false;
        currentBlogId.value = null;
        store.dispatch('fetchUserBlogs', { page: 1 });
      };

      const deleteBlog = async (id) => {
        await store.dispatch('deleteBlog', id);
        store.dispatch('fetchUserBlogs', { page: 1 });
      };

      const editBlog = (blog) => {
        isEdit.value = true;
        currentBlogId.value = blog.id;
        form.value.title = blog.title;
        form.value.content = blog.content;
        form.value.imagePreview = blog.image;
      };

      return {
        user,
        status,
        blogs,
        form,
        isEdit,
        currentBlogId,
        handleSubmit,
        deleteBlog,
        editBlog,
        errors,
        handleFileChange,
      };
    },
  };
  </script>

  <style scoped>
  .container {
    max-width: 800px;
  }
  </style>
