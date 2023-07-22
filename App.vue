<script setup>
    import DatePicker from './DatePicker.vue';
    import moment from 'moment';

    let selectedFromDate = ref();
    let selectedToDate = ref();

    function doSomething() {
        window.axios.get('/webapi/do-something', {
            params: {
                selectedFromDate: selectedFromDate.value ? moment(selectedFromDate.value).toISOString(true) : moment().subtract(31, 'days').startOf('day').toISOString(true),
                selectedToDate: selectedToDate.value ? moment(selectedToDate.value).toISOString(true) : moment().startOf('day').toISOString(true),
            }
        }).then((response) => {
            selectedFromDate.value = response.data.selectedFromDate;
            selectedToDate.value = response.data.selectedToDate;
        })
    }
</script>

<template>
    <DatePicker class="mb-12 mr-5" v-model="selectedFromDate">From Date</DatePicker> 
    <DatePicker class="mb-12" showCalendarOrientation="top" v-model="selectedToDate" :disabledDatesBefore="selectedFromDate" :disabledDatesAfter="selectedFromDate ? moment(selectedFromDate).add(31, 'days').format('YYYY-MM-DD') : null">To Date</DatePicker>
    <p class="mt-2 text-sm text-red-500 font-semibold">*Max 31 days</p>
    <button type="button" @click="doSomething" class="inline-block my-5 rounded-md bg-green-500 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-green-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 mr-1"
        :class="{'opacity-50 cursor-not-allowed': !selectedFromDate || !selectedToDate}"
        :disabled="!selectedFromDate || !selectedToDate"
    >Apply</button>
</template>