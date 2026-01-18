<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { upload } from '@/routes';
import type { BreadcrumbItemType } from '@/types';
import { Head } from '@inertiajs/vue3';


const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Upload',
        href: upload().url,
    },
];

</script>

<template>
    <Head title="Upload" />

    <AppLayout
        :breadcrumbs="breadcrumbs"
        :content-container="true"
        :content-padded="true"
    >
    </AppLayout>
</template>