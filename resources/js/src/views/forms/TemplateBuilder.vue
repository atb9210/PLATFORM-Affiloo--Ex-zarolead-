<!-- =========================================================================================
  File Name: FormWizardValidation.vue
  Description: Form wizard with validation
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
  <vx-card title="" code-toggler>
    <div class="mt-5">
      <form-wizard color="rgba(var(--vs-primary), 1)" errorColor="rgba(var(--vs-danger), 1)" :title="null" :subtitle="null" finishButtonText="Finish">
        <!-- tab 1 content -->
        <tab-content title="General" class="mb-5" icon="feather icon-info" :before-change="validateStep1">
          <form data-vv-scope="step-1">
          <div class="vx-row">

            <div class="vx-col md:w-full w-full mt-5">
              <label>TEMPLATE NAME</label>
              <vs-input icon-pack="feather" icon="icon-tag" icon-no-border v-model="templateName" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Title</label>
              <vs-input icon-pack="feather" icon="icon-hash" icon-no-border v-model="strings.head.title" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Short description</label>
              <vs-input icon-pack="feather" icon="icon-hash" icon-no-border v-model="strings.head.short_description" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Long description</label>
              <vs-input icon-pack="feather" icon="icon-hash" icon-no-border v-model="strings.head.description" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>URL</label>
              <vs-input icon-pack="feather" icon="icon-globe" icon-no-border v-model="strings.head.url" class="w-full" />
            </div>

            <div class="vx-col md:w-1/2 w-full mt-5">
              <label>Language</label>
              <my-select v-model="strings.head.language" :options="languageOptions" class="w-full select-large" :autocomplete="true" />
            </div>

            <div class="vx-col md:w-1/2 w-full mt-5">
              <label>Currency</label>
              <my-select v-model="strings.general.currency" :options="currencyOptions" class="w-full select-large" :autocomplete="true" />
            </div>

          </div>
          </form>
        </tab-content>

        <!-- tab 2 content -->
        <tab-content title="Section 1" class="mb-5" icon="feather icon-dollar-sign" :before-change="validateStep2">
          <form data-vv-scope="step-2">
          <div class="vx-row">

            <div class="vx-col md:w-full w-full mt-5">
              <label>Title</label>
              <vs-input icon-pack="feather" icon="icon-tag" icon-no-border v-model="strings['section-1'].title" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Advantages</label>
            </div>
            <div class="vx-col md:w-full w-full mt-2" v-for="(adv, index) in strings['section-1'].advantages" :key="'adv' + index">
              <vs-input icon-pack="feather" icon="icon-plus" icon-no-border v-model="strings['section-1'].advantages[index]" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Images: {{ strings['section-1'].image.length }}/3 selected</label>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-cloud" @click="chooseImagesFromDropbox(strings['section-1'].image, true)">Choose From Dropbox</vs-button>
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Truck symbol text</label>
              <vs-input icon-pack="feather" icon="icon-truck" icon-no-border v-model="strings['section-1']['truck-symbol-text']" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Money symbol text</label>
              <vs-input icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="strings['section-1']['money-symbol-text']" class="w-full" />
            </div>
            
          </div>
          </form>
        </tab-content>

        <!-- tab 3 content -->
        <tab-content title="Section 2" class="mb-5" icon="feather icon-settings" :before-change="validateStep3">
          <form data-vv-scope="step-3">
          <div class="vx-row">

            <div class="vx-col md:w-full w-full mt-5">
              <label>Title</label>
              <vs-input icon-pack="feather" icon="icon-tag" icon-no-border v-model="strings['section-2'].title" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Old price</label>
              <vs-input icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="strings['section-2'].old_price" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>New price</label>
              <vs-input icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="strings['section-2'].new_price" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Button text</label>
              <vs-input icon-pack="feather" icon="icon-dollar-sign" icon-no-border v-model="strings['section-2'].button_text" class="w-full" />
            </div>

          </div>
          </form>
        </tab-content>

        <!-- tab 4 content -->
        <tab-content title="Section 3" class="mb-5" icon="feather icon-plus-square" :before-change="validateStep4">
          <form data-vv-scope="step-4">
          <div class="vx-row">

            <div class="vx-col md:w-full w-full mt-5">
              <label>Title</label>
              <vs-input icon-pack="feather" icon="icon-tag" icon-no-border v-model="strings['section-3'].title" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Sub title</label>
              <vs-input icon-pack="feather" icon="icon-tag" icon-no-border v-model="strings['section-3'].sub_title" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Image: {{ (strings['section-3'].image.length > 0) ? 'selected' : 'not selected' }}</label>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-cloud" @click="chooseImagesFromDropbox(strings['section-3'].image, false)">Choose From Dropbox</vs-button>
            </div>

          </div>
          </form>
        </tab-content>

        <!-- tab 5 content -->
        <tab-content title="Section 4" class="mb-5" icon="feather icon-plus-square" :before-change="validateStep5">
          <form data-vv-scope="step-5">
          <div class="vx-row">

            <div class="vx-col md:w-full w-full mt-5">
              <label>Description</label>
              <vs-input icon-pack="feather" icon="icon-tag" icon-no-border v-model="strings['section-4'].description" class="w-full" />
            </div>

          </div>

          <div class="vx-row">
            <div class="vx-col md:w-full w-full mt-5">
              <label>Items</label>
            </div>
          </div>

          <div class="vx-row mb-5" v-for="(item, index) in strings['section-4'].items" :key="'item' + index">

            <div class="vx-col md:w-full w-full mt-5">
              <label>Title</label>
              <vs-input icon-pack="feather" icon="icon-tag" icon-no-border v-model="strings['section-4'].items[index].title" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Description</label>
              <vs-input icon-pack="feather" icon="icon-tag" icon-no-border v-model="strings['section-4'].items[index].description" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Image: {{ (strings['section-4'].items[index].image_url.length > 0) ? 'selected' : 'not selected' }}</label>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-cloud" @click="chooseImagesFromDropbox(strings['section-4'].items[index].image_url, false)">Choose From Dropbox</vs-button>
            </div>

            <hr class="mt-5" />

          </div>

          </form>
        </tab-content>

        <!-- tab 6 content -->
        <tab-content title="Section 5" class="mb-5" icon="feather icon-plus-square" :before-change="validateStep6">
          <form data-vv-scope="step-6">
          <div class="vx-row">

            <div class="vx-col md:w-full w-full mt-5">
              <label>Title</label>
              <vs-input icon-pack="feather" icon="icon-tag" icon-no-border v-model="strings['section-5'].title" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Caratteristiche</label>
            </div>
            <div class="vx-col md:w-full w-full mt-2" v-for="(caratt, index) in strings['section-5'].caratteristiche" :key="'caratteristiche' + index">
              <vs-input icon-pack="feather" icon="icon-plus" icon-no-border v-model="strings['section-5'].caratteristiche[index]" class="w-full" />
            </div>

            <div class="vx-col md:w-full w-full mt-5">
              <label>Images: {{ strings['section-5'].image.length }}/3 selected</label>
              <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-cloud" @click="chooseImagesFromDropbox(strings['section-5'].image, true)">Choose From Dropbox</vs-button>
            </div>

          </div>
          </form>
        </tab-content>

      </form-wizard>
    </div>

  </vx-card>
