<script setup>
    import DatePicker from './DatePicker.vue';

    let selectedFromDate = ref();
</script>

<template>
    <DatePicker v-model="selectedFromDate">Button Text</DatePicker>
</template>