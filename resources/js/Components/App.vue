<template>
    <div class="flex overflow-hidden">
        <navigation :auth="auth" :csrf="csrf" class="h-screen overflow-hidden"/>
        <reminder-list :channels="channels" :frequencies="frequencies" :csrf="csrf" />
    </div>
</template>

<script>
export default {
    props: ["auth", "channels", "frequencies", "csrf"],
};
</script>

<style></style>
