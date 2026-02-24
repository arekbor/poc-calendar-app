<script setup lang="ts">
import { useCalendarStore } from '@/stores/calendar';
import type { CalendarEvent } from '@/types/calendarEvent';
import { computed, onMounted, ref, watch, type CSSProperties } from 'vue';
import CalendarDialog from '@/components/CalendarEventDialog.vue';

const calendarStore = useCalendarStore();

const HOUR_HEIGHT = 60;

const calendarDialogRef = ref<InstanceType<typeof CalendarDialog>>();
const hourLineStyle = ref<CSSProperties>();

const daysOfWeek = computed(() => {
    const currentDate = calendarStore.date;

    const dayOfWeek = currentDate.getDay();
    const diffToMonday = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;

    const monday = new Date(currentDate);
    monday.setDate(currentDate.getDate() + diffToMonday);

    const week: Date[] = [];

    for (let i = 0; i < 7; i++) {
        const day = new Date(monday);
        day.setDate(monday.getDate() + i);
        day.setHours(0, 0, 0);
        week.push(day);
    }

    return week;
});

const onAddEvent = (dayGridDate: Date, weekCellHour: number) => {
    const start = new Date(dayGridDate.getFullYear(), dayGridDate.getMonth(), dayGridDate.getDate(), weekCellHour);
   
    const end = new Date(start);
    end.setHours(weekCellHour + 1);

    const endOfDayLimit = new Date(start);
    endOfDayLimit.setHours(23, 59, 0, 0);

    if(end.getTime() >= endOfDayLimit.getTime()) {
        end.setHours(23, 59, 0, 0);
    }

    calendarDialogRef.value?.openCreateEventDialog({
        title: '',
        start_date: dateToString(start),
        end_date: dateToString(end),
        color: null,
        isRecurring: false,
    });
};

const onEditEvent = (calendarEvent: CalendarEvent) => {
    calendarDialogRef.value?.openEditEventDialog({
        id: calendarEvent.id,
        title: calendarEvent.title,
        start_date: dateToString(calendarEvent.start_date),
        end_date: dateToString(calendarEvent.end_date),
        color: calendarEvent.color,
        isRecurring: false,
    })
};

const isCurrentDay = (weekDate: Date): boolean => {
    const now = new Date();

    return (
        now.getDate() === weekDate.getDate() &&
        now.getMonth() === weekDate.getMonth() &&
        now.getFullYear() === weekDate.getFullYear()
    );
};

const minutesFromMidnight = (date: Date): number => {
    return date.getHours() * 60 + date.getMinutes();
};

const getEventStyle = (event: CalendarEvent): CSSProperties => {
    const startMinutes = minutesFromMidnight(event.start_date);
    const endMinutes = minutesFromMidnight(event.end_date);

    const top = (startMinutes / 60) * HOUR_HEIGHT;
    const height = ((endMinutes - startMinutes) / 60) * HOUR_HEIGHT;

    return {
        top: `${top}px`,
        height: `${height}px`,
        background: event.color ?? '#1976d2'
    };
};

//on every minute updates actual hour line
const updateHourLineStyle = () => {
    const currentDate = new Date();

    const minutes = minutesFromMidnight(currentDate);
    const top = (minutes / 60) * HOUR_HEIGHT;

    hourLineStyle.value = {
        top: `${top}px`
    };
};

const dateToString = (d: Date) => {
    const pad = (n: number) => n.toString().padStart(2, '0')
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
};

watch(daysOfWeek, (days) => {
    const weekStart = days[0];
    const weekEnd = days[6];

    if (!weekStart || !weekEnd) return;

    calendarStore.fetchEvents(weekStart, weekEnd);
}, {
    immediate: true
});

onMounted(() => {
    updateHourLineStyle();
    setInterval(updateHourLineStyle, 60_000);
});

</script>

