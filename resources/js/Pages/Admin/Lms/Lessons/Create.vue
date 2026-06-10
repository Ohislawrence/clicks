<template>
    <LessonForm :course="course" />
</template>

<script setup>
import LessonForm from './LessonForm.vue';
defineProps({ course: Object });
</script>
