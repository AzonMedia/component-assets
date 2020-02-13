<template>
    <div>
        <div>Path: {{current_dir_display_path}}</div>
        <div>
            <ButtonC v-bind:ButtonData="Buttons.ParentDirButton"></ButtonC>
            <ButtonC v-bind:ButtonData="Buttons.CreateDirButton"></ButtonC>
            <ButtonC v-bind:ButtonData="Buttons.UploadFileButton"></ButtonC>
            <ButtonC v-bind:ButtonData="Buttons.CopyButton"></ButtonC>
            <ButtonC v-bind:ButtonData="Buttons.RenameButton"></ButtonC>
            <ButtonC v-bind:ButtonData="Buttons.DeleteButton"></ButtonC>
            <ButtonC v-bind:ButtonData="Buttons.PropertiesButton"></ButtonC>
        </div>
        <div style="clear:both"></div>
        <div>
            <FileC v-for="(FileData, index) in Files" v-bind:FileData="FileData"/>
        </div>
    </div>
</template>

<script>

    import FileC from '@GuzabaPlatform.Assets/components/File.vue'
    import ButtonC from '@GuzabaPlatform.Platform/components/Button.vue'
    import AliasesMixin from '@GuzabaPlatform.Platform/aliasesMixin.js'

    export default {
        name: "AssetsAdmin",
        mixins: [AliasesMixin],
        components: {
            FileC,
            ButtonC,
        },

        data() {
            return {
                current_dir_path : '',
                //current_dir_path : this.base_dir_path,
                //base_dir_path : '[/public/assets] /',
                //base_dir_path : '/',
                current_dir_display_path : '',
                base_dir_display_path : '[/public/assets] /',
                Files : {},
                Buttons: {
                    ParentDirButton: {
                        label: 'Parent Dir',
                        is_active: true,
                        handler: this.create_dir_handler,
                    },
                    CreateDirButton: {
                        label: 'Create Dir',
                        is_active: true,
                    },
                    UploadFileButton: {
                        label: 'Upload File',
                        is_active: true,
                    },
                    CopyButton: {
                        label: 'Copy',
                        is_active: true,
                    },
                    RenameButton: {
                        label: 'Rename/Move',
                        is_active: true,
                    },
                    DeleteButton: {
                        label: 'Delete',
                        is_active: true,
                    },
                    PropertiesButton: {
                        label: 'Properties',
                        is_active: true,
                    },
                },
            }
        },
        watch: {
            $route (to, from) { // needed because by default no class is loaded and when it is loaded the component for the two routes is the same.
                // this.selectedClassName = this.$route.params.class.split('-').join('\\');
                // //console.log("ASD " + this.selectedClassName)
                // this.getClassObjects(this.selectedClassName);
                //console.log(this.$route.params);
                let path = this.current_dir_path;
                if (typeof this.$route.params.pathMatch !== "undefined") {
                    path = this.$route.params.pathMatch
                }
                this.get_dir_files(path)
            }
        },
        methods: {
            get_dir_files(path) {
                //console.log("ASD")
                this.current_dir_path = path;
                this.current_dir_display_path = this.base_dir_display_path + this.current_dir_path;
                let self = this;
                this.$http.get('/admin/assets/' + path )
                    .then(resp => {
                        self.Files = Object.values(resp.data.files);
                    })
                    .catch(err => {
                        console.log(err);
                        self.Files = [];
                        //self.requestError = err;
                        //self.items_permissions = [];
                    }).finally(function(){
                        //self.$bvModal.show('class-permissions');
                    });
            },
            file_click_handler(FileData) {
                if (FileData.is_dir) {
                    let path = this.current_dir_path + FileData.name;
                    path = path.split('./').join('');
                    this.$router.push('/admin/assets/' + path)
                    //this.$router.push('/assets/' + ;
                } else {

                }
            },
            create_dir_handler() {

            },
        },
        mounted() {
            //this.get_dir_files(this.current_dir_path);
            console.log(this.current_dir_path);
            let path = this.current_dir_path;
            //console.log(typeof this.$route.params.pathMatch);
            //console.log(path);
            if (typeof this.$route.params.pathMatch !== "undefined") {
                console.log("VVVVVVVVVV")
                path = this.$route.params.pathMatch
            }
            //console.log(path);
            //console.log("AAAAAAAAAAAAAA")
            this.get_dir_files(path);
        }
    }
</script>

<style scoped>

</style>