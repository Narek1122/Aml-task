<template>
    <div class="container py-5">
      <h1 class="text-center mb-4">Blogs</h1>

      <!-- Loading Indicator -->
      <div v-if="isLoading" class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>

      <!-- Error Alert -->
      <!-- <div v-if="errors" class="alert alert-danger" role="alert">
        <strong>Error:</strong> {{ errors }}
      </div> -->

      <!-- Blog List -->
      <div v-if="blogs && blogs.length > 0">
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <div v-for="blog in blogs" :key="blog.id" class="col">
            <div class="card shadow-sm">
                <div class="card-body">
                <!-- Conditionally show image if it exists -->
                <div v-if="blog.image" class="card-img-wrapper">
                    <img :src="blog.image" class="card-img-top img-fluid" alt="Blog Image" />
                </div>

                <h5 class="card-title">{{ blog.title }}</h5>
                <p class="card-text">{{ blog.content }}</p>
                <p class="text-muted mb-0">
                    <small>By: {{ blog.user }}</small>
                </p>
                <p class="text-muted">
                    <small>{{ formatDate(blog.created_at) }}</small>
                </p>
                </div>
            </div>
            </div>
        </div>
        </div>



      <!-- No Blogs Found -->
      <div v-else-if="!isLoading" class="text-center py-5">
        <h4 class="text-muted">No blogs found.</h4>
      </div>

      <!-- Pagination -->
      <nav v-if="pagination.total > pagination.perPage" class="mt-4">
        <ul class="pagination justify-content-center">
          <li class="page-item" :class="{ disabled: !pagination.prevPage }">
            <button class="page-link" @click="fetchBlogs(pagination.prevPage)">Previous</button>
          </li>
          <li
            class="page-item"
            v-for="page in pagination.totalPages"
            :key="page"
            :class="{ active: page === pagination.currentPage }"
          >
            <button class="page-link" @click="fetchBlogs(page)">{{ page }}</button>
          </li>
          <li class="page-item" :class="{ disabled: !pagination.nextPage }">
            <button class="page-link" @click="fetchBlogs(pagination.nextPage)">Next</button>
          </li>
        </ul>
      </nav>
    </div>
  </template>

  <script>
  import { computed, onMounted } from "vue";
  import { useStore } from "vuex";

  export default {
    name: "Home",
    setup() {
      const store = useStore();

      const blogs = computed(() => store.state.blogs); // Blogs data
      const pagination = computed(() => store.state.pagination); // Pagination data
      const errors = computed(() => store.getters.errors); // Error messages
      const isLoading = computed(() => store.getters.isLoading); // Loading status

      // Fetch blogs with sorting and pagination
      const fetchBlogs = (page = 1) => {
        store.dispatch("fetchBlogs", { page });
      };

      // Format date for display
      const formatDate = (dateString) => {
        const options = { year: "numeric", month: "long", day: "numeric" };
        return new Date(dateString).toLocaleDateString(undefined, options);
      };

      // Fetch blogs on component mount
      onMounted(() => {
        fetchBlogs();
      });

      return {
        blogs,
        pagination,
        errors,
        isLoading,
        fetchBlogs,
        formatDate,
      };
    },
  };
  </script>

  <style scoped>
  .card {
    transition: transform 0.2s;
  }

  .card:hover {
    transform: scale(1.02);
  }
  </style>
