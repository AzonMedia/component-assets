<template>
    <div class="assets-file" v-on:dblclick="$parent.file_click_handler(FileData)">
        <!-- <img :alt="file_data.name" src="@GuzabaPlatform.Assets/assets/file.png"> -->
        <img :alt="FileData.name" :src="require(`@GuzabaPlatform.Assets/assets/${get_icon_src(FileData)}`)">
        <!-- <img :alt="file_data.name" :src="get_icon_src(file_data)"> -->
        <br />
        {{FileData.name}}
    </div>
</template>

<script>
    // https://github.com/vuejs-templates/webpack/issues/450
    //<img :src="require(`@/assets/${posts.img}`)" alt="">
    import AliasesMixin from '@GuzabaPlatform.Platform/aliasesMixin.js'
    export default {
        name: "File",
        mixins: [AliasesMixin],
        props: {
            FileData : Object
        },
        methods: {
            get_icon_src(FileData) {
                //webpack does not resolve here
                // let src = "@GuzabaPlatform.Assets/assets/file.png";
                // if (file_data.is_dir) {
                //     src = "@GuzabaPlatform.Assets/assets/dir.png";
                // }
                //neither the full path does the job...
                // let src = this.resolve_alias('@GuzabaPlatform.Assets');
                // if (file_data.is_dir) {
                //     src += '/assets/dir.png'
                // } else {
                //     src += '/assets/file.png'
                // }
                let src = 'file.png';
                if (FileData.is_dir) {
                    src = 'dir.png'
                }
                return src;
            },
        }
    }
</script>

<style scoped>
.assets-file
{
    float: left;
}
</style>