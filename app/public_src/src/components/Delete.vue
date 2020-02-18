<template>
    <b-modal title="Delete" id="delete-file-modal" :ok-only="!ModalData.HighlightedFile.name" @ok="ModalOkHandler">
        <div v-if="ModalData.HighlightedFile.name">
            <p>Please confirm you would like to delete file/dir: {{ ModalData.CurrentDirPath.name + '/' + ModalData.HighlightedFile.name }}</p>
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
        },
        methods: {
            ModalOkHandler(bvModalEvent) {
                let url = '/admin/assets/' + this.ModalData.CurrentDirPath.name + '/' + this.ModalData.HighlightedFile.name;
                let self = this;
                this.$http.delete(url).
                    then(function() {
                        //self.$parent.get_dir_files(self.$parent.CurrentDirPath.name);
                    }).catch(function(err) {
                        //self.$parent.get_dir_files(self.$parent.CurrentDirPath.name);//refresh just in case
                        self.$parent.show_toast(err.response.data.message);
                    }).finally(function(){
                        self.$parent.get_dir_files(self.ModalData.CurrentDirPath.name);//refresh just in case
                    });
            }
        }
    }
</script>

<style scoped>

</style>