</template>

<script>
import { FormWizard, TabContent } from 'vue-form-wizard'
import 'vue-form-wizard/dist/vue-form-wizard.min.css'
import * as utils from 'utils'

function getFilename(pathname) {
  return pathname.replace(/^.*[\\\/]/, '')
}

function getFilenameNoExtension(pathname) {
  return pathname.replace(/^.*[\\\/]/, '').replace(/\.[^/.]+$/, "")
}

function getFileExtension(pathname) {
  let ext = pathname.split('.').pop()
  if (ext === undefined) {
    ext = ''
  }
  return ext
}

function isValidHttpUrl(s) {
  let url = null
  try {
    url = new URL(s)
  } catch (_) {
    return false
  }
  return url.protocol === "http:" || url.protocol === "https:"
}

function createImageFromUrl(url) {
  return new Promise((resolve, reject) => {
    let img = new Image();
    img.crossOrigin = 'Anonymous';
    img.onload = () => resolve(img)
    img.onerror = reject
    img.src = url;
  })
}

function createImageFromFile(file) {
  return new Promise((resolve, reject) => {
    let img = new Image();
    img.crossOrigin = 'Anonymous';
    img.onload = () => resolve(img)
    img.onerror = reject
    img.src = URL.createObjectURL(file);
  })
}

