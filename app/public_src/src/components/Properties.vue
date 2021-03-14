<template>
    <b-modal :title="ModalData.HighlightedFile.name + ' Properties'" id="properties-modal" :ok-only="true" @show="modal_show_handler">
        <div v-if="ModalData.HighlightedFile.name">
            <table v-if="FileData.name">
                <tr>
                    <td>Name:</td>
                    <td>{{ FileData.name }}</td>
                </tr>
                <tr>
                    <td>Path:</td>
                    <td>{{ FileData.path }}</td>
                </tr>
                <tr>
                    <td>Type:</td>
                    <td>{{ FileData.type }}</td>
                </tr>
                <tr>
                    <td>Mime-type:</td>
                    <td>{{ FileData.mime_type }}</td>
                </tr>
                <tr>
                    <td>Size:</td>
                    <td>{{ FileData.size }}</td>
                </tr>
                <tr>
                    <td>Created:</td>
                    <td>{{ FileData.ctime }}</td>
                </tr>
                <tr>
                    <td>Modified:</td>
                    <td>{{ FileData.mtime }}</td>
                </tr>
                <tr>
                    <td>Accessed:</td>
                    <td>{{ FileData.atime }}</td>
                </tr>
            </table>
            <img v-if="image_preview" :src="image_preview" height="300">
        </div>
        <div v-else>
            <p>There is no file or directory selected!</p>
        </div>
    </b-modal>
</template>

<script>
    import config from '@/config.js'
    export default {
        name: "Delete",
        props: {
            ModalData : Object
        },
        data() {
            return {
                FileData: {},
                image_preview: '',
            }
        },
        methods: {
            modal_show_handler(bvModalEvent) {
                let url = '/admin/asset/properties/' + this.ModalData.CurrentDirPath.name + '/' + this.ModalData.HighlightedFile.name;
                let self = this
                this.$http.get(url).
                    then(function(resp) {
                        self.FileData = resp.data;
                        if (self.is_image(self.FileData.mime_type)) {
                            console.log(self.FileData.path);
                            //todo - move this to ConfigMixin ov env file
                            //self.image_preview = 'http://localhost:8081/assets/' + self.FileData.path
                            self.image_preview = config[config.deployment].assets_base + 'assets/' + self.FileData.path
                            //console.log(self.image_preview);
                        }
                    }).catch(function(err) {
                        self.$parent.show_toast(err.response.data.message);
                    });
            },
            is_image(mime_type) {
                return true;
            }
        }
    }
</script>

<style scoped>

</style>