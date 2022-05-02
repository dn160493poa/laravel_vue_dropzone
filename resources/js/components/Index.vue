<template>
    <div class="w-25">
        <input v-model="title" type="text" class="form-control mb-3" placeholder="title">
        <div ref="dropzone" class="p-5 bg-dark text-center text-light cursor-pointer mb-3">
            Upload
        </div>
        <div class="mb-3">
            <vue-editor useCustomImageHandler @image-added="handleImageAdded" v-model="content"  />
        </div>
<!--        How the method do you want use? Switch comments to true and close false (store or update)-->
<!--        <input @click.prevent="store" type="submit" class="btn btn-primary" value="Done">-->
        <input @click.prevent="update" type="submit" class="btn btn-primary" value="Update">
        <div class="mt-5">
            <div v-if="post">
                <h4>{{ post.title}}</h4>
                <div v-for="image in post.images" class="mb-3">
                    <img :src="image.preview_url" class="mb-3">
                    <img :src="image.url" >
                </div>
                <div v-html="post.content" class="ql-editor">

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
            data.append('content', this.content)
            this.title = ''
            this.content = ''
            axios.post('/api/posts', data)
            .then(res => {
                this.getPost()
            })
        },

        update(){
            const data = new FormData()
            const files = this.dropzone.getAcceptedFiles();
            files.forEach(file => {
                data.append('images[]', file)
                this.dropzone.removeFile(file)
            })
            data.append('title', this.title)
            data.append('content', this.content)
            data.append('_method', 'PATCH')
            this.title = ''
            this.content = ''
            axios.post(`/api/posts/${this.post.id}`, data)
                .then(res => {
                    this.getPost()
                })
        },

        getPost(){
            axios.get('/api/posts')
            .then(res => {
                this.post = res.data.data

                this.title = this.post.title
                this.content = this.post.content

                this.post.images.forEach( image => {
                    let file = { name: image.name, size: image.size };
                    this.dropzone.displayExistingFile(file, image.preview_url);
                })
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

<style>
.dz-success-mark, .dz-error-mark{
    display: none;
}
</style>