<template>
    <div class="grid-container">
        <div class="week-header-container">
            <div class="week-header-spacer"></div>

            <div 
                v-for="date in daysOfWeek" 
                :key="date.toISOString()" 
                :class="`week-header-day ${isCurrentDay(date) ? 'current' : ''}`"
            >
                <div class="week-header-day-label">
                    {{ date.toLocaleDateString('pl-PL', { weekday: 'short' }) }}
                </div>

                <div class="week-header-day-date">
                    {{ date.getDate() }}
                </div>
            </div>
        </div>

        <div class="week-grid">
            <div class="week-hours-column">
                <div 
                    v-for="hour in 24" 
                    :key="hour" 
                    class="week-hour-label"
                >
                    {{ hour - 1 }}:00
                </div>
            </div>

            <div 
                v-for="dayGridDate in daysOfWeek" 
                :key="dayGridDate.toISOString()" 
                class="week-cell-container"
            >
                <div class="week-cell-day-grid">
                    <div 
                        v-for="cellHour in 24" 
                        :key="cellHour" 
                        class="week-cell-hour" 
                        v-on:dblclick="onAddEvent(dayGridDate, cellHour - 1)"
                    >
                    </div>

                    <div 
                        v-if="isCurrentDay(dayGridDate)" 
                        class="current-hour-line" 
                        :style="hourLineStyle"
                    >
                    </div>
                </div>

                <div 
                    v-for="event in calendarStore.eventsByDate.get(dayGridDate.toDateString()) ?? []" 
                    :key="event.id" 
                    class="week-event-container" 
                    v-on:dblclick="onEditEvent(event)"
                    :style="getEventStyle(event)"
                >
                    {{ event.title }}
                </div>
            </div>
        </div>
    </div>

    <CalendarDialog ref="calendarDialogRef" />
</template>

<style scoped>
.grid-container {
    display: flex;
    flex-direction: column;
}

.grid-container .week-header-container {
    display: grid;
    grid-template-columns: var(--calendar-cell-size) repeat(7, 1fr);
    border-bottom: 1px solid #ddd;
    background: #fff;
    height: var(--calendar-week-header-height);
}

.grid-container .week-header-container .week-header-spacer {
    border-right: 1px solid #ddd;
}

.grid-container .week-header-container .week-header-day {
    text-align: center;
    padding: 6px 0;
    border-right: 1px solid #eee;
}

.grid-container .week-header-container .week-header-day.current {
    color: #5f6cb1;
}

.grid-container .week-header-container .week-header-day-label {
    font-size: 10px;
    color: #666;
}

.grid-container .week-header-container .week-header-day-date {
    font-size: 14px;
    font-weight: 600;
}

.grid-container .week-grid {
    display: grid;
    grid-template-columns: var(--calendar-cell-size) repeat(7, 1fr);
    overflow-y: auto;
    overflow-y: auto;
    height: calc(100vh - var(--calendar-header-height) - var(--calendar-week-header-height) - var(--calendar-cell-size)/2);
}

.grid-container .week-grid .week-hours-column {
    display: grid;
    grid-template-rows: repeat(24, var(--calendar-cell-size));
    border-right: 1px solid #ddd;
}

.grid-container .week-grid .week-hours-column .week-hour-label {
    font-size: 12px;
    padding: 2px 4px;
    border-bottom: 1px solid #eee;
    text-align: right;
}

.grid-container .week-grid .week-cell-container {
    position: relative;
    border-right: 1px solid #ddd;
}

.grid-container .week-grid .week-cell-container .week-cell-day-grid {
    display: grid;
    grid-template-rows: repeat(24, var(--calendar-cell-size));
}

.grid-container .week-grid .week-cell-container .week-cell-day-grid .week-cell-hour{
    border-bottom: 1px solid #eee;
    box-sizing: border-box;
}

.grid-container .week-grid .week-cell-container .week-cell-day-grid .current-hour-line {
    background-color: red;
    position: absolute;
    height: 2px;
    width: 100%;
    z-index: 1;
}

.grid-container .week-grid .week-cell-container .week-event-container {
    position: absolute;
    width: 100%;
    color: white;
    box-sizing: border-box;
}

</style>