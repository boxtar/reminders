<template>
    <div>
        <!-- Navigation menu -->
        <div class="nav-panel h-screen overflow-x-hidden overflow-y-scroll">
            <navigation :auth="auth" :csrf="csrf" />
        </div>

        <!-- Main panel -->
        <div class="content-panel">
            <reminder-list :channels="channels" :frequencies="frequencies" :csrf="csrf" />
        </div>
    </div>
</template>

<script>
export default {
    props: ["auth", "channels", "frequencies", "csrf"],
};
</script>

<style>
:root {
    --navigation-width: 50px;
}
.nav-panel {
    position: absolute;
    top: 0;
    left: 0;
    width: var(--navigation-width);
}
.content-panel {
    width: calc(100% - var(--navigation-width));
    margin-left: var(--navigation-width);
}

@media screen and (min-width: 767px) {
    :root {
        --navigation-width: 80px;
    }
}
</style>
