import { createStore } from 'vuex';
import apiService from '../services/apiService';

export default createStore({
  state() {
    return {
      blogs: [],
      pagination: { total: 0, perPage: 10, currentPage: 1, totalPages: 0, nextPage: null, prevPage: null },
      registrationStatus: null,
      errors: {},
      checkEmail: false,
      user: null,
      token: localStorage.getItem('token') || null,
    };
  },
  mutations: {
    setBlog(state, blog) {
      const index = state.blogs.findIndex(b => b.id === blog.id);
      if (index !== -1) {
        state.blogs[index] = blog;
      } else {
        state.blogs.push(blog);
      }
    },
    removeBlog(state, id) {
      state.blogs = state.blogs.filter(blog => blog.id !== id);
    },
    setBlogs(state, { data, meta }) {
      state.blogs = data;
      state.pagination = {
        total: meta.total,
        perPage: meta.per_page,
        currentPage: meta.current_page,
        totalPages: Math.ceil(meta.total / meta.per_page),
        nextPage: meta.current_page < state.pagination.totalPages ? meta.current_page + 1 : null,
        prevPage: meta.current_page > 1 ? meta.current_page - 1 : null,
      };
    },
    clearBlogs(state) {
      state.blogs = [];
    },
    setRegistrationStatus(state, message) {
      state.registrationStatus = message;
    },
    setCheckEmail(state) {
      state.checkEmail = true;
    },
    setErrors(state, errors) {
      state.errors = errors;
    },
    clearErrors(state) {
      state.errors = {};
    },
    clearCheckEmail(state) {
      state.checkEmail = false;
    },
    setUser(state, user) {
      state.user = user;
      if (user.token) {
        state.token = user.token;
        localStorage.setItem('token', user.token);
      }
    },
    clearUser(state) {
      state.user = null;
      state.token = null;
      localStorage.removeItem('token');
    },
  },
  actions: {
    async fetchBlogs({ commit }, { page = 1 }) {
      try {
        const data = await apiService.fetchBlogs(page);
        commit('setBlogs', { data: data.data, meta: data.meta });
      } catch (error) {
        commit('setErrors', error.message || 'Error fetching blogs');
      }
    },

    async registerUser({ commit }, formData) {
      commit('clearErrors');
      try {
        const data = await apiService.registerUser(formData);
        commit('setRegistrationStatus', data.message);
        commit('setCheckEmail');
      } catch (error) {
        commit('setErrors', error.message || 'Registration failed');
      }
    },

    async loginUser({ commit }, formData) {
      commit('clearErrors');
      try {
        const data = await apiService.loginUser(formData);
        commit('setUser', data);
      } catch (error) {
        commit('setErrors', error.message || 'Login failed');
      }
    },

    async fetchUserData({ commit, state }) {
      const token = state.token;
      if (!token) return;

      commit('setStatus', 'loading');
      try {
        const data = await apiService.fetchUserData(token);
        commit('setUser', data.data);
        commit('setStatus', 'success');
      } catch (error) {
        commit('setStatus', 'error');
        commit('clearUser');
        commit('setErrors', error.message || 'Error fetching user data');
      }
    },

    async fetchUserBlogs({ commit }, { page = 1 }) {
      try {
        const data = await apiService.fetchUserBlogs(page);
        commit('setBlogs', { data: data.data, meta: data.meta });
      } catch (error) {
        commit('setErrors', error.message || 'Error fetching user blogs');
      }
    },

    async createBlog({ commit }, blog) {
      try {
        const response = await apiService.createBlog(blog);
        commit('setBlog', response.data); // Adds new blog to the state
      } catch (error) {
        commit('setErrors', error.message || 'Error creating blog');
      }
    },

    async updateBlog({ commit }, { id, blog }) {
      try {
        const response = await apiService.updateBlog(id, blog);
        commit('setBlog', response.data); // Updates the blog in state
      } catch (error) {
        commit('setErrors', error.message || 'Error updating blog');
      }
    },

    async deleteBlog({ commit }, id) {
      try {
        await apiService.deleteBlog(id);
        commit('removeBlog', id); // Removes the deleted blog from state
      } catch (error) {
        commit('setErrors', error.message || 'Error deleting blog');
      }
    },
  },
  getters: {
    blogs(state) {
      return state.blogs;
    },
    pagination(state) {
      return state.pagination;
    },
    errors(state) {
      return state.errors;
    },
    registrationStatus(state) {
      return state.registrationStatus;
    },
    user(state) {
      return state.user;
    },
    token(state) {
      return state.token;
    },
    isEmailChecked(state) {
      return state.checkEmail;
    },
    isAuthenticated(state) {
      return !!state.token;
    },
  },
});
