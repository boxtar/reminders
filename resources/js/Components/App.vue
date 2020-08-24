<template>
    <div>
        <!-- Navigation menu -->
        <div class="nav-panel">
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
    --navigation-width: 100%;
}
.nav-panel {
    width: var(--navigation-width);
}
.content-panel {
    width: 100%;
}

@media screen and (min-width: 767px) {
    :root {
        --navigation-width: 80px;
    }
    .nav-panel {
        position: absolute;
        height: 100vh;
        top: 0;
        left: 0;
        overflow-x: hidden;
    }
    .content-panel {
        width: calc(100% - var(--navigation-width));
        margin-left: var(--navigation-width);
    }
}
</style>
