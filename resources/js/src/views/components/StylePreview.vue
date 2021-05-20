<!-- =========================================================================================
    File Name: StylePreview.vue
    Description: StylePreview - Show a form preview with the specified style
    ----------------------------------------------------------------------------------------
    Author: driverfury
    Author URL: https://github.com/driverfury
========================================================================================== -->


<template>
    <div class="vx-col w-full">
        <div id="css-plugin-preview-form-container" :style="cssPluginContainerStyle">
            <!-- Countdown -->
            <div class="css-plugin-preview-countdown" :style="cssPluginCountdownStyle">
                <div class="css-plugin-preview-countdown-text-container">
                    <span class="css-plugin-preview-countdown-text" :style="cssPluginCountdownTextStyle">L'OFFERTA SCADE TRA:</span>
                </div>
                <div><p class="css-plugin-preview-countdown-time" :style="cssPluginCountdownTimeStyle">{{cssPluginCountdownTime}}</p></div>
            </div>

            <!-- People viewing -->
            <div class="css-plugin-preview-people-viewing" :style="cssPluginPeopleViewingStyle">
                <i class="fas fa-eye"></i>
                <span class="css-plugin-preview-people-viewing-count" :style="cssPluginPeopleViewingCounterStyle">10</span>
                <span>Persone stanno visualizzando</span>
            </div>

            <!-- Available quantity -->
            <div class="css-plugin-preview-available-quantity" :style="cssPluginAvailableQuantityStyle">
                <div class="css-plugin-preview-available-quantity-bar" data-label="5/100 DISPONIBILI" :style="cssPluginAvailableQuantityBarStyle"></div>
            </div>

            <!-- Form -->
            <div id="css-plugin-preview-main-form" style="display: block;">
                <div>
                    <div style="margin-botton: 1rem; display: block;">
                        <label style="display: inline-block;margin-bottom: .5rem;">Nome e cognome</label>
                        <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                    </div>
                    <div style="margin-botton: 1rem; display: block;">
                        <label style="display: inline-block;margin-bottom: .5rem;">Telefono (meglio cellulare)</label>
                        <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                    </div>
                    <div style="margin-botton: 1rem; display: block;" :style="cssPluginInputAddressStyle">
                        <label style="display: inline-block;margin-bottom: .5rem;">Città e indirizzo di consegna</label>
                        <input type="text" class="css-plugin-preview-form-input" :style="cssPluginInputStyle">
                    </div>
                </div>
                <div class="css-plugin-preview-trust-badge" :style="cssPluginTrustBadgeStyle">
                    <div style="text-align: right;">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div style="padding: 0.5rem 0.5rem; font-size: 0.7rem; display: flex; align-items: center;">
                        <p>
                            I tuoi dati sono al sicuro.<br>
                            Un nostro consulente ti contatterà per aiutarti a completare il tuo ordine.
                        </p>
                    </div>
                </div>
                <button class="css-plugin-preview-form-button" :style="cssPluginButtonStyle">{{ cssPluginButtonText }}</button>
            </div>

        </div>
    </div>
</template>

<script>
export default {
  props: {
    styleObject: Object,
  },
  data () {
    return {
      defaultStyle:  {
        name: '',
        type: 'css',
        code: '',
        background_color: '#ffffff',
        text_color: '#000000',
        input_background_color: '#ffffff',
        input_text_color: '#000000',
        input_border_color: '#f2e832',
        input_address_visible: true,
        button_background_color: '#eaac00',
        button_text_color: '#ffffff',
        button_text: 'Conferma l\'ordine',
        countdown_visible: false,
        countdown_text_background_color: '#ff2233',
        countdown_text_color: '#ffffff',
        countdown_time_color: '#000000',
        countdown_time: 5,
        people_viewing_visible: false,
        people_viewing_text_color: '#000000',
        people_viewing_counter_color: '#ffffff',
        people_viewing_counter_background_color: '#ff2233',
        available_quantity_visible: false,
        available_quantity_text_color: '#ffffff',
        available_quantity_bar_background_color: '#919191',
        available_quantity_bar_color: '#ff330f',
        trust_badge_visible: false,
        trust_badge_color: '#000000',
      },
    }
  },
  computed: {
    cssPluginContainerStyle() {
      return {
        backgroundColor: this.styleObject.background_color,
        color: this.styleObject.text_color,
      }
    },
    cssPluginInputStyle() {
      return {
        backgroundColor: this.styleObject.input_background_color,
        color: this.styleObject.input_text_color,
        borderColor: this.styleObject.input_border_color,
      }
    },
    cssPluginButtonStyle() {
      return {
        backgroundColor: this.styleObject.button_background_color,
        color: this.styleObject.button_text_color,
      }
    },
    cssPluginButtonText() {
      return this.styleObject.button_text
    },
    cssPluginInputAddressStyle() {
      return {
        display: (this.styleObject.input_address_visible) ? 'block' : 'none',
      }
    },
    cssPluginCountdownStyle() {
      return {
        display: (this.styleObject.countdown_visible) ? 'grid' : 'none',
      }
    },
    cssPluginCountdownTextStyle() {
      return {
        backgroundColor: this.styleObject.countdown_text_background_color,
        color: this.styleObject.countdown_text_color,
      }
    },
    cssPluginCountdownTimeStyle() {
      return {
        color: this.styleObject.countdown_time_color,
      }
    },
    cssPluginCountdownTime() {
      return this.styleObject.countdown_time + ':00'
    },
    cssPluginPeopleViewingStyle() {
      return {
        display: (this.styleObject.people_viewing_visible) ? 'block' : 'none',
        color: (this.styleObject.people_viewing_text_color),
      }
    },
    cssPluginPeopleViewingCounterStyle() {
      return {
        backgroundColor: (this.styleObject.people_viewing_counter_background_color),
        color: (this.styleObject.people_viewing_counter_color),
      }
    },
    cssPluginAvailableQuantityStyle() {
      return {
        display: (this.styleObject.available_quantity_visible) ? 'block' : 'none',
      }
    },
    cssPluginAvailableQuantityBarStyle() {
      return {
        '--barColor': this.styleObject.available_quantity_bar_color,
        'background-color': this.styleObject.available_quantity_bar_background_color,
        'color': this.styleObject.available_quantity_text_color,
      }
    },
    cssPluginTrustBadgeStyle() {
      return {
        display: (this.styleObject.trust_badge_visible) ? 'grid' : 'none',
        color: this.styleObject.trust_badge_color,
      }
    },
  },
  created() {
    if (this.styleObject == null || this.styleObject == undefined) {
      this.styleObject = this.defaultStyle
    }
  },
}
</script>

