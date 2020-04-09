<template>
    <div>
        <div>Path: {{current_dir_display_path}}</div>
        <div>
            <ButtonC v-bind:ButtonData="Buttons.ParentDirButton"></ButtonC>
            <ButtonC v-bind:ButtonData="Buttons.CreateDirButton" v-b-modal.create-directory-modal></ButtonC>
            <ButtonC v-bind:ButtonData="Buttons.UploadFileButton" v-b-modal.upload-file-modal></ButtonC>
            <!-- <ButtonC v-bind:ButtonData="Buttons.CopyButton"></ButtonC> -->
            <!-- <ButtonC v-bind:ButtonData="Buttons.RenameButton"></ButtonC> -->
            <ButtonC v-bind:ButtonData="Buttons.DeleteButton" v-b-modal.delete-file-modal></ButtonC>
            <ButtonC v-bind:ButtonData="Buttons.PropertiesButton" v-b-modal.properties-modal></ButtonC>
            <!-- <ButtonC v-bind:ButtonData="Buttons.AddToNavigationButton"></ButtonC> -->
        </div>
        <div style="clear:both"></div>
        <div>
            <FileC v-for="(FileData, index) in Files" v-bind:FileData="FileData" v-bind:key="FileData.name"/>
            <div v-if="!Files.length">
                There are no files or directories.
            </div>
            <div v-if="error_message" class="error-message">
                {{ error_message }}
            </div>
        </div>

        <div id="preview">

        </div>

        <!-- modals -->
        <CreateDirC v-bind:ModalData="ModalData"></CreateDirC>
        <UploadFileC v-bind:ModalData="ModalData"></UploadFileC>
        <DeleteC v-bind:ModalData="ModalData"></DeleteC>
        <PropertiesC v-bind:ModalData="ModalData"></PropertiesC>

    </div>
</template>

