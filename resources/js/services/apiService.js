const APP_API_URL = import.meta.env.VITE_API_URL;

const apiService = {
  async fetchBlogs(page = 1) {
    try {
      const token = localStorage.getItem('token');
      const response = await fetch(`${APP_API_URL}blogs?page=${page}`, {
        headers: {
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`,
        },
      });
      if (!response.ok) throw new Error('Failed to fetch blogs');
      const data = await response.json();
      return data;
    } catch (error) {
      throw error;
    }
  },

  async registerUser(formData) {
    try {
      const response = await fetch(`${APP_API_URL}register`, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || 'Failed to register user');
      return data;
    } catch (error) {
      throw error;
    }
  },

  async loginUser(formData) {
    try {
      const response = await fetch(`${APP_API_URL}login`, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });
      const data = await response.json();
      if (!response.ok) throw new Error(data.message || 'Failed to login');
      return data;
    } catch (error) {
      throw error;
    }
  },

  async fetchUserData(token) {
    try {
      const response = await fetch(`${APP_API_URL}me`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json',
        },
      });
      const data = await response.json();
      if (!response.ok) throw new Error('Failed to fetch user data');
      return data;
    } catch (error) {
      throw error;
    }
  },

  async fetchUserBlogs(page = 1) {
    try {
      const token = localStorage.getItem('token');
      const response = await axios.get(`${APP_API_URL}blogs/user?page=${page}`, {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      });
      return response.data;
    } catch (error) {
      throw error;
    }
  },

  async createBlog(blog) {
    try {
      const token = localStorage.getItem('token');
      const response = await axios.post(`${APP_API_URL}blogs`, blog, {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      });
      return response.data;
    } catch (error) {
      throw error;
    }
  },

  async updateBlog(id, blog) {
    try {
      const token = localStorage.getItem('token');
      const response = await axios.post(`${APP_API_URL}blogs/${id}`, blog, {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      });
      return response.data;
    } catch (error) {
      throw error;
    }
  },

  async deleteBlog(id) {
    try {
      const token = localStorage.getItem('token');
      const response = await axios.delete(`${APP_API_URL}blogs/${id}`, {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      });
      return response.data;
    } catch (error) {
      throw error;
    }
  },
};

export default apiService;
