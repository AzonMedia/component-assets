<template>
    <b-modal title="Delete" id="delete-file-modal" :ok-only="!ModalData.HighlightedFile.name">
        <div v-if="ModalData.HighlightedFile.name">
            <p>Please confirm you would like to delete file: {{ ModalData.HighlightedFile.name }}</p>
        </div>
        <div v-else>
            <p>There is no file or directory selected!</p>
        </div>
    </b-modal>
</template>

<script>
    export default {
        name: "Delete",
        props: {
            ModalData : Object
        }
    }
</script>

<style scoped>

</style>