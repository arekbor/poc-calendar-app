<script setup lang="ts">
import { ref } from 'vue';
import Dialog from './Dialog.vue';
import type { CalendarEventForm } from '@/types/calendarEventForm';
import { useCalendarStore } from '@/stores/calendar';

const calendarStore = useCalendarStore();

const calendarEventDialogRef = ref<InstanceType<typeof Dialog> | null>(null);

const form = ref<CalendarEventForm>({
    id: null,
    title: null,
    start_date: null,
    end_date: null,
    color: null
})

const loading = ref(false);

const errorMessage = ref<string | null>(null);

const openCreateEventDialog = (payload: Omit<CalendarEventForm, 'id'>) => {
    errorMessage.value = null;
    form.value = { id: null, ...payload };

    calendarEventDialogRef.value?.show();
};

const openEditEventDialog = (payload: CalendarEventForm) => {
    errorMessage.value = null;
    form.value = { ...payload };

    calendarEventDialogRef.value?.show();
};

const closeCalendarEventDialog = () => {
    calendarEventDialogRef.value?.close();
};

const onCreateSubmit = async () => {
    loading.value = true;

    try {
        const start_date = form.value.start_date ? new Date(form.value.start_date) : null;
        const end_date = form.value.end_date ? new Date(form.value.end_date) : null;

        if (!start_date || !end_date) {
            errorMessage.value = "Start or end date not defined.";
            return;
        }

        if (!form.value.title) {
            errorMessage.value = "Title is required";
            return;
        }

        await calendarStore.addCalendarEvent({
            title:  form.value.title,
            start_date: start_date,
            end_date: end_date,
            color: form.value.color
        }); 

        closeCalendarEventDialog();
    } finally {
        loading.value = false;
    }
};

const onEditSubmit = async () => {
    loading.value = true;

    try {
        const id = form.value.id;
        if (!id) throw new Error('id not found');

        const start_date = form.value.start_date ? new Date(form.value.start_date) : null;
        const end_date = form.value.end_date ? new Date(form.value.end_date) : null;

        if (!start_date || !end_date) {
            errorMessage.value = "Start or end date not defined."
            return;
        }

        if (!form.value.title) {
            errorMessage.value = "Title is required";
            return;
        }

        await calendarStore.updateCalendarEvent({
            id: id,
            title: form.value.title,
            start_date: start_date,
            end_date: end_date,
            color: form.value.color
        });

        closeCalendarEventDialog();
    } finally {
        loading.value = false;
    }
};

const onDelete = async () => {
    loading.value = true;

    const id = form.value.id;
    if (!id) throw new Error('id not found');

    try {
        await calendarStore.deleteCalendarEvent(id);
        closeCalendarEventDialog();
    } finally {
        loading.value = false;
    }
};

defineExpose({
    openCreateEventDialog,
    openEditEventDialog,
    closeCalendarEventDialog
});
</script>

<template>
    <Dialog ref="calendarEventDialogRef">
        <form v-on:submit.prevent="form.id !== null ? onEditSubmit() : onCreateSubmit()">
            <h3>
                {{ form.id !== null ? `Edit event ID: ${form.id}` : 'Create event' }}
            </h3>

            <template v-if="form.id !== null">
                <button type="button" v-on:click="onDelete" :disabled="loading">Delete event</button>
            </template>

            <div class="form-cell">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" v-model="form.title" />
            </div>

            <div class="form-cell">
                <label for="start">Start</label>
                <input type="datetime-local" name="start" id="start" v-model="form.start_date" />
            </div>

            <div class="form-cell">
                <label for="end">End</label>
                <input type="datetime-local" name="end" id="end" v-model="form.end_date" />
            </div>

            <div class="form-cell">
                <button type="submit" :disabled="loading">
                    {{ form.id !== null ? 'Save' : 'Create' }}
                </button>
            </div>

            <div v-if="errorMessage">
                <small>{{ errorMessage }}</small>
            </div>
        </form>
    </Dialog>
</template>