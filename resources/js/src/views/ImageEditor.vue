<template>
  <vx-card title="Image Editor" code-toggler>
    <div class="vx-row">
      <!--
      <div class="vx-col w-full">
        <label>Input Image</label>
        <input type="file" @change="onImageUpload" />
      </div>
      -->
      <div class="vx-col sm:w-1/2 w-full">
        <vs-input type="number" step="1" min="1" v-model="imageSettings.width" class="w-full" label="Width" />
      </div>
      <div class="vx-col sm:w-1/2 w-full">
        <vs-input type="number" step="1" min="1" v-model="imageSettings.height" class="w-full" label="Height" />
      </div>
      <div class="vx-col sm:w-full w-full">
        <my-select v-model="imageSettings.resizeMode" :options="resizeModeOptions" class="w-full select-large" label="Resize Mode" :autocomplete="true" />
      </div>
      <div class="vx-col sm:w-full w-full flex items-center">
        <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-download" @click="resizeImage">Resize</vs-button>
      </div>
    </div>
    <div class="vx-row">
      <div class="vx-col w-full">
        <canvas ref="outputCanvas"></canvas>
      </div>
    </div>
  </vx-card>
</template>

<script>
// TODO: to delete (and uninstall from npm packages)?
import Cropper from 'cropperjs'

function createImageFromUrl(url) {
  return new Promise((resolve, reject) => {
    let img = new Image();
    img.crossOrigin = 'Anonymous';
    img.onload = () => resolve(img)
    img.onerror = reject
    img.src = url;
  })
}

export default {
  data () {
    return {
      inputImage: null,
      inputImageSrc: '',

      imageSettings: {
        width: 0,
        height: 0,
        resizeMode: 'shrink',
      },

      resizeModeOptions: [
        'crop',
        'shrink',
      ],

      cropper: {},
      destination: {},
    }
  },
  methods: {
    onImageUpload(e) {
      try {
        this.inputImage = e.target.files[0]
        let imgSrc = URL.createObjectURL(this.inputImage)
        this.inputImageSrc = imgSrc
      } catch (error) {
        this.inputImage = null
      }
    },
    resizeImage() {
      // TODO: to delete
      console.log('resizeImage()')

      // TODO: HERE
      // TODO: to delete?
      /*
      this.cropper = new Cropper(this.$refs.inputImage, {
        zoomable: false,
        scalable: false,
        aspectRatio: 1,
      })
      */
    },
  },
  async created() {
    let url = 'https://hips.hearstapps.com/hmg-prod.s3.amazonaws.com/images/dog-puppy-on-garden-royalty-free-image-1586966191.jpg'
    //let url = 'https://files.liveworksheets.com/def_files/2020/4/30/4302314300854/4302314300854001.jpg'
    let image = await createImageFromUrl(url)

    // TODO: to delete
    console.log('image', image)
    console.log('image.width', image.width)
    console.log('image.height', image.height)

    const canvas = this.$refs.outputCanvas
    canvas.width = 800
    canvas.height = 800

    let newImageSize = parseInt(Math.min(canvas.width, canvas.height))
    let newImageSizeHalf = parseInt(newImageSize / 2)
    let halfWidth  = parseInt(image.width / 2)
    let halfHeight = parseInt(image.height / 2)

    // Point of interest (it is the center for now)
    let POI = {
      x: halfWidth,
      y: halfHeight,
    }

    let startX = POI.x - newImageSizeHalf
    let startY = POI.y - newImageSizeHalf

    const ctx = canvas.getContext('2d')
    ctx.drawImage(image, startX, startY, newImageSize, newImageSize, 0, 0, canvas.width, canvas.height)
  },
}
</script>
