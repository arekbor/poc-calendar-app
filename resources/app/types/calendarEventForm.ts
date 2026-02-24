export interface CalendarEventForm {
    id: number | null,
    title: string|null,
    start_date: string|null,
    end_date: string|null,
    color: string|null,
    isRecurring: boolean
};