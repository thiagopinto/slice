<script setup lang="ts">
import AppLayout from '../Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import FileUpload from '@/components/ui/FileUpload.vue';

</script>

<template>
    <AppLayout>

        <Head title="Home" />

        <div class="container mx-auto bg-slate-100 p-6 rounded-lg shadow-md">
            <FileUpload endpoint="api/v1/ep747/upload" label="Upload EP747 (TXT)" accept=".txt, .TXT" :debug="true" />

        </div>
    </AppLayout>
</template>
