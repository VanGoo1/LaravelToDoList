<template>
    <Header></Header>
    <main>
        <router-view/>
    </main>
</template>

<script setup>
import Header from "@/components/Header.vue";

</script>

<style lang="sass" scoped>
@import "/resources/css/app.css"
</style>