function createImageFileFromUrl(url, fileName) {
  return new Promise((resolve, reject) => {
    const response = fetch(url).then((response) => {
      response.blob().then((blob) => {
        const file = new File([blob], fileName, {type: blob.type});
        resolve(file)
      }).catch((error) => {
        reject('invalid image')
      })
    }).catch((error) => {
      reject('invalid image url')
    })
  })
}

function createFileFromBase64DataURL(base64DataUrl, fileName = null) {
  if (fileName === null) {
    let tmp = (base64DataUrl.split(';'))[0]
    tmp = (tmp.split('/'))[1]
    let extension = tmp
    if (extension == 'jpeg') {
      extension = 'jpg'
    }
    fileName = 'test.' + extension
  }

  let arr = base64DataUrl.split(',')
  let mime = arr[0].match(/:(.*?);/)[1]
  let bstr = atob(arr[1])
  let n = bstr.length
  let u8arr = new Uint8Array(n)
  while (n--) {
    u8arr[n] = bstr.charCodeAt(n)
  }
  return new File([u8arr], fileName, {type:mime})
}

function getDomainFromUrl(url) {
  let domain = ''

  try {
    url = url.trim()
    if (url.indexOf('http://') !== 0 && url.indexOf('https://') !== 0) {
      url = 'https://' + url
    }

    let u = new URL(url)
    domain = u.host

    let prefix = 'www.'
    if (domain.indexOf(prefix) === 0) {
      domain = domain.substr(prefix.length)
    }
  } catch (error) {
    // TODO: Log error?
  }

  return domain
}

