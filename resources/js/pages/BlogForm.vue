<template>
    <form @submit.prevent="createBlog">
        <input v-model="title" type="text" placeholder="Blog Title" />
        <textarea v-model="content" placeholder="Blog Content"></textarea>
        <input @change="handleFile" type="file" />
        <button type="submit">Create Blog</button>
    </form>
</template>

<script setup>
import { ref } from 'vue';
import { useStore } from 'vuex';

const title = ref('');
const content = ref('');
const file = ref(null);

const store = useStore();

const handleFile = (event) => {
    file.value = event.target.files[0];
};

const createBlog = async () => {
    const formData = new FormData();
    formData.append('title', title.value);
    formData.append('content', content.value);
    if (file.value) formData.append('image', file.value);

    const response = await axios.post('/api/blogs', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
    });

    store.commit('addBlog', response.data);
};
</script>
