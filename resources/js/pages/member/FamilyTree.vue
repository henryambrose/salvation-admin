<template>
    <div class="p-4">
        <h2 class="mb-4 text-xl font-bold">Family Tree for {{ member.first_name }} {{ member.last_name }}</h2>
        <ul class="list-disc pl-6">
            <li v-for="relation in relationships" :key="relation.id">
                {{ relation.relationship.name }}: {{ relation.related_member.first_name }} {{ relation.related_member.last_name }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: ['member', 'relationships'],
};
</script>
