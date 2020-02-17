<template>
    <!--  @show="ModalShowHandler" -->
    <b-modal title="Upload File" id="upload-file-modal" @ok="ModalOkHandler" @cancel="ModalCancelHandler">
        <div>
            <p>Upload file to /{{ModalData.CurrentDirPath.name}}/ <input type="file" name="uploaded_file" id="uploaded-file" v-on:change="FileChangeHandler" /></p>
        </div>
    </b-modal>
</template>

<script>
    // https://serversideup.net/uploading-files-vuejs-axios/

    // https://codepen.io/Atinux/pen/qOvawK/

    // https://phil.tech/api/2016/01/04/http-rest-api-file-uploads/

    export default {
        name: "UploadFile",
        props: {
            ModalData : Object
        },
        data() {
            return {
                Files: [],
            };
        },
        methods: {
            FileChangeHandler(Event) {
                // https://blog.logrocket.com/how-to-use-refs-to-access-your-application-dom-in-vue-js/
                //this.file = this.$refs.file.files[0];

                var Files = Event.target.files || Event.dataTransfer.files;
                if (!Files.length) {
                    return;
                }
                this.Files = Files;
            },
            ModalOkHandler(bvModalEvent) {
                let UploadFormData = new FormData();
                UploadFormData.append('uploaded_file', this.Files[0]);
                let url = '/admin/assets/' + this.ModalData.CurrentDirPath.name;
                let Config = {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
                let self = this;
                this.$http.post(url, UploadFormData, Config).
                    then(function() {
                        self.Files = [];
                        self.$parent.get_dir_files(self.$parent.CurrentDirPath.name);
                    }).catch(function() {

                    });
            },
            ModalCancelHandler(bvModalEvent) {

            },
            ModalShowHandler(bvModalEvent) {

            }
        }
    }
</script>

<style scoped>

</style>