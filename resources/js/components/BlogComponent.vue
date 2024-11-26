<template>
    <div>
      <h1>Blogs</h1>

      <!-- Display loading state -->
      <div v-if="loading">Loading...</div>
      <!-- Display error messages -->
      <div v-if="errors" class="error">{{ errors }}</div>

      <!-- Display Blogs -->
      <ul v-if="!loading">
        <li v-for="blog in blogs" :key="blog.id">
          <strong>{{ blog.title }}</strong>
          <p>{{ blog.content }}</p>
          <button @click="editBlog(blog)">Edit</button>
          <button @click="deleteBlog(blog.id)">Delete</button>
        </li>
      </ul>

      <!-- Create/Update Blog Form -->
      <form @submit.prevent="handleSubmit">
        <input v-model="form.title" placeholder="Blog Title" />
        <textarea v-model="form.content" placeholder="Blog Content"></textarea>
        <button type="submit">{{ isEdit ? 'Update' : 'Create' }} Blog</button>
      </form>
    </div>
  </template>

  <script>
  import { computed, ref, onMounted } from 'vue';
  import { useStore } from 'vuex';

  export default {
    data() {
      return {
        form: {
          title: '',
          content: '',
        },
        isEdit: false,
        currentBlogId: null,
      };
    },
    computed: {
      blogs() {
        return this.$store.getters.blogs;
      },
      errors() {
        return this.$store.getters.errors;
      },
      loading() {
        return this.$store.getters.loading; // You can customize if you want to track the loading state
      },
    },
    methods: {
      async fetchUserBlogs() {
        await this.$store.dispatch('fetchUserBlogs', { page: 1 });
      },

      async deleteBlog(id) {
        await this.$store.dispatch('deleteBlog', id);
        this.fetchUserBlogs();
      },

      async handleSubmit() {
        const blogData = { ...this.form };
        if (this.isEdit) {
          await this.$store.dispatch('updateBlog', { id: this.currentBlogId, blog: blogData });
        } else {
          await this.$store.dispatch('createBlog', blogData);
        }
        this.clearForm();
        this.fetchUserBlogs();
      },

      async editBlog(blog) {
        this.isEdit = true;
        this.currentBlogId = blog.id;
        this.form.title = blog.title;
        this.form.content = blog.content;
      },

      clearForm() {
        this.form.title = '';
        this.form.content = '';
        this.isEdit = false;
        this.currentBlogId = null;
      },
    },
    mounted() {
      this.fetchUserBlogs();
    },
  };
  </script>

  <style scoped>
  .error {
    color: red;
  }
  </style>
