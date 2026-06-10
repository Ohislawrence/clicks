<template>
    <LessonForm :course="course" :lesson="lesson" />
</template>

<script setup>
import LessonForm from './LessonForm.vue';
defineProps({ course: Object, lesson: Object });
</script>