export default {
  components: {
    FormWizard,
    TabContent,
  },
  data () {
    return {
      languageOptions: [
        'it_IT',
        'en_US',
      ],

      currencyOptions: [
        { value: '€', label: 'EUR' },
        { value: '$', label: 'USD' },
      ],

      templateName: 'Template #1',

      strings: {
        'general': {
          'currency': '€'
        },
        'head': {
          'title': "KitchPro - Pianeta Bellezza",
          'short_description': "Esplora il tuo vero stile.",
          'description': " Carne, verdura e frutta si tritano facilmente e velocemente Precedente Successivo RISPARMIA TEMPO UN DESIGN FACILE DA USARE 2 modalità di macinazione Facile da tagliare e durerà per anni. Acciaio inossidabile, taglio efficace. Adatto per uso domestico e aziendale SODDISFARE IL BISOGNO DI CASA - GRANDE CAPACITÀ Interruttore conveniente, lavora facilmente. Sicuro da usare,...",
          'url': "https://pianetabellezza.com/kitchpro/",
          'language': "it_IT",
        },
        'section-1': {
          'title': "Carne, verdura e frutta si tritano facilmente e velocemente",
          'advantages': {
            1: 'RISPARMIA TEMPO',
            2: 'UN DESIGN FACILE DA USARE',
            3: '2 modalità di macinazione',
            4: 'Facile da tagliare e durerà per anni.',
            5: 'Acciaio inossidabile, taglio efficace.',
            6: 'Adatto per uso domestico e aziendale',
            7: 'SODDISFARE IL BISOGNO DI CASA - GRANDE CAPACITÀ',
            8: 'Interruttore conveniente, lavora facilmente.',
            9: 'Sicuro da usare, il motore termico si fermerà automaticamente',
          },
          'image': [],
          /*
          {
            1: './KitchPro - Pianeta Bellezza_files/B-2.jpeg',
            2: './KitchPro - Pianeta Bellezza_files/AAGIIF.gif',
            3: './KitchPro - Pianeta Bellezza_files/C.png',
          },
          */
          'truck-symbol-text': 'Consegna gratuita entro 48 ore',
          'money-symbol-text': 'Pagamento sicuro alla consegna',
        },
        'section-2': {
          'title': 'OFFERTA SPECIALE -40%',
          'old_price': '99,83',
          'new_price': '59,90',
          'button_text': 'ORDINA ORA -40%',
        },
        'section-3': {
          'title': "L'elettrodomestico da cucina universale macina, taglia, sminuzza e impasta il cibo per preparare piatti deliziosi più velocemente!",
          'sub_title': 'Prepara pasti completi con un solo utensile da cucina ! Ordinando oggi a SOLI 59€',
          'image': [], // Only one
        },
        'section-4': {
          'description': "<b><i>KitchPro  </i></b> è una cucina polivalente, che macina, taglia, sminuzza e impasta i cibi per preparare piatti sfiziosi più velocemente! Con una forte lama in acciaio inossidabile taglierà facilmente la carne più dura e le verdure più dure. Scegli come e quanto vuoi tagliare o macinare gli ingredienti nei tuoi piatti.<br>Sfrutta al massimo il tuo tempo! Questo apparecchio è il modo più efficiente di cucinare perché elabora tutti gli ingredienti necessari per il cibo della tua famiglia.",
          'items': [
            {
              'image_url': [], // Only one
              'title': 'TUTTO È COTTO IN MODO UNIFORME',
              'description': 'Il robot taglia gli ingredienti in pezzi di uguali dimensioni. Carne e verdure saranno cotte contemporaneamente, senza bruciature e pezzi crudi.',
            },
            {
              'image_url': [], // Only one
              'title': 'ELABORAZIONE VELOCE DEGLI INGREDIENTI',
              'description': 'Bastano solo 30 secondi per tagliare o macinare gli ingredienti desiderati! Ciò è garantito da un potente motore in rame da 300 W, un albero di alloggiamento in acciaio e 4 robuste lame.',
            },
            {
              'image_url': [], // Only one
              'title': 'DISORDINE IN CUCINA',
              'description': 'Oltre a fornire più spazio sulla scrivania, la parte inferiore del dispositivo è protetta da un anello antiscivolo. Il dispositivo non ruota e gli ingredienti non si rovesciano o si rovesciano sul piano di lavoro.',
            },
            {
              'image_url': [], // Only one
              'title': 'SOSTITUISCE ALTRI ELETTRODOMESTICI DA CUCINA',
              'description': 'Dì addio a un bancone della cucina disordinato pieno di elettrodomestici diversi! In esso ottieni tutte le funzioni di un dispositivo per spremere, frantumare, mescolare e impastare il succo.',
            },
          ],
        },
        'section-5': {
          'title': 'CARATTERISTICHE:',
          'caratteristiche': {
            1: 'Capacità: 2L',
            2: 'Peso del prodotto: 2 KG',
            3: 'Tensione nominale: 220V.',
            4: 'Frequenza nominale: 50 Hz.',
            5: 'Materiale: acciaio inossidabile.',
            6: 'Potenza nominale: 200 W-300W.',
            7: 'Dimensioni del pacchetto: 22,5 * 25 cm.',
            8: 'Velocità di rotazione: 18000-22000 giri/min.',
          },
          'image': [],
          /*
          {
            1: './KitchPro - Pianeta Bellezza_files/2-3.jpeg',
            2: './KitchPro - Pianeta Bellezza_files/3-1.jpeg',
            3: './KitchPro - Pianeta Bellezza_files/1.png',
          },
          */
        },
      }

    }
  },
  watch: {
    selectedFormsStyle: function (val) {
      this.plugins['css_style'].stylesheet_url = val.url
      this.plugins['css_style'].style_id = val.id
      this.plugins['css_style'].button_text = val.settings.button_text
      this.plugins['css_style'].countdown_time = val.settings.countdown_time
    },
  },
  computed: {
    canChangeUser() {
      return (this.$store.getters.userHasRole('developer') || this.$store.getters.userHasRole('admin'))
    },
    flowPathname() {
      let flowType = this.flow_type
      if (flowType == 'redirect') {
        return 'https://zarolead.com/flo/' + this.flow_path_name
      } else if (flowType == 'hotload') {
        let domain = null
        for (let i = 0; i < this.domains.length; i++) {
          let d = this.domains[i]
          if (d.id == this.flow_domain_id) {
            domain = d
            break
          }
        }
        if (domain !== null) {
          return 'http://' + domain.domain + '/' + this.flow_path_name
        }
      }
      return ''
    },
  },
  methods: {
    getObjectIndexById(id, arr) {
      let index = -1;
      for (let i = 0; i < arr.length; i++) {
        if (arr[i].id == id) {
          index = i
          break
        }
      }
      return index
    },
    changeNetwork() {
      let prod = null
      let prodIndex = this.getObjectIndexById(this.product, this.products)
      if (prodIndex >= 0) {
        prod = this.products[prodIndex]
      }

      this.network_params = []
      this.network_params_values = []
      if (this.network != '' && this.network > 0) {
        let index = this.getObjectIndexById(this.network, this.networks)
        if (index != -1 && this.networks[index].api.hasOwnProperty('form')
            && this.networks[index].api.form.hasOwnProperty('params')) {
          let networkSlug = this.networks[index].slug
          for (const [key, value] of Object.entries(this.networks[index].api.form.params)) {
            this.network_params.push(value)
            this.network_params[this.network_params.length - 1].name = key
            this.network_params[this.network_params.length - 1].validation = this.networks[index].api.form.validation.params[key];

            let val = ''
            if (this.settings !== null && this.settings.hasOwnProperty(networkSlug) && this.settings[networkSlug] !== null) {
              if (this.settings[networkSlug].hasOwnProperty(key) && this.settings[networkSlug][key] !== null) {
                val = this.settings[networkSlug][key]
              }
            }
            if (prod !== null && prod.hasOwnProperty('info') && prod.info !== null) {
              if (prod.info.hasOwnProperty(networkSlug) && prod.info[networkSlug] !== null &&
                  prod.info[networkSlug].hasOwnProperty(key) && prod.info[networkSlug][key] !== null) {
                val = prod.info[networkSlug][key]
              }
            }

            this.network_params_values.push(val)
            this.network_params[this.network_params.length - 1].value = val
          }
        }
      }
    },
    changeTrafficSource() {
      this.flow_traffic_source_id = this.traffic_source
    },
    async getFormsStyles() {
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      const response = await this.$http.get('/api/v1/forms-styles', { headers })
      this.formsStyles = response.data.data
    },
    previewFormsStyle() {
      if (this.selectedFormsStyle == null) return
      this.formsStylePreviewObject = this.selectedFormsStyle.settings
      this.showPopup['forms_style_preview'] = true
    },
    showForm(type) {
      this.showPopup[type] = true
    },
    clearForm(type) {
      if (type == 'cross_sell') {
        this.newObject[type] = {
          title: '',
          description: '',
          img: '',
          old_price: '',
          price: '',
          commission: '',
          sub_commission: '',
        }
      } else if (type == 'delivery') {
        this.newObject[type] = {
          options: [{
            name: '',
            days: 1,
            price: 0,
          }],
        }
      } else {
        this.newObject[type] = {
          price: '',
          commission: '',
          sub_commission: '',
          old_price: '',
          discount: '',
        }
      }
    },
    isValidForm(type) {
      let obj = this.newObject[type]
      if (type == 'cross_sell') {
        return (obj.sku != '' && obj.title != '' && obj.description != '' &&
          obj.img != '' &&
          obj.price != '' && obj.old_price &&
          obj.commission != '' && obj.sub_commission)
      } else if (type == 'delivery') {
        return true
      } else if (type == 'create_landing') {
        return (this.landing.name.length > 0 && this.landing.product != '' &&
                this.landing.url.length > 0)
      } else {
        return false
      }
    },
    validateStep1() {
      return new Promise((resolve, reject) => {
        this.$validator.validateAll('step-1').then(result => {
          if (result) {
            resolve(true)
          } else {
            reject('correct all values')
          }
        })
      })
    },
    validateStep2() {
      return new Promise((resolve, reject) => {
        this.$validator.validateAll('step-2').then(result => {
          if (result) {
            resolve(true)
          } else {
            reject('correct all values')
          }
        })
      })
    },
    validateStep3() {
      return new Promise((resolve, reject) => {
        this.$validator.validateAll('step-3').then(result => {
          if (result) {
            resolve(true)
          } else {
            reject('correct all values')
          }
        })
      })
    },
    validateStep4() {
      return new Promise((resolve, reject) => {
        this.$validator.validateAll('step-4').then(result => {
          if (result) {
            resolve(true)
          } else {
            reject('correct all values')
          }
        })
      })
    },
    validateStep5() {
      return new Promise((resolve, reject) => {
        this.$validator.validateAll('step-5').then(result => {
          if (result) {
            resolve(true)
          } else {
            reject('correct all values')
          }
        })
      })
    },
    validateStep6() {
      return new Promise((resolve, reject) => {
        this.$validator.validateAll('step-6').then(result => {
          if (!result) {
            reject('correct all values')
          }

          let strings = utils.deepCloneObject(this.strings)
          if (strings['section-3']['image'].length > 0) {
            strings['section-3']['image'] = strings['section-3']['image'][0]
          } else {
            strings['section-3']['image'] = ''
          }
          for (let i = 0; i < strings['section-4']['items'].length; i++) {
            let item = strings['section-4']['items'][i]
            if (item.image_url.length > 0) {
              strings['section-4']['items'][i].image_url = item.image_url[0]
            } else {
              strings['section-4']['items'][i].image_url = ''
            }
          }

          let headers = {
            Authorization: 'Bearer ' + localStorage.getItem('accessToken')
          }
          let params = {
            name: this.templateName,
            info: JSON.stringify({
              strings: strings,
            }),
          }
          this.$http.post('/api/v1/templates', params, { headers }).then((response) => {
            if (response !== null && response.hasOwnProperty('data') && response.data !== null &&
                response.data.hasOwnProperty('data') && response.data.data !== null) {
              this.$vs.notify({
                title: 'Success',
                text: 'Template created',
                iconPack: 'feather',
                icon: 'icon-alert-circle',
                color: 'success'
              })
              this.$router.push('/dashboard/templates')

              resolve(true)
            } else {
              reject('unknown error')
            }
          }).catch((error) => {
            reject(error)
          })

          reject('cannot create the template')
        })
      })
    },

    async createTemporaryImage(imageFile) {
      let temporaryImage = null

      try {
        let headers = {
          'Authorization': 'Bearer ' + localStorage.getItem('accessToken'),
          'Content-Type': 'multipart/form-data',
        }
        let formData = new FormData()
        formData.append('file', imageFile)
        const response = await this.$http.post('/api/v1/product-medias/temp', formData, { headers })
        temporaryImage = response.data.data
        temporaryImage.type = 'image'
      } catch (error) {
        // Nothing
      }

      return temporaryImage
    },

    chooseImagesFromDropbox(destination, multiple = true) {
      this.$vs.loading()

      let options = {
        success: async function(files) {
          for (let i = 0; i < files.length; i++) {
            let file = files[i]

            let ext = getFileExtension(file.name)

            let imageUrl = null

            let imageFile = await createImageFileFromUrl(file.link, file.name)
            let uploadedImage = await this.createTemporaryImage(imageFile)
            imageUrl = uploadedImage.url

            if (imageUrl === null) {
              // TODO: Log error
            }

            destination.push(imageUrl)
          }

          this.$vs.loading.close()
        }.bind(this),
        cancel: function() {
          this.$vs.loading.close()
        }.bind(this),

        linkType: "direct", // or "direct"
        multiselect: multiple, // or true
        // NOTE: You may also specify file types, such as "video" or "images" in the list. For more information,
        // see File types below. By default, all extensions are allowed.
        extensions: ['images'],//['.png', '.jpg', '.jpeg'],
        folderselect: false,
      }
      this.$dropbox.choose(options)
    }
  },
  async created() {
    this.$vs.loading()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // TODO: Do we really need this?
    // Fetch settings
    try {
      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }
      const response = await this.$http.get('/api/v1/settings', { headers })
      if (response !== null && response.hasOwnProperty('data') && response.data !== null &&
          response.data.hasOwnProperty('data') && response.data.data !== null) {
        this.settings = response.data.data
      }
    } catch (error) {
      // Nothing
    }

    this.$vs.loading.close()
  },
}
</script>
