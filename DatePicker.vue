<script setup>
    import { ref, onMounted, watch, computed } from 'vue';
    import moment from 'moment';

    const props = defineProps([
        'class',
        'modelValue',

        'disabledDatesBefore',
        'disabledDatesAfter',
        'showCalendarOrientation', //top, bottom
    ]);

    const emit = defineEmits([
        'update:modelValue'
    ]);

    let showCalendar = ref(false);
    
    const now = moment();
    const currentDate = now.date();
    const currentMonth = now.month() + 1;
    const currentYear = now.year();

    let showingMonth = ref(currentMonth);
    watch(showingMonth, (newValue, oldValue) => {
        setCalendar(newValue, showingYear.value);
    });

    let showingYear = ref(currentYear);
    //years is 100 years before and 20 years after current year sort by greatest number
    let years = ref(Array.from({length: 120}, (v, k) => currentYear - 100 + k).sort((a, b) => b - a));
    watch(showingYear, (newValue, oldValue) => {
        setCalendar(showingMonth.value, newValue);
    });

    let selectedDate = ref(0);
    let selectedMonth = ref(0);
    let selectedYear = ref(0);
    let formattedSelectedDate = computed(() => {
        if (selectedDate.value && selectedMonth.value && selectedYear.value) {

            //if selected date is before disabled dates before the reset selected date
            if (
                (props.disabledDatesBefore && moment(selectedYear.value + '-' + selectedMonth.value + '-' + selectedDate.value, 'YYYY-M-D').isBefore(props.disabledDatesBefore)) 
                || 
                (props.disabledDatesAfter && moment(selectedYear.value + '-' + selectedMonth.value + '-' + selectedDate.value, 'YYYY-M-D').isAfter(props.disabledDatesAfter))
            ) {
                selectedDate.value = 0;
                selectedMonth.value = 0;
                selectedYear.value = 0;

                emit('update:modelValue', null);

                return null;
            }

            return moment(selectedYear.value + '-' + selectedMonth.value + '-' + selectedDate.value, 'YYYY-M-D').format('YYYY-MM-DD');

        } else {
            return null;
        }
    })
    let readableSelectedDate = computed(() => {
        return formattedSelectedDate.value ? moment(formattedSelectedDate.value, 'YYYY-MM-DD').format('dddd, D MMMM YYYY') : 'Choose a date...';
    });
    function selectDate(date) {
        selectedDate.value = date;
        selectedMonth.value = showingMonth.value;
        selectedYear.value = showingYear.value;

        emit('update:modelValue', formattedSelectedDate.value);

        showCalendar.value = false;
    };

    let daysInPreviousShowingMonthArray = ref([]);
    let daysInShowingMonthArray = ref([]);
    let daysInNextShowingMonthArray = ref([]);

    function daysInMonth(month, year) {
        return moment(year + '-' + month, 'YYYY-MM').daysInMonth();
    };
    function firstDayOfMonth(month, year) {
        return moment(year + '-' + month, 'YYYY-MM').startOf('month').isoWeekday();
    };

    function setCalendar(month, year) {
        
        let firstDayOfShowingMonth = firstDayOfMonth(month, year);
        let daysInShowingMonth = daysInMonth(month, year);
        let daysInPreviousShowingMonth = daysInMonth((month - 1) ? (month - 1) : 12, year);

        daysInPreviousShowingMonthArray.value = [];
        daysInShowingMonthArray.value = [];
        daysInNextShowingMonthArray.value = [];

        for (let i = 1; i < firstDayOfShowingMonth; i++) {
            daysInPreviousShowingMonthArray.value.push(daysInPreviousShowingMonth - (firstDayOfShowingMonth - 1) + i);
        }

        for (let i = 1; i <= daysInShowingMonth; i++) {
            daysInShowingMonthArray.value.push(i);
        }

        for (let i = 1; i <= 42 - (daysInPreviousShowingMonthArray.value.length + daysInShowingMonthArray.value.length); i++) {
            daysInNextShowingMonthArray.value.push(i);
        }

    };

    function nextMonth() {
        if (showingMonth.value == 12) {
            showingMonth.value = 1;
            showingYear.value++;
        } else {
            showingMonth.value++;
        }
        setCalendar(showingMonth.value, showingYear.value);
    }

    function previousMonth() {
        if (showingMonth.value == 1) {
            showingMonth.value = 12;
            showingYear.value--;
        } else {
            showingMonth.value--;
        }
        setCalendar(showingMonth.value, showingYear.value);
    }

    setCalendar(showingMonth.value, showingYear.value);

    const calendarElement = ref(null);
    onMounted(() => {
        document.addEventListener('click', function(event) {
            if (!calendarElement.value.contains(event.target)) {
                showCalendar.value = false;
            }
        });
    });
