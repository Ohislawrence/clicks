<script setup>
import Create from './Create.vue';

defineProps({
    offer: Object,
    categories: Array
});
</script>

<template>
    <Create :offer="offer" :categories="categories" />
</template>