<script>

    import FileC from '@GuzabaPlatform.Assets/components/File.vue'
    import ButtonC from '@GuzabaPlatform.Platform/components/Button.vue'

    import DeleteC from '@GuzabaPlatform.Assets/components/Delete.vue'
    import CreateDirC from '@GuzabaPlatform.Assets/components/CreateDir.vue'
    import UploadFileC from '@GuzabaPlatform.Assets/components/UploadFile.vue'
    import PropertiesC from '@GuzabaPlatform.Assets/components/Properties.vue'

    import AliasesMixin from '@GuzabaPlatform.Platform/aliasesMixin.js'
    import ToastMixin from '@GuzabaPlatform.Platform/ToastMixin.js'

    export default {
        name: "AssetsAdmin",
        mixins: [
            AliasesMixin,
            ToastMixin,
        ],
        components: {
            FileC,
            ButtonC,

            CreateDirC,
            UploadFileC,
            DeleteC,
            PropertiesC,
        },

        data() {
            return {
                error_message: '',
                HighlightedFile: {
                    name: '',
                },
                //current_dir_path : '',
                CurrentDirPath: {
                    name: '',
                },
                ModalData: {
                    //highlighted_file: this.highlighted_file,
                    //HighlightedFile: this.HighlightedFile,//it is not possible to assign it here to property of this
                    //it is done in mounted()
                    HighlightedFile: {},
                    CurrentDirPath: {},
                },


                //current_dir_path : this.base_dir_path,
                //base_dir_path : '[/public/assets] /',
                //base_dir_path : '/',
                current_dir_display_path : '',
                base_dir_display_path : '[/public/assets] /',
                //highlighted_file: '',

                Files : {},
                Buttons: {
                    ParentDirButton: {
                        label: 'Parent Dir',
                        is_active: true,
                        handler: this.parent_dir_handler,
                    },
                    CreateDirButton: {
                        label: 'Create Dir',
                        is_active: true,
                        handler: this.blank_button_handler,
                    },
                    UploadFileButton: {
                        label: 'Upload File',
                        is_active: true,
                        handler: this.blank_button_handler,
                    },
                    CopyButton: {
                        label: 'Copy',
                        is_active: true,
                        handler: this.blank_button_handler,
                    },
                    RenameButton: {
                        label: 'Rename/Move',
                        is_active: true,
                        handler: this.blank_button_handler,
                    },
                    DeleteButton: {
                        label: 'Delete',
                        is_active: true,
                        handler: this.blank_button_handler,
                    },
                    PropertiesButton: {
                        label: 'Properties',
                        is_active: true,
                        handler: this.blank_button_handler,
                    },
                    AddToNavigationButton: {
                        label: 'Add to Navigation',
                        is_active: true,
                        handler: this.add_to_navigation_handler,
                    },
                },
            }
        },
        watch: {
            $route (to, from) { // needed because by default no class is loaded and when it is loaded the component for the two routes is the same.
                let path = '';
                if (typeof this.$route.params.pathMatch !== "undefined") {
                    path = this.$route.params.pathMatch
                }
                this.get_dir_files(path)
            }
        },
        methods: {
            get_dir_files(path) {
                //this.highlighted_file = '';//clear the currently highglighted file when dir is changed
                this.HighlightedFile.name = '';
                //this.current_dir_path = path;
                this.CurrentDirPath.name = path;
                //this.current_dir_display_path = this.base_dir_display_path + this.current_dir_path;
                this.current_dir_display_path = this.base_dir_display_path + this.CurrentDirPath.name;
                let self = this;
                this.$http.get('/admin/assets/' + path )
                    .then(resp => {
                        if (typeof resp.data.files !== "undefined") {
                            //this will not work - assigning and then setting the property
                            //the property first needs to be set on all records and then assigned to Files as otherwise the File.vue template will fail
                            //self.Files = Object.values(resp.data.files);
                            //this.unhighlight_all_files();
                            let Files = Object.values(resp.data.files);
                            for (const el in Files) {
                                Files[el].is_highlighted = 0;
                            }
                            self.Files = Files;
                        } else {
                            //console.log('No Files data received');
                            //self.show_toast('No Files data was received.');
                            this.error_message = 'No Files data was received.';
                        }

                    })
                    .catch(err => {
                        //console.log(err);
                        //self.show_toast(err.response.data.message);
                        this.error_message = err.response.data.message;
                        self.Files = [];
                        //self.requestError = err;
                        //self.items_permissions = [];
                    }).finally(function(){
                        //self.$bvModal.show('class-permissions');
                    });
            },
            //opens the file
            file_dblclick_handler(FileData) {
                if (FileData.is_dir) {
                    //let path = this.current_dir_path + FileData.name;
                    let path = this.CurrentDirPath.name
                    if (path) {
                        path += '/';
                    }
                     path += FileData.name;
                    path = path.split('./').join('');
                    this.$router.push('/admin/assets/' + path)
                } else {

                }
            },
            //highlights the file
            file_click_handler(FileData) {
                this.highlight_file(FileData);
            },
            parent_dir_handler() {
                //let path_arr = this.current_dir_path.split('/');
                let path_arr = this.CurrentDirPath.name.split('/');
                path_arr.pop();
                let path = path_arr.join('/');
                this.$router.push('/admin/assets/' + path)
            },
            blank_button_handler() {
                //just an empty handler for the button component on click event
                //some dont use these handlers but the modal one
            },
            // create_dir_handler() {
            //
            // },
            // upload_file_handler() {
            //
            // },
            // copy_handler() {
            //
            // },
            // rename_handler() {
            //
            // },
            // delete_handler() {
            //
            // },
            // properties_handler() {
            //
            // },
            // add_to_navigation_handler() {
            //
            // },
            highlight_file(FileData) {
                //clear any previously highlighted file first
                this.unhighlight_all_files();
                //if (this.highlighted_file === FileData.name) {
                if (this.HighlightedFile.name === FileData.name) {
                    //if it is already highglighted unhighlight it
                    //this.highlighted_file = '';
                    this.HighlightedFile.name = '';
                    return;
                }
                //this.highlighted_file = FileData.name;
                this.HighlightedFile.name = FileData.name;
                //this.ModalData.highlighted_file = this.highlighted_file;
                for (const el in this.Files) {
                    if (this.Files[el].name === FileData.name) {
                        this.Files[el].is_highlighted = 1;
                    }
                }
                console.log(this.Files);
            },
            unhighlight_all_files() {
                for (const el in this.Files) {
                    this.Files[el].is_highlighted = 0;
                }
            }

        },
        mounted() {
            this.ModalData.HighlightedFile = this.HighlightedFile;
            this.ModalData.CurrentDirPath = this.CurrentDirPath;
            //let path = this.current_dir_path;
            let path = this.CurrentDirPath.name;
            if (typeof this.$route.params.pathMatch !== "undefined") {
                path = this.$route.params.pathMatch
            }
            this.get_dir_files(path);
        }
    }
</script>

<style scoped>
.error-message {
    border: 2px solid red;
}
</style>