<style>
#css-plugin-preview-form-container {
    width: 100%;
    /*color: black;*/
    /*background-color: white;*/
}

#css-plugin-preview-main-form {
   padding: 15px;
   height: 100%;
}

.css-plugin-preview-form-input {
	margin-bottom: 0.5em;
	font-weight: 400;
	width: 100%;
	border-radius: 3px;
	padding: 15px;
	font-size: 22px;
	border-width: 1px;
	border-style: double;
	/*border-color: #f2e832;*/
	box-shadow: 0px 3px 10px 0px rgba(0,0,0,.5);
	font-family: "Open Sans",Sans-serif;
	font-weight: 400;
}

.css-plugin-preview-form-button {
    margin-bottom: 3em;
    border: 1px solid #e0e0e0;
    font-size: 25px;
    padding: 10px;
    width: 90%;
    margin-left: auto !important;
    margin-right: auto !important;
    display: block;
    float: none;
    font-family: "Arial",Sans-serif;
    font-weight: 600;
    text-transform: none;
    /*color: #fff;*/
    /*background-color: #eaac00;*/
    border-radius: 8px;
    box-shadow: 0px 0px 10px 0px rgba(0,0,0,.5);
}

.css-plugin-preview-countdown {
    width: 100%;
    padding: 0.2rem;
    font-size: 3.5rem;
    display: grid;
    grid-template-columns: auto auto auto;
    font-weight: bold;
}

.css-plugin-preview-countdown-text-container {
    padding: 0.5rem 0.5rem;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.css-plugin-preview-countdown-text {
    background-color: #ff2233;
    color: #ffffff;
    border-radius: 0.4rem;
    padding: 0rem 0.4rem;
}

.css-plugin-preview-countdown-time {
    font-size: 3rem;
}

.css-plugin-preview-people-viewing {
    display: none;
    width: 100%;
    padding: 0.2rem;
    text-align: center;
    color: #000000;
    font-weight: bold;
}

.css-plugin-preview-people-viewing-count {
    background-color: #ff2233;
    color: #ffffff;
    border-radius: 0.4rem;
    padding: 0rem 0.4rem;
}

.css-plugin-preview-available-quantity {
    display: none;
    width: 100%;
    padding: 0.2rem;
}

.css-plugin-preview-available-quantity-bar {
    --barColor: #ff330f;
    position: relative;
    width: 80%;
    margin-right: auto;
    margin-left: auto;
    height: 2.2em;
    background-color: #919191;
    color: #ffffff;
    border-radius: 0.5em;
}

.css-plugin-preview-available-quantity-bar::before {
    content: '';
    display: flex;
    align-items: center;
    position: absolute;
    left: 0.0em;
    top: 0.0em;
    bottom: 0.0em;
    width: 20%;
    min-width: 2rem;
    max-width: calc(100% - 1em);
    background-color: var(--barColor);
    border-radius: 0.5em;
    padding: 1em;
}

.css-plugin-preview-available-quantity-bar::after {
    content: attr(data-label);
    display: flex;
    align-items: center;
    position: absolute;
    right: 3.5em;
    top: 0.5em;
    bottom: 0.6em;
}

.css-plugin-preview-trust-badge {
    width: 100%;
    padding: 0.2rem;
    font-size: 3.5rem;
    margin-bottom: 1rem;
    display: grid;
    grid-template-columns: auto auto auto;
}
</style>
