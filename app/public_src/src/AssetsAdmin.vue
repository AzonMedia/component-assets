<template>
    <div>Assets admin
        <div>AssetsStore: {{current_dir_path}}</div>
        <div>
            <File v-for="(file_data, index) in files" v-bind:file_data="file_data"/>
        </div>
    </div>
</template>

<script>

    import File from '@GuzabaPlatform.Assets/components/File.vue'
    //const aliases = require('@/../components_config/webpack.components.runtime.json')
    import AliasesMixin from '@GuzabaPlatform.Platform/aliasesMixin'

    export default {
        name: "AssetsAdmin",
        mixins: [AliasesMixin],
        components: {
            File,
        },
        data() {
            return {
                current_dir_path : './',
                files : [],
            }
        },
        methods: {
            get_dir_files(path) {
                //console.log("ASD")
                let self = this;
                this.$http.get('/admin/assets/' + this.current_dir_path )
                    .then(resp => {
                        self.files = Object.values(resp.data.files);
                        console.log(self.files);
                        // //self.items_permissions = Object.values(resp.data.items);
                        // self.items_permissions = Object.values(resp.data.items);
                        // //self.fields_permissions = self.fields_permissions_base;//reset the columns
                        // self.fields_permissions = JSON.parse(JSON.stringify(self.fields_permissions_base)) //deep clone and produce again Array
                        // for (let action_name in self.items_permissions[0].permissions) {
                        //     self.fields_permissions.push({
                        //         key: action_name,
                        //         label: action_name,
                        //         sortable: true,
                        //     });
                        // }
                    })
                    .catch(err => {
                        console.log(err);
                        //self.requestError = err;
                        //self.items_permissions = [];
                    }).finally(function(){
                        //self.$bvModal.show('class-permissions');
                    });
            },
        },
        mounted() {
            this.get_dir_files(this.current_dir_path);
        }
    }
</script>

<style scoped>

</style>