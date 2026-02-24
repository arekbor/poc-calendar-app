import api from "@/api";
import type { AddCalendarEvent } from "@/types/addCalendarEvent";
import type { CalendarEvent } from "@/types/calendarEvent";
import { defineStore } from "pinia";
import { computed, ref } from "vue";

export const useCalendarStore = defineStore('calendar', () => {
    const date = ref<Date>(new Date());
    const events = ref<CalendarEvent[]>([]);

    const setDate = (d: Date) => {
        date.value = d;
    };

    const getYMD = (date: Date) => {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');

        return `${y}-${m}-${d}`;
    };

    const parseCalendarEvent = (calendarEvent: CalendarEvent): CalendarEvent => {
        console.log(calendarEvent);

        return {
            ...calendarEvent,
            start_date: new Date(calendarEvent.start_date),
            end_date: new Date(calendarEvent.end_date)
        };
    };

    const fetchEvents = async (weekStart: Date, weekEnd: Date) => {
        const start = getYMD(weekStart);
        const end = getYMD(weekEnd);
        
        const { data } = await api.get<CalendarEvent[]>(`/calendarEvent/${start}/${end}`);
        events.value = data.map(parseCalendarEvent);
    };

    const addCalendarEvent = async (addCalendarEvent: AddCalendarEvent) => {
        const { data } = await api.post<CalendarEvent>('/calendarEvent', addCalendarEvent);

        events.value.push(parseCalendarEvent(data));
    };

    const updateCalendarEvent = async (calendarEvent: CalendarEvent) => {
        const { data } = await api.put<CalendarEvent>(`/calendarEvent`, calendarEvent);

        const calendarEventToUpdate = events.value.find(e => e.id === calendarEvent.id);

        if (calendarEventToUpdate) {
            const parsedCalendarEvent = parseCalendarEvent(data);

            calendarEventToUpdate.title = parsedCalendarEvent.title;
            calendarEventToUpdate.start_date = parsedCalendarEvent.start_date;
            calendarEventToUpdate.end_date = parsedCalendarEvent.end_date;
            calendarEventToUpdate.color = parsedCalendarEvent.color;
        }
    };

    const deleteCalendarEvent = async (id: number) => {
        await api.delete(`/calendarEvent/${id}`);

        events.value = events.value.filter((event) => { return event.id !== id });
    };

    const eventsByDate = computed(() => {
        const map = new Map<string, CalendarEvent[]>();

        for(const event of events.value) {
            const key = event.start_date.toDateString();

            if (!map.has(key)) map.set(key, []);
            map.get(key)!.push(event);
        }

        return map;
    });

    return {
        date,
        events,
        fetchEvents,
        addCalendarEvent,
        updateCalendarEvent,
        deleteCalendarEvent,
        eventsByDate,
        setDate,
    };
});