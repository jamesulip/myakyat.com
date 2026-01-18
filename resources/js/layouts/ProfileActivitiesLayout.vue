<script setup lang="ts">
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

withDefaults(
    defineProps<{
        profileTitle?: string;
        activitiesTitle?: string;

        showActivitiesCard?: boolean;
        showActivitiesHeader?: boolean;
    }>(),
    {
        profileTitle: 'Profile',
        activitiesTitle: 'Activities',

        showActivitiesCard: true,
        showActivitiesHeader: true,
    },
);
</script>

<template>
    <div class="flex flex-col gap-4">
        <slot name="cover" />

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <slot name="profile" />
            </div>

            <Card v-if="showActivitiesCard" class="lg:col-span-8 shadow-none">
                <CardHeader v-if="showActivitiesHeader">
                    <CardTitle>{{ activitiesTitle }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <slot name="activities" />
                </CardContent>
            </Card>

            <div v-else class="lg:col-span-8">
                <slot name="activities" />
            </div>
        </div>
    </div>
</template>
