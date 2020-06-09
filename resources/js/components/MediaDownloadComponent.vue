<template>
    <main>
        <div class="video" v-if="this.downloading === false && this.download === ''">
            <form>
                <label for="url" class="sr-only">Video URL</label>
                <input type="text" name="url" id="url" v-model="url" autofocus>
                <label for="quality" class="sr-only">Quality</label>
                <select id="quality" name="quality" v-model="quality" class="select-css">
                    <option value="720">720p</option>
                    <option value="1080">1080p</option>
                    <option value="2160">4K</option>
                </select>
                <button type="button" class="button" :disabled="this.url === '' || this.loading" v-on:click="initDownload">
                    <img v-if="this.loading" src="/images/loading.svg" alt="">
                    <span v-else>Request</span>
                </button>
            </form>
        </div>
        <div class="downloading" v-if="this.downloading">
            <span>Generating download link</span>
            <video v-if="this.mp4" autoplay loop muted playsinline>
                <source :src="this.mp4" type="video/mp4">
            </video>
        </div>
        <div class="download" v-if="this.download !== ''">
            <a :href="this.download" class="button">Download</a>
        </div>
    </main>
</template>

<script>
    export default {
        data() {
            return {
                url: '',
                quality: '1080',
                loading: false,
                downloading: false,
                download: '',
                mp4: false,
            }
        },
        props: {
            tenorApiKey: {
                type: String,
                required: false,
                default: '',
            }
        },
        methods: {
            initDownload: function () {
                this.loading = true;
                // Initiate download
                fetch('/api/media', {
                    method: 'POST',
                    mode: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        'url': this.url,
                        'quality': this.quality,
                    }),
                })
                .then((response) => {
                    return response.json();
                })
                .then((data) => {
                    this.url = '';
                    this.loading = false;
                    this.downloading = true;
                    Echo.channel('media-downloaded.' + data.media_id)
                        .listen('MediaDownloaded', (event) => {
                            this.downloading = false;
                            this.download = event.download;
                        });
                });
                // Get gif ready for loading screen
                if (this.tenorApiKey !== '') {
                    const tenor = new URL('/v1/random', 'https://api.tenor.com');
                    const searchParams = new URLSearchParams();
                    searchParams.append('q', 'computer');
                    searchParams.append('key', this.tenorApiKey);
                    searchParams.append('limit', '1');
                    searchParams.append('contentfilter', 'low');
                    tenor.search = searchParams.toString();
                    fetch(tenor, {
                        method: 'GET',
                    })
                        .then((response) => {
                            return response.json();
                        })
                        .then((json) => {
                            this.mp4 = json.results[0].media[0].mp4.url;
                        })
                        .catch((error) => {
                            console.error(error);
                        });
                }
            },
        }
    }
</script>

<style scoped>
    .sr-only {
        position: absolute;
        top: -9999px;
        left: -9999px;
    }
</style>
