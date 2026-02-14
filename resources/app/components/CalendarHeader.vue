<script setup lang="ts">
import { useCalendarStore } from '@/stores/calendar';
import { computed } from 'vue';

const calendarStore = useCalendarStore();

const getTranslatedMonth = computed(() => {
    const translatedMonths: Record<number, string> = {
        0: 'January',
        1: 'February',
        2: 'March',
        3: 'April',
        4: 'May',
        5: 'June',
        6: 'July',
        7: 'August',
        8: 'September',
        9: 'October',
        10: 'November',
        11: 'December'
    };

    const date = calendarStore.date;
    return `${translatedMonths[date.getMonth()]} ${date.getFullYear()}`;
});

const onTodayClick = (): void => {
    calendarStore.setDate(new Date());
};

const onPrevClick = () => {
    const calendarDate = calendarStore.date;
    const dayOfWeek = calendarDate.getDay();

    const daysToPrevMonday = dayOfWeek === 0 ? 6 + 7 : dayOfWeek - 1 + 7;

    const prevWeek = new Date(calendarDate);
    prevWeek.setDate(prevWeek.getDate() - daysToPrevMonday);

    calendarStore.setDate(prevWeek);
};

const onNextClick = () => {
    const calendarDate = calendarStore.date;
    const dayOfWeek = calendarDate.getDay();

    const daysToNextMonday = dayOfWeek === 0 ? 1 + 7 : (8 - dayOfWeek);

    const nextWeek = new Date(calendarDate);
    nextWeek.setDate(nextWeek.getDate() + daysToNextMonday);

    calendarStore.setDate(nextWeek);
};
</script>

<template>
    <div class="header-container">
        <button @click="onTodayClick">Today</button>

        <div class="button-group">
            <button @click="onPrevClick">Prev</button>
            <button @click="onNextClick">Next</button>
        </div>

        <h5>{{ getTranslatedMonth }}</h5>
    </div>
</template>

<style scoped>
    .header-container {
        display: flex;
        justify-content: space-around;
        align-items: center;
        max-height: var(--calendar-header-height);
    }

    .header-container .button-group {
        display: flex;
        gap: 10px;
    }
</style>