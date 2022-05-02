<template>
    <div class="w-25">
        <input v-model="title" type="text" class="form-control mb-3" placeholder="title">
        <div ref="dropzone" class="p-5 bg-dark text-center text-light cursor-pointer mb-3">
            Upload
        </div>
        <div class="mb-3">
            <vue-editor useCustomImageHandler @image-added="handleImageAdded" v-model="content"  />
        </div>
        <input @click.prevent="store" type="submit" class="btn btn-primary" value="Add">
        <div class="mt-5">
            <div v-if="post">
                <h4>{{ post.title}}</h4>
                <div v-for="image in post.images" class="mb-3">
                    <img :src="image.preview_url" class="mb-3">
                    <img :src="image.url" >
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Dropzone from 'dropzone'
import { VueEditor } from "vue2-editor";
export default {
    name: "Index",

    data() {
        return {
            dropzone: null,
            title: null,
            post: null,
            content: null
        }
    },

    components:{
        VueEditor
    },

    mounted() {
        this.dropzone = new Dropzone(this.$refs.dropzone, {
            url: '/api/posts',
            autoProcessQueue: false,
            addRemoveLinks: true
        })

        this.getPost()
    },

    methods: {
        store() {
            const data = new FormData()
            const files = this.dropzone.getAcceptedFiles();
            files.forEach(file => {
                data.append('images[]', file)
                this.dropzone.removeFile(file)
            })
            data.append('title', this.title)
            this.title = ''
            axios.post('/api/posts', data)
            .then(res => {
                this.getPost()
            })
        },

        getPost(){
            axios.get('/api/posts')
            .then(res => {
                this.post = res.data.data
                console.log(res);
            })
        },

        handleImageAdded(file, Editor, cursorLocation, resetUploader){
            const formData = new FormData();
            formData.append("image", file);

            axios.post('/api/posts/images', formData)
                .then(result => {
                    const url = result.data.url; // Get url from response
                    Editor.insertEmbed(cursorLocation, "image", url);
                    resetUploader();
                })
                .catch(err => {
                    console.log(err);
                });
        }
    }

}
</script>

<style scoped>

</style>