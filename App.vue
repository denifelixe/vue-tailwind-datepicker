<script setup>
    import DatePicker from './DatePicker.vue';
    import moment from 'moment';

    let selectedFromDate = ref();
    let selectedToDate = ref();
</script>

<template>
    <DatePicker class="mb-12 mr-5" v-model="selectedFromDate">From Date</DatePicker> 
    <DatePicker class="mb-12" showCalendarOrientation="top" v-model="selectedToDate" :disabledDatesBefore="selectedFromDate" :disabledDatesAfter="selectedFromDate ? moment(selectedFromDate).add(31, 'days').format('YYYY-MM-DD') : null">To Date</DatePicker>
</template>