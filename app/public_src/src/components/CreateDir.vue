<template>
    <b-modal title="Create Directory" id="create-directory-modal" @ok="modal_ok_handler" @cancel="modal_cancel_handler" @show="modal_show_handler">
        <div>
            <p>Create directory: /{{ModalData.CurrentDirPath.name}}/ <input v-model="new_directory_name" type="text" placeholder="new dir name" /></p>
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
                new_directory_name: '',
            };
        },
        methods: {
            modal_ok_handler(bvModalEvent) {
                let url = '/admin/assets/' + this.ModalData.CurrentDirPath.name;
                let self = this;
                //form data submits forms not plain JSON
                //let DirFormData = new FormData();
                //DirFormData.append('new_directory_name', this.new_directory_name);
                //this.$http.post(url, DirFormData).
                let SendValues = {};
                SendValues.new_directory_name = this.new_directory_name;
                this.$http.post(url, SendValues).
                    then(function() {

                    }).catch(function(err) {
                        self.$parent.show_toast(err.response.data.message);
                    }).finally(function(){
                        self.$parent.get_dir_files(self.ModalData.CurrentDirPath.name);
                    });
            },
            modal_cancel_handler(bvModalEvent) {
                this.new_directory_name = '';
            },
            modal_show_handler(bvModalEvent) {
                this.new_directory_name = '';
            }
        }
    }
</script>

<style scoped>

</style>