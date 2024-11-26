<template>
    <div>
        <h1>Blogs</h1>
        <div v-for="blog in blogs" :key="blog.id">
            <h3>{{ blog.title }}</h3>
            <p>{{ blog.content }}</p>
            <p>By: {{ blog.user.name }}</p>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useStore } from 'vuex';

const store = useStore();

const blogs = computed(() => store.state.blogs);

onMounted(() => {
    store.dispatch('fetchBlogs');
});
</script>
