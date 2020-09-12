<template>
    <b-tab title="To File">
        <AssetsAdminC v-bind:EmbeddedData="EmbeddedData"></AssetsAdminC>
    </b-tab>
</template>

<script>
    import ToastMixin from '@GuzabaPlatform.Platform/ToastMixin.js'

    import AssetsAdminC from '@GuzabaPlatform.Assets/AssetsAdmin.vue'

    export default {
        name: "AddLinkFile",
        mixins: [
            ToastMixin,
        ],
        components: {
            AssetsAdminC
        },
        data() {
            return {
                EmbeddedData: {
                    file_dblclick_handler: function(FileData) {
                        if (FileData.is_dir) {
                            let path = this.get_path_from_file_data(FileData)
                            AssetsAdminC.get_dir_files(path)
                        } else {
                            console.log(FileData)
                            //TODO - open the file
                        }
                    }
                }
            }
        },
        methods: {
            /**
             * Overrides the methods (and some properties) of the child component AssetsAdmin
             */
            override_child_methods() {
                //console.log(this.$children[0].$options.name)
                //console.log(this.$children[0].$children)
                let AssetsAdminC = this.get_child_component_by_name('AssetsAdmin');

                //override the handler
                AssetsAdminC.file_dblclick_handler = function(FileData) {
                    if (FileData.is_dir) {
                        let path = this.get_path_from_file_data(FileData)
                        AssetsAdminC.get_dir_files(path)
                    } else {
                        //do nothing - instead the highlighted file will be used as link
                    }
                }
                //overriding directly the handler will not override it in the button
                //AssetsAdminC.parent_dir_handler = function() {
                //the whole button data needs to be overriden instead
                let parent_dir_handler = function() {
                    //let path_arr = this.current_dir_path.split('/');
                    let path_arr = AssetsAdminC.CurrentDirPath.name.split('/');
                    path_arr.pop();
                    let path = path_arr.join('/');
                    AssetsAdminC.get_dir_files(path)
                }
                AssetsAdminC.Buttons.ParentDirButton = {
                    label: AssetsAdminC.Buttons.ParentDirButton.label,
                    is_active: AssetsAdminC.Buttons.ParentDirButton.is_active,
                    handler: parent_dir_handler,
                }

                let original_highlight_file = AssetsAdminC.highlight_file
                AssetsAdminC.highlight_file = function (FileData) {
                    original_highlight_file(FileData)
                    if (!FileData.is_dir) {
                        let AddLinkC = this.get_parent_component_by_name('AddLink')
                        AddLinkC.Link.link_class_name = null
                        AddLinkC.Link.link_object_uuid = null
                        AddLinkC.Link.link_name = FileData.name
                        AddLinkC.Link.link_redirect = AssetsAdminC.document_root_assets_dir + '/' + AssetsAdminC.CurrentDirPath.name + '/' + FileData.name
                        console.log(AddLinkC.Link.link_redirect)
                    }

                }
            }
        },
        mounted() {
            this.override_child_methods()
        }

    }
</script>

<style scoped>

</style>