</script>


<template>
    <!-- This component is from TailwindUI -->
    <div class="relative inline-block" ref="calendarElement" :class="class">

        <button type="button" @click="showCalendar = true" class="inline-block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 mr-1"><slot></slot></button> <span>{{ readableSelectedDate  }}</span>

        <div class="w-96 max-w-sm p-5 bg-white border rounded-lg ring-1 ring-gray-200 z-50" 
            :class="{ 
                'absolute bottom-12' : showCalendarOrientation == 'top',
                'absolute top-12' : !showCalendarOrientation || showCalendarOrientation == 'bottom',
            }" 
        v-show="showCalendar">
            <div class="flex items-center justify-center">
                <div class="flex text-sm font-semibold text-gray-900 items-center justify-center">
                    <div class="inline-block mr-2">
                        <select id="selected-month" v-model="showingMonth" class="rounded-md border-0 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                    <div class="inline-block mr-2">
                        <select id="selected-month" v-model="showingYear" class="rounded-md border-0 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option v-for="year in years" :value="year" :key="year">{{ year }}</option>
                        </select>
                    </div>
                </div>
                <button type="button" @click="previousMonth" class="-my-1.5 flex flex-none items-center justify-center p-1.5 text-gray-400 hover:text-gray-500">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                    </svg>
                </button>
                <button type="button" @click="nextMonth" class="-my-1.5 -mr-1.5 ml-2 flex flex-none items-center justify-center p-1.5 text-gray-400 hover:text-gray-500">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                </button>
                
                <button type="button" @click="showCalendar = false" class="-my-1.5 -mr-1.5 ml-8 flex flex-none items-center justify-center p-1.5 text-gray-400 hover:text-gray-500">
                    <i class="fa-regular fa-circle-xmark text-lg text-red-600"></i>
                </button>
            </div>
            <div class="mt-10 grid grid-cols-7 text-center text-xs leading-6 text-gray-500">
                <div>M</div>
                <div>T</div>
                <div>W</div>
                <div>T</div>
                <div>F</div>
                <div>S</div>
                <div>S</div>
            </div>
            <div class="mt-2 grid grid-cols-7 text-sm">
                <div class="py-2" v-for="date in daysInPreviousShowingMonthArray" :key="date">
                    <button @click="previousMonth(); selectDate(date)" type="button" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-400"
                        :class="{ 
                            'relative cursor-not-allowed pointer-events-none' : disabledDatesBefore && moment(((showingMonth - 1) == 0 ? (showingYear - 1) : showingYear) + '-' + ((showingMonth - 1) == 0 ? 12 : (showingMonth - 1)) + '-' + date, 'YYYY-M-D').isBefore(disabledDatesBefore),
                            'relative cursor-not-allowed pointer-events-none' : disabledDatesAfter && moment(((showingMonth - 1) == 0 ? (showingYear - 1) : showingYear) + '-' + ((showingMonth - 1) == 0 ? 12 : (showingMonth - 1)) + '-' + date, 'YYYY-M-D').isAfter(disabledDatesAfter),
                            'text-indigo-500 hover:bg-gray-200' : date == currentDate && (showingMonth == ((currentMonth + 1) != 13 ? (currentMonth + 1) : 1)) && (showingYear == ((currentMonth + 1) == 13 ? currentYear + 1 : currentYear)) ,
                            'bg-gray-200 hover:bg-gray-200' : date == selectedDate && (showingMonth == ((selectedMonth + 1) != 13 ? (selectedMonth + 1) : 1)) && (showingYear == ((selectedMonth + 1) == 13 ? selectedYear + 1 : selectedYear)) 
                        }"
                    >
                        <time>{{ date }}</time>
                        <i v-if="disabledDatesBefore && moment(((showingMonth - 1) == 0 ? (showingYear - 1) : showingYear) + '-' + ((showingMonth - 1) == 0 ? 12 : (showingMonth - 1)) + '-' + date, 'YYYY-M-D').isBefore(disabledDatesBefore)" class="fa-sharp fa-light fa-ban absolute text-3xl text-gray-300"></i>
                        <i v-if="disabledDatesAfter && moment(((showingMonth - 1) == 0 ? (showingYear - 1) : showingYear) + '-' + ((showingMonth - 1) == 0 ? 12 : (showingMonth - 1)) + '-' + date, 'YYYY-M-D').isAfter(disabledDatesAfter)" class="fa-sharp fa-light fa-ban absolute text-3xl text-gray-300"></i>
                    </button>
                </div>

                <div class="py-2" v-for="date in daysInShowingMonthArray" :key="date">
                    <button type="button" @click="selectDate(date)" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-900" 
                        :class="{ 
                            'relative cursor-not-allowed pointer-events-none' : disabledDatesBefore && moment(showingYear + '-' + showingMonth + '-' + date, 'YYYY-M-D').isBefore(disabledDatesBefore),
                            'relative cursor-not-allowed pointer-events-none' : disabledDatesAfter && moment(showingYear + '-' + showingMonth + '-' + date, 'YYYY-M-D').isAfter(disabledDatesAfter),
                            'font-semibold text-indigo-600 hover:bg-gray-200' : date == currentDate && showingMonth == currentMonth && showingYear == currentYear,
                            'bg-indigo-600 hover:bg-indigo-600 text-white' : date == selectedDate && showingMonth == selectedMonth && showingYear == selectedYear 
                        }"

                    >
                        <time>{{ date }}</time>
                        <i v-if="disabledDatesBefore && moment(showingYear + '-' + showingMonth + '-' + date, 'YYYY-M-D').isBefore(disabledDatesBefore)" class="fa-sharp fa-light fa-ban absolute text-3xl text-gray-400"></i>
                        <i v-if="disabledDatesAfter && moment(showingYear + '-' + showingMonth + '-' + date, 'YYYY-M-D').isAfter(disabledDatesAfter)" class="fa-sharp fa-light fa-ban absolute text-3xl text-gray-400"></i>
                    </button>
                </div>

                <div class="py-2" v-for="date in daysInNextShowingMonthArray" :key="date">
                    <button type="button" @click="nextMonth(); selectDate(date)" class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-gray-400"
                        :class="{ 
                            'relative cursor-not-allowed pointer-events-none' : disabledDatesBefore && moment(((showingMonth + 1) == 13 ? showingYear + 1 : showingYear) + '-' + ((showingMonth + 1) == 13 ? 1 : (showingMonth + 1)) + '-' + date, 'YYYY-M-D').isBefore(disabledDatesBefore),
                            'relative cursor-not-allowed pointer-events-none' : disabledDatesAfter && moment(((showingMonth + 1) == 13 ? showingYear + 1 : showingYear) + '-' + ((showingMonth + 1) == 13 ? 1 : (showingMonth + 1)) + '-' + date, 'YYYY-M-D').isAfter(disabledDatesAfter),
                            'text-indigo-500 hover:bg-gray-200' : date == currentDate && (showingMonth == ((currentMonth - 1) != 0 ? (currentMonth - 1) : 12)) && (showingYear == ((currentMonth - 1) == 0 ? currentYear - 1 : currentYear)),
                            'bg-gray-200 hover:bg-gray-200' : date == selectedDate && (showingMonth == ((selectedMonth - 1) != 0 ? (selectedMonth - 1) : 12)) && (showingYear == ((selectedMonth - 1) == 0 ? selectedYear - 1 : selectedYear))
                        }"
                    >
                        <time>{{ date }}</time>
                        <i v-if="disabledDatesBefore && moment(((showingMonth + 1) == 13 ? showingYear + 1 : showingYear) + '-' + ((showingMonth + 1) == 13 ? 1 : (showingMonth + 1)) + '-' + date, 'YYYY-M-D').isBefore(disabledDatesBefore)" class="fa-sharp fa-light fa-ban absolute text-3xl text-gray-300"></i>
                        <i v-if="disabledDatesAfter && moment(((showingMonth + 1) == 13 ? showingYear + 1 : showingYear) + '-' + ((showingMonth + 1) == 13 ? 1 : (showingMonth + 1)) + '-' + date, 'YYYY-M-D').isAfter(disabledDatesAfter)" class="fa-sharp fa-light fa-ban absolute text-3xl text-gray-300"></i>
                    </button>
                </div>

            </div>
        </div>

    </div>
</template>