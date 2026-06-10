<template>
    <CourseForm :course="course" />
</template>

<script setup>
import CourseForm from './CourseForm.vue';

defineProps({
    course: Object,
});
</script>
