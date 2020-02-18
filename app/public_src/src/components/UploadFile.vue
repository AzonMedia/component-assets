<template>
    <b-modal title="Upload File" id="upload-file-modal" @ok="modal_ok_handler" @cancel="modal_cancel_handler" @show="modal_show_handler">
        <div v-if="!Files[0]">
            <p>Upload file to /{{ModalData.CurrentDirPath.name}}/ <input type="file" name="uploaded_file" id="uploaded-file" v-on:change="file_change_handler" /></p>
        </div>
        <div id="upload-preview" v-else>
            You are about to upload {{ Files[0].name }} of {{ Math.round(Files[0].size / 1024 , 2) }} kb
            <button @click="remove_file">Remove</button>
            <img :src="previe_image" height="300">
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
                previe_image: '',
            };
        },
        methods: {
            file_change_handler(Event) {
                // https://blog.logrocket.com/how-to-use-refs-to-access-your-application-dom-in-vue-js/
                //this.file = this.$refs.file.files[0];

                var Files = Event.target.files || Event.dataTransfer.files;
                if (!Files.length) {
                    return;
                }
                this.Files = Files;
                this.create_image(this.Files[0]);
            },
            modal_ok_handler(bvModalEvent) {
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
                    }).catch(function(err) {
                        self.$parent.get_dir_files(self.$parent.CurrentDirPath.name);//refresh just in case
                        self.$parent.show_toast(err.response.data.message);
                    });
            },
            modal_cancel_handler(bvModalEvent) {
                this.Files = [];
            },
            modal_show_handler(bvModalEvent) {
                this.Files = [];
            },
            create_image(file) {
                console.log(file);
                const image = new Image();
                const reader = new FileReader();
                const self = this;

                reader.onload = (Event) => {
                    self.previe_image = Event.target.result;
                };
                reader.readAsDataURL(file);
            },
            remove_file(Event) {
                this.Files = [];
                this.previe_image = '';
            },
        }
    }
</script>

<style scoped>

</style>