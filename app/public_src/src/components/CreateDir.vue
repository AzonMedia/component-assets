<template>
    <b-modal title="Create Directory" id="create-directory-modal">
        <div>
            <p>Create directory: /{{ModalData.CurrentDirPath.name}}/ <input name="directory_name" placeholder="new dir name" /></p>
        </div>
    </b-modal>
</template>

<script>
    export default {
        name: "CreateDirectory",
        props: {
            ModalData : Object
        },
        data() {
            return {
                directory_name: '',
            };
        },
    }
</script>

<style scoped>

</style>