<template>
    <div class="w-25">
        <input v-model="title" type="text" class="form-control mb-3" placeholder="title">
        <div ref="dropzone" class="p-5 bg-dark text-center text-light cursor-pointer mb-3">
            Upload
        </div>
        <input @click.prevent="store" type="submit" class="btn btn-primary" value="Add">
    </div>
</template>

<script>
import Dropzone from 'dropzone'

export default {
    name: "Index",

    data() {
        return {
            dropzone: null,
            title: null
        }
    },

    mounted() {
        this.dropzone = new Dropzone(this.$refs.dropzone, {
            url: '/api/posts',
            autoProcessQueue: false,
            addRemoveLinks: true
        })
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

        }
    }

}
</script>

<style scoped>